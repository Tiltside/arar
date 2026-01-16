<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mysql', 'root', '');
    echo 'PDO работает! Подключение успешно.';
} catch (PDOException $e) {
    echo 'Ошибка PDO: ' . $e->getMessage();
}
?>
