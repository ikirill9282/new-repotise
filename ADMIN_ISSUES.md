# Проблемные места в админке Filament

## 1. Методы table() без поддержки Infolist

### Проблема
Некоторые методы `table()` в Filament Pages не поддерживают тип `Infolist`, что вызывает ошибки типа при попытке Filament автоматически определить тип.

### Файлы, требующие исправления:

#### 1.1. `app/Filament/Pages/SettingsSecurity.php`
**Строка 330:**
```php
public function table(Table $table): Table
```
**Исправление:**
```php
use Filament\Infolists\Infolist;

public function table(Table|Infolist $table): Table|Infolist
{
    if ($table instanceof Infolist) {
        return $table;
    }
    
    return $table
        // ... остальной код
}
```

#### 1.2. `app/Filament/Pages/LoginHistory.php`
**Строка 27:**
```php
public function table(Table $table): Table
```
**Исправление:**
```php
use Filament\Infolists\Infolist;

public function table(Table|Infolist $table): Table|Infolist
{
    if ($table instanceof Infolist) {
        return $table;
    }
    
    return $table
        // ... остальной код
}
```

#### 1.3. `app/Filament/Pages/ContactForms.php`
**Строка 27:**
```php
public function table(Table $table): Table
```
**Исправление:**
```php
use Filament\Infolists\Infolist;

public function table(Table|Infolist $table): Table|Infolist
{
    if ($table instanceof Infolist) {
        return $table;
    }
    
    return $table
        // ... остальной код
}
```

---

## 2. Использование route() вместо Resource::getUrl()

### Проблема
Использование `route()` для генерации URL ресурсов Filament может привести к ошибкам, если маршрут не определен. Рекомендуется использовать `Resource::getUrl()`.

### Файлы, требующие исправления:

#### 2.1. `app/Filament/Resources/ProductComplaintResource.php`

**Строка 92:**
```php
->url(fn ($record) => $record->reportable ? route('filament.admin.resources.products.edit', ['record' => $record->reportable_id]) : null)
```
**Исправление:**
```php
use App\Filament\Resources\ProductResource;

->url(fn ($record) => $record->reportable ? ProductResource::getUrl('edit', ['record' => $record->reportable_id]) : null)
```

**Строка 141:**
```php
->url(fn ($record) => route('filament.admin.resources.product-complaints.view', ['record' => $record->id]))
```
**Исправление:**
```php
->url(fn ($record) => ProductComplaintResource::getUrl('view', ['record' => $record->id]))
```

---

## 3. Уже исправленные проблемы (для справки)

### ✅ Исправлено:
- `app/Filament/Pages/UserFundsHistory.php` - метод `table()` теперь поддерживает Infolist
- `app/Filament/Pages/RolesPermissions.php` - методы `rolesTable()` и `permissionsTable()` теперь поддерживают Infolist
- `app/Filament/Pages/SettingsSecurity.php` - метод `passwordForm()` теперь поддерживает Infolist
- `app/Filament/Resources/RefundRequestResource.php` - заменено `UserResource::getUrl('edit')` на `UserResource::getUrl('view')`
- `app/Filament/Resources/ProductComplaintResource.php` - заменено `UserResource::getUrl('edit')` на `UserResource::getUrl('view')` (2 места)
- `app/Filament/Resources/PayoutResource.php` - заменено `UserResource::getUrl('edit')` на `UserResource::getUrl('view')`
- `app/Filament/Resources/PayoutResource/Pages/ViewPayout.php` - заменено `UserResource::getUrl('edit')` на `UserResource::getUrl('view')`
- Удален `defaultSort()` с колонок в:
  - `app/Filament/Pages/SettingsSecurity.php`
  - `app/Filament/Resources/PageResource.php`
  - `app/Filament/Resources/ModerationQueueResource.php`
  - `app/Filament/Resources/CommentResource.php`
  - `app/Filament/Resources/UserComplaintResource.php`
- `app/Models/SystemSetting.php` - добавлено `protected $table = 'settings';`

---

## Резюме

### Требуется исправление:
1. **3 метода table()** без поддержки Infolist:
   - `SettingsSecurity::table()`
   - `LoginHistory::table()`
   - `ContactForms::table()`

2. **2 использования route()** вместо Resource::getUrl():
   - `ProductComplaintResource` строка 92 (products.edit)
   - `ProductComplaintResource` строка 141 (product-complaints.view)

### Всего проблемных мест: 5

---

## 4. Оптимизация скорости загрузки сайта

### Проблема
Сайт может загружаться медленно из-за неоптимизированных ресурсов, отсутствия кэширования и других факторов производительности.

### Рекомендации по оптимизации:

#### 4.1. Оптимизация CSS и JavaScript
- **Минификация и объединение файлов:**
  - Использовать Laravel Mix/Vite для сборки и минификации CSS/JS
  - Включить tree-shaking для удаления неиспользуемого кода
  - Объединить мелкие файлы в один bundle где это возможно

- **Lazy loading для JavaScript:**
  ```javascript
  // В blade шаблонах использовать defer или async
  <script src="..." defer></script>
  // Или для некритичного контента:
  <script src="..." async></script>
  ```

- **Проверка текущей конфигурации:**
  - Проверить `vite.config.js` на наличие минификации в production режиме
  - Убедиться, что в production используется сжатие файлов

#### 4.2. Оптимизация изображений
- **Сжатие изображений:**
  - Использовать WebP формат вместо JPEG/PNG где возможно
  - Применять сжатие изображений при загрузке (например, через Intervention Image)
  - Генерировать несколько размеров изображений (thumbnail, medium, large)

- **Lazy loading изображений:**
  ```html
  <img src="..." loading="lazy" alt="...">
  ```

- **Responsive изображения:**
  ```html
  <img srcset="image-small.jpg 480w, image-medium.jpg 768w, image-large.jpg 1200w"
       sizes="(max-width: 480px) 100vw, (max-width: 768px) 50vw, 33vw"
       src="image-large.jpg" alt="...">
  ```

#### 4.3. Кэширование
- **Laravel Cache:**
  ```bash
  # Использовать Redis или Memcached для кэша
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

- **Кэширование запросов к базе данных:**
  - Использовать `Cache::remember()` для часто запрашиваемых данных
  - Кэшировать результаты сложных запросов
  ```php
  Cache::remember('products.popular', 3600, function () {
      return Product::popular()->get();
  });
  ```

- **Кэширование конфигурации в production:**
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

- **HTTP кэширование (Nginx):**
  - Настроить кэширование статических файлов в `docker/nginx/project.conf`
  - Установить правильные заголовки Cache-Control для статических ресурсов

#### 4.4. Оптимизация базы данных
- **Индексы:**
  - Убедиться, что все часто используемые поля в WHERE/JOIN имеют индексы
  - Проверить наличие индексов на foreign keys
  - Добавить составные индексы для частых комбинаций полей

- **Eager Loading:**
  - Использовать `with()` для предотвращения N+1 проблем
  ```php
  // Вместо:
  $products = Product::all(); // N+1 queries
  
  // Использовать:
  $products = Product::with('category', 'images')->get();
  ```

- **Оптимизация запросов:**
  - Использовать `select()` для получения только нужных полей
  - Применять пагинацию вместо `get()` для больших наборов данных
  - Проверить использование `DB::query()` для сложных запросов

#### 4.5. Оптимизация сервера и PHP
- **OPcache:**
  - Убедиться, что OPcache включен в production
  - Настроить правильные значения для `opcache.memory_consumption` и `opcache.max_accelerated_files`

- **Nginx оптимизация:**
  - Включить gzip сжатие для текстовых файлов
  - Настроить keep-alive соединения
  - Увеличить `client_max_body_size` если нужно, но не чрезмерно

- **PHP-FPM настройки:**
  - Оптимизировать количество процессов (pm.max_children, pm.start_servers)
  - Использовать `pm = ondemand` или `pm = dynamic` в зависимости от нагрузки

#### 4.6. CDN и статические ресурсы
- **CDN для статических файлов:**
  - Разместить CSS, JS, изображения на CDN (Cloudflare, AWS CloudFront и т.д.)
  - Использовать разные домены для статических ресурсов (subdomain.domain.com)

- **Asset версионирование:**
  - Использовать query strings или hash в именах файлов для cache busting
  - Laravel Mix/Vite автоматически добавляет хэши к файлам

#### 4.7. Мониторинг и анализ
- **Инструменты для проверки скорости:**
  - Google PageSpeed Insights
  - GTmetrix
  - Lighthouse (встроен в Chrome DevTools)
  - Laravel Debugbar (только для разработки)

- **Профилирование запросов:**
  - Использовать Laravel Telescope для отслеживания медленных запросов
  - Проверить логи медленных запросов базы данных

#### 4.8. Специфично для Laravel
- **Оптимизация автозагрузки:**
  ```bash
  composer install --optimize-autoloader --no-dev
  ```

- **Очереди для тяжелых операций:**
  - Использовать Jobs и Queues для отправки email, обработки изображений
  - Настроить Supervisor для worker процессов (уже настроен в `docker/php/laravel-worker.conf`)

- **Database Query Caching:**
  - Использовать кэширование результатов запросов через Redis/Memcached

### Файлы для проверки и оптимизации:
- `vite.config.js` - настройка сборки ассетов
- `docker/nginx/project.conf` - конфигурация Nginx для кэширования
- `docker/php/Dockerfile` - настройки OPcache
- `config/cache.php` - настройки кэширования
- `config/database.php` - оптимизация подключений к БД

### Приоритетные действия:
1. ✅ Включить кэширование конфигурации, маршрутов и представлений в production
2. ✅ Настроить gzip сжатие в Nginx
3. ✅ Оптимизировать изображения (WebP, размеры)
4. ✅ Проверить и добавить недостающие индексы в базе данных
5. ✅ Использовать Eager Loading для предотвращения N+1 проблем
6. ✅ Настроить OPcache для PHP
7. ✅ Минифицировать CSS/JS через Vite в production режиме

---

## 5. Скрытие пользователей в UserResource

### Проблема
На странице Users не отображаются все аккаунты платформы. Системные пользователи (с ролью `system`) полностью скрыты из списка из-за фильтра в `getEloquentQuery()`.

### Файл, требующий проверки:

#### 5.1. `app/Filament/Resources/UserResource.php`
**Строка 49-55:**
```php
public static function getEloquentQuery(): Builder
{
  return parent::getEloquentQuery()
    ->withoutGlobalScopes([SoftDeletingScope::class])
    ->with('roles')
    ->whereDoesntHave('roles', fn($q) => $q->where('name', 'system'));
}
```

**Проблема:**
- Фильтр `->whereDoesntHave('roles', fn($q) => $q->where('name', 'system'))` исключает всех пользователей с ролью `system`
- Удаленные пользователи (soft deleted) не показываются по умолчанию, требуется включить фильтр "Deleted"

**Варианты решения:**

**Вариант 1: Показывать всех пользователей, включая системных**
```php
public static function getEloquentQuery(): Builder
{
  return parent::getEloquentQuery()
    ->withoutGlobalScopes([SoftDeletingScope::class])
    ->with('roles');
    // Убрать фильтр ->whereDoesntHave('roles', ...)
}
```

**Вариант 2: Добавить фильтр в таблицу для показа системных пользователей (рекомендуется)**
- Оставить текущий фильтр в `getEloquentQuery()` (скрывать системных по умолчанию)
- Добавить toggle фильтр "Show System Users" в таблицу для опционального показа

**Вариант 3: Показывать удаленных пользователей по умолчанию**
- Изменить фильтр "Deleted" с toggle на необязательный или включенный по умолчанию

### Рекомендация:
Рекомендуется **Вариант 2** - оставить скрытие системных пользователей по умолчанию (так как это технические аккаунты), но добавить возможность их просмотра через фильтр для администраторов, которым это нужно.

---

## Примечания

После исправления всех проблем рекомендуется:
1. Очистить кэш Laravel:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   ```

2. Проверить работу всех страниц админки

3. Убедиться, что все маршруты работают корректно

