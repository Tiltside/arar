<?php
// Временно отключаем сессию для CSS
if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

include __DIR__ . '/../config.php';
header("Content-type: text/css");

// Принудительно обновляем настройки из БД
$stmt = $pdo->prepare("SELECT * FROM theme_settings WHERE id = 1");
$stmt->execute();
$theme_settings = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$theme_settings) {
    $theme_settings = [
        'header_color' => '#2c5530',
        'footer_color' => '#2c5530',
        'button_color' => '#2c5530'
    ];
}

$header_color = $theme_settings['header_color'];
$footer_color = $theme_settings['footer_color'];
$button_color = $theme_settings['button_color'];
?>

/* Reset and base styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f8f9fa;
    color: #333;
    line-height: 1.6;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

main {
    flex: 1;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header styles */
header {
    background-color: <?= $header_color ?> !important;
    color: white;
    padding: 15px 0;
    margin-bottom: 30px;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 24px;
    font-weight: bold;
}

/* Footer styles */
footer {
    background-color: <?= $footer_color ?> !important;
    color: white;
    padding: 40px 0 20px;
    margin-top: auto;
    width: 100%;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.footer-section h3 {
    margin-bottom: 15px;
    font-size: 18px;
}

.footer-section p, .footer-section a {
    color: #e0e0e0;
    margin-bottom: 10px;
    display: block;
    text-decoration: none;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.social-icon {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
}

.social-icon:hover {
    background: rgba(255,255,255,0.2);
}

.copyright {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: #bdbdbd;
    font-size: 14px;
}

/* Button styles */
.btn {
    display: inline-block;
    padding: 12px 24px;
    text-align: center;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-checkout {
    background: <?= $button_color ?> !important;
    color: white;
    border: none;
}

.btn-checkout:hover {
    background: <?= $button_color ?> !important;
    opacity: 0.9;
    transform: translateY(-2px);
}

.btn-primary {
    background: <?= $button_color ?> !important;
    border-color: <?= $button_color ?> !important;
    color: white;
}

.btn-primary:hover {
    background: <?= $button_color ?> !important;
    opacity: 0.9;
    transform: translateY(-2px);
}

.btn-outline-primary {
    border-color: <?= $button_color ?> !important;
    color: <?= $button_color ?> !important;
}

.btn-outline-primary:hover {
    background: <?= $button_color ?> !important;
    color: white !important;
}

/* Card styles */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #e0e0e0;
    color: #333 !important;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-title {
    color: #2c3e50 !important;
    font-weight: 600;
}

.card-text {
    color: #666 !important;
}

/* Category cards */
.category-card {
    text-align: center;
    padding: 20px;
}

.category-card .category-icon {
    margin-bottom: 20px;
}

.category-card .card-title {
    color: #2c3e50 !important;
    margin-bottom: 15px;
}

.category-card .card-text {
    color: #666 !important;
    margin-bottom: 20px;
}

/* Product cards */
.product-card .card-img-container {
    height: 250px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    position: relative;
}

.product-card .card-img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}

.product-card .price {
    color: #28a745 !important;
    font-weight: bold;
    font-size: 1.25rem;
}

.product-card .action-buttons {
    display: flex;
    gap: 8px;
    margin-top: 15px;
}

.product-card .action-buttons .btn {
    flex: 1;
    font-size: 0.85rem;
    padding: 8px 12px;
}

/* Promotion cards */
.promotion-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    text-align: center;
    padding: 30px 20px;
}

.promotion-card .card-title {
    color: #2c3e50 !important;
    margin-bottom: 15px;
}

.promotion-card .card-text {
    color: #666 !important;
}

/* Bonus section */
.bonus-info {
    background: linear-gradient(135deg, #fff9e6 0%, #fff3cd 100%);
    border-left: 4px solid #ffc107;
}

.bonus-info ul {
    list-style: none;
    padding-left: 0;
}

.bonus-info ul li {
    padding: 5px 0;
    color: #666 !important;
}

.bonus-info ul li i {
    color: #28a745;
    margin-right: 10px;
}

/* Section titles */
.section-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 15px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, <?= $button_color ?> 0%, #667eea 100%);
    border-radius: 2px;
}

/* Cart styles */
.cart-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
}

.cart-table th {
    background-color: #f1f8e9;
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #2c5530;
}

.cart-table td {
    padding: 15px;
    border-bottom: 1px solid #eaeaea;
}

.quantity-controls {
    display: flex;
    align-items: center;
}

.quantity-btn {
    width: 35px;
    height: 35px;
    background: #f1f8e9;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.quantity-btn:hover {
    background: #dcedc8;
}

.quantity-input {
    width: 60px;
    height: 35px;
    text-align: center;
    margin: 0 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.image-placeholder {
    width: 80px;
    height: 80px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: #6c757d;
}

/* Responsive design */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .header-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
    
    .product-card .action-buttons {
        flex-direction: column;
    }
    
    .cart-table {
        font-size: 14px;
    }
    
    .cart-table th, .cart-table td {
        padding: 10px;
    }
}

/* Utility classes */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 0.5rem; }
.mb-2 { margin-bottom: 1rem; }
.mb-3 { margin-bottom: 1.5rem; }
.mb-4 { margin-bottom: 2rem; }
.mb-5 { margin-bottom: 3rem; }
.mt-0 { margin-top: 0; }
.mt-1 { margin-top: 0.5rem; }
.mt-2 { margin-top: 1rem; }
.mt-3 { margin-top: 1.5rem; }
.mt-4 { margin-top: 2rem; }
.mt-5 { margin-top: 3rem; }
/* Фиксированные размеры для консистентности */
.card-img-container {
    height: 250px !important;
}

.card-img-top {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}

/* Гарантия одинаковой высоты карточек */
.product-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card .card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-card .card-text {
    flex-grow: 1;
}

/* Исправление для категорий */
.category-card .card-body {
    padding: 2rem 1rem;
}

.category-card .category-icon {
    margin-bottom: 1.5rem;
}