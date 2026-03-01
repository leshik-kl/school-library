# fix-resources-windows.ps1
Write-Host "🔧 Исправление структуры ресурсов Moonshine для Windows" -ForegroundColor Green

cd C:\Users\lexa\Desktop\school-library

Write-Host "`n📁 Текущая структура:" -ForegroundColor Yellow
docker compose exec php ls -la /var/www/html/app/MoonShine/Resources/

Write-Host "`n🔄 Перенос файлов в корневую папку..." -ForegroundColor Yellow

# Выполняем команды по отдельности
docker compose exec php bash -c "mkdir -p /var/www/html/app/MoonShine/Resources"

Write-Host "Переносим файлы из Author..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Author/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

Write-Host "Переносим файлы из Book..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Book/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

Write-Host "Переносим файлы из Category..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Category/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

Write-Host "Переносим файлы из Loan..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Loan/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

Write-Host "Переносим файлы из Publisher..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Publisher/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

Write-Host "Переносим файлы из Reader..."
docker compose exec php bash -c "mv /var/www/html/app/MoonShine/Resources/Reader/*.php /var/www/html/app/MoonShine/Resources/ 2>/dev/null"

# Удаляем пустые папки
Write-Host "`n🗑️ Удаление пустых папок..." -ForegroundColor Yellow
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Author"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Book"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Category"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Loan"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Publisher"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/Reader"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/MoonShineUser"
docker compose exec php bash -c "rm -rf /var/www/html/app/MoonShine/Resources/MoonShineUserRole"

Write-Host "`n📝 Исправление namespace в файлах..." -ForegroundColor Yellow

# Исправляем namespace в каждом файле
$resources = @("Author", "Book", "Category", "Loan", "Publisher", "Reader")

foreach ($resource in $resources) {
    Write-Host "Исправляю ${resource}Resource.php..."
    
    # Создаем временный файл с исправленным содержимым
    docker compose exec php bash -c "cat /var/www/html/app/MoonShine/Resources/${resource}Resource.php | sed 's/namespace App\\\MoonShine\\\Resources\\\${resource};/namespace App\\\MoonShine\\\Resources;/' > /tmp/${resource}Resource.php"
    
    # Копируем обратно
    docker compose exec php bash -c "cp /tmp/${resource}Resource.php /var/www/html/app/MoonShine/Resources/${resource}Resource.php"
    
    # Добавляем use модели если нужно
    docker compose exec php bash -c "grep -q 'use App\\\Models\\\${resource};' /var/www/html/app/MoonShine/Resources/${resource}Resource.php || sed -i '/namespace/a use App\\\Models\\\${resource};' /var/www/html/app/MoonShine/Resources/${resource}Resource.php"
}

Write-Host "`n🔍 Проверка результата:" -ForegroundColor Yellow
docker compose exec php ls -la /var/www/html/app/MoonShine/Resources/

Write-Host "`n📋 Проверка namespace в файлах:" -ForegroundColor Yellow
foreach ($resource in $resources) {
    Write-Host "${resource}Resource.php:" -ForegroundColor Cyan
    docker compose exec php bash -c "head -n 5 /var/www/html/app/MoonShine/Resources/${resource}Resource.php | grep -E 'namespace|use'"
    Write-Host "---"
}

Write-Host "`n🔄 Очистка кэша и перезагрузка автозагрузчика:" -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && php artisan optimize:clear && composer dump-autoload"

Write-Host "`n✅ Готово! Теперь ресурсы должны работать." -ForegroundColor Green
Write-Host "Попробуйте открыть админку: http://localhost/admin" -ForegroundColor Cyan

# Проверка классов
Write-Host "`n🔍 Проверка существования классов:" -ForegroundColor Yellow
docker compose exec php bash -c "cd /var/www/html && php artisan tinker --execute=\"
\$resources = ['AuthorResource', 'BookResource', 'CategoryResource', 'LoanResource', 'PublisherResource', 'ReaderResource'];
foreach (\$resources as \$resource) {
    \$class = 'App\\\\MoonShine\\\\Resources\\\\' . \$resource;
    if (class_exists(\$class)) {
        echo \\\"✅ \$class существует\\\n\\\";
    } else {
        echo \\\"❌ \$class не существует\\\n\\\";
    }
}
\""