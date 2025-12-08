<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверка наличия товаров в корзине
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Получаем данные о товарах в корзине
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $item_total = $product['price'] * $quantity;
        $total_price += $item_total;

        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'total' => $item_total
        ];
    }
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $delivery_address = trim($_POST['delivery_address']);
    $phone = trim($_POST['phone']);
    $comment = trim($_POST['comment'] ?? '');

    // Автоматически добавляем +7 к номеру телефона, если его нет
    if (!empty($phone) && !preg_match('/^\+7/', $phone)) {
        $phone = '+7' . preg_replace('/[^0-9]/', '', $phone);
    }

    // Валидация
    $errors = [];
    if (empty($delivery_address)) {
        $errors[] = 'Укажите адрес доставки';
    }
    if (empty($phone)) {
        $errors[] = 'Укажите номер телефона';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Создаем заказ
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, delivery_address, phone, comment, status, created_at)
                                   VALUES (?, ?, ?, ?, ?, 'new', NOW())");
            $stmt->execute([$user_id, $total_price, $delivery_address, $phone, $comment]);
            $order_id = $pdo->lastInsertId();

            // Добавляем товары в заказ
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();

            // Очищаем корзину
            $_SESSION['cart'] = [];

            // Перенаправляем на страницу успеха
            header('Location: order_success.php?order_id=' . $order_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Ошибка при оформлении заказа. Попробуйте позже.';
        }
    }
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .btn-place-order {
            background-color: <?= $theme_settings['button_color'] ?> !important;
            border-color: <?= $theme_settings['button_color'] ?> !important;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container my-5">
        <h1 class="mb-4"><i class="fas fa-credit-card me-2"></i>Оформление заказа</h1>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Данные получателя</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkout-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">ФИО</label>
                                <input type="text" class="form-control" id="name"
                                       value="<?= htmlspecialchars(trim(($user['surname'] ?? '') . ' ' . ($user['name'] ?? '') . ' ' . ($user['patronymic'] ?? ''))) ?>" readonly>
                                <small class="text-muted">Изменить данные можно в <a href="profile.php">личном кабинете</a></small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">+7</span>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?= htmlspecialchars(preg_replace('/^\+?7/', '', $user['phone'] ?? '')) ?>"
                                           placeholder="(___) ___-__-__" maxlength="15" required>
                                </div>
                                <small class="text-muted">
                                    <?php if (empty($user['phone'])): ?>
                                        Укажите номер или <a href="profile.php">заполните в профиле</a>
                                    <?php else: ?>
                                        Изменить номер можно в <a href="profile.php">личном кабинете</a>
                                    <?php endif; ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="delivery_address" class="form-label">Адрес доставки <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="delivery_address" name="delivery_address"
                                          rows="3" placeholder="Город, улица, дом, квартира" required><?= htmlspecialchars($user['delivery_address'] ?? '') ?></textarea>
                                <small class="text-muted">
                                    <?php if (empty($user['delivery_address'])): ?>
                                        Укажите адрес или <a href="profile.php">заполните в профиле</a>
                                    <?php else: ?>
                                        Изменить адрес можно в <a href="profile.php">личном кабинете</a>
                                    <?php endif; ?>
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">Комментарий к заказу</label>
                                <textarea class="form-control" id="comment" name="comment"
                                          rows="3" placeholder="Укажите удобное время доставки или другие пожелания"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Ваш заказ</h5>
                    </div>
                    <div class="card-body">
                        <div class="order-summary mb-3">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <div class="flex-grow-1">
                                    <small><?= htmlspecialchars($item['name']) ?></small>
                                    <br>
                                    <small class="text-muted"><?= $item['quantity'] ?> шт. × <?= number_format($item['price'], 2, '.', ' ') ?> руб.</small>
                                </div>
                                <div class="text-end">
                                    <strong><?= number_format($item['total'], 2, '.', ' ') ?> руб.</strong>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Промежуточный итог:</span>
                            <span><?= number_format($total_price, 2, '.', ' ') ?> руб.</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Доставка:</span>
                            <span class="text-success">0 руб.</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Итого:</strong>
                            <strong class="h5 text-success"><?= number_format($total_price, 2, '.', ' ') ?> руб.</strong>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Вернуться в корзину
                            </a>
                            <button type="submit" form="checkout-form" name="place_order" class="btn btn-place-order btn-lg">
                                <i class="fas fa-check me-2"></i>Оформить заказ
                            </button>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>Безопасная оплата
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Форматирование номера телефона при вводе
        const phoneInput = document.getElementById('phone');

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Удаляем все нечисловые символы

            if (value.length > 10) {
                value = value.substring(0, 10);
            }

            let formattedValue = '';

            if (value.length > 0) {
                formattedValue = '(' + value.substring(0, 3);
            }
            if (value.length >= 4) {
                formattedValue += ') ' + value.substring(3, 6);
            }
            if (value.length >= 7) {
                formattedValue += '-' + value.substring(6, 8);
            }
            if (value.length >= 9) {
                formattedValue += '-' + value.substring(8, 10);
            }

            e.target.value = formattedValue;
        });

        // Удаляем все нечисловые символы при отправке формы (кроме скобок, пробелов и дефисов)
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const phoneValue = phoneInput.value;
            // Оставляем форматирование как есть, сервер сам обработает
        });
    </script>
</body>
</html>
