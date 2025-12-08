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
        .then(() => {
            showNotification('Товар добавлен в корзину!', 'success');
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showNotification('Ошибка при добавлении товара', 'error');
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
        .then(() => {
            showNotification('Товар добавлен в избранное!', 'success');
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showNotification('Ошибка при добавлении в избранное', 'error');
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
        .then(() => {
            showNotification('Товар добавлен в сравнение!', 'success');
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showNotification('Ошибка при добавлении в сравнение', 'error');
        });
    }

    // Функция показа уведомлений
    function showNotification(message, type = 'success') {
        // Создаем контейнер для уведомлений, если его еще нет
        let container = document.getElementById('notification-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        // Создаем элемент уведомления
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        // Добавляем иконку в зависимости от типа
        const icon = type === 'success' ? '✓' : '✕';
        notification.innerHTML = `
            <span class="notification-icon">${icon}</span>
            <span class="notification-message">${message}</span>
        `;

        // Добавляем уведомление в контейнер
        container.appendChild(notification);

        // Показываем уведомление с анимацией
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Удаляем уведомление через 3 секунды
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
                // Удаляем контейнер, если он пустой
                if (container.children.length === 0) {
                    container.remove();
                }
            }, 300);
        }, 3000);
    }
});

// Функция для показа модального окна подтверждения
function showConfirmDialog(title, message, onConfirm, onCancel) {
    // Создаем overlay
    const overlay = document.createElement('div');
    overlay.className = 'confirmation-overlay';

    // Создаем модальное окно
    overlay.innerHTML = `
        <div class="confirmation-modal">
            <div class="confirmation-header">
                <h3>
                    <span class="icon">⚠</span>
                    ${title}
                </h3>
            </div>
            <div class="confirmation-body">
                <p>${message}</p>
            </div>
            <div class="confirmation-footer">
                <button class="btn btn-cancel">Отмена</button>
                <button class="btn btn-confirm">Удалить</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    // Показываем с анимацией
    setTimeout(() => {
        overlay.classList.add('show');
    }, 10);

    // Обработчики кнопок
    const confirmBtn = overlay.querySelector('.btn-confirm');
    const cancelBtn = overlay.querySelector('.btn-cancel');

    const closeModal = () => {
        overlay.classList.remove('show');
        setTimeout(() => {
            overlay.remove();
        }, 300);
    };

    confirmBtn.addEventListener('click', () => {
        closeModal();
        if (onConfirm) onConfirm();
    });

    cancelBtn.addEventListener('click', () => {
        closeModal();
        if (onCancel) onCancel();
    });

    // Закрытие по клику на overlay
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeModal();
            if (onCancel) onCancel();
        }
    });

    // Закрытие по ESC
    const escHandler = (e) => {
        if (e.key === 'Escape') {
            closeModal();
            if (onCancel) onCancel();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
}