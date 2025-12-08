<?php
include 'config.php';

// Получаем ID категории из URL
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Получаем информацию о категории
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? AND active = 1");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

// Получаем товары этой категории
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.category_id = ? AND p.active = 1 
    ORDER BY p.created_at DESC
");
$stmt->execute([$category_id]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $category['name'] ?> - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <!-- Хлебные крошки скрыты -->
        <style>
            .breadcrumb { display: none; }
        </style>
        
        <!-- Заголовок категории -->
       <div class="row mb-5">
    <div class="col-12">
        <div class="category-header text-center py-5 rounded" style="background: linear-gradient(135deg, <?= $theme_settings['header_color'] ?> 0%, <?= $theme_settings['button_color'] ?> 100%); color: white;">
            <h1 class="display-5 fw-bold mb-3"><?= $category['name'] ?></h1>
            <p class="lead mb-0 fs-5"><?= $category['description'] ?></p>
        </div>
    </div>
</div>
<style>
.product-card {
    background: white;
    border: 1px solid #dee2e6 !important;
    border-radius: 10px;
}
.product-card .card-title {
    color: #333 !important;
    font-weight: 600;
}
.product-card .card-text {
    color: #666 !important;
}
.product-card .price {
    color: #28a745 !important;
    font-weight: bold;
}
.action-buttons .btn {
    border-radius: 6px;
}
</style> 
       
        <!-- Товары категории -->
<section id="category-products">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="badge fs-5 px-3 py-2" style="background-color: <?= $theme_settings['header_color'] ?>;">Найдено: <?= count($products) ?> товаров</span>
    </div>
    
    <?php if(count($products) > 0): ?>
       <div class="row" id="products-container">
    <?php foreach($products as $product): 
        $product_image = getProductImage($product['id'], $product['name']);
    ?>
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card product-card h-100 shadow-sm">
            <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                <div class="card-img-container position-relative" style="height: 250px; overflow: hidden;">
                    <img src="<?= $product_image ?>" class="card-img-top" alt="<?= $product['name'] ?>"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.src='images/no-image.jpg'">
                    <span class="position-absolute top-0 start-0 text-white px-3 py-1 small" style="background-color: <?= $theme_settings['header_color'] ?>;">
                        <?= $product['category_name'] ?>
                    </span>
                </div>
            </a>
            <div class="card-body d-flex flex-column">
                <a href="product.php?id=<?= $product['id'] ?>" class="text-decoration-none">
                    <h5 class="card-title text-dark"><?= $product['name'] ?></h5>
                </a>
                <p class="card-text text-muted flex-grow-1"><?= $product['description'] ?></p>
                <div class="mt-auto">
                    <p class="price h5 text-success fw-bold mb-3"><?= number_format($product['price'], 2, '.', ' ') ?> руб.</p>
                    <div class="action-buttons d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-fill add-to-cart" data-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-cart me-1"></i>В корзину
                        </button>
                        <button class="btn btn-outline-secondary btn-sm add-to-favorites" data-id="<?= $product['id'] ?>" 
                                title="В избранное">
                            <i class="far fa-heart"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm add-to-compare" data-id="<?= $product['id'] ?>" 
                                title="Сравнить">
                            <i class="fas fa-balance-scale"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">В этой категории пока нет товаров</h3>
            </div>
            <p class="text-muted mb-4">Мы скоро добавим новые товары в эту категорию</p>
            <a href="index.php#categories" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Вернуться к категориям
            </a>
        </div>
    <?php endif; ?>
</section>
        
        <!-- Другие категории -->
       <section class="mt-5">
    <h4 class="mb-4">Другие категории</h4>
    <div class="row">
        <?php
        $stmt = $pdo->prepare("
            SELECT * FROM categories 
            WHERE id != ? AND active = 1 
            ORDER BY name 
            LIMIT 3
        ");
        $stmt->execute([$category_id]);
        $other_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($other_categories as $other_category):
        ?>
                <div class="col-md-4 mb-3">
                    <div class="card category-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?= $other_category['name'] ?></h5>
                            <p class="card-text"><?= $other_category['description'] ?></p>
                            <a href="category.php?id=<?= $other_category['id'] ?>" class="btn btn-outline-primary">
                                Смотреть товары
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>