<?php
session_start();
require_once 'config.php';

// Если корзина не существует в сессии, создаем ее
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Обработка добавления товара в корзину
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    // Добавляем товар в корзину
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    header('Location: cart.php');
    exit;
}

// Обработка обновления количества
if (isset($_POST['update_quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
    
    header('Location: cart.php');
    exit;
}

// Обработка удаления товара
if (isset($_POST['remove_from_cart'])) {
    $product_id = intval($_POST['product_id']);
    unset($_SESSION['cart'][$product_id]);
    
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
            'total' => $item_total,
            'image' => getProductImage($product['id'], $product['name'])
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .btn-checkout {
            background-color: <?= $theme_settings['button_color'] ?> !important;
            border-color: <?= $theme_settings['button_color'] ?> !important;
        }
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            cursor: pointer;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
            margin: 0 5px;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .image-placeholder {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <h1 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Корзина</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Ваша корзина пуста</h3>
                </div>
                <p class="text-muted mb-4">Добавьте товары из каталога, чтобы сделать заказ</p>
                <a href="index.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Перейти к покупкам
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Товары в корзине</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="row align-items-center mb-4 pb-4 border-bottom">
                                <div class="col-md-2">
                                    <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="product-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="image-placeholder" style="display: none;">
                                        <i class="fas fa-image fa-lg"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['name']) ?></h6>
                                    <p class="text-muted mb-0 small">Артикул: <?= $item['id'] ?></p>
                                </div>
                                <div class="col-md-2">
                                    <span class="h6"><?= number_format($item['price'], 2, '.', ' ') ?> руб.</span>
                                </div>
                                <div class="col-md-2">
                                    <form method="POST" class="d-flex align-items-center">
                                        <button type="button" class="quantity-btn minus" data-id="<?= $item['id'] ?>">-</button>
                                        <input type="number" name="quantity" class="form-control quantity-input" 
                                               value="<?= $item['quantity'] ?>" min="1" data-id="<?= $item['id'] ?>">
                                        <button type="button" class="quantity-btn plus" data-id="<?= $item['id'] ?>">+</button>
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="update_quantity" value="1">
                                    </form>
                                </div>
                                <div class="col-md-2">
                                    <span class="h6 text-success"><?= number_format($item['total'], 2, '.', ' ') ?> руб.</span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="remove_from_cart" value="1">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                onclick="return confirm('Удалить товар из корзины?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Сумма заказа</h5>
                        </div>
                        <div class="card-body">
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
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Продолжить покупки
                                </a>
                                
                                <?php if (isLoggedIn()): ?>
                                    <a href="checkout.php" class="btn btn-checkout btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Оформить заказ
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-checkout btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i>Войти для оформления
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Обработка изменения количества
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const input = document.querySelector(`.quantity-input[data-id="${productId}"]`);
                let value = parseInt(input.value);
                
                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus') && value > 1) {
                    value--;
                }
                
                input.value = value;
                
                // Автоматическое обновление при изменении количества
                setTimeout(() => {
                    const form = input.closest('form');
                    form.submit();
                }, 300);
            });
        });

        // Автообновление при ручном вводе количества
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                this.closest('form').submit();
            });
        });
    </script>
</body>
</html>