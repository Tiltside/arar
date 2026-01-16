<?php
session_start();
require 'config/db.php';

// Получаем все курсы из БД
try {
    $stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    $courses = [];
    error_log('Ошибка загрузки курсов: ' . $e->getMessage());
}
?>

<?php require 'templates/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4 text-center">Доступные курсы</h1>

    <?php if (empty($courses)): ?>
        <div class="alert alert-info">
            Пока нет доступных курсов. Администратор скоро их добавит!
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($course['image_url']): ?>
                            <img src="<?= htmlspecialchars($course['image_url']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($course['title']) ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                            <p class="card-text text-muted">
                                <?= htmlspecialchars(substr($course['description'], 0, 120)) ?>
                                <?php if (strlen($course['description']) > 120): ?>...<?php endif; ?>
                            </p>
                            <ul class="list-unstyled mb-3">
                                <li><strong>Длительность:</strong> <?= htmlspecialchars($course['duration']) ?></li>
                                <li><strong>Цена:</strong> <span class="text-primary fs-5">
                                    <?= number_format($course['price'], 2, ',', ' ') ?> ₽
                                </span></li>
                            </ul>
                            <div class="d-grid">
                                <a href="/apply.php?course_id=<?= $course['id'] ?>"
                                   class="btn btn-primary">Записаться на курс</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'templates/footer.php'; ?>
