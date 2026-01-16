<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_id = (int)$_POST['app_id'];
    $review = trim($_POST['review']);

    if (empty($review)) {
        $_SESSION['message'] = 'Отзыв не может быть пустым.';
        header('Location: /dashboard.php');
        exit;
    }

    try {
        // Проверяем, что заявка принадлежит пользователю
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE id = ? AND user_id = ?");
        $stmt->execute([$app_id, $_SESSION['user_id']]);
        if (!$stmt->fetch()) {
            $_SESSION['message'] = 'Ошибка: заявка не найдена.';
            header('Location: /dashboard.php');
            exit;
        }

        // Добавляем отзыв
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, application_id, text) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $app_id, $review]);

        $_SESSION['message'] = 'Отзыв добавлен!';
    } catch (PDOException $e) {
        $_SESSION['message'] = 'Ошибка БД: ' . $e->getMessage();
    }
}

header('Location: /dashboard.php');
exit;
?>
