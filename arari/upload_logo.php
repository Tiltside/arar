<?php
include 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['product_image'])) {
    $upload_dir = 'images/products/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Создаем папку если ее нет
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file = $_FILES['product_image'];
    $filename = $_POST['filename'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Ошибка загрузки файла";
    } elseif (!in_array($file['type'], $allowed_types)) {
        $error = "Разрешены только JPG, PNG, GIF и WebP файлы";
    } elseif ($file['size'] > $max_size) {
        $error = "Файл слишком большой (максимум 5MB)";
    } else {
        // Получаем расширение файла
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = $filename . '.' . $extension;
        $filepath = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $success = "Изображение успешно загружено: " . $new_filename;
        } else {
            $error = "Ошибка при сохранении файла";
        }
    }
}

// Перенаправляем обратно в админку
header('Location: admin.php?tab=images');
exit;
?>