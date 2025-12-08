<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Получаем ID заказа
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header('Location: index.php');
    exit;
}

// Получаем данные заказа
$stmt = $pdo->prepare("SELECT o.*, u.name as user_name, u.email
                       FROM orders o
                       JOIN users u ON o.user_id = u.id
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Получаем товары заказа
$stmt = $pdo->prepare("SELECT oi.*, p.name as product_name
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ оформлен - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
        }
        .status-new {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container my-5">
        <div class="text-center mb-5">
            <i class="fas fa-check-circle success-icon"></i>
            <h1 class="mb-3">Заказ успешно оформлен!</h1>
            <p class="lead text-muted">Спасибо за ваш заказ. Мы свяжемся с вами в ближайшее время.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Информация о заказе
                            <span class="status-badge status-new float-end">Новый</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="order-details">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Номер заказа:</strong> #<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Дата оформления:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Получатель:</strong> <?= htmlspecialchars($order['user_name']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Сумма заказа:</strong>
                                    <span class="text-success h5"><?= number_format($order['total_amount'], 2, '.', ' ') ?> руб.</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Адрес доставки:</strong><br>
                                    <?= nl2br(htmlspecialchars($order['delivery_address'])) ?>
                                </div>
                            </div>
                            <?php if (!empty($order['comment'])): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <strong>Комментарий:</strong><br>
                                    <?= nl2br(htmlspecialchars($order['comment'])) ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h6 class="mb-3"><i class="fas fa-box me-2"></i>Товары в заказе:</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Товар</th>
                                        <th class="text-center">Количество</th>
                                        <th class="text-end">Цена</th>
                                        <th class="text-end">Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                                        <td class="text-center"><?= $item['quantity'] ?> шт.</td>
                                        <td class="text-end"><?= number_format($item['price'], 2, '.', ' ') ?> руб.</td>
                                        <td class="text-end"><?= number_format($item['price'] * $item['quantity'], 2, '.', ' ') ?> руб.</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Итого:</strong></td>
                                        <td class="text-end"><strong class="text-success"><?= number_format($order['total_amount'], 2, '.', ' ') ?> руб.</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Что дальше?</strong><br>
                    Наш менеджер свяжется с вами в течение 24 часов для подтверждения заказа и уточнения деталей доставки.
                    Информация о заказе также отправлена на ваш email.
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-home me-2"></i>На главную
                    </a>
                    <a href="cart.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Продолжить покупки
                    </a>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
