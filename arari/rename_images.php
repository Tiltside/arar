<?php
include 'config.php';

if (!isAdmin()) {
    die('Доступ запрещен');
}

echo "<h3>Утилита переименования изображений</h3>";

// Получаем все товары
$products = $pdo->query("SELECT * FROM products")->fetchAll();
$image_dir = 'images/';

foreach ($products as $product) {
    echo "<h4>Товар: {$product['name']} (ID: {$product['id']})</h4>";
    
    $possible_names = [
        translit($product['name']),
        $product['id'],
        translit(explode(' ', $product['name'])[0])
    ];
    
    $found = false;
    foreach ($possible_names as $name) {
        foreach (['jpg', 'jpeg', 'png', 'gif'] as $ext) {
            $filename = $name . '.' . $ext;
            if (file_exists($image_dir . $filename)) {
                echo "✓ Найдено: $filename<br>";
                $found = true;
            }
        }
    }
    
    if (!$found) {
        echo "✗ Изображение не найдено<br>";
        echo "Система ищет файлы: ";
        foreach ($possible_names as $name) {
            echo "$name.jpg, ";
        }
        echo "<br>";
    }
    echo "<hr>";
}
?>