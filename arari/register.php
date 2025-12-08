<?php 
include 'config.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if (empty($name) || empty($email) || empty($password)) {
        $error = "Все поля обязательны для заполнения";
    } elseif ($password !== $confirm_password) {
        $error = "Пароли не совпадают";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен содержать минимум 6 символов";
    } else {
        // Проверяем, существует ли пользователь с таким email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Пользователь с таким email уже существует";
        } else {
            // Создаем нового пользователя
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, bonus_points) VALUES (?, ?, ?, 100)");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['is_admin'] = 0;
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Ошибка при создании аккаунта";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - WoodTech</title>
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
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h3 class="mb-0"><i class="fas fa-user-plus me-2"></i>Регистрация</h3>
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
                                <label for="name" class="form-label">Имя</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" required placeholder="Введите ваше имя">
                                </div>
                            </div>
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
                                    <input type="password" class="form-control" id="password" name="password" required placeholder="Минимум 6 символов">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Повторите пароль">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                            </button>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-2">Уже есть аккаунт? <a href="login.php" class="text-decoration-none">Войдите</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- Бонусная информация -->
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <h5 class="card-title text-success">
                            <i class="fas fa-gift me-2"></i>Бонусы за регистрацию
                        </h5>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-primary">
                                    <i class="fas fa-coins fa-2x mb-2"></i>
                                    <h6>100 баллов</h6>
                                    <small>При регистрации</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-success">
                                    <i class="fas fa-percentage fa-2x mb-2"></i>
                                    <h6>30% скидка</h6>
                                    <small>Бонусами от заказа</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>