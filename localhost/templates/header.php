<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корочки.есть</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/">Корочки.есть</a>
            <div class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a class="nav-link" href="/dashboard.php">Мой кабинет</a>
                    <a class="nav-link" href="/apply.php">Подать заявку</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a class="nav-link" href="/admin/admin.php">Панель админа</a>
                    <?php endif; ?>
                    <a class="nav-link" href="/logout.php">Выход</a>
                <?php else: ?>
                    <a class="nav-link" href="/login.php">Вход</a>
                    <a class="nav-link" href="/register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
