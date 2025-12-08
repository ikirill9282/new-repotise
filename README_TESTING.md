# Testing Guide

## Запуск тестов

### Все тесты
```bash
php artisan test
# или
composer test
```

### Unit тесты
```bash
php artisan test --testsuite=Unit
# или
composer test:unit
```

### Feature тесты
```bash
php artisan test --testsuite=Feature
# или
composer test:feature
```

### Browser тесты (Dusk)
```bash
php artisan dusk
# или
composer test:dusk
```

### С покрытием кода
```bash
php artisan test --coverage
# или
composer test:coverage
```

### Параллельное выполнение
```bash
php artisan test --parallel
# или
composer test:parallel
```

## Покрытие кода

После запуска тестов с `--coverage`, отчеты будут доступны в:
- HTML: `coverage/html/index.html`
- Text: `coverage/coverage.txt`
- Clover XML: `coverage/clover.xml`

## CI/CD

### GitHub Actions
Тесты автоматически запускаются при push в `main` или `develop` ветки.

### Pre-commit Hook
Перед каждым коммитом автоматически запускаются Unit тесты.

## Структура тестов

```
tests/
├── Unit/              # Unit тесты
│   ├── Helpers/       # Тесты хелперов
│   ├── Jobs/          # Тесты Jobs
│   ├── Events/        # Тесты Events
│   ├── Listeners/     # Тесты Listeners
│   └── Services/     # Тесты сервисов
├── Feature/           # Feature тесты
│   ├── Api/           # API тесты
│   ├── Livewire/      # Livewire тесты
│   └── Security/      # Тесты безопасности
├── Browser/           # Browser тесты (Dusk)
├── E2E/               # End-to-end тесты
└── Performance/      # Тесты производительности
```

## Настройка окружения

Все тесты используют:
- SQLite in-memory базу данных
- Отключенный Scout (Meilisearch)
- Замокированные внешние сервисы (Stripe, Mailgun, GA4, Socialite)
- Автоматический TestSeeder для базовых данных

## Добавление новых тестов

1. Создайте файл в соответствующей директории
2. Наследуйтесь от `Tests\TestCase`
3. Используйте `RefreshDatabase` trait для очистки БД
4. Используйте `$this->createUserWithoutEvents()` для создания пользователей
5. Запустите тесты: `php artisan test`

## Моки внешних сервисов

Все внешние сервисы автоматически мокируются в `TestCase::setUp()`:
- Stripe
- Mailgun
- Google Analytics (GA4)
- Socialite (Google, Facebook, X)

Для дополнительных моков используйте методы:
- `$this->mockStripe()`
- `$this->mockSocialite('google')`
- `$this->mockGA4Service()`
