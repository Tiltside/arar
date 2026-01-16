
<?php

session_start();

// Подключение к БД
require __DIR__ . '/../config/db.php'; // Корректируйте путь при необходимости

// Функция: проверить, является ли текущий пользователь админом
function isAdmin() {
    return (
        isset($_SESSION['user']) &&
        is_array($_SESSION['user']) &&
        $_SESSION['user']['role'] === 'admin'
    );
}

// Если не админ — показываем форму входа прямо на этой странице
if (!isAdmin()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            $error = 'Заполните все поля!';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
                $stmt->execute([$login]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['role'] === 'admin') {
                        // Успешная авторизация админа
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'login' => $user['login'],
                            'full_name' => $user['full_name'],
                            'role' => $user['role']
                        ];
                    } else {
                        $error = 'Доступ только для администраторов!';
                    }
                } else {
                    $error = 'Неверный логин или пароль!';
                }
            } catch (PDOException $e) {
                $error = 'Ошибка БД: ' . htmlspecialchars($e->getMessage());
            }
        }
    }

    // Показываем форму входа (если не авторизованы)
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Вход в админку</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Вход в админ-панель</h3>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="login" class="form-label">Логин</label>
                                    <input type="text"
                                           class="form-control"
                                           id="login"
                                           name="login"
                                           value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                                           required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Пароль</label>
                                    <input type="password"
                                           class="form-control"
                                           id="password"
                                           name="password"
                                           required>
                                </div>
                                <button type="submit" class="btn btn-primary">Войти</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit; // Прекращаем выполнение, если не админ
}

// Если пользователь — админ, показываем админ-панель
$currentUser = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Верхняя панель -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">Админ-панель</a>
                <div class="navbar-nav ms-auto">
                    <span class="nav-link text-white">
                        Администратор: <?= htmlspecialchars($currentUser['full_name'] ?? $currentUser['login']) ?>
                    </span>
                    <a href="#" onclick="if(confirm('Выйти?')){document.cookie='PHPSESSID=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT'; location.reload();}" class="nav-link">Выход</a>
                </div>
            </div>
        </nav>

        <!-- Основной контент -->
        <div class="mt-4">
            <div class="mt-4">
    <h2>Управление курсами</h2>

    <!-- Форма добавления нового курса -->
    <form method="POST" action="" class="mb-4">
        <div class="mb-3">
            <label for="new_title" class="form-label">Название курса</label>
            <input type="text" class="form-control" id="new_title" name="new_title" required>
        </div>
        <div class="mb-3">
            <label for="new_description" class="form-label">Описание курса</label>
            <textarea class="form-control" id="new_description" name="new_description" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Добавить курс</button>
    </form>

    <!-- Таблица с курсами -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Получаем список курсов из БД
            $stmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($courses as $course) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($course['id']) . '</td>';
                echo '<td>' . htmlspecialchars($course['title']) . '</td>';
                echo '<td>' . htmlspecialchars($course['description']) . '</td>';
                echo '<td>';
                if ($course['is_active']) {
                    echo '<span class="text-success">Активен</span>';
                } else {
                    echo '<span class="text-muted">Неактивен</span>';
                }
                echo '</td>';
                echo '<td>';
                echo '<div class="btn-group" role="group">';
                echo '<a href="edit_course.php?id=' . $course['id'] . '" class="btn btn-sm btn-primary">Редактировать</a>';
                echo '<button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal' . $course['id'] . '">Удалить</button>';
                echo '</div>';

                // Модальное окно для подтверждения удаления
                echo '<div class="modal fade" id="deleteModal' . $course['id'] . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $course['id'] . '" aria-hidden="true">';
                echo '<div class="modal-dialog">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title" id="deleteModalLabel' . $course['id'] . '">Подтверждение удаления</h5>';
                echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
                echo '</div>';
                echo '<div class="modal-body">';
                echo 'Вы действительно хотите удалить курс "' . htmlspecialchars($course['title']) . '"?';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>';
                echo '<form method="POST" action="delete_course.php" style="display:inline;">';
                echo '<input type="hidden" name="course_id" value="' . $course['id'] . '">';
                echo '<button type="submit" class="btn btn-danger">Удалить</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';

                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
