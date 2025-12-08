<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $patronymic = trim($_POST['patronymic']);
    $phone = trim($_POST['phone']);
    $delivery_address = trim($_POST['delivery_address']);

    // Автоматически добавляем +7 к номеру телефона, если его нет
    if (!empty($phone) && !preg_match('/^\+7/', $phone)) {
        $phone = '+7' . preg_replace('/[^0-9]/', '', $phone);
    }

    // Валидация
    $errors = [];
    if (empty($name)) {
        $errors[] = 'Укажите имя';
    }
    if (empty($surname)) {
        $errors[] = 'Укажите фамилию';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, surname = ?, patronymic = ?, phone = ?, delivery_address = ? WHERE id = ?");
            $stmt->execute([$name, $surname, $patronymic, $phone, $delivery_address, $user_id]);
            $success_message = 'Профиль успешно обновлен!';
        } catch (Exception $e) {
            $error_message = 'Ошибка при обновлении профиля';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Обработка смены пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Получаем текущий пароль
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    $errors = [];
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = 'Неверный текущий пароль';
    }
    if (strlen($new_password) < 6) {
        $errors[] = 'Новый пароль должен быть не менее 6 символов';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
        $success_message = 'Пароль успешно изменен!';
    } else {
        $error_message = implode('<br>', $errors);
    }
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Получаем заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .profile-header {
            background: linear-gradient(135deg, <?= $theme_settings['header_color'] ?> 0%, <?= $theme_settings['button_color'] ?> 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: <?= $theme_settings['header_color'] ?>;
            margin: 0 auto 20px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-new { background-color: #007bff; color: white; }
        .status-processing { background-color: #ffc107; color: #000; }
        .status-shipped { background-color: #17a2b8; color: white; }
        .status-delivered { background-color: #28a745; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .nav-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
        }
        .nav-tabs .nav-link.active {
            color: <?= $theme_settings['button_color'] ?>;
            border-bottom-color: <?= $theme_settings['button_color'] ?>;
            background: transparent;
        }
        .btn-save {
            background-color: <?= $theme_settings['button_color'] ?> !important;
            border-color: <?= $theme_settings['button_color'] ?> !important;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="profile-header">
        <div class="container text-center">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h2><?= htmlspecialchars($user['surname'] ?? '') ?> <?= htmlspecialchars($user['name']) ?> <?= htmlspecialchars($user['patronymic'] ?? '') ?></h2>
            <p class="mb-0"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($user['email']) ?></p>
            <?php if (!empty($user['phone'])): ?>
            <p class="mb-0"><i class="fas fa-phone me-2"></i><?= htmlspecialchars($user['phone']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <main class="container my-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $success_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $error_message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                    <i class="fas fa-user me-2"></i>Личные данные
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button">
                    <i class="fas fa-shopping-bag me-2"></i>Мои заказы
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button">
                    <i class="fas fa-lock me-2"></i>Безопасность
                </button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <!-- Личные данные -->
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Редактирование профиля</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="surname" class="form-label">Фамилия <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="surname" name="surname"
                                           value="<?= htmlspecialchars($user['surname'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">Имя <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="patronymic" class="form-label">Отчество</label>
                                    <input type="text" class="form-control" id="patronymic" name="patronymic"
                                           value="<?= htmlspecialchars($user['patronymic'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email_display" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email_display"
                                           value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                    <small class="text-muted">Email нельзя изменить</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_profile" class="form-label">Телефон</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+7</span>
                                        <input type="tel" class="form-control" id="phone_profile" name="phone"
                                               value="<?= htmlspecialchars(preg_replace('/^\+?7/', '', $user['phone'] ?? '')) ?>"
                                               placeholder="(___) ___-__-__" maxlength="15">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="delivery_address_profile" class="form-label">Адрес доставки по умолчанию</label>
                                <textarea class="form-control" id="delivery_address_profile" name="delivery_address"
                                          rows="3" placeholder="Город, улица, дом, квартира"><?= htmlspecialchars($user['delivery_address'] ?? '') ?></textarea>
                                <small class="text-muted">Этот адрес будет автоматически подставляться при оформлении заказа</small>
                            </div>

                            <div class="text-end">
                                <button type="submit" name="update_profile" class="btn btn-save btn-lg">
                                    <i class="fas fa-save me-2"></i>Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Мои заказы -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>История заказов</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">У вас пока нет заказов</h5>
                                <a href="index.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-shopping-cart me-2"></i>Начать покупки
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>№ Заказа</th>
                                            <th>Дата</th>
                                            <th>Сумма</th>
                                            <th>Статус</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                                            <td><?= number_format($order['total_amount'], 2, '.', ' ') ?> руб.</td>
                                            <td>
                                                <?php
                                                $status_labels = [
                                                    'new' => 'Новый',
                                                    'processing' => 'В обработке',
                                                    'shipped' => 'Отправлен',
                                                    'delivered' => 'Доставлен',
                                                    'cancelled' => 'Отменен'
                                                ];
                                                ?>
                                                <span class="status-badge status-<?= $order['status'] ?>">
                                                    <?= $status_labels[$order['status']] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="order_success.php?order_id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>Подробнее
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Безопасность -->
            <div class="tab-pane fade" id="password" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Смена пароля</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Текущий пароль</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Новый пароль</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <small class="text-muted">Минимум 6 символов</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Подтвердите новый пароль</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" name="change_password" class="btn btn-save btn-lg">
                                    <i class="fas fa-key me-2"></i>Изменить пароль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Форматирование номера телефона в профиле
        const phoneProfileInput = document.getElementById('phone_profile');

        if (phoneProfileInput) {
            phoneProfileInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');

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
        }
    </script>
</body>
</html>
