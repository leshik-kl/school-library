<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "<h1>Проверка guard</h1>";
echo "<pre>";
echo "Auth check (web): " . (auth()->guard('web')->check() ? 'YES' : 'NO') . "\n";
echo "Auth check (moonshine): " . (auth()->guard('moonshine')->check() ? 'YES' : 'NO') . "\n";
echo "User (moonshine): " . (auth()->guard('moonshine')->user()?->email ?? 'null') . "\n";
echo "</pre>";
?>
