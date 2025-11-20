# Инструкция по настройке Google Analytics 4 (GA4)

## Шаг 1: Создание аккаунта Google Analytics

1. **Перейдите на Google Analytics**
   - Откройте: https://analytics.google.com/
   - Войдите в свой Google аккаунт (или создайте новый)

2. **Создание аккаунта**
   - Нажмите кнопку **"Начать измерение"** (Start measuring)
   - Заполните информацию об аккаунте:
     - **Имя аккаунта**: например, "TrekGuider" или название вашей компании
     - **Имя ресурса**: название вашего сайта (например, "TrekGuider Website")
     - **Часовой пояс**: выберите ваш часовой пояс
     - **Валюта отчетов**: выберите валюту (обычно USD)

3. **Настройка потока данных (Data Stream)**
   - Выберите **"Веб-сайт"** (Web)
   - Заполните данные:
     - **URL веб-сайта**: например, `https://trekguider.com`
     - **Имя потока**: например, "TrekGuider Website"
     - Нажмите **"Создать поток"**

## Шаг 2: Получение Measurement ID (G-XXXXXXXXXX)

После создания потока данных:

1. **Найдите Measurement ID**
   - В разделе **"Сведения о потоке данных"** (Stream Details)
   - Скопируйте **Measurement ID** (формат: `G-XXXXXXXXXX`)
   - Это значение нужно для фронтенда (уже настроено в коде)

2. **Пример Measurement ID**: `G-ABC123XYZ789`

## Шаг 3: Получение Property ID (числовой ID)

1. **Найдите Property ID**
   - В левом меню нажмите **Admin** (шестеренка внизу)
   - В колонке **Property** найдите **Property Settings**
   - Скопируйте **Property ID** (числовой, например: `123456789`)
   - Это значение нужно для API доступа

## Шаг 4: Настройка API доступа (опционально, для серверных данных)

Если вы хотите использовать API для получения данных аналитики в админке:

1. **Создание Service Account**
   - Откройте Google Cloud Console: https://console.cloud.google.com/
   - Создайте новый проект или выберите существующий
   - Перейдите в **IAM & Admin** → **Service Accounts**
   - Нажмите **"Create Service Account"**
   - Заполните:
     - **Service account name**: например, "GA4 API Access" GA4 API Access
     - **Service account ID**: автоматически сгенерируется ga4-api-access
   - Нажмите **"Create and Continue"**

2. **Предоставление прав доступа**
   - В разделе **Grant this service account access to project**:
     - Роль: **Viewer** (достаточно для чтения данных)
   - Нажмите **"Continue"** → **"Done"**

3. **Создание ключа**
   - Найдите созданный Service Account в списке
   - Нажмите на него
   - Перейдите во вкладку **"Keys"**
   - Нажмите **"Add Key"** → **"Create new key"**
   - Выберите формат **JSON**
   - Нажмите **"Create"**
   - JSON файл скачается на ваш компьютер

4. **Предоставление доступа к GA4 Property**
   - Вернитесь в Google Analytics 4
   - Перейдите в **Admin** → **Property** → **Property Access Management**
   - Нажмите **"+"** → **"Add users"**
   - Вставьте email Service Account (находится в JSON файле, поле `client_email`)
   - Права: **Viewer**
   - Нажмите **"Add"**

## Шаг 5: Настройка в админке TrekGuider

1. **Войдите в админку**
   - Перейдите в **Settings** → **Integrations**
   - Найдите интеграцию **GA4** или создайте новую

2. **Заполните данные**
   - **Integration Name**: `ga4` (обязательно)
   - **Type**: `Analytics`
   - **Status**: `Active` (после настройки)
   
   В разделе **Configuration**:
   - **Property ID**: вставьте числовой Property ID (например: `123456789`)
   - **Measurement ID**: вставьте Measurement ID (например: `G-ABC123XYZ789`)
   - **Credentials JSON**: 
     - Откройте скачанный JSON файл
     - Скопируйте весь его содержимое
     - Вставьте в это поле
   
3. **Сохраните**
   - Нажмите **"Save"**
   - Опционально: нажмите **"Test Connection"** для проверки

## Шаг 6: Проверка работы

1. **Проверка на сайте**
   - Откройте ваш сайт в браузере
   - Откройте DevTools (F12)
   - Перейдите во вкладку **Network**
   - Найдите запросы к `google-analytics.com` или `googletagmanager.com`
   - Это означает, что GA4 подключен

2. **Проверка в GA4**
   - Вернитесь в Google Analytics 4
   - Перейдите в **Reports** → **Realtime**
   - Откройте ваш сайт в новой вкладке
   - Через несколько секунд вы должны увидеть активного пользователя в Real-time отчете

## Быстрый чеклист

- [ ] Создан аккаунт Google Analytics 4
- [ ] Создан Property в GA4
- [ ] Создан Web Stream
- [ ] Скопирован Measurement ID (G-XXXXXXXXXX)
- [ ] Скопирован Property ID (числовой)
- [ ] Создан Service Account (если нужен API доступ)
- [ ] Скачан JSON ключ Service Account
- [ ] Предоставлен доступ Service Account к GA4 Property
- [ ] Настроена интеграция в админке TrekGuider
- [ ] Проверена работа GA4 на сайте

## Полезные ссылки

- Google Analytics: https://analytics.google.com/
- Google Cloud Console: https://console.cloud.google.com/
- Документация GA4: https://developers.google.com/analytics/devguides/collection/ga4

## Примечания

- **Measurement ID** используется для фронтенда (уже настроено в коде)
- **Property ID** используется для API доступа (для получения данных в админке)
- **Credentials JSON** нужен только если вы используете API для получения данных аналитики
- Если вам нужен только базовый трекинг посетителей, достаточно Measurement ID




<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-B54NR2TRLF"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-B54NR2TRLF');
</script>