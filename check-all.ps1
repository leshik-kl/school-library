# fix-permissions.ps1
Write-Host "🔧 Исправление прав доступа и ошибок" -ForegroundColor Green

cd C:\Users\lexa\Desktop\school-library

Write-Host "`n🔄 Исправление прав от root..." -ForegroundColor Yellow
docker compose exec -u root php bash -c "
    cd /var/www/html && \
    chmod -R 777 storage bootstrap/cache && \
    chown -R www:www storage bootstrap/cache && \
    echo '✅ Права исправлены'
"

Write-Host "`n🔑 Генерация ключа приложения..." -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && php artisan key:generate --force"

Write-Host "`n🧹 Очистка кэша..." -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && php artisan optimize:clear"

Write-Host "`n📝 Проверка .env..." -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && grep APP_DEBUG .env"

Write-Host "`n📊 Логи Laravel:" -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && tail -n 20 storage/logs/laravel.log 2>/dev/null || echo 'Лог пуст или не существует'"

Write-Host "`n🌐 Создание тестового файла..." -ForegroundColor Yellow
echo '<?php echo "PHP работает! Версия: " . phpversion(); ?>' | docker compose exec -T php bash -c "cat > /var/www/html/public/test.php"

Write-Host "`n✅ Готово! Проверьте:" -ForegroundColor Green
Write-Host "Тестовый файл: http://localhost/test.php" -ForegroundColor Cyan
Write-Host "Сайт: http://localhost" -ForegroundColor Cyan
Write-Host "Админка: http://localhost/admin" -ForegroundColor Cyan