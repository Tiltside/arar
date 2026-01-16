<?php
session_start();
require __DIR__ . '/config/db.php';


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Проверяем, что пользователь — админ
            if ($user['role'] === 'admin') {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'login' => $user['login'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ];
                // Редирект в админ-панель
                header('Location: /admin/admin.php');
                exit;
            } else {
                $errors[] = 'Доступ только для администраторов';
            }
        } else {
            $errors[] = 'Неверный логин или пароль';
        }
    } catch (PDOException $e) {
        $errors[] = 'Ошибка БД: ' . htmlspecialchars($e->getMessage());
    }
}
?>

<?php require 'templates/header.php'; ?>

<h2>Вход</h2>

<!-- Вывод ошибок -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Вывод сообщений из сессии -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<!-- Форма входа -->
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Логин</label>
        <input type="text" 
               class="form-control"
               name="login"
               value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
               required>
    </div>
    <div class="mb-3">
        <label class="form-label">Пароль</label>
        <input type="password"
               class="form-control"
               name="password"
               required>
    </div>
    <button type="submit" class="btn btn-primary">Войти</button>
    <p class="mt-3">
        <a href="/register.php">Ещё не зарегистрированы? Регистрация</a>
    </p>
</form>

<?php require 'templates/footer.php'; ?>
