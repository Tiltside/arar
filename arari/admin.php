<?php 
include 'config.php'; 

// Проверяем авторизацию и админские права
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

// Обновление настроек оформления
if (isset($_POST['update_theme'])) {
    $header_color = $_POST['header_color'];
    $footer_color = $_POST['footer_color'];
    $button_color = $_POST['button_color'];
    $logo_url = $_POST['logo_url'];
    
    $stmt = $pdo->prepare("UPDATE theme_settings SET header_color = ?, footer_color = ?, button_color = ?, logo_url = ? WHERE id = 1");
    if ($stmt->execute([$header_color, $footer_color, $button_color, $logo_url])) {
        $success = "Настройки оформления успешно обновлены";
        // Обновляем переменную с настройками
        $theme_settings = getThemeSettings($pdo);
    } else {
        $error = "Ошибка при обновлении настроек";
    }
}

// Добавление категории
if (isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
    $success = "Категория успешно добавлена";
}

// Добавление товара
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    // Убираем поле image из формы
    
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $category_id]);
    $success = "Товар успешно добавлен";
}

// Удаление категории
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];
    $stmt = $pdo->prepare("UPDATE categories SET active = 0 WHERE id = ?");
    $stmt->execute([$category_id]);
    $success = "Категория удалена";
}

// Удаление товара
if (isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    $stmt = $pdo->prepare("UPDATE products SET active = 0 WHERE id = ?");
    $stmt->execute([$product_id]);
    $success = "Товар удален";
}

// Получение категорий и товаров
$categories = $pdo->query("SELECT * FROM categories WHERE active = 1")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.active = 1")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main class="container my-5">
        <h2 class="mb-4"><i class="fas fa-cog me-2"></i>Админ-панель</h2>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <ul class="nav nav-tabs" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="theme-tab" data-bs-toggle="tab" data-bs-target="#theme" type="button" role="tab"><i class="fas fa-palette me-2"></i>Оформление</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button" role="tab"><i class="fas fa-th-large me-2"></i>Категории</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab"><i class="fas fa-box me-2"></i>Товары</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="images-tab" data-bs-toggle="tab" data-bs-target="#images" type="button" role="tab"><i class="fas fa-images me-2"></i>Изображения</button>
            </li>
        </ul>
        
        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="adminTabsContent">
            <!-- Вкладка оформления -->
            <div class="tab-pane fade show active" id="theme" role="tabpanel">
                <h4>Настройки оформления</h4>
                <p class="text-muted">Изменения применяются ко всему сайту для всех пользователей</p>
                
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цвет шапки</label>
                                <input type="color" name="header_color" class="form-control form-control-color" value="<?= $theme_settings['header_color'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Цвет подвала</label>
                                <input type="color" name="footer_color" class="form-control form-control-color" value="<?= $theme_settings['footer_color'] ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цвет кнопок</label>
                                <input type="color" name="button_color" class="form-control form-control-color" value="<?= $theme_settings['button_color'] ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL логотипа</label>
                                <input type="text" name="logo_url" class="form-control" value="<?= $theme_settings['logo_url'] ?>" required>
                                <small class="form-text text-muted">
                                    Укажите путь к изображению (например: images/logo.png)
                                </small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="update_theme" class="btn btn-primary">Сохранить настройки</button>
                </form>
                
                <div class="mt-4">
                    <h5>Предпросмотр текущих настроек:</h5>
                    <div class="preview-section">
                        <div class="preview-header" style="background-color: <?= $theme_settings['header_color'] ?>; padding: 15px; color: white;">
                            <strong>WoodTech</strong> - Предпросмотр шапки
                        </div>
                        <div class="preview-content p-4 bg-light">
                            <p>Пример контента страницы</p>
                            <button class="btn me-2" style="background-color: <?= $theme_settings['button_color'] ?>; color: white;">
                                Основная кнопка
                            </button>
                            <button class="btn btn-outline-secondary" style="border-color: <?= $theme_settings['button_color'] ?>; color: <?= $theme_settings['button_color'] ?>;">
                                Контурная кнопка
                            </button>
                        </div>
                        <div class="preview-footer" style="background-color: <?= $theme_settings['footer_color'] ?>; padding: 15px; color: white;">
                            WoodTech - Предпросмотр подвала
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Текущий логотип:</h5>
                    <?php if ($theme_settings['logo_url'] && file_exists($theme_settings['logo_url'])): ?>
                        <img src="<?= $theme_settings['logo_url'] ?>" alt="Логотип" style="max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                        <p class="mt-2"><small>Путь: <?= $theme_settings['logo_url'] ?></small></p>
                    <?php else: ?>
                        <p class="text-muted">Логотип не найден по указанному пути</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Вкладка категорий -->
            <div class="tab-pane fade" id="categories" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Добавить категорию</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Название категории</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Описание</label>
                                        <textarea name="description" class="form-control" required></textarea>
                                    </div>
                                    <button type="submit" name="add_category" class="btn btn-primary">Добавить категорию</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Список категорий</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Название</th>
                                                <th>Описание</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($categories as $category): ?>
                                            <tr>
                                                <td><?= $category['name'] ?></td>
                                                <td><?= $category['description'] ?></td>
                                                <td>
                                                    <a href="admin.php?delete_category=<?= $category['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить категорию?')">Удалить</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Вкладка товаров -->
            <div class="tab-pane fade" id="products" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Добавить товар</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">Название товара</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Описание</label>
                                        <textarea name="description" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Цена</label>
                                        <input type="number" name="price" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Категория</label>
                                        <select name="category_id" class="form-control" required>
                                            <?php foreach($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="add_product" class="btn btn-primary">Добавить товар</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Список товаров</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Название</th>
                                                <th>Цена</th>
                                                <th>Категория</th>
                                                <th>Изображение</th>
                                                <th>Действия</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($products as $product): 
                                                $product_image = getProductImage($product['id'], $product['name']);
                                            ?>
                                            <tr>
                                                <td><?= $product['name'] ?></td>
                                                <td><?= $product['price'] ?> руб.</td>
                                                <td><?= $product['category_name'] ?></td>
                                                <td>
                                                    <img src="<?= $product_image ?>" alt="<?= $product['name'] ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <a href="admin.php?delete_product=<?= $product['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">Удалить</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Новая вкладка для управления изображениями -->
            <div class="tab-pane fade" id="images" role="tabpanel">
                <h4>Управление изображениями товаров</h4>
                <div class="alert alert-info">
                    <strong>Инструкция:</strong> Для добавления изображения к товару, загрузите файл в папку <code>images/products/</code> с именем:
                    <ul class="mt-2">
                        <li><code>ID_товара.jpg</code> (например: <code>1.jpg</code>)</li>
                        <li>Или транслитерированное название товара (например: <code>doska_obresnaya.jpg</code>)</li>
                    </ul>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Загрузить изображение</h5>
                            </div>
                            <div class="card-body">
                                <form action="upload_logo.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Выберите изображение</label>
                                        <input type="file" name="product_image" class="form-control" accept="image/*" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Имя файла (без расширения)</label>
                                        <input type="text" name="filename" class="form-control" placeholder="1 или doska_obresnaya" required>
                                        <small class="form-text text-muted">Используйте ID товара или его название</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Загрузить</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Доступные изображения</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $product_images = getAllProductImages();
                                    foreach($product_images as $image):
                                    ?>
                                    <div class="col-6 mb-3">
                                        <img src="<?= $image ?>" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                        <small class="d-block"><?= basename($image) ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <?php if(empty($product_images)): ?>
                                        <div class="col-12">
                                            <p class="text-muted">Нет загруженных изображений</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>