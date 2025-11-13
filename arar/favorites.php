<?php
include 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Добавление товара в избранное
if (isset($_POST['add_to_favorites'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Проверяем, есть ли уже товар в избранном
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    
    if ($stmt->rowCount() == 0) {
        // Добавляем товар в избранное
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        $success = "Товар добавлен в избранное";
    } else {
        $error = "Товар уже в избранном";
    }
}

// Удаление товара из избранного
if (isset($_GET['remove'])) {
    $favorite_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->execute([$favorite_id, $_SESSION['user_id']]);
    header('Location: favorites.php');
    exit;
}

// Очистка всего избранного
if (isset($_POST['clear_favorites'])) {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $success = "Избранное очищено";
}

// Перемещение в корзину
if (isset($_POST['move_to_cart'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Проверяем, есть ли уже товар в корзине
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    
    if ($stmt->rowCount() > 0) {
        // Увеличиваем количество
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } else {
        // Добавляем новый товар
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $product_id]);
    }
    
    // Удаляем из избранного
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    
    $success = "Товар перемещен в корзину";
}

// Получение избранных товаров
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT f.id as favorite_id, p.*, cat.name as category_name 
    FROM favorites f 
    JOIN products p ON f.product_id = p.id 
    JOIN categories cat ON p.category_id = cat.id 
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorite_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-heart me-2 text-danger"></i>Избранное</h2>
            <?php if(count($favorite_items) > 0): ?>
                <div class="d-flex gap-2">
                    <a href="compare.php" class="btn btn-outline-primary">
                        <i class="fas fa-balance-scale me-1"></i>К сравнению
                    </a>
                    <form method="POST" class="d-inline">
                        <button type="submit" name="clear_favorites" class="btn btn-outline-danger" 
                                onclick="return confirm('Очистить всё избранное?')">
                            <i class="fas fa-trash me-1"></i>Очистить все
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(count($favorite_items) > 0): ?>
            <div class="row">
                <?php foreach($favorite_items as $item): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="card-img-container position-relative">
                            <img src="<?= getProductImage($item['id'], $item['name']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= $item['name'] ?>"
                                 onerror="this.src='images/no-image.jpg'">
                            <a href="favorites.php?remove=<?= $item['favorite_id'] ?>" 
                               class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                               onclick="return confirm('Удалить из избранного?')"
                               title="Удалить из избранного">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary"><?= $item['category_name'] ?></span>
                                <small class="text-muted">Добавлено: <?= date('d.m.Y', strtotime($item['created_at'])) ?></small>
                            </div>
                            <h5 class="card-title"><?= $item['name'] ?></h5>
                            <p class="card-text flex-grow-1"><?= $item['description'] ?></p>
                            <p class="price text-success fw-bold mb-3"><?= number_format($item['price'], 2, '.', ' ') ?> руб.</p>
                            <div class="action-buttons">
                                <button class="btn btn-primary add-to-cart" data-id="<?= $item['id'] ?>">
                                    <i class="fas fa-shopping-cart me-1"></i>В корзину
                                </button>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="move_to_cart" class="btn btn-outline-success btn-sm" 
                                            title="Переместить в корзину">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </form>
                                <button class="btn btn-outline-secondary btn-sm add-to-compare" data-id="<?= $item['id'] ?>"
                                        title="Добавить к сравнению">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <h5 class="card-title"><i class="fas fa-lightbulb me-2 text-warning"></i>Совет</h5>
                            <p class="card-text mb-0">
                                Сохраняйте понравившиеся товары в избранном, чтобы не потерять их и быстро найти в будущем.
                                Вы можете переместить товары в корзину для оформления заказа.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Избранное пусто</h3>
                </div>
                <p class="text-muted mb-4">Добавляйте товары в избранное, чтобы не потерять их</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="index.php#products" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Перейти к товарам
                    </a>
                    <a href="cart.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-cart-arrow-down me-2"></i>В корзину
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Добавление в корзину
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                addToCart(productId);
            });
        });
        
        // Добавление в сравнение
        const addToCompareButtons = document.querySelectorAll('.add-to-compare');
        addToCompareButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                addToCompare(productId);
            });
        });
        
        function addToCart(productId) {
            fetch('cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'add_to_cart=true&product_id=' + productId
            })
            .then(response => response.text())
            .then(data => {
                alert('Товар добавлен в корзину!');
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }
        
        function addToCompare(productId) {
            fetch('compare.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'add_to_compare=true&product_id=' + productId
            })
            .then(response => response.text())
            .then(data => {
                alert('Товар добавлен в сравнение!');
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }
    });
    </script>
</body>
</html>