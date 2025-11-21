# Настройка OAuth для X (Twitter)

## Шаг 1: Настройка в Twitter Developer Portal

### 1.1. Создание/настройка приложения

1. Перейдите на https://developer.twitter.com/en/portal/dashboard
2. Войдите в свой аккаунт
3. Выберите ваше приложение или создайте новое:
   - Нажмите "Create App" или выберите существующее приложение
   - Заполните необходимые поля (App name, Website URL)

### 1.2. Настройка Callback URL

1. В настройках приложения найдите раздел **"App settings"** → **"User authentication settings"**
2. Включите **"Set up"** для OAuth 1.0a или OAuth 2.0 (рекомендуется OAuth 2.0)
3. В поле **"Callback URI / Redirect URL"** укажите:
   ```
   https://ваш-домен.com/auth/x/callback
   ```
   Например:
   ```
   https://trekguider.com/auth/x/callback
   https://www.trekguider.com/auth/x/callback
   ```
   ⚠️ **Важно**: Укажите все варианты домена (с www и без), если используете оба.

4. В поле **"Website URL"** укажите главный URL вашего сайта:
   ```
   https://ваш-домен.com
   ```

5. В разделе **"App permissions"** выберите:
   - **Read** (для OAuth 2.0) - достаточно для получения данных пользователя и email
   - Или **Read and write** (если планируете публикацию твитов)

6. Нажмите **"Save"**

### 1.3. Получение ключей

1. После сохранения настроек перейдите в раздел **"Keys and tokens"**
2. Найдите:
   - **API Key** (это ваш `X_CLIENT_ID`)
   - **API Secret Key** (это ваш `X_CLIENT_SECRET`)
   
   ⚠️ **Важно**: Для OAuth 2.0 также понадобится:
   - **Client ID** (для OAuth 2.0)
   - **Client Secret** (для OAuth 2.0)

3. Скопируйте эти значения

### 1.4. Настройка разрешений (Scopes)

Для получения email пользователя нужно:

1. В настройках OAuth убедитесь, что включены следующие разрешения:
   - `tweet.read` - чтение твитов
   - `users.read` - чтение информации о пользователе (включая email)
   - `offline.access` - для refresh token (опционально)

2. В разделе **"App info"** → **"Additional OAuth 2.0 scopes"** добавьте:
   ```
   users.read
   tweet.read
   offline.access
   ```

### 1.5. Включение получения email

⚠️ **Критически важно для работы регистрации:**

1. В настройках OAuth 2.0 найдите раздел **"Request email address from users"**
2. Включите эту опцию ✅
3. Заполните Privacy Policy URL и Terms of Service URL (обязательно для получения email)

## Шаг 2: Настройка в проекте (.env файл)

Откройте файл `.env` в корне проекта и добавьте/проверьте следующие переменные:

```env
X_CLIENT_ID=ваш_api_key_или_client_id
X_CLIENT_SECRET=ваш_api_secret_или_client_secret
X_REDIRECT_URI=https://ваш-домен.com/auth/x/callback
```

**Пример:**
```env
X_CLIENT_ID=abc123xyz456
X_CLIENT_SECRET=def789ghi012jkl345mno678pqr901
X_REDIRECT_URI=https://trekguider.com/auth/x/callback
```

⚠️ **Важно**: 
- URL в `X_REDIRECT_URI` должен **точно совпадать** с URL в Twitter Developer Portal
- Используйте HTTPS (не HTTP)
- Не добавляйте trailing slash `/` в конце

## Шаг 3: Проверка конфигурации

### 3.1. Проверка в коде

Текущая конфигурация находится в:
- `config/services.php` - настройки OAuth
- `app/Http/Controllers/AuthController.php` - метод `xCallback()`
- `app/Livewire/Modals/Register.php` - метод `xAuth()`
- `routes/web.php` - маршрут `/auth/x/callback`

### 3.2. Проверка через консоль

Выполните команду для проверки конфигурации:
```bash
php artisan tinker
```

Затем в Tinker:
```php
config('services.x');
```

Должно вернуть массив с `client_id`, `client_secret`, `redirect`.

### 3.3. Проверка URL

Убедитесь, что callback URL доступен:
```bash
curl -I https://ваш-домен.com/auth/x/callback
```

## Шаг 4: Тестирование

1. Откройте сайт и нажмите кнопку "Sign in with X (Twitter)" или "Register with X"
2. Должен произойти редирект на Twitter/X для авторизации
3. После авторизации вы будете перенаправлены обратно на сайт
4. Проверьте логи Laravel на наличие ошибок:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Часто встречающиеся ошибки

### Ошибка: "Invalid redirect URI"
**Причина**: Callback URL в Twitter Developer Portal не совпадает с URL в `.env`
**Решение**: Убедитесь, что оба URL идентичны (включая протокол HTTPS)

### Ошибка: "Email not provided"
**Причина**: В настройках Twitter не включена опция запроса email
**Решение**: 
1. Включите "Request email address from users" в настройках OAuth
2. Заполните Privacy Policy URL и Terms of Service URL

### Ошибка: "Invalid client credentials"
**Причина**: Неправильные `X_CLIENT_ID` или `X_CLIENT_SECRET`
**Решение**: Перепроверьте ключи в `.env` и Twitter Developer Portal

### Ошибка: "Unauthorized"
**Причина**: Приложение не имеет необходимых разрешений
**Решение**: Убедитесь, что в настройках OAuth включены необходимые scopes

## Проверочный чеклист

- [ ] Приложение создано в Twitter Developer Portal
- [ ] OAuth 2.0 настроен и включен
- [ ] Callback URL настроен в Twitter Developer Portal
- [ ] Callback URL совпадает с `X_REDIRECT_URI` в `.env`
- [ ] Опция "Request email address from users" включена
- [ ] Privacy Policy URL и Terms of Service URL заполнены
- [ ] Необходимые scopes добавлены (`users.read`, `tweet.read`)
- [ ] `X_CLIENT_ID` и `X_CLIENT_SECRET` правильно указаны в `.env`
- [ ] URL в `.env` используют HTTPS (не HTTP)
- [ ] Маршрут `/auth/x/callback` настроен в `routes/web.php`
- [ ] Метод `xCallback()` реализован в `AuthController`

## Дополнительные ресурсы

- Twitter Developer Portal: https://developer.twitter.com/en/portal/dashboard
- Twitter OAuth 2.0 Documentation: https://developer.twitter.com/en/docs/authentication/oauth-2-0
- Laravel Socialite Documentation: https://laravel.com/docs/socialite

