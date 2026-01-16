<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Получение заявок пользователя
try {
    $stmt = $pdo->prepare("
        SELECT a.*, r.text as review
        FROM applications a
        LEFT JOIN reviews r ON a.id = r.application_id AND r.user_id = ?
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Ошибка БД: ' . $e->getMessage());
}
?>

<?php require 'templates/header.php'; ?>

<h2>Мой кабинет</h2>

<?php if ($applications): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Курс</th>
                <th>Дата начала</th>
                <th>Способ оплаты</th>
                <th>Статус</th>
                <th>Отзыв</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?= htmlspecialchars($app['course_name']) ?></td>
                    <td><?= htmlspecialchars($app['start_date']) ?></td>
                    <td><?= htmlspecialchars($app['payment_method']) ?></td>
                    <td><?= htmlspecialchars($app['status']) ?></td>
                    <td>
                        <?php if ($app['review']): ?>
                            <?= htmlspecialchars($app['review']) ?>
                        <?php else: ?>
                            <form method="POST" action="/add_review.php" class="d-inline">
                                <input type="hidden" name="app_id" value="<?= $app['id'] ?>">
                                <textarea name="review" class="form-control form-control-sm" rows="2" placeholder="Ваш отзыв" required></textarea>
                                <button type="submit" class="btn btn-sm btn-success mt-1">Отправить</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>У вас пока нет заявок.</p>
<?php endif; ?>

<p>
    <a href="/apply.php" class="btn btn-primary">Подать новую заявку</a>
</p>

<?php require 'templates/footer.php'; ?>  
