<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name']);
    $start_date = $_POST['start_date'];
    $payment_method = $_POST['payment_method'];

    if (empty($course_name)) {
        $errors[] = 'Название курса обязательно.';
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
        $errors[] = 'Некорректная дата.';
    }
    if (!in_array($payment_method, ['наличными', 'переводом'])) {
        $errors[] = 'Выберите способ оплаты.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO applications (user_id, course_name, start_date, payment_method) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $course_name,
                $start_date,
                $payment_method
            ]);
            $_SESSION['message'] = 'Заявка отправлена на рассмотрение!';
            header('Location: /dashboard.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Ошибка БД: ' . $e->getMessage();
        }
    }
}
?>

<?php require 'templates/header.php'; ?>

<h2>Подать заявку на обучение</h2>
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
        <label class="form-label">Название курса</label>
        <input type="text" class="form-control" name="course_name" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Желаемая дата начала</label>
        <input type="date" class="form-control" name="start_date" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Способ оплаты</label>
        <div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" value="наличными" id="cash" required>
                <label class="form-check-label" for="cash">Наличными</label>
            </div>
                        <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" value="переводом" id="transfer" required>
                <label class="form-check-label" for="transfer">Переводом по номеру телефона</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Отправить заявку</button>
</form>

<?php require 'templates/footer.php'; ?>
