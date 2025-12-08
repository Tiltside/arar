<?php
session_start();

$host = 'localhost';
$dbname = 'woodtech';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}

// Проверяем, не объявлены ли функции уже
if (!function_exists('isLoggedIn')) {
    // Функция для проверки авторизации
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('isAdmin')) {
    // Функция для проверки админских прав
    function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
}

if (!function_exists('getThemeSettings')) {
    // Функция получения настроек оформления
    function getThemeSettings($pdo) {
        $stmt = $pdo->prepare("SELECT * FROM theme_settings WHERE id = 1");
        $stmt->execute();
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$settings) {
            // Создаем настройки по умолчанию
            $default_settings = [
                'header_color' => '#2c3e50',
                'footer_color' => '#34495e', 
                'button_color' => '#3498db',
                'logo_url' => 'images/logo.jpg'
            ];
            
            $stmt = $pdo->prepare("INSERT INTO theme_settings (header_color, footer_color, button_color, logo_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$default_settings['header_color'], $default_settings['footer_color'], $default_settings['button_color'], $default_settings['logo_url']]);
            
            return $default_settings;
        }
        
        return $settings;
    }
}

if (!function_exists('getProductImage')) {
    function getProductImage($product_id, $product_name) {
        $image_dir = 'images/';
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Убираем размеры из названия для поиска
        $clean_name = preg_replace('/\d+[xх]\d+[xх]\d+/', '', $product_name);
        $clean_name = preg_replace('/\d+\.\d+/', '', $clean_name);
        $clean_name = trim($clean_name);
        
        // Варианты имен для поиска
        $search_names = [
            translit($clean_name),
            translit($product_name),
            $product_id,
            translit(explode(' ', $clean_name)[0])
        ];
        
        // Убираем дубликаты
        $search_names = array_unique($search_names);
        
        foreach ($search_names as $name) {
            foreach ($allowed_extensions as $ext) {
                $filename = $name . '.' . $ext;
                if (file_exists($image_dir . $filename)) {
                    return $image_dir . $filename;
                }
            }
        }
        
        return 'images/no-image.jpg';
    }
}

if (!function_exists('translit')) {
    // Функция транслитерации для названий файлов
    function translit($string) {
        $converter = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            
            ' ' => '_', '-' => '_', ',' => '', '.' => '', '!' => '',
            '?' => '', '(' => '', ')' => '', '[' => '', ']' => '',
            '{' => '', '}' => '', '<' => '', '>' => '', '#' => '',
            '$' => '', '%' => '', '^' => '', '&' => '', '*' => '',
            '+' => '', '=' => '', '|' => '', '\\' => '', '/' => '',
            ':' => '', ';' => '', '"' => '', "'" => '', '`' => '',
            '~' => ''
        ];
        
        $string = strtr($string, $converter);
        $string = preg_replace('/_{2,}/', '_', $string);
        $string = trim($string, '_');
        $string = strtolower($string);
        
        return $string;
    }
}

if (!function_exists('getAllProductImages')) {
    // Функция для получения всех изображений товаров
    function getAllProductImages() {
        $image_dir = 'images/';
        $images = [];
        
        if (is_dir($image_dir)) {
            $files = scandir($image_dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && !is_dir($image_dir . $file)) {
                    // Исключаем логотип из списка товаров
                    if ($file != 'logo.jpg' && strpos($file, 'logo.') === false) {
                        $images[] = $image_dir . $file;
                    }
                }
            }
        }
        
        return $images;
    }
}

// Получаем текущие настройки темы
$theme_settings = getThemeSettings($pdo);
?>