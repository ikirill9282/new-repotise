# Отчет о проверке Dashboard

## Дата проверки: 2024

## Статус: ТРЕБУЮТСЯ ДОРАБОТКИ

---

## 1. КРИТИЧЕСКИЕ ОТСУТСТВУЮЩИЕ ЭЛЕМЕНТЫ

### 1.1. Date Range Selector
**Статус**: ❌ ОТСУТСТВУЕТ  
**Требование ТЗ**: Селектор диапазона дат должен быть вверху страницы с предустановленными диапазонами (Сегодня, Вчера, Последние 7 дней, Последние 30 дней, Этот месяц, Последние 90 дней, Этот год) и возможностью выбора произвольного диапазона.  
**Текущее состояние**: Виджеты используют жестко заданный период 30 дней в методе `getPeriod()`.  
**Файлы для изменений**:
- `app/Filament/Pages/Dashboard.php`
- `resources/views/filament/pages/dashboard.blade.php`
- Все виджеты в `app/Filament/Widgets/`

---

### 1.2. Key Metrics Overview - Отсутствующие метрики
**Статус**: ❌ ЧАСТИЧНО РЕАЛИЗОВАНО  

#### Отсутствуют следующие метрики из ТЗ:

| Метрика | Источник данных | Статус | Приоритет |
|---------|----------------|--------|-----------|
| GMV (Gross Merchandise Volume) | Transactions Log | ❌ Отсутствует | 🔴 Высокий |
| Net Platform Revenue | Transactions Log (application_fee_amount) | ❌ Отсутствует | 🔴 Высокий |
| New Orders | Transactions Log | ❌ Отсутствует | 🔴 Высокий |
| New Subscriptions | Stripe Billing API / Internal DB | ❌ Отсутствует | 🔴 Высокий |
| Platform Active Sellers | User Login Logs | ❌ Отсутствует | 🟡 Средний |
| Stripe Active Sellers (Monthly) | Stripe API / Payout Logs | ❌ Отсутствует | 🟡 Средний |
| Sellers Awaiting Payout | Stripe Balance API | ❌ Отсутствует | 🟡 Средний |
| Estimated Stripe Seller Fees | Calculation | ❌ Отсутствует | 🟡 Средний |
| Visits | Google Analytics API | ❌ Отсутствует | 🟡 Средний |
| New Buyer Referrals | Referral Tracking Data | ❌ Отсутствует | 🟡 Средний |
| New Seller Referrals | Referral Tracking Data | ❌ Отсутствует | 🟡 Средний |
| Referral Bonuses Paid ($) | Transactions Log | ❌ Отсутствует | 🟡 Средний |
| Referral Promo Codes Issued | Internal Promo Code Log | ❌ Отсутствует | 🟢 Низкий |
| Total Donations (GMV) | Transactions Log | ❌ Отсутствует | 🟡 Средний |

#### Реализованные метрики:
- ✅ Active Users (UsersWidget)
- ✅ New Users (UsersWidget)
- ✅ Active Products (ProductsWidget)
- ✅ Pending Products (ProductsWidget)

**Примечание**: Реализованные метрики не показывают % изменения к предыдущему периоду, как требует ТЗ.

---

### 1.3. Moderation & Tasks Overview
**Статус**: ⚠️ ЧАСТИЧНО РЕАЛИЗОВАНО  

#### Реализовано:
- ✅ Total Pending Moderation (ModerationWidget)
- ✅ Pending Products (ModerationWidget)
- ✅ New Complaints (ComplaintsWidget)

#### Отсутствует:
- ❌ Verification Action Required (Stripe)
- ❌ Verification Pending Manual Review
- ❌ Pending Full Verification ($100 Threshold)
- ❌ Pending Articles
- ❌ New Support Tickets
- ❌ Open Disputes
- ❌ Flagged Comments/Reviews
- ❌ User Complaints (отдельно от общих жалоб)
- ❌ Seller Profile Reviews
- ❌ Category/Location Suggestions

**Проблема**: Текущий ModerationWidget показывает только Products, Articles, Reviews, но не все типы задач из ТЗ.

---

### 1.4. Recent Activity Feeds
**Статус**: ❌ ПОЛНОСТЬЮ ОТСУТСТВУЕТ  

#### Отсутствующие ленты:
- ❌ Recent Orders (ID Заказа, Название Продукта, Покупатель, Сумма, Время)
- ❌ Recent Registrations (Имя Пользователя, Роль, Время)
- ❌ Recent Moderation Queue Items (Тип Задачи, Элемент/Продавец, Время)
- ❌ Recent Payouts (Failed/Paid) (Stripe Payout ID, Продавец, Сумма, Статус, Время)
- ❌ Recent Content (Заголовок, Автор, Тип, Время публикации)

**Требование ТЗ**: По 5 последних записей для каждой ленты с ссылками на соответствующие разделы.

---

### 1.5. Performance Charts
**Статус**: ⚠️ ЧАСТИЧНО РЕАЛИЗОВАНО  

#### Реализовано:
- ✅ Transactions Trend (TransactionsWidget) - частично соответствует GMV Trend
- ✅ User Activity Chart (ActivityWidget) - показывает логины, заказы, статьи, комментарии
- ✅ Revenue by Category (RevenueWidget) - pie chart

#### Отсутствует/Не соответствует:
- ❌ GMV Trend (должен быть отдельный график)
- ❌ Net Platform Revenue Trend
- ❌ User Registrations Trend (отдельный график)
- ❌ Visits Trend (из GA4)

**Проблема**: Графики не имеют ссылок "Подробнее" -> Analytics, как требует ТЗ.

---

### 1.6. Admin Notifications
**Статус**: ⚠️ ЧАСТИЧНО РЕАЛИЗОВАНО  

#### Реализовано:
- ✅ Базовый виджет уведомлений (NotificationsWidget)
- ✅ Отображение непрочитанных уведомлений

#### Отсутствует функционал:
- ❌ Критические ошибки Stripe API (автоматическое обнаружение)
- ❌ Проблемы с Stripe Webhooks
- ❌ Массовые сбои выплат (автоматическое обнаружение > 5 failed за час или > 10% за 24ч)
- ❌ Резкий рост споров (автоматическое обнаружение > 5 новых за 24ч или > $500)
- ❌ Проблемы с верификацией (автоматическое обнаружение)
- ❌ Проблемы с API внешних сервисов
- ❌ Уведомления о безопасности
- ❌ Дублирование критических уведомлений на email
- ❌ Кнопка 'Dismiss' для удаления уведомлений

---

### 1.7. Quick Actions
**Статус**: ❌ ПОЛНОСТЬЮ ОТСУТСТВУЕТ  

#### Отсутствующие кнопки быстрого доступа:
- ❌ Добавить Продукт -> Products -> Add New Product
- ❌ Добавить Статью -> Content -> Add New Article
- ❌ Добавить Пользователя -> Users -> Add New User
- ❌ Перейти к Модерации -> Community -> Queue Moderation
- ❌ Просмотреть Транзакции -> Financials -> Transactions Log
- ❌ Общие Настройки -> Settings -> General

---

## 2. ТЕХНИЧЕСКИЕ ПРОБЛЕМЫ

### 2.1. Виджеты не используют общий Date Range
**Проблема**: Каждый виджет имеет свой метод `getPeriod()` с жестко заданным периодом 30 дней.  
**Решение**: Необходимо реализовать общий механизм передачи периода с Dashboard через Livewire properties.

**Затронутые файлы**:
- `app/Filament/Widgets/UsersWidget.php`
- `app/Filament/Widgets/ProductsWidget.php`
- `app/Filament/Widgets/TransactionsWidget.php`
- `app/Filament/Widgets/ActivityWidget.php`
- `app/Filament/Widgets/RevenueWidget.php`

---

### 2.2. Отсутствие расчета % изменения
**Проблема**: Метрики не показывают % изменения к предыдущему периоду.  
**Решение**: Реализовать расчет предыдущего периода и % изменения в каждом виджете.

---

### 2.3. Отсутствие ссылок на детальные отчеты
**Проблема**: Не все метрики имеют ссылки на соответствующие разделы, как требует ТЗ.  
**Решение**: Добавить `->url()` для всех метрик в соответствии с ТЗ.

---

## 3. РЕКОМЕНДАЦИИ ПО ИСПРАВЛЕНИЮ

### Приоритет 1 (Критический):
1. ✅ Добавить Date Range Selector на Dashboard
2. ✅ Реализовать передачу периода во все виджеты
3. ✅ Добавить основные метрики: GMV, Net Platform Revenue, New Orders, New Subscriptions

### Приоритет 2 (Высокий):
4. ✅ Добавить Recent Activity Feeds
5. ✅ Расширить Moderation & Tasks Overview
6. ✅ Реализовать расчет % изменения для метрик

### Приоритет 3 (Средний):
7. ✅ Добавить Quick Actions
8. ✅ Улучшить Admin Notifications с автоматическим обнаружением проблем
9. ✅ Добавить недостающие графики Performance Charts

### Приоритет 4 (Низкий):
10. ✅ Добавить оставшиеся метрики (Referrals, Stripe метрики)
11. ✅ Добавить ссылки "Подробнее" на все графики

---

## 4. ФАЙЛЫ, ТРЕБУЮЩИЕ ИЗМЕНЕНИЙ

### Основные файлы:
- `app/Filament/Pages/Dashboard.php` - добавление Date Range Selector и Quick Actions
- `resources/views/filament/pages/dashboard.blade.php` - обновление шаблона
- `app/Filament/Widgets/*.php` - обновление всех виджетов для использования Date Range

### Новые файлы (возможные):
- `app/Filament/Widgets/GMVWidget.php` - новый виджет для GMV метрик
- `app/Filament/Widgets/RevenueMetricsWidget.php` - виджет для финансовых метрик
- `app/Filament/Widgets/RecentActivityWidget.php` - виджет для Recent Activity Feeds
- `app/Services/Dashboard/MetricCalculatorService.php` - сервис для расчетов метрик
- `app/Services/Dashboard/NotificationDetectorService.php` - сервис для автоматического обнаружения проблем

---

## 5. ИНТЕГРАЦИИ, ТРЕБУЮЩИЕ ПРОВЕРКИ

- ✅ Google Analytics API - для метрики Visits и Visits Trend
- ✅ Stripe API - для метрик продавцов, выплат, споров
- ✅ User Login Logs - для Platform Active Sellers
- ✅ Referral Tracking Data - для реферальных метрик

---

**Итоговая оценка соответствия ТЗ: ~30%**

