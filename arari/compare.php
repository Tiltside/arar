<?php
include 'config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Добавление товара в сравнение
if (isset($_POST['add_to_compare'])) {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Проверяем, есть ли уже товар в сравнении
    $stmt = $pdo->prepare("SELECT * FROM comparison WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    
    if ($stmt->rowCount() == 0) {
        // Добавляем товар в сравнение
        $stmt = $pdo->prepare("INSERT INTO comparison (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $product_id]);
        $success = "Товар добавлен в сравнение";
    } else {
        $error = "Товар уже в списке сравнения";
    }
}

// Удаление товара из сравнения
if (isset($_GET['remove'])) {
    $compare_id = $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM comparison WHERE id = ? AND user_id = ?");
    $stmt->execute([$compare_id, $_SESSION['user_id']]);
    header('Location: compare.php');
    exit;
}

// Очистка всего списка сравнения
if (isset($_POST['clear_compare'])) {
    $stmt = $pdo->prepare("DELETE FROM comparison WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $success = "Список сравнения очищен";
}

// Получение товаров для сравнения
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT c.id as compare_id, p.*, cat.name as category_name 
    FROM comparison c 
    JOIN products p ON c.product_id = p.id 
    JOIN categories cat ON p.category_id = cat.id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([$user_id]);
$compare_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сравнение товаров - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-balance-scale me-2"></i>Сравнение товаров</h2>
            <?php if(count($compare_items) > 0): ?>
                <form method="POST" class="d-inline" id="clear-compare-form">
                    <input type="hidden" name="clear_compare" value="1">
                    <button type="button" class="btn btn-outline-danger" id="clear-all-btn">
                        <i class="fas fa-trash me-1"></i>Очистить все
                    </button>
                </form>
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
        
        <?php if(count($compare_items) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 200px;">Товар</th>
                            <?php foreach($compare_items as $item): ?>
                            <th class="text-center" style="width: 300px;">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="<?= getProductImage($item['id'], $item['name']) ?>" 
                                         alt="<?= $item['name'] ?>" 
                                         class="img-fluid mb-2" 
                                         style="max-height: 150px; object-fit: contain;"
                                         onerror="this.src='images/no-image.jpg'">
                                    <div class="mt-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger remove-compare-btn"
                                                data-remove-url="compare.php?remove=<?= $item['compare_id'] ?>"
                                                data-product-name="<?= htmlspecialchars($item['name']) ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Название</strong></td>
                            <?php foreach($compare_items as $item): ?>
                            <td class="text-center">
                                <h6><?= $item['name'] ?></h6>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Категория</strong></td>
                            <?php foreach($compare_items as $item): ?>
                            <td class="text-center"><?= $item['category_name'] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Описание</strong></td>
                            <?php foreach($compare_items as $item): ?>
                            <td class="text-center"><?= $item['description'] ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Цена</strong></td>
                            <?php foreach($compare_items as $item): ?>
                            <td class="text-center">
                                <span class="h5 text-success fw-bold"><?= number_format($item['price'], 2, '.', ' ') ?> руб.</span>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong>Действия</strong></td>
                            <?php foreach($compare_items as $item): ?>
                            <td class="text-center">
                                <div class="d-flex flex-column gap-2">
                                    <button class="btn btn-primary btn-sm add-to-cart" data-id="<?= $item['id'] ?>">
                                        <i class="fas fa-shopping-cart me-1"></i>В корзину
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm add-to-favorites" data-id="<?= $item['id'] ?>">
                                        <i class="far fa-heart me-1"></i>В избранное
                                    </button>
                                </div>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>О сравнении</h5>
                            <p class="card-text">
                                Вы можете сравнивать до 5 товаров одновременно. Для добавления новых товаров 
                                перейдите в каталог и нажмите кнопку сравнения.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-shopping-cart me-2"></i>Быстрые действия</h5>
                            <div class="d-grid gap-2">
                                <a href="index.php#products" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-1"></i>Добавить товары
                                </a>
                                <a href="cart.php" class="btn btn-success">
                                    <i class="fas fa-cart-arrow-down me-1"></i>Перейти в корзину
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-balance-scale fa-4x text-muted mb-3"></i>
                    <h3 class="text-muted">Список сравнения пуст</h3>
                </div>
                <p class="text-muted mb-4">Добавьте товары для сравнения их характеристик и цен</p>
                <a href="index.php#products" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Перейти к товарам
                </a>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Обработка удаления товара из сравнения с подтверждением
        document.querySelectorAll('.remove-compare-btn').forEach(button => {
            button.addEventListener('click', function() {
                const removeUrl = this.getAttribute('data-remove-url');
                const productName = this.getAttribute('data-product-name');

                showConfirmDialog(
                    'Удалить из сравнения?',
                    `Вы уверены, что хотите удалить "${productName}" из сравнения?`,
                    () => {
                        // Подтверждение - переходим по ссылке
                        window.location.href = removeUrl;
                    },
                    () => {
                        // Отмена - ничего не делаем
                        console.log('Удаление отменено');
                    }
                );
            });
        });

        // Обработка кнопки "Очистить все"
        const clearAllBtn = document.getElementById('clear-all-btn');
        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function() {
                const form = document.getElementById('clear-compare-form');

                showConfirmDialog(
                    'Очистить весь список сравнения?',
                    'Вы уверены, что хотите удалить все товары из сравнения? Это действие нельзя отменить.',
                    () => {
                        // Подтверждение - отправляем форму
                        form.submit();
                    },
                    () => {
                        // Отмена - ничего не делаем
                        console.log('Очистка отменена');
                    }
                );
            });
        }
    </script>
</body>
</html>