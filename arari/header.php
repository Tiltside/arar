<?php 
// Не включаем config.php здесь, он уже включен в index.php
?>
<style>
.header, .navbar {
    background-color: <?= $theme_settings['header_color'] ?> !important;
}
</style>
<!-- Шапка сайта -->
<header id="site-header" class="header">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="<?= $theme_settings['logo_url'] ?>" alt="WoodTech" height="50" onerror="this.src='images/logo.jpg'">
                <span id="site-title">WoodTech</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Левая часть навигации -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#categories">Категории</a></li>
                    <li class="nav-item"><a class="nav-link" href="#promotions">Акции</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bonus">Бонусы</a></li>
                </ul>

                <!-- Правая часть навигации -->
                <ul class="navbar-nav ms-auto">
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="favorites.php"><i class="fas fa-heart"></i> Избранное</a></li>
                        <li class="nav-item"><a class="nav-link" href="compare.php"><i class="fas fa-balance-scale"></i> Сравнение</a></li>
                        <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Корзина</a></li>
                        <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user-circle"></i> Профиль</a></li>
                        <?php if(isAdmin()): ?>
                            <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cog"></i> Админ-панель</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Войти</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Floating theme toggle button -->
<button id="theme-toggle-floating" title="Переключить тему">
    <i class="fas fa-moon"></i>
</button>

<script>
    // Переключение темы
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle-floating');
        const themeIcon = themeToggle.querySelector('i');

        // Проверяем сохраненную тему
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Обработчик переключения темы
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });

        // Позиционирование кнопки относительно футера
        function updateButtonPosition() {
            const footer = document.getElementById('site-footer');
            const button = document.getElementById('theme-toggle-floating');

            if (!footer || !button) return;

            const footerRect = footer.getBoundingClientRect();
            const windowHeight = window.innerHeight;

            // Если футер виден на экране
            if (footerRect.top < windowHeight) {
                const offset = windowHeight - footerRect.top + 30; // 30px отступ
                button.style.bottom = offset + 'px';
            } else {
                button.style.bottom = '30px';
            }
        }

        // Обновляем позицию при скролле
        window.addEventListener('scroll', updateButtonPosition);
        window.addEventListener('resize', updateButtonPosition);

        // Начальная позиция
        updateButtonPosition();
    });
</script>