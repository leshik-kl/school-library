<?php
session_start();
echo "<h1>Проверка авторизации</h1>";
echo "<pre>";
echo "SESSION: \n";
print_r($_SESSION);
echo "\nCOOKIE: \n";
print_r($_COOKIE);
echo "</pre>";
echo "<p><a href='/admin/resource/author-resource/crud'>Перейти к ресурсу</a></p>";
?>
