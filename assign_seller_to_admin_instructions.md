# Инструкция: Добавление роли продавца администратору

## Способ 1: Через Artisan команду (рекомендуется)

Если Docker контейнеры запущены:

```bash
docker-compose exec app php artisan admin:assign-seller-role
```

Или если используется Laravel Sail:

```bash
./vendor/bin/sail artisan admin:assign-seller-role
```

## Способ 2: Через Tinker

```bash
php artisan tinker
```

Затем выполните:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

// Найти админа
$admin = User::role(['super-admin', 'admin'])->first();

// Если не найден по роли, попробовать по email
if (!$admin) {
    $admin = User::where('email', env('ADMIN_MAIL'))->first();
}

// Получить или создать роль creator (продавца)
$creatorRole = Role::firstOrCreate(
    ['name' => 'creator'],
    ['name' => 'creator', 'title' => 'Creator']
);

// Назначить роль, если её еще нет
if ($admin && !$admin->hasRole('creator')) {
    $admin->assignRole($creatorRole);
    echo "Роль продавца успешно добавлена админу: {$admin->email}\n";
} else {
    echo "Админ уже имеет роль продавца или админ не найден\n";
}
```

## Способ 3: Прямой SQL (если нужно)

Если нужно выполнить напрямую через SQL, можно использовать:

```sql
-- Найти ID админа
SELECT id, email FROM users WHERE id IN (
    SELECT model_id FROM model_has_roles WHERE role_id IN (
        SELECT id FROM roles WHERE name IN ('super-admin', 'admin')
    )
) LIMIT 1;

-- Найти ID роли creator
SELECT id FROM roles WHERE name = 'creator';

-- Добавить роль (замените USER_ID и ROLE_ID на реальные значения)
INSERT INTO model_has_roles (role_id, model_type, model_id)
SELECT r.id, 'App\\Models\\User', u.id
FROM roles r, users u
WHERE r.name = 'creator'
AND u.id = USER_ID
AND NOT EXISTS (
    SELECT 1 FROM model_has_roles mhr
    WHERE mhr.role_id = r.id
    AND mhr.model_id = u.id
    AND mhr.model_type = 'App\\Models\\User'
);
```


