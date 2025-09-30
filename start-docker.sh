#!/bin/bash

# WordPress Docker Setup Script

echo "🚀 Запуск WordPress Docker контейнеров..."

# Проверка наличия Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен. Пожалуйста, установите Docker сначала."
    exit 1
fi

# Проверка наличия Docker Compose
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен. Пожалуйста, установите Docker Compose сначала."
    exit 1
fi

# Создание .env файла если его нет
if [ ! -f .env ]; then
    echo "📝 Создание .env файла..."
    cp docker.env .env
    echo "✅ Файл .env создан из docker.env"
fi

# Остановка существующих контейнеров
echo "🛑 Остановка существующих контейнеров..."
docker-compose down

# Запуск контейнеров
echo "🏗️  Сборка и запуск контейнеров..."
docker-compose up -d --build

# Ожидание запуска сервисов
echo "⏳ Ожидание запуска сервисов..."
sleep 10

# Проверка статуса контейнеров
echo "📊 Статус контейнеров:"
docker-compose ps

echo ""
echo "✅ WordPress Docker контейнеры запущены!"
echo ""
echo "🌐 Доступные сервисы:"
echo "   WordPress:    http://localhost:8080"
echo "   phpMyAdmin:   http://localhost:8081"
echo ""
echo "🔑 Данные для входа в phpMyAdmin:"
echo "   Пользователь: wordpress"
echo "   Пароль:       wordpress_password_123"
echo ""
echo "📖 Подробные инструкции см. в DOCKER_README.md"
