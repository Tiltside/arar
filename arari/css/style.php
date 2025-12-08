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
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Dark mode styles */
body.dark-mode {
    background-color: #1a1a1a;
    color: #e0e0e0;
}

body.dark-mode .card {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode .product-card,
body.dark-mode .category-card,
body.dark-mode .feature-card {
    background-color: #2d2d2d;
    border-color: #404040;
}

body.dark-mode .card-title,
body.dark-mode .product-card .card-title,
body.dark-mode .category-card .card-title {
    color: #ffffff !important;
}

body.dark-mode .card-text,
body.dark-mode .product-card .card-text,
body.dark-mode .category-card .card-text {
    color: #b0b0b0 !important;
}

body.dark-mode .section-title {
    color: #ffffff;
}

body.dark-mode .breadcrumb {
    background-color: #2d2d2d;
}

body.dark-mode .breadcrumb-item a {
    color: <?= $button_color ?>;
}

body.dark-mode .breadcrumb-item.active {
    color: #e0e0e0;
}

body.dark-mode .form-control,
body.dark-mode .form-select {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode .form-control:focus,
body.dark-mode .form-select:focus {
    background-color: #2d2d2d;
    border-color: <?= $button_color ?>;
    color: #e0e0e0;
}

body.dark-mode .input-group-text {
    background-color: #404040;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode .table {
    color: #e0e0e0;
}

body.dark-mode .table-hover tbody tr:hover {
    background-color: #404040;
}

body.dark-mode .alert-info {
    background-color: #2d4a5c;
    border-color: #3d5a6c;
    color: #e0e0e0;
}

body.dark-mode h1,
body.dark-mode h2,
body.dark-mode h3,
body.dark-mode h4,
body.dark-mode h5,
body.dark-mode h6 {
    color: #ffffff;
}

body.dark-mode p,
body.dark-mode li,
body.dark-mode span:not(.badge) {
    color: #e0e0e0;
}

body.dark-mode .text-muted {
    color: #b0b0b0 !important;
}

body.dark-mode .badge {
    color: #ffffff !important;
}

body.dark-mode .btn-outline-secondary {
    color: #e0e0e0;
    border-color: #6c757d;
}

body.dark-mode .btn-outline-secondary:hover {
    background-color: #6c757d;
    color: #ffffff;
}

body.dark-mode label {
    color: #e0e0e0;
}

body.dark-mode .card-header {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #ffffff;
}

body.dark-mode .nav-tabs {
    border-color: #404040;
}

body.dark-mode .nav-tabs .nav-link {
    color: #e0e0e0;
    background-color: transparent;
    border-color: transparent;
}

body.dark-mode .nav-tabs .nav-link:hover {
    color: #ffffff;
    border-color: #404040;
}

body.dark-mode .nav-tabs .nav-link.active {
    color: #ffffff;
    background-color: #2d2d2d;
    border-color: #404040 #404040 #2d2d2d;
}

body.dark-mode .table {
    background-color: #2d2d2d;
    border-color: #404040;
}

body.dark-mode .table thead th {
    background-color: #404040;
    color: #ffffff;
    border-color: #505050;
}

body.dark-mode .table tbody td {
    border-color: #505050;
    color: #ffffff;
    background-color: #2d2d2d;
}

body.dark-mode .table tbody tr {
    background-color: #2d2d2d;
}

body.dark-mode .table tbody td h6 {
    color: #ffffff;
}

body.dark-mode .alert {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode .alert-success {
    background-color: #1e4620;
    border-color: #2d5a2f;
    color: #9cdfad;
}

body.dark-mode .alert-danger {
    background-color: #4a1e1e;
    border-color: #6a2d2d;
    color: #f5b7b1;
}

body.dark-mode a {
    color: <?= $button_color ?>;
}

body.dark-mode a:hover {
    color: <?= $button_color ?>;
    opacity: 0.8;
}

body.dark-mode .text-success {
    color: #9cdfad !important;
}

body.dark-mode .text-danger {
    color: #f5b7b1 !important;
}

body.dark-mode .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #ffffff;
}

body.dark-mode .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

body.dark-mode .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    color: #ffffff;
}

body.dark-mode .btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

body.dark-mode .list-unstyled li {
    color: #e0e0e0;
}

body.dark-mode .feature-card {
    background-color: #2d2d2d;
    border-color: #404040;
}

body.dark-mode .stats-section {
    background-color: #1a1a1a;
}

body.dark-mode .stat-number {
    color: <?= $button_color ?>;
}

body.dark-mode .team-avatar {
    background-color: <?= $button_color ?>;
}

body.dark-mode .order-summary {
    background-color: #2d2d2d;
}

body.dark-mode strong {
    color: #ffffff;
}

body.dark-mode small {
    color: #b0b0b0;
}

body.dark-mode .notification {
    background-color: #2d2d2d;
    color: #e0e0e0;
}

body.dark-mode .notification-message {
    color: #e0e0e0;
}

body.dark-mode .confirmation-modal {
    background-color: #2d2d2d;
}

body.dark-mode .confirmation-header {
    border-color: #404040;
}

body.dark-mode .confirmation-header h3 {
    color: #ffffff;
}

body.dark-mode .confirmation-body p {
    color: #e0e0e0;
}

body.dark-mode .confirmation-footer .btn-cancel {
    background-color: #404040;
    color: #e0e0e0;
    border-color: #505050;
}

body.dark-mode .confirmation-footer .btn-cancel:hover {
    background-color: #505050;
}

body.dark-mode .table-light {
    background-color: #404040 !important;
    color: #ffffff !important;
}

body.dark-mode .table-light th {
    background-color: #404040 !important;
    color: #ffffff !important;
}

body.dark-mode .table-bordered {
    border-color: #505050;
}

body.dark-mode .table-bordered th,
body.dark-mode .table-bordered td {
    border-color: #505050;
}

body.dark-mode .btn-outline-danger {
    color: #f5b7b1;
    border-color: #dc3545;
}

body.dark-mode .btn-outline-danger:hover {
    background-color: #dc3545;
    color: #ffffff;
}

body.dark-mode .btn-outline-primary {
    color: <?= $button_color ?>;
    border-color: <?= $button_color ?>;
}

body.dark-mode .btn-outline-primary:hover {
    background-color: <?= $button_color ?>;
    color: #ffffff;
}

body.dark-mode .btn-primary {
    color: #ffffff !important;
}

body.dark-mode .btn {
    color: #ffffff;
}

body.dark-mode .card-body h5,
body.dark-mode .card-body h6 {
    color: #ffffff;
}

body.dark-mode .card-body p {
    color: #e0e0e0;
}

body.dark-mode .text-muted {
    color: #b0b0b0 !important;
}

body.dark-mode .fa-balance-scale {
    color: #e0e0e0;
}

body.dark-mode .promotion-card {
    background: linear-gradient(135deg, #2d2d2d 0%, #3a3a3a 100%);
}

body.dark-mode .bonus-info {
    background: linear-gradient(135deg, #3a3a00 0%, #4a4a00 100%);
    border-left-color: #ffc107;
}

body.dark-mode .bonus-info ul li {
    color: #e0e0e0 !important;
}

body.dark-mode .bonus-info ul li i {
    color: #9cdfad;
}

body.dark-mode .breadcrumb {
    background-color: #2d2d2d;
}

body.dark-mode .breadcrumb-item a {
    color: <?= $button_color ?>;
}

body.dark-mode .breadcrumb-item.active {
    color: #e0e0e0;
}

body.dark-mode .review-card {
    background-color: #2d2d2d;
    border-left-color: <?= $button_color ?>;
}

body.dark-mode .stock-badge {
    color: #ffffff;
}

body.dark-mode .rating-input label {
    color: #505050;
}

body.dark-mode .rating-input input[type="radio"]:checked ~ label,
body.dark-mode .rating-input label:hover,
body.dark-mode .rating-input label:hover ~ label {
    color: #ffc107;
}

body.dark-mode textarea.form-control {
    background-color: #2d2d2d;
    border-color: #404040;
    color: #e0e0e0;
}

body.dark-mode textarea.form-control:focus {
    background-color: #2d2d2d;
    border-color: <?= $button_color ?>;
    color: #e0e0e0;
}

body.dark-mode textarea.form-control::placeholder {
    color: #808080;
}

body.dark-mode input.form-control::placeholder {
    color: #808080;
}

body.dark-mode .alert-info {
    background-color: #1a3a52;
    border-color: #2d5a7f;
    color: #9cd5f5;
}

body.dark-mode .alert-info a {
    color: #ffffff;
    text-decoration: underline;
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
    color: white !important;
    padding: 15px 0;
    margin-bottom: 30px;
}

header .nav-link {
    color: white !important;
}

header .navbar-brand,
header .navbar-brand span {
    color: white !important;
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

/* Navbar styles */
.navbar {
    align-items: center !important;
}

.navbar-brand {
    font-size: 28px !important;
    font-weight: 700 !important;
    display: flex !important;
    align-items: center !important;
}

.navbar-brand span {
    font-size: 28px !important;
    font-weight: 700 !important;
    margin-left: 10px;
}

.navbar-nav {
    align-items: center !important;
}

.nav-item {
    display: flex !important;
    align-items: center !important;
}

.nav-link {
    font-size: 18px !important;
    font-weight: 600 !important;
    padding: 8px 16px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    white-space: nowrap !important;
    line-height: 1.5 !important;
}

.nav-link i {
    font-size: 16px;
    line-height: 1;
}

/* Floating theme toggle button */
#theme-toggle-floating {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: <?= $button_color ?>;
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

#theme-toggle-floating:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
}

#theme-toggle-floating i {
    font-size: 24px;
}

body.dark-mode #theme-toggle-floating {
    background: #404040;
}

@media (max-width: 768px) {
    #theme-toggle-floating {
        width: 50px;
        height: 50px;
        bottom: 20px;
        right: 20px;
    }

    #theme-toggle-floating i {
        font-size: 20px;
    }
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
    color: <?= $header_color ?> !important;
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
    color: <?= $header_color ?>;
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
    background: linear-gradient(135deg, <?= $header_color ?> 0%, <?= $button_color ?> 100%);
    border-radius: 2px;
}

/* Cart styles */
.remove-item-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    min-height: 38px;
}

.remove-item-btn i {
    margin: 0 !important;
}

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

/* Notification styles */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.notification {
    background: white;
    padding: 16px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 300px;
    max-width: 400px;
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    flex-shrink: 0;
}

.notification-success {
    border-left: 4px solid #28a745;
}

.notification-success .notification-icon {
    background: #28a745;
    color: white;
}

.notification-error {
    border-left: 4px solid #dc3545;
}

.notification-error .notification-icon {
    background: #dc3545;
    color: white;
}

.notification-message {
    color: #333;
    font-size: 14px;
    font-weight: 500;
}

@media (max-width: 768px) {
    .notification-container {
        top: 10px;
        right: 10px;
        left: 10px;
    }

    .notification {
        min-width: auto;
        width: 100%;
    }
}

/* Confirmation Modal styles */
.confirmation-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.confirmation-overlay.show {
    opacity: 1;
}

.confirmation-modal {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    max-width: 450px;
    width: 90%;
    padding: 0;
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.confirmation-overlay.show .confirmation-modal {
    transform: scale(1);
}

.confirmation-header {
    padding: 24px 24px 16px;
    border-bottom: 1px solid #e0e0e0;
}

.confirmation-header h3 {
    margin: 0;
    color: #333;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.confirmation-header .icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff3cd;
    color: #856404;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.confirmation-body {
    padding: 24px;
}

.confirmation-body p {
    margin: 0;
    color: #666;
    font-size: 15px;
    line-height: 1.5;
}

.confirmation-footer {
    padding: 16px 24px 24px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.confirmation-footer .btn {
    padding: 10px 24px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.confirmation-footer .btn-cancel {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #dee2e6;
}

.confirmation-footer .btn-cancel:hover {
    background: #e9ecef;
}

.confirmation-footer .btn-confirm {
    background: #dc3545;
    color: white;
}

.confirmation-footer .btn-confirm:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .confirmation-modal {
        width: 95%;
    }

    .confirmation-footer {
        flex-direction: column-reverse;
    }

    .confirmation-footer .btn {
        width: 100%;
    }
}