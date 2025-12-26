# VideoHub

Полнофункциональная платформа для хостинга и потокового воспроизведения видео, построенная с использованием Laravel и Vue 3.

## 🌟 Особенности

- **Загрузка видео** - Надежная загрузка видео с интеграцией Cloudinary
- **Потоковое воспроизведение** - Оптимизированное воспроизведение с автоматическим качеством
- **Система комментариев** - Пользователи могут комментировать видео
- **Система лайков** - Поддержка лайков и дизлайков
- **Поиск и фильтрация** - Расширенный поиск по названию и описанию
- **Управление администратором** - Блокировка/разблокировка контента
- **Аутентификация** - Безопасная аутентификация через Laravel Sanctum
- **Управление профилем** - Просмотр и редактирование профиля пользователя

## 🛠️ Технологический стек

### Backend

- **Laravel 12** - Современный PHP фреймворк
- **PHP 8.2** - Язык программирования
- **SQLite** - Легкая реляционная БД (недавно мигрирована с PostgreSQL)
- **Laravel Sanctum** - Аутентификация на основе токенов
- **Cloudinary** - Облачное хранилище и обработка видео
- **PHPUnit** - Тестирование

### Frontend

- **Vue 3** - Современный JavaScript фреймворк
- **TypeScript** - Типизированный JavaScript
- **Vite** - Сверхбыстрый сборщик модулей
- **Pinia** - Управление состоянием
- **Vue Router** - Маршрутизация
- **Axios** - HTTP клиент

## 📋 Требования

- **PHP 8.2** или выше
- **Node.js 18** или выше (для фронтенда)
- **npm** или **yarn**
- **Composer** (для зависимостей PHP)
- **Cloudinary аккаунт** (для хранения видео)

## 🚀 Быстрый старт

### Установка Backend

```bash
cd backend

# Установка зависимостей
composer install

# Копирование конфигурации
cp .env.example .env

# Генерация ключа приложения
php artisan key:generate

# Создание БД и выполнение миграций
php artisan migrate

# Запуск сервера разработки
php artisan serve
```

**То же самое можно выполнить с помощью Одной командой:**

```bash
cd backend && composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && php artisan serve
```

**API будет доступен на:** `http://localhost:8000`

### Установка Frontend

```bash
cd frontend

# Установка зависимостей
npm install

# Запуск сервера разработки
npm run dev
```

**Приложение будет доступно на:** `http://localhost:5173`



## ⚙️ Конфигурация

### Переменные окружения (.env)

```env
# База данных (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Cloudinary (для хранения видео)
CLOUDINARY_URL=cloudinary://your_key:your_secret@your_cloud

# Приложение
APP_NAME=VideoHub
APP_ENV=local
APP_URL=http://localhost:8000

# Sanctum (аутентификация)
SANCTUM_STATEFUL_DOMAINS=localhost:5173

# Очередь, сессии, кеш (используют БД)
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

## 📁 Структура проекта

```
videohub/
├── backend/                    # Laravel приложение
│   ├── app/
│   │   ├── Http/Controllers/  # API контроллеры
│   │   ├── Models/            # Eloquent модели
│   │   └── Middleware/        # Пользовательские middleware
│   ├── database/
│   │   ├── migrations/        # Миграции БД
│   │   └── database.sqlite    # SQLite файл БД
│   ├── routes/                # API маршруты
│   ├── tests/                 # PHPUnit тесты
│   └── config/                # Конфигурационные файлы
│
└── frontend/                   # Vue 3 приложение
    ├── src/
    │   ├── views/             # Страницы компонентов
    │   ├── components/        # Переиспользуемые компоненты
    │   ├── stores/            # Pinia хранилища
    │   ├── router/            # Конфигурация маршрутов
    │   ├── api/               # API клиент
    │   └── assets/            # Статические файлы
    └── vite.config.js         # Конфигурация Vite
```

## 🔌 API Endpoints

### Аутентификация (`/api/auth`)

```
POST   /register       - Регистрация нового пользователя
POST   /login          - Вход в систему
GET    /me             - Получить текущего пользователя
```

### Видео (`/api/videos`)

```
GET    /              - Список видео (с фильтрацией и поиском)
POST   /              - Загрузить видео
GET    /{id}          - Получить видео с комментариями
DELETE /{id}          - Удалить видео
POST   /{id}/block    - Заблокировать видео (только админ)
```

### Комментарии (`/api/comments`)

```
POST   /              - Создать комментарий
DELETE /{id}          - Удалить комментарий
```

### Лайки (`/api/likes`)

```
POST   /              - Создать/обновить лайк
DELETE /{id}          - Удалить лайк
```

### Здоровье API

```
GET    /api/health    - Проверить статус API
```

## 🗄️ База данных

### Миграция на SQLite

Проект был недавно мигрирован с PostgreSQL на SQLite для упрощения разработки и развертывания.

**Созданные таблицы:**

- `users` - Пользователи системы
- `videos` - Видео контент
- `comments` - Комментарии к видео
- `likes` - Лайки и дизлайки
- `personal_access_tokens` - Токены аутентификации (Sanctum)
- `jobs` - Очередь заданий
- `failed_jobs` - Неудачные задания
- `job_batches` - Батчи заданий
- `sessions` - Сессии пользователей
- `cache` - Кеш данных

## 🧪 Тестирование

### Backend тесты

```bash
cd backend

# Запустить все тесты
php artisan test

# Запустить тесты с подробным выводом
php artisan test --verbose

# Запустить конкретный тестовый файл
php artisan test tests/Feature/AuthControllerTest.php
```

### Frontend тесты

```bash
cd frontend

# Запустить тесты (когда будут добавлены)
npm run test
```

## 🏗️ Построение для продакшена

### Backend

```bash
cd backend

# Кеширование конфигурации
php artisan config:cache

# Кеширование маршрутов
php artisan route:cache

# Оптимизация загрузчика автозагрузки
composer install --optimize-autoloader --no-dev
```

### Frontend

```bash
cd frontend

# Построение production сборки
npm run build

# Выходная папка: dist/
```

## 📝 Модели данных

### User

```php
- id: UUID
- email: string (unique)
- password: string (bcrypt)
- role: enum('GUEST', 'USER', 'ADMIN')
- created_at, updated_at
```

### Video

```php
- id: UUID
- title: string
- description: text
- url: string (Cloudinary URL)
- publicId: string (Cloudinary ID)
- authorId: UUID (FK to User)
- isBlocked: boolean (default: false)
- created_at, updated_at
```

### Comment

```php
- id: UUID
- text: text
- userId: UUID (FK to User)
- videoId: UUID (FK to Video)
- created_at, updated_at
```

### Like

```php
- id: UUID
- isLike: boolean (true для лайка, false для дизлайка)
- userId: UUID (FK to User)
- videoId: UUID (FK to Video)
- created_at, updated_at
```

## 🔐 Безопасность

### Аутентификация

- Использует **Laravel Sanctum** для токен-базированной аутентификации
- Токены хранятся в таблице `personal_access_tokens`
- Токены имеют время жизни 1 год (настраивается в `config/sanctum.php`)

### Авторизация

- Роли пользователей: `GUEST`, `USER`, `ADMIN`
- Middleware `RoleMiddleware` для проверки прав доступа
- Только владельцы контента или админы могут удалять видео

### CORS

- Настроен в `config/cors.php`
- Frontend origin должен быть добавлен в `allowed_origins`

## 🐛 Отладка

### Backend логи

```bash
# Просмотр логов в реальном времени
php artisan pail --timeout=0
```

### Frontend

- Используйте **Vue DevTools** расширение для Chrome/Firefox
- **Browser DevTools** для отладки JavaScript

## 📦 Основные зависимости

### Backend

- `laravel/sanctum` - API аутентификация
- `cloudinary/cloudinary_php` - Работа с Cloudinary
- `ramsey/uuid` - UUID для моделей

### Frontend

- `vue@3` - UI фреймворк
- `pinia` - State management
- `vue-router` - Маршрутизация
- `axios` - HTTP запросы

## 🚦 Статус проекта

✅ **Функционал:**

- Аутентификация и авторизация
- CRUD операции с видео
- Система комментариев
- Система лайков
- Поиск и фильтрация
- Админ панель

🔄 **В разработке:**

- Расширенная аналитика
- Рекомендации видео
- Трансляции в прямом эфире

## 📞 Контакты и поддержка

Для вопросов и предложений свяжитесь с командой разработки.

## 📄 Лицензия

Этот проект лицензирован под MIT лицензией.

---

**Последнее обновление:** Декабрь 2025
**База данных:** SQLite (мигрирована с PostgreSQL)
