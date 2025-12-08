<?php
session_start();
require_once 'config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас - WoodTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.php?v=<?= time() ?>">
    <style>
        .about-header {
            background: linear-gradient(135deg, <?= $theme_settings['header_color'] ?> 0%, <?= $theme_settings['button_color'] ?> 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .feature-icon {
            font-size: 48px;
            color: <?= $theme_settings['button_color'] ?>;
            margin-bottom: 20px;
        }
        .feature-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .stats-section {
            background: #f8f9fa;
            padding: 60px 0;
            margin: 40px 0;
        }
        .stat-number {
            font-size: 48px;
            font-weight: 700;
            color: <?= $theme_settings['button_color'] ?>;
        }
        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }
        .team-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: <?= $theme_settings['button_color'] ?>;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="about-header">
        <div class="container text-center">
            <h1 class="display-4 mb-3">О компании WoodTech</h1>
            <p class="lead">Ваш надежный партнер в мире строительных материалов</p>
        </div>
    </div>

    <main class="container my-5">
        <!-- О компании -->
        <section class="mb-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4">
                    <h2 class="mb-4">Кто мы такие?</h2>
                    <p class="lead">WoodTech - это современный интернет-магазин строительных материалов, специализирующийся на пиломатериалах и сопутствующих товарах.</p>
                    <p>Мы работаем на рынке с 2015 года и за это время успели завоевать доверие тысяч клиентов по всей России. Наша миссия - предоставить качественные материалы по доступным ценам с максимально удобным сервисом.</p>
                    <p>Мы постоянно расширяем ассортимент и улучшаем качество обслуживания, следим за новинками рынка и предлагаем только проверенную продукцию от надежных производителей.</p>
                </div>
                <div class="col-lg-6 mb-4">
                    <img src="images/about-company.jpg" alt="О компании" class="img-fluid rounded shadow"
                         onerror="this.src='https://via.placeholder.com/600x400?text=WoodTech'">
                </div>
            </div>
        </section>

        <!-- Наши преимущества -->
        <section class="mb-5">
            <h2 class="text-center mb-5">Наши преимущества</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-certificate feature-icon"></i>
                        <h4>Качество</h4>
                        <p>Все материалы проходят строгий контроль качества. Мы работаем только с проверенными поставщиками.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-truck feature-icon"></i>
                        <h4>Быстрая доставка</h4>
                        <p>Доставка по Москве в течение 1-2 дней. По России - от 3 до 7 дней. Бесплатная доставка от 10 000 руб.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-ruble-sign feature-icon"></i>
                        <h4>Выгодные цены</h4>
                        <p>Работаем напрямую с производителями, что позволяет предлагать лучшие цены на рынке.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-headset feature-icon"></i>
                        <h4>Поддержка 24/7</h4>
                        <p>Наши специалисты всегда готовы помочь с выбором материалов и ответить на любые вопросы.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-shield-alt feature-icon"></i>
                        <h4>Гарантия</h4>
                        <p>Предоставляем гарантию на всю продукцию. Возврат и обмен в течение 14 дней.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card text-center">
                        <i class="fas fa-gift feature-icon"></i>
                        <h4>Бонусная программа</h4>
                        <p>Накапливайте бонусы с каждой покупки и оплачивайте ими до 30% следующего заказа.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Статистика -->
        <section class="stats-section">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-3 mb-4">
                        <div class="stat-number">10+</div>
                        <p class="text-muted">лет на рынке</p>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-number">5000+</div>
                        <p class="text-muted">довольных клиентов</p>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-number">500+</div>
                        <p class="text-muted">товаров в ассортименте</p>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="stat-number">99%</div>
                        <p class="text-muted">положительных отзывов</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Наша команда -->
        <section class="mb-5">
            <h2 class="text-center mb-5">Наша команда</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4>Иван Петров</h4>
                        <p class="text-muted">Генеральный директор</p>
                        <p>Более 15 лет опыта в строительной индустрии</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4>Мария Сидорова</h4>
                        <p class="text-muted">Руководитель отдела продаж</p>
                        <p>Эксперт по подбору материалов для любых проектов</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4>Алексей Иванов</h4>
                        <p class="text-muted">Руководитель службы доставки</p>
                        <p>Гарантирует своевременную доставку заказов</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Контакты -->
        <section class="mb-5">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Свяжитесь с нами</h2>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5><i class="fas fa-map-marker-alt me-2"></i>Адрес</h5>
                            <p>г. Москва, ул. Строительная, д. 15</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5><i class="fas fa-phone me-2"></i>Телефон</h5>
                            <p>+7 (999) 123-45-67</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5><i class="fas fa-envelope me-2"></i>Email</h5>
                            <p>info@woodtech.ru</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h5><i class="fas fa-clock me-2"></i>Режим работы</h5>
                            <p>Пн-Пт: 9:00 - 18:00<br>Сб-Вс: 10:00 - 16:00</p>
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
