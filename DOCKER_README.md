# WordPress Docker Setup

Этот проект содержит Docker контейнер для WordPress с Apache, MySQL и phpMyAdmin.

## Структура проекта

- `docker-compose.yml` - основной файл конфигурации Docker Compose
- `Dockerfile` - кастомный образ WordPress с Apache
- `apache-config.conf` - конфигурация Apache для WordPress
- `docker.env` - переменные окружения
- `uploads.ini` - настройки PHP для загрузки файлов

## Быстрый старт

### 1. Подготовка

Убедитесь, что у вас установлены:
- Docker
- Docker Compose (или Docker с встроенным Compose)

### 2. Настройка переменных окружения

Скопируйте файл `docker.env` в `.env` и при необходимости измените значения:

```bash
cp docker.env .env
```

**Примечание:** Файл `.env` уже создан автоматически при первом запуске скрипта.

Основные настройки в `.env`:
- `MYSQL_DATABASE` - имя базы данных
- `MYSQL_USER` - пользователь MySQL
- `MYSQL_PASSWORD` - пароль пользователя MySQL
- `MYSQL_ROOT_PASSWORD` - пароль root для MySQL

### 3. Запуск контейнеров

```bash
# Быстрый запуск с помощью скрипта
./start-docker.sh

# Или вручную
docker compose up -d --build
```

**Примечание:** Используйте `docker compose` (с пробелом) для новых версий Docker или `docker-compose` (с дефисом) для старых версий.

### 4. Доступ к сервисам

После запуска будут доступны:

- **WordPress**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Пользователь: `wordpress` (из переменной MYSQL_USER)
  - Пароль: `wordpress_password_123` (из переменной MYSQL_PASSWORD)
  - Или root: `root_password_123` (из переменной MYSQL_ROOT_PASSWORD)

### 5. Настройка WordPress

1. Откройте http://localhost:8080
2. Выберите язык
3. Заполните форму установки:
   - Название сайта
   - Имя пользователя администратора
   - Пароль администратора
   - Email администратора

## Управление контейнерами

### Остановка
```bash
docker-compose down
```

### Остановка с удалением данных
```bash
docker-compose down -v
```

### Просмотр логов
```bash
# Все сервисы
docker-compose logs

# Конкретный сервис
docker-compose logs wordpress
docker-compose logs db
docker-compose logs phpmyadmin
```

### Перезапуск сервиса
```bash
docker-compose restart wordpress
```

### Подключение к контейнеру
```bash
# WordPress контейнер
docker-compose exec wordpress bash

# MySQL контейнер
docker-compose exec db mysql -u wordpress -p
```

## Структура сервисов

### WordPress (wordpress)
- **Порт**: 8080
- **Образ**: Кастомный на базе wordpress:6.4-php8.2-apache
- **Том**: `./wp-content` монтируется в `/var/www/html/wp-content`

### MySQL (db)
- **Порт**: 3306 (внутренний)
- **Образ**: mysql:8.0
- **Том**: `db_data` для постоянного хранения данных

### phpMyAdmin (phpmyadmin)
- **Порт**: 8081
- **Образ**: phpmyadmin/phpmyadmin
- **Подключение**: Автоматически к MySQL сервису

## Настройки безопасности

- Отключены ненужные HTTP методы
- Защищен доступ к файлам конфигурации
- Настроены правильные права доступа
- Отключена индексация директорий

## Разработка

### Добавление плагинов и тем

Плагины и темы нужно добавлять в папку `wp-content/plugins` и `wp-content/themes` соответственно. Они автоматически будут доступны в WordPress.

### Изменение конфигурации

Для изменения конфигурации Apache отредактируйте файл `apache-config.conf` и пересоберите образ:

```bash
docker-compose up -d --build
```

### Резервное копирование

Для резервного копирования базы данных:

```bash
# Создание дампа
docker-compose exec db mysqldump -u wordpress -p wordpress > backup.sql

# Восстановление
docker-compose exec -T db mysql -u wordpress -p wordpress < backup.sql
```

## Устранение проблем

### Проблемы с правами доступа
```bash
# Исправление прав доступа
docker-compose exec wordpress chown -R www-data:www-data /var/www/html
docker-compose exec wordpress chmod -R 755 /var/www/html
```

### Проблемы с подключением к базе данных
1. Убедитесь, что MySQL контейнер запущен: `docker-compose ps`
2. Проверьте логи: `docker-compose logs db`
3. Убедитесь, что переменные окружения корректны

### Очистка и перезапуск
```bash
# Полная очистка
docker-compose down -v
docker system prune -a
docker-compose up -d --build
```

## Полезные команды

```bash
# Просмотр статуса контейнеров
docker-compose ps

# Просмотр использования ресурсов
docker stats

# Очистка неиспользуемых образов
docker image prune

# Очистка неиспользуемых томов
docker volume prune
```
