# fix-resources-structure.ps1
Write-Host "🔧 Исправление структуры ресурсов Moonshine" -ForegroundColor Green

cd C:\Users\lexa\Desktop\school-library

Write-Host "`n📁 Текущая структура:" -ForegroundColor Yellow
docker compose exec php bash -c "ls -la /var/www/html/app/MoonShine/Resources/"

Write-Host "`n🔄 Перенос файлов в корневую папку..." -ForegroundColor Yellow
docker compose exec php bash -c "
    cd /var/www/html && \
    
    # Создаем корневую папку
    mkdir -p app/MoonShine/Resources && \
    
    # Переносим все PHP файлы из подпапок в корень
    find app/MoonShine/Resources -name '*.php' -exec mv {} app/MoonShine/Resources/ \; 2>/dev/null && \
    
    # Удаляем пустые подпапки
    find app/MoonShine/Resources -type d -empty -delete 2>/dev/null && \
    
    echo '✅ Файлы перенесены'
"

Write-Host "`n📝 Исправление namespace в файлах..." -ForegroundColor Yellow
docker compose exec php bash -c "
    cd /var/www/html && \
    
    for file in app/MoonShine/Resources/*Resource.php; do
        if [ -f \"\$file\" ]; then
            echo \"Исправляю \$file\"
            # Заменяем namespace
            sed -i 's/namespace App\\\\MoonShine\\\\Resources\\\\[A-Za-z0-9_]*;/namespace App\\\\MoonShine\\\\Resources;/' \"\$file\"
            
            # Добавляем use модели если её нет
            model=\$(basename \"\$file\" .php | sed 's/Resource//')
            if ! grep -q \"use App\\\\\\\\Models\\\\\\\\\$model;\" \"\$file\"; then
                sed -i \"/namespace/a use App\\\\\\\\Models\\\\\\\\\$model;\" \"\$file\"
            fi
        fi
    done && \
    
    echo '✅ Namespace исправлен'
"

Write-Host "`n🔍 Проверка результата:" -ForegroundColor Yellow
docker compose exec php bash -c "ls -la /var/www/html/app/MoonShine/Resources/"

Write-Host "`n📋 Проверка namespace в файлах:" -ForegroundColor Yellow
docker compose exec php bash -c "
    cd /var/www/html && \
    for file in app/MoonShine/Resources/*Resource.php; do
        echo \"\$file:\"
        head -n 5 \"\$file\" | grep -E 'namespace|use'
        echo \"---\"
    done
"

Write-Host "`n🔄 Очистка кэша и перезагрузка автозагрузчика:" -ForegroundColor Yellow
docker compose exec php bash -c "
    cd /var/www/html && \
    php artisan optimize:clear && \
    composer dump-autoload
"

Write-Host "`n✅ Готово! Теперь ресурсы должны работать." -ForegroundColor Green
Write-Host "Попробуйте открыть админку: http://localhost/admin" -ForegroundColor Cyan