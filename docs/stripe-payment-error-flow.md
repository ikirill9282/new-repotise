# Карта Stripe для неудачной оплаты

## Обзор процесса

```
[Stripe Payment Intent] 
    ↓ (ошибка)
[PaymentController::error()] 
    ↓
[resolveStripeErrorPayload()] 
    ↓
[mapStripeError()] 
    ↓
[resolvePaymentContext()] 
    ↓
[payment-error.blade.php]
```

## Маршрут

**URL:** `/payment/error?payment_intent={payment_intent_id}&reason={error_code}&decline_reason={decline_code}`

**Route:** `Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');`

**Controller:** `App\Http\Controllers\PaymentController::error()`

## Поток обработки ошибки

### 1. Получение Payment Intent
```php
$paymentIntent = $this->fetchPaymentIntent($request->query('payment_intent'));
```
- Извлекает Payment Intent из Stripe API по ID
- Может быть `null`, если ID не передан или не найден

### 2. Разрешение ошибки Stripe
```php
$errorPayload = $this->resolveStripeErrorPayload($request, $paymentIntent);
```

**Логика:**
1. Если есть `$paymentIntent` и `last_payment_error`:
   - Использует `last_payment_error->code` и `last_payment_error->decline_code`
2. Если есть query параметры `reason` или `decline_reason`:
   - Использует их напрямую
3. Иначе:
   - Возвращает дефолтное сообщение об ошибке

### 3. Маппинг ошибки
```php
mapStripeError(?string $code, ?string $declineCode, ?string $currency = null): array
```

**Приоритет проверки:**
1. `$code` (основной код ошибки)
2. `$declineCode` (код отклонения от банка)
3. `'default'` (дефолтное сообщение)

**Возвращает:**
```php
[
    'title' => 'Название ошибки',
    'message' => 'Подробное сообщение для пользователя',
    'push' => 'Краткое сообщение',
    'code' => 'код_ошибки'
]
```

### 4. Разрешение контекста платежа
```php
$context = $this->resolvePaymentContext($request, $paymentIntent, $errorPayload);
```

**Возвращает:**
```php
[
    'cart' => [
        'products' => [...], // Товары из заказа
    ],
    'summary' => [
        'items' => 0,
        'subtotal' => 0.00,
        'discount' => 0.00,
        'tax' => 0.00,
        'total' => 0.00,
        'currency' => 'USD',
    ],
    'paymentDetails' => [
        'method' => 'Card payment',
        'order_number' => null,
        'date' => '12.04.2025',
        'time' => '14:30',
        'status' => 'Payment failed',
    ],
]
```

**Логика получения заказа:**
1. Ищет Payment по `payment_intent_id` в БД
2. Если найден, получает связанный Order через `paymentable`
3. Если не найден, пытается получить Order из сессии (`resolveOrderFromSession()`)

## Типы ошибок Stripe

### Ошибки карты

| Код | Название | Описание |
|-----|----------|----------|
| `card_declined` | Card declined (generic) | Карта отклонена банком (общая ошибка) |
| `insufficient_funds` | Insufficient funds | Недостаточно средств на карте |
| `expired_card` | Expired card | Карта просрочена |
| `incorrect_number` | Incorrect card number | Неверный номер карты |
| `invalid_number` | Invalid card number (format) | Неверный формат номера карты |
| `incorrect_cvc` | Incorrect CVC | Неверный CVC код |
| `invalid_cvc` | Invalid CVC (format) | Неверный формат CVC |
| `incorrect_zip` | Incorrect postal code | Неверный почтовый индекс |
| `incorrect_address` | Incorrect billing address | Неверный адрес для выставления счета |

### Ошибки даты истечения

| Код | Название | Описание |
|-----|----------|----------|
| `invalid_expiry_month` | Invalid expiry month | Неверный месяц истечения |
| `invalid_expiry_year` | Invalid expiry year | Неверный год истечения |

### Ошибки аутентификации (3D Secure)

| Код | Название | Описание |
|-----|----------|----------|
| `payment_intent_authentication_failure` | Authentication failed (3DS) | Аутентификация не пройдена |
| `setup_intent_authentication_failure` | Authentication failed (3DS) | Аутентификация не пройдена (setup) |
| `invoice_payment_intent_requires_action` | Authentication required (3DS) | Требуется аутентификация |
| `payment_intent_action_required` | Authentication required (3DS) | Требуется действие для аутентификации |

### Ошибки метода оплаты

| Код | Название | Описание |
|-----|----------|----------|
| `payment_method_not_available` | Processor unavailable | Метод оплаты временно недоступен |
| `payment_method_currency_mismatch` | Currency not supported by card | Карта не поддерживает валюту |

### Ошибки обработки

| Код | Название | Описание |
|-----|----------|----------|
| `processing_error` | Processing error | Ошибка обработки платежа |
| `payment_method_provider_timeout` | Provider timeout | Таймаут провайдера платежей |
| `payment_method_provider_decline` | Provider/issuer decline | Отклонено провайдером/банком |

### Ошибки лимитов

| Код | Название | Описание |
|-----|----------|----------|
| `card_decline_rate_limit_exceeded` | Card declined — too many attempts | Слишком много попыток |

### Ошибки времени

| Код | Название | Описание |
|-----|----------|----------|
| `charge_expired_for_capture` | Authorization expired | Авторизация истекла |

### Другие ошибки

| Код | Название | Описание |
|-----|----------|----------|
| `duplicate_transaction` | Duplicate / already submitted | Дублирующая транзакция |
| `self_purchase` | Purchase unavailable | Покупка собственного товара |
| `internal_error` | Payment failed | Внутренняя ошибка |
| `default` | Payment failed | Дефолтное сообщение об ошибке |

## Структура данных ошибки

```php
[
    'title' => string,      // Заголовок ошибки (для отображения)
    'message' => string,   // Подробное сообщение для пользователя
    'push' => string,      // Краткое сообщение (для push-уведомлений)
    'code' => string,      // Код ошибки для логирования
]
```

## Примеры использования

### Пример 1: Ошибка от Stripe API
```
GET /payment/error?payment_intent=pi_1234567890
```
- Payment Intent содержит `last_payment_error->code = 'card_declined'`
- Результат: отображается сообщение "Card declined (generic)"

### Пример 2: Ошибка через query параметры
```
GET /payment/error?reason=insufficient_funds&decline_reason=insufficient_funds
```
- Результат: отображается сообщение "Insufficient funds"

### Пример 3: Неизвестная ошибка
```
GET /payment/error?payment_intent=pi_unknown
```
- Payment Intent не найден или не содержит ошибок
- Результат: отображается дефолтное сообщение "Payment failed"

## Страница ошибки

**View:** `resources/views/site/pages/payment-error.blade.php`

**Отображает:**
- Заголовок ошибки (`$errorInfo['title']`)
- Сообщение об ошибке (`$errorInfo['message']`)
- Дополнительное сообщение (`$errorInfo['push']`)
- Детали заказа (товары, сумма)
- Детали платежа (метод, дата, статус)
- Кнопка "Try Again" (ведет на checkout или products)

## Логирование

Ошибки логируются в:
- `storage/logs/laravel-{date}.log`
- При необходимости можно добавить логирование в `mapStripeError()`

## Рекомендации

1. **Всегда проверяйте наличие Payment Intent** перед обработкой ошибки
2. **Используйте приоритет кодов:** сначала `code`, потом `decline_code`, потом `default`
3. **Обрабатывайте валюту:** некоторые ошибки требуют замены `[CURRENCY]` на реальную валюту
4. **Сохраняйте контекст:** всегда передавайте информацию о заказе для отображения пользователю
5. **Предоставляйте возможность повтора:** кнопка "Try Again" должна вести на корректную страницу

## Тестовые карты Stripe для неудачной оплаты

### Общие параметры для всех тестовых карт:
- **CVC:** любой 3-значный код (например, `123`)
- **Дата истечения:** любая будущая дата (например, `12/25`)
- **ZIP код:** любой (например, `12345`)

### Карты для тестирования ошибок:

#### 1. Карта отклонена (generic decline)
```
Номер карты: 4000 0000 0000 0002
Ошибка: card_declined
Сообщение: "Your card was declined. Please try a different card or contact your bank for details."
```

#### 2. Недостаточно средств
```
Номер карты: 4000 0000 0000 9995
Ошибка: insufficient_funds
Сообщение: "Insufficient funds. Your bank declined the charge — please use another card or payment method."
```

#### 3. Просроченная карта
```
Номер карты: 4000 0000 0000 0069
Ошибка: expired_card
Сообщение: "This card has expired. Please use a different card or update the expiration date."
```

#### 4. Неверный номер карты
```
Номер карты: 4000 0000 0000 0127
Ошибка: incorrect_number
Сообщение: "Invalid card number. Please check the card number and try again."
```

#### 5. Неверный CVC
```
Номер карты: 4000 0000 0000 0101
Ошибка: incorrect_cvc
Сообщение: "Incorrect security code (CVC). Re-enter the 3- or 4-digit code from your card."
```

#### 6. Неверный почтовый индекс
```
Номер карты: 4000 0000 0000 0036
Ошибка: incorrect_zip
Сообщение: "Billing postal code looks incorrect. Update the ZIP/postal code and try again."
```

#### 7. Ошибка обработки
```
Номер карты: 4000 0000 0000 0119
Ошибка: processing_error
Сообщение: "A processing error occurred. Please try again or use a different payment method."
```

#### 8. Требуется аутентификация (3D Secure)
```
Номер карты: 4000 0025 0000 3155
Ошибка: payment_intent_action_required
Сообщение: "Your bank requires authentication. Complete the verification in the bank's window to finish the payment."
```

#### 9. Аутентификация не пройдена (3D Secure)
```
Номер карты: 4000 0000 0000 3220
Ошибка: payment_intent_authentication_failure
Сообщение: "Authentication failed. Your bank didn't complete verification — try again or use a different payment method."
```

#### 10. Отклонено банком/провайдером
```
Номер карты: 4000 0000 0000 0341
Ошибка: payment_method_provider_decline
Сообщение: "Payment was declined by the card issuer. Try a different card or contact your bank."
```

#### 11. Слишком много попыток
```
Номер карты: 4000 0000 0000 0341
(после нескольких попыток)
Ошибка: card_decline_rate_limit_exceeded
Сообщение: "Too many attempts with this card. Please wait 24 hours or try a different card."
```

#### 12. Таймаут провайдера
```
Номер карты: 4000 0000 0000 0119
(в некоторых случаях)
Ошибка: payment_method_provider_timeout
Сообщение: "We couldn't reach the payment provider. Please try again in a few minutes or use another payment method."
```

### Успешные тестовые карты (для сравнения):

#### Успешная оплата
```
Номер карты: 4242 4242 4242 4242
Результат: Успешная оплата
```

#### Успешная оплата (Visa)
```
Номер карты: 4000 0566 5566 5556
Результат: Успешная оплата
```

### Как использовать:

1. **В тестовом режиме Stripe:**
   - Используйте эти номера карт в форме оплаты
   - Stripe автоматически вернет соответствующую ошибку
   - Не требуется реальная карта

2. **Для тестирования в коде:**
   ```php
   // Пример: тестирование ошибки недостатка средств
   $cardNumber = '4000000000009995';
   $cvc = '123';
   $expiryMonth = '12';
   $expiryYear = '2025';
   ```

3. **Проверка на странице ошибки:**
   - После использования тестовой карты с ошибкой
   - Перейдите на `/payment/error?payment_intent={payment_intent_id}`
   - Или используйте query параметры: `/payment/error?reason=insufficient_funds`

### Дополнительные тестовые карты Stripe:

Полный список тестовых карт доступен в документации Stripe:
https://stripe.com/docs/testing#cards

### Важно:

- ✅ Эти карты работают **только в тестовом режиме** Stripe
- ✅ Используйте **test API keys** (начинаются с `sk_test_` и `pk_test_`)
- ❌ Не работают в **production режиме**
- ❌ Не требуют реальных денег

## Связанные файлы

- `app/Http/Controllers/PaymentController.php` - основной контроллер
- `resources/views/site/pages/payment-error.blade.php` - шаблон страницы ошибки
- `routes/web.php` - маршруты (строка 93)
- `app/Models/Payments.php` - модель платежей
- `app/Models/Order.php` - модель заказов

