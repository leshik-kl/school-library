# Школьная библиотека

Веб-приложение для управления школьной библиотекой на Laravel 11 с админ-панелью Moonshine 4.8.

## 🚀 Технологии

- **Backend**: Laravel 11
- **Admin Panel**: Moonshine 4.8
- **Database**: PostgreSQL 15
- **Web Server**: Nginx
- **PHP**: 8.2
- **Containerization**: Docker + Docker Compose

## 📋 Функциональность

### Публичная часть
- Каталог книг с фильтрацией и поиском
- Просмотр детальной информации о книге
- Авторы, издательства, категории

### Админ-панель (Moonshine)
- Управление книгами (CRUD)
- Управление авторами (CRUD)
- Управление категориями (CRUD)
- Управление издательствами (CRUD)
- Управление читателями (CRUD)
- Управление выдачами книг (CRUD)
- Дашборд со статистикой

## 🛠️ Установка и запуск

### Предварительные требования

- Docker и Docker Compose
- Git
- (Опционально) pgAdmin 4 для управления БД

### Пошаговая установка

#### 1. Клонирование репозитория

```bash
git clone https://github.com/leshik-kl/school-library.git
cd school-library

===Запуск Docker контейнеров

docker compose up -d


===Установка зависимостей Laravel

docker compose exec php bash

cd /var/www/html

composer install

---Настройка окружения


cp .env.example .env

php artisan key:generate

Настройка базы данных

# В .env файле должны быть правильные настройки:
# DB_CONNECTION=pgsql
# DB_HOST=postgres
# DB_PORT=5432
# DB_DATABASE=library_db
# DB_USERNAME=library_user
# DB_PASSWORD=library_password

 Запуск миграций и сидов

php artisan migrate --seed

Создание администратора Moonshine

php artisan moonshine:user
# Следуйте инструкциям для создания пользователя
# Рекомендуется:
# Name: Admin
# Email: admin@library.local
# Password: password

Настройка прав доступа

chmod -R 777 storage bootstrap/cache
chmod -R 777 storage/framework/sessions


Выход из контейнера

exit

Доступ к приложению
После запуска контейнеров приложение будет доступно по адресам:

Публичная часть: http://localhost

Каталог книг: http://localhost/catalog

Админ-панель: http://localhost/admin/login

Email: admin@library.local

Password: password

Ресурсы админки (после входа):

Книги: http://localhost/admin/resource/book-resource/crud

Авторы: http://localhost/admin/resource/author-resource/crud

Категории: http://localhost/admin/resource/category-resource/crud

Издательства: http://localhost/admin/resource/publisher-resource/crud

Читатели: http://localhost/admin/resource/reader-resource/crud

Выдачи: http://localhost/admin/resource/loan-resource/crud


Доступ к базе данных

docker compose exec postgres psql -U library_user -d library_db

Полезные команды

# Запуск контейнеров
docker compose up -d

# Остановка контейнеров
docker compose down

# Перезапуск контейнеров
docker compose restart

# Просмотр логов
docker compose logs -f php
docker compose logs -f nginx
docker compose logs -f postgres


Работа с Laravel


# Вход в PHP контейнер
docker compose exec php bash

# Очистка кэша
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Создание ресурса Moonshine
php artisan moonshine:resource ResourceName

# Создание миграции
php artisan make:migration create_table_name
php artisan migrate






