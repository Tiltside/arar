<?php
session_start();
require 'config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    // Валидация
    if (strlen($login) < 6 || !preg_match('/^[a-zA-Z0-9]+$/', $login)) {
        $errors[] = 'Логин: 6+ символов, латиница и цифры.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Пароль: минимум 8 символов.';
    }
    if (!preg_match('/^[а-яА-ЯёЁ\s]+$/u', $full_name)) {
        $errors[] = 'ФИО: только кириллица и пробелы.';
    }

    // Улучшенная валидация телефона
    $normalized_phone = preg_replace('/[^0-9]/', '', $phone); // Убираем всё кроме цифр
    if (strlen($normalized_phone) !== 11 || $normalized_phone[0] !== '7' && $normalized_phone[0] !== '8') {
        $errors[] = 'Телефон: укажите корректный номер (начинается с 7 или 8, 11 цифр).';
    } else {
        // Форматируем в нужный вид: 8(XXX)XXX-XX-XX
        $area_code = substr($normalized_phone, 1, 3);
        $part1 = substr($normalized_phone, 4, 3);
        $part2 = substr($normalized_phone, 7, 2);
        $part3 = substr($normalized_phone, 9, 2);
        $phone = "8($area_code)$part1-$part2-$part3";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    }

    if (empty($errors)) {
        try {
            // Проверка уникальности логина
            $stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
            $stmt->execute([$login]);
            if ($stmt->fetch()) {
                $errors[] = 'Логин уже занят.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $login,
                    password_hash($password, PASSWORD_DEFAULT),
                    $full_name,
                    $phone,
                    $email
                ]);
                $_SESSION['message'] = 'Регистрация успешна! Войдите в систему.';
                header('Location: /login.php');
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = 'Ошибка БД: ' . $e->getMessage();
        }
    }
}
?>

<?php require 'templates/header.php'; ?>

<h2>Регистрация</h2>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['message']) ?></div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Логин (6+ символов, латиница/цифры)</label>
        <input type="text" class="form-control" name="login" value="<?= htmlspecialchars($login ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Пароль (минимум 8 символов)</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <div class="mb-3">
        <label class="form-label">ФИО (кириллица)</label>
        <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($full_name ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Телефон (пример: 8(999)123-45-67)</label>
        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Создать пользователя</button>
    <p class="mt-3">
        <a href="/login.php">Уже зарегистрированы? Вход</a>
    </p>
</form>

<?php require 'templates/footer.php'; ?>
