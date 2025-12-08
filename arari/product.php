<?php
session_start();
require_once 'config.php';

// Получаем ID товара
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Получаем информацию о товаре
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id = ? AND p.active = 1
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Добавление отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    if (!isLoggedIn()) {
        $error = "Войдите, чтобы оставить отзыв";
    } else {
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $comment = trim($_POST['comment']);

        if ($rating < 1 || $rating > 5) {
            $error = "Оценка должна быть от 1 до 5";
        } elseif (empty($comment)) {
            $error = "Напишите комментарий";
        } else {
            // Проверяем, не оставлял ли пользователь уже отзыв
            $stmt = $pdo->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
            $stmt->execute([$product_id, $user_id]);

            if ($stmt->rowCount() > 0) {
                $error = "Вы уже оставляли отзыв на этот товар";
            } else {
                $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($stmt->execute([$product_id, $user_id, $rating, $comment])) {
                    $success = "Отзыв успешно добавлен";
                    // Перезагружаем страницу, чтобы показать новый отзыв
                    header("Location: product.php?id=$product_id&success=1");
                    exit;
                } else {
                    $error = "Ошибка при добавлении отзыва";
                }
            }
        }
    }
}

// Получаем отзывы
$stmt = $pdo->prepare("
    SELECT r.*, u.name, u.surname
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll();

// Вычисляем средний рейтинг
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = ?");
$stmt->execute([$product_id]);
$rating_data = $stmt->fetch();
$avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
$total_reviews = $rating_data['total_reviews'];

// Получаем количество на складе
$stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$stock = $stmt->fetch();
$stock_quantity = $stock['stock_quantity'] ?? 0;

if (isset($_GET['success'])) {
    $success = "Отзыв успешно добавлен";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .product-image {
            max-height: 500px;
            object-fit: contain;
            width: 100%;
        }

        .rating-stars {
            color: #ffc107;
            font-size: 1.5rem;
        }

        .rating-stars-small {
            color: #ffc107;
            font-size: 1rem;
        }

        .review-card {
            border-left: 4px solid <?= $theme_settings['button_color'] ?>;
        }

        .stock-badge {
            font-size: 1.1rem;
            padding: 8px 16px;
        }

        .rating-input {
            display: flex;
            gap: 10px;
            font-size: 2rem;
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input label {
            cursor: pointer;
            color: #ddd;
            transition: color 0.2s;
        }

        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="container my-5">
        <!-- Хлебные крошки скрыты -->
        <style>
            .breadcrumb { display: none; }
        </style>

        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Изображение товара -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <img src="<?= getProductImage($product['id'], $product['name']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="product-image card-img-top"
                         onerror="this.src='images/no-image.jpg'">
                </div>
            </div>

            <!-- Информация о товаре -->
            <div class="col-md-6">
                <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Рейтинг -->
                <div class="mb-3">
                    <span class="rating-stars">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $avg_rating ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </span>
                    <span class="ms-2 text-muted"><?= $avg_rating ?> (<?= $total_reviews ?> отзывов)</span>
                </div>

                <!-- Цена -->
                <div class="mb-4">
                    <h2 class="text-success mb-0"><?= number_format($product['price'], 2, '.', ' ') ?> руб.</h2>
                </div>

                <!-- Наличие на складе -->
                <div class="mb-4">
                    <?php if($stock_quantity > 0): ?>
                        <span class="badge bg-success stock-badge">
                            <i class="fas fa-check-circle me-2"></i>В наличии: <?= $stock_quantity ?> шт.
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger stock-badge">
                            <i class="fas fa-times-circle me-2"></i>Нет в наличии
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Описание -->
                <div class="mb-4">
                    <h4>Описание</h4>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <!-- Категория -->
                <div class="mb-4">
                    <strong>Категория:</strong>
                    <a href="category.php?id=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a>
                </div>

                <!-- Кнопки действий -->
                <?php if($stock_quantity > 0): ?>
                    <div class="d-grid gap-2 mb-3">
                        <button class="btn btn-primary btn-lg add-to-cart" data-id="<?= $product['id'] ?>">
                            <i class="fas fa-shopping-cart me-2"></i>Добавить в корзину
                        </button>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 add-to-favorites" data-id="<?= $product['id'] ?>">
                                    <i class="far fa-heart me-2"></i>В избранное
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-secondary w-100 add-to-compare" data-id="<?= $product['id'] ?>">
                                    <i class="fas fa-balance-scale me-2"></i>Сравнить
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Товар временно отсутствует
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Отзывы -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4"><i class="fas fa-comments me-2"></i>Отзывы покупателей</h3>

                <!-- Форма добавления отзыва -->
                <?php if(isLoggedIn()): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Оставить отзыв</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Ваша оценка</label>
                                    <div class="rating-input">
                                        <input type="radio" name="rating" value="5" id="star5" required>
                                        <label for="star5"><i class="fas fa-star"></i></label>

                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="fas fa-star"></i></label>

                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="fas fa-star"></i></label>

                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="fas fa-star"></i></label>

                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="comment" class="form-label">Ваш отзыв</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="4"
                                              placeholder="Расскажите о своих впечатлениях от товара" required></textarea>
                                </div>

                                <button type="submit" name="add_review" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Отправить отзыв
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <a href="login.php">Войдите</a>, чтобы оставить отзыв
                    </div>
                <?php endif; ?>

                <!-- Список отзывов -->
                <?php if(count($reviews) > 0): ?>
                    <?php foreach($reviews as $review): ?>
                        <div class="card review-card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($review['surname'] . ' ' . $review['name']) ?></h6>
                                        <span class="rating-stars-small">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i><?= date('d.m.Y', strtotime($review['created_at'])) ?>
                                    </small>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Пока нет отзывов. Будьте первым!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
