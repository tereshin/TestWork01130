#!/bin/bash

# WordPress Docker Stop Script

echo "🛑 Остановка WordPress Docker контейнеров..."

# Остановка контейнеров
docker-compose down

echo "✅ Контейнеры остановлены!"
echo ""
echo "💡 Для полной очистки (включая данные) выполните:"
echo "   docker-compose down -v"
echo "   docker system prune -a"
