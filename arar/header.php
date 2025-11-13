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
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="<?= $theme_settings['logo_url'] ?>" alt="WoodTech" height="50" onerror="this.src='images/logo.jpg'">
                <span id="site-title">WoodTech</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Главная</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories">Категории</a></li>
                    <li class="nav-item"><a class="nav-link" href="#promotions">Акции</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bonus">Бонусы</a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="favorites.php"><i class="fas fa-heart"></i> Избранное</a></li>
                        <li class="nav-item"><a class="nav-link" href="compare.php"><i class="fas fa-balance-scale"></i> Сравнение</a></li>
                        <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Корзина</a></li>
                        <?php if(isAdmin()): ?>
                            <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-cog"></i> Админ-панель</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="index.php#categories">Категории</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Войти</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>