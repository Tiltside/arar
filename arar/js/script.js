// Обработка кнопок "Смотреть товары" на главной странице
document.addEventListener('DOMContentLoaded', function() {
    // Обработка кнопок категорий
    const viewCategoryButtons = document.querySelectorAll('.view-category');
    viewCategoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-id');
            window.location.href = 'category.php?id=' + categoryId;
        });
    });

    // Добавление в корзину
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId);
        });
    });
    
    // Добавление в избранное
    const addToFavoritesButtons = document.querySelectorAll('.add-to-favorites');
    addToFavoritesButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToFavorites(productId);
        });
    });
    
    // Добавление в сравнение
    const addToCompareButtons = document.querySelectorAll('.add-to-compare');
    addToCompareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCompare(productId);
        });
    });
    
    // Функция добавления в корзину
    function addToCart(productId) {
        fetch('cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'add_to_cart=true&product_id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            alert('Товар добавлен в корзину!');
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    }
    
    // Функция добавления в избранное
    function addToFavorites(productId) {
        fetch('favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'add_to_favorites=true&product_id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            alert('Товар добавлен в избранное!');
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    }
    
    // Функция добавления в сравнение
    function addToCompare(productId) {
        fetch('compare.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'add_to_compare=true&product_id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            alert('Товар добавлен в сравнение!');
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
    }
});