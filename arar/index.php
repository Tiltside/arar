<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WoodTech - Интернет-магазин стройматериалов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        /* Дополнительные гарантии для растягивания футера */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Основной контент -->
    <main class="container my-4">
        <!-- Категории товаров -->
        <section id="categories" class="mb-5">
            <h2 class="section-title">Категории товаров</h2>
            <div class="row">
                <?php
                $categories = $pdo->query("SELECT * FROM categories WHERE active = 1")->fetchAll();
                foreach($categories as $category):
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card category-card h-100">
                        <div class="card-body text-center">
                            <div class="category-icon mb-3">
                                <?php if($category['name'] == 'Пиломатериалы'): ?>
                                    <i class="fas fa-tree fa-3x text-success"></i>
                                <?php elseif($category['name'] == 'Крепеж'): ?>
                                    <i class="fas fa-screwdriver fa-3x text-warning"></i>
                                <?php elseif($category['name'] == 'Отделочные материалы'): ?>
                                    <i class="fas fa-paint-roller fa-3x text-primary"></i>
                                <?php else: ?>
                                    <i class="fas fa-box fa-3x text-secondary"></i>
                                <?php endif; ?>
                            </div>
                            <h4 class="card-title"><?= $category['name'] ?></h4>
                            <p class="card-text"><?= $category['description'] ?></p>
                            <a href="category.php?id=<?= $category['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i>Смотреть товары
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- Товары -->
        <section id="products" class="mb-5">
            <h2 class="section-title">Популярные товары</h2>
            <div class="row" id="products-container">
                <?php
                $products = $pdo->query("SELECT * FROM products WHERE active = 1 LIMIT 6")->fetchAll();
                foreach($products as $product):
                    $product_image = getProductImage($product['id'], $product['name']);
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="card-img-container">
                            <img src="<?= $product_image ?>" class="card-img-top" alt="<?= $product['name'] ?>" 
                                 onerror="this.src='images/no-image.jpg'">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text flex-grow-1"><?= $product['description'] ?></p>
                            <p class="price text-success fw-bold mb-3"><?= number_format($product['price'], 2, '.', ' ') ?> руб.</p>
                            <div class="action-buttons">
                                <button class="btn btn-primary add-to-cart" data-id="<?= $product['id'] ?>">
                                    <i class="fas fa-shopping-cart me-1"></i>В корзину
                                </button>
                                <button class="btn btn-outline-secondary add-to-favorites" data-id="<?= $product['id'] ?>" 
                                        title="В избранное">
                                    <i class="far fa-heart"></i>
                                </button>
                                <button class="btn btn-outline-secondary add-to-compare" data-id="<?= $product['id'] ?>" 
                                        title="Сравнить">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Акции -->
        <section id="promotions" class="mb-5">
            <h2 class="section-title">Акции</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card promotion-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="promotion-icon mb-3">
                                <i class="fas fa-gift fa-3x text-danger"></i>
                            </div>
                            <h4 class="card-title text-dark mb-3">Скидка 15% на первый заказ</h4>
                            <p class="card-text text-muted mb-0">Для новых клиентов при регистрации на сайте</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card promotion-card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="promotion-icon mb-3">
                                <i class="fas fa-truck fa-3x text-primary"></i>
                            </div>
                            <h4 class="card-title text-dark mb-3">Бесплатная доставка</h4>
                            <p class="card-text text-muted mb-0">При заказе от 5000 рублей по Москве</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Бонусная система -->
        <section id="bonus" class="mb-5">
            <h2 class="section-title">Бонусная система</h2>
            <div class="card bonus-info border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="card-text text-muted mb-3">За каждую покупку вы получаете бонусные баллы, которые можно потратить на следующие заказы.</p>
                            <ul class="list-unstyled text-muted">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>1% от суммы заказа начисляется бонусами</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Бонусы действуют 6 месяцев</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Можно оплатить до 30% стоимости заказа бонусами</li>
                            </ul>
                            <?php if(isLoggedIn()): ?>
                                <?php
                                $user_id = $_SESSION['user_id'];
                                $bonus_query = $pdo->prepare("SELECT bonus_points FROM users WHERE id = ?");
                                $bonus_query->execute([$user_id]);
                                $user_bonus = $bonus_query->fetchColumn();
                                ?>
                                <div class="alert alert-info mt-3 mb-0">
                                    <strong>Ваши бонусы:</strong> <span class="fw-bold"><?= $user_bonus ?> баллов</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="bonus-visual bg-light rounded-circle p-4 mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-coins fa-4x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>