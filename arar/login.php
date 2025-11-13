<?php 
include 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный email или пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в аккаунт - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php">
</head>
<body>
    <header id="site-header" class="header">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="<?= $theme_settings['logo_url'] ?>" alt="WoodTech" height="50" onerror="this.src='images/logo.jpg'">
                    <span id="site-title">WoodTech</span>
                </a>
                <div class="navbar-nav ms-auto">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-home me-1"></i>На главную
                    </a>
                </div>
            </div>
        </nav>
    </header>
    
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Вход в аккаунт</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" required placeholder="Введите ваш email">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Введите ваш пароль">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Войти
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-2">Нет аккаунта? <a href="register.php" class="text-decoration-none">Зарегистрируйтесь</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- Бонусная информация -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-gift me-2"></i>Бонусная система
                        </h5>
                        <p class="card-text mb-2">При регистрации вы получаете <strong>100 бонусных баллов</strong>!</p>
                        <small class="text-muted">
                            Бонусы можно использовать для оплаты до 30% стоимости заказа
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>