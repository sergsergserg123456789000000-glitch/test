<?php
/**
 * Database Connection Class
 * Поддерживает SQLite-файл рядом с сайтом и MySQL.
 */

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            if (defined('DB_DRIVER') && DB_DRIVER === 'sqlite') {
                $this->connectSqlite($options);
            } else {
                $this->connectMysql($options);
            }
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            die(json_encode([
                'error' => 'Database connection failed',
                'details' => 'Проверьте права на папку сайта и наличие расширения pdo_sqlite/PDO.'
            ], JSON_UNESCAPED_UNICODE));
        }
    }

    private function connectSqlite(array $options) {
        $dbPath = DB_PATH;
        $isNewDatabase = !file_exists($dbPath);

        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!is_writable($dir)) {
            throw new PDOException('Папка сайта недоступна для записи. Нужны права на создание database.sqlite');
        }

        $this->pdo = new PDO('sqlite:' . $dbPath, null, null, $options);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
        $this->pdo->exec('PRAGMA journal_mode = WAL');

        if ($isNewDatabase || !$this->tableExists('admin_users')) {
            $this->initializeSqliteSchema();
        }
        
        // Таблица отзывов
        if (!$this->tableExists('testimonials')) {
            $this->pdo->exec("CREATE TABLE testimonials (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                author_name TEXT NOT NULL,
                author_role TEXT,
                text TEXT NOT NULL,
                rating INTEGER DEFAULT 5,
                avatar TEXT,
                is_published INTEGER DEFAULT 1,
                sort_order INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            // Стартовые отзывы
            $seed = array(
                array('Алексей Морозов', 'CTO, АльфаТех', 'За 2 года использования NimbusGuard Pro не было ни одного инцидента с вирусами на 250+ рабочих станциях.', 5, 'А'),
                array('Мария Соколова', 'IT-директор, Северные сети', 'Перешли с импортного решения за один weekend. Поддержка отвечает за минуты, а не дни.', 5, 'М'),
                array('Дмитрий Иванов', 'Фрилансер', 'NimbusClean — лучшая утилита для очистки, что я пробовал. Ускорила мой старый ноутбук в 2 раза.', 5, 'Д'),
            );
            $stmt = $this->pdo->prepare("INSERT INTO testimonials (author_name, author_role, text, rating, avatar, is_published, sort_order) VALUES (?, ?, ?, ?, ?, 1, ?)");
            $i = 0;
            foreach ($seed as $row) {
                $stmt->execute(array($row[0], $row[1], $row[2], $row[3], $row[4], $i++));
            }
        }

        // Миграции: создаём таблицы трекинга, если их нет в старой БД
        if (!$this->tableExists('visitors')) {
            $this->pdo->exec("CREATE TABLE visitors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                visitor_id TEXT NOT NULL,
                session_id TEXT NOT NULL UNIQUE,
                ip_address TEXT,
                user_agent TEXT,
                country TEXT, country_code TEXT, region TEXT, city TEXT, isp TEXT,
                browser TEXT, os TEXT, device_type TEXT,
                referer TEXT, source TEXT, first_url TEXT,
                pages_count INTEGER DEFAULT 1,
                duration_seconds INTEGER DEFAULT 0,
                created_ts INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                last_activity TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $this->pdo->exec("CREATE INDEX idx_visitors_created ON visitors(created_at)");
        }
        if (!$this->tableExists('visitor_pageviews')) {
            $this->pdo->exec("CREATE TABLE visitor_pageviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                visitor_id INTEGER NOT NULL,
                url TEXT,
                page_title TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $this->pdo->exec("CREATE INDEX idx_pageviews_visitor ON visitor_pageviews(visitor_id)");
        }

        $this->migrateUsersMailProvider();
        $this->migrateProductCoverImage();

        $this->ensureSetting('site_logo_path', 'images/Logo-Master-Software.ico', 'text', 'Логотип сайта');
        $this->ensureSetting('site_favicon_path', 'images/Logo-Master-Software.ico', 'text', 'Фавикон сайта');
        $this->ensureSetting('oauth_vk_client_id', '', 'text', 'VK Client ID');
        $this->ensureSetting('oauth_vk_client_secret', '', 'text', 'VK Client Secret');
        $this->ensureSetting('oauth_ok_client_id', '', 'text', 'OK Client ID');
        $this->ensureSetting('oauth_ok_client_secret', '', 'text', 'OK Client Secret');
        $this->ensureSetting('oauth_yandex_client_id', '', 'text', 'Yandex Client ID');
        $this->ensureSetting('oauth_yandex_client_secret', '', 'text', 'Yandex Client Secret');
        $this->ensureSetting('oauth_mail_client_id', '', 'text', 'Mail.ru Client ID');
        $this->ensureSetting('oauth_mail_client_secret', '', 'text', 'Mail.ru Client Secret');
        $this->ensureSetting('oauth_vk_enabled', '0', 'boolean', 'VK вход включён');
        $this->ensureSetting('oauth_ok_enabled', '0', 'boolean', 'OK вход включён');
        $this->ensureSetting('oauth_yandex_enabled', '0', 'boolean', 'Yandex вход включён');
        $this->ensureSetting('oauth_mail_enabled', '0', 'boolean', 'Mail.ru вход включён');
        // Цвета
        $this->ensureSetting('color_primary', '#0056D2', 'text', 'Основной цвет');
        $this->ensureSetting('color_cta',     '#FF6B00', 'text', 'Цвет CTA-кнопок');
        $this->ensureSetting('color_text',    '#1A1A1A', 'text', 'Цвет основного текста');
        $this->ensureSetting('color_bg',      '#FFFFFF', 'text', 'Цвет фона');
        $this->ensureSetting('site_bg_mode', 'color', 'text', 'Тип фона сайта');
        $this->ensureSetting('site_bg_image', '', 'text', 'Фоновое изображение сайта');
        $this->ensureSetting('site_bg_opacity', '0', 'number', 'Прозрачность фонового изображения');

        // Миграция: cover_image для products
        $this->migrateProductCoverImage();
        // Миграция: cover_image для blog_posts
        if (!$this->tableExists('blog_posts')) { /* skip */ }
        else {
            try { $this->pdo->exec("ALTER TABLE blog_posts ADD COLUMN cover_image TEXT"); } catch (PDOException $e) {}
        }

        if (!$this->tableExists('product_images')) {
            $this->pdo->exec("CREATE TABLE product_images (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                product_id INTEGER NOT NULL,
                image_path TEXT NOT NULL,
                image_type TEXT DEFAULT 'screenshot',
                sort_order INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $this->pdo->exec("CREATE INDEX idx_product_images_product ON product_images(product_id)");
        }

        // Категории продуктов
        if (!$this->tableExists('product_categories')) {
            $this->pdo->exec("CREATE TABLE product_categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                sort_order INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
            $seed = array(
                array('Безопасность', 'bezopasnost', 0),
                array('Утилиты', 'utility', 1),
                array('Облако', 'oblako', 2),
                array('Бизнес', 'biznes', 3),
            );
            $st = $this->pdo->prepare("INSERT INTO product_categories (name, slug, sort_order) VALUES (?, ?, ?)");
            foreach ($seed as $s) $st->execute($s);
        }
        // Блоки главной — вкл/выкл
        $this->ensureSetting('block_hero_enabled',        '1', 'boolean', 'Блок Hero');
        $this->ensureSetting('block_trusted_enabled',     '1', 'boolean', 'Блок «Нам доверяют»');
        $this->ensureSetting('block_products_enabled',    '1', 'boolean', 'Блок «Продукты»');
        $this->ensureSetting('block_features_enabled',    '1', 'boolean', 'Блок «Почему мы»');
        $this->ensureSetting('block_solutions_enabled',   '1', 'boolean', 'Блок «Решения»');
        $this->ensureSetting('block_testimonials_enabled','1', 'boolean', 'Блок «Отзывы»');
        $this->ensureSetting('block_map_enabled',         '1', 'boolean', 'Блок «Карта офиса»');
        $this->ensureSetting('block_cta_enabled',         '1', 'boolean', 'Блок «CTA»');
        $this->ensureSetting('block_blog_enabled',        '1', 'boolean', 'Блок «Блог»');
        // Контент Hero
        $this->ensureSetting('hero_badge',    'Версия 12.4 с обновлённым AI-движком', 'text', 'Hero: бейдж');
        $this->ensureSetting('hero_title1',   'Профессиональное ПО',              'text', 'Hero: заголовок строка 1');
        $this->ensureSetting('hero_title2',   'для вашего бизнеса',               'text', 'Hero: заголовок строка 2');
        $this->ensureSetting('hero_subtitle', 'Российский разработчик антивирусов, утилит, облачных и корпоративных решений. Защищаем 2.5 млн пользователей в 40+ странах мира.', 'text', 'Hero: подзаголовок');
        $this->ensureSetting('hero_btn1',     'Смотреть продукты',                'text', 'Hero: кнопка 1');
        $this->ensureSetting('hero_btn2',     'Сравнить версии',                  'text', 'Hero: кнопка 2');
        $this->ensureSetting('hero_stat1',    '2.5M+',                            'text', 'Hero: статистика 1');
        $this->ensureSetting('hero_stat2',    '15 лет',                           'text', 'Hero: статистика 2');
        $this->ensureSetting('hero_reviews',  '2 847 отзывов',                    'text', 'Hero: кол-во отзывов');
        // Контент «Нам доверяют»
        $this->ensureSetting('trusted_title', 'Нам доверяют компании из разных отраслей', 'text', 'Доверие: заголовок');
        $this->ensureSetting('trusted_logos', 'Альфа-Банк, СберМаркет, Тинькофф, М.Видео, DNS, Ситилинк, ВкусВилл, Ozon', 'text', 'Доверие: логотипы (через запятую)');
        // Контент CTA
        $this->ensureSetting('cta_title',    'Попробуйте бесплатно 30 дней',   'text', 'CTA: заголовок');
        $this->ensureSetting('cta_subtitle', 'Полный функционал всех продуктов, без ограничений. Без привязки карты. Установка за 2 минуты.', 'text', 'CTA: подзаголовок');
        $this->ensureSetting('cta_btn1',     'Скачать бесплатно',               'text', 'CTA: кнопка 1');
        $this->ensureSetting('cta_btn2',     'Связаться с нами',                'text', 'CTA: кнопка 2');
        // Контент «Продукты»
        $this->ensureSetting('products_label',    'Наши продукты',                'text', 'Продукты: метка');
        $this->ensureSetting('products_title',    'Решения для любых задач',      'text', 'Продукты: заголовок');
        $this->ensureSetting('products_subtitle', 'От защиты домашнего ПК до комплексной безопасности корпоративной сети на тысячи устройств.', 'text', 'Продукты: подзаголовок');
        // Контент «Почему мы»
        $this->ensureSetting('features_label',    'Почему мы',                    'text', 'Преимущества: метка');
        $this->ensureSetting('features_title',    'Технологии, которым доверяют',  'text', 'Преимущества: заголовок');
        $this->ensureSetting('features_subtitle', 'Каждый продукт создан инженерами с опытом 15+ лет в индустрии и проходит многоступенчатое тестирование.', 'text', 'Преимущества: подзаголовок');
        // Контент «Решения»
        $this->ensureSetting('solutions_label',    'Отраслевые решения',          'text', 'Решения: метка');
        $this->ensureSetting('solutions_title',    'Подходим под вашу индустрию',  'text', 'Решения: заголовок');
        $this->ensureSetting('solutions_subtitle', 'Готовые отраслевые кейсы, учитывающие специфику бизнеса и требования регуляторов.', 'text', 'Решения: подзаголовок');
        // Контент «Отзывы»
        $this->ensureSetting('testimonials_label', 'Отзывы',                       'text', 'Отзывы: метка');
        $this->ensureSetting('testimonials_title', 'Нам доверяют тысячи',          'text', 'Отзывы: заголовок');
        // Контент «Карта офиса»
        $this->ensureSetting('map_label',    'Офис компании',                      'text', 'Карта: метка');
        $this->ensureSetting('map_title',    'Приезжайте в гости',                 'text', 'Карта: заголовок');
        $this->ensureSetting('map_subtitle', 'Загляните в наш офис в Санкт-Петербурге — будем рады обсудить сотрудничество лично', 'text', 'Карта: подзаголовок');
        // Контент «Блог»
        $this->ensureSetting('blog_label', 'Блог',                                 'text', 'Блог: метка');
        $this->ensureSetting('blog_title', 'Последние статьи',                     'text', 'Блог: заголовок');
    }

    private function migrateProductCoverImage() {
        if (!$this->tableExists('products')) return;
        try {
            $this->pdo->exec("ALTER TABLE products ADD COLUMN cover_image TEXT");
        } catch (PDOException $e) {}
    }

    private function migrateUsersMailProvider() {
        if (!$this->tableExists('users')) {
            return;
        }

        $stmt = $this->pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='users'");
        $sql = (string) $stmt->fetchColumn();
        if (stripos($sql, "'mail'") !== false) {
            return;
        }

        $this->pdo->exec('PRAGMA foreign_keys = OFF');
        $this->pdo->exec("CREATE TABLE users_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT,
            name TEXT NOT NULL,
            phone TEXT,
            company TEXT,
            email_verified INTEGER DEFAULT 0,
            verification_code TEXT,
            verification_code_expires TEXT,
            social_provider TEXT DEFAULT 'local' CHECK (social_provider IN ('local', 'vk', 'ok', 'yandex', 'mail')),
            social_id TEXT,
            remember_token TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
            last_login TEXT,
            is_active INTEGER DEFAULT 1
        )");
        $this->pdo->exec("INSERT INTO users_new (id,email,password_hash,name,phone,company,email_verified,verification_code,verification_code_expires,social_provider,social_id,remember_token,created_at,updated_at,last_login,is_active)
            SELECT id,email,password_hash,name,phone,company,email_verified,verification_code,verification_code_expires,social_provider,social_id,remember_token,created_at,updated_at,last_login,is_active FROM users");
        $this->pdo->exec('DROP TABLE users');
        $this->pdo->exec('ALTER TABLE users_new RENAME TO users');
        $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_verification_code ON users(verification_code)');
        $this->pdo->exec('CREATE INDEX IF NOT EXISTS idx_users_social ON users(social_provider, social_id)');
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    private function connectMysql(array $options) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options[PDO::ATTR_PERSISTENT] = false;
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    private function tableExists($tableName) {
        $stmt = $this->pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name = ?");
        $stmt->execute([$tableName]);
        return (bool) $stmt->fetch();
    }

    private function initializeSqliteSchema() {
        if (!file_exists(DB_SCHEMA_FILE)) {
            throw new PDOException('Не найден файл схемы SQLite: ' . DB_SCHEMA_FILE);
        }

        $schema = file_get_contents(DB_SCHEMA_FILE);
        $this->pdo->exec($schema);

        $this->seedDefaultAdmin();
        $this->seedDefaultSettings();
        $this->seedDefaultProducts();
    }

    private function seedDefaultAdmin() {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO admin_users (email, password_hash, name, role, is_active) VALUES (?, ?, ?, ?, 1)'
        );
        $stmt->execute([
            'admin@mastersoftware.ru',
            password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]),
            'Администратор',
            'super_admin'
        ]);
    }

    private function seedDefaultSettings() {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM site_settings')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $settings = [
            ['site_name', 'PROFESSIONAL SOFTWARE', 'text', 'Название сайта'],
            ['site_email', 'info@mastersoftware.ru', 'text', 'Email сайта'],
            ['site_phone', '+7 (812) 945-31-43', 'text', 'Телефон'],
            ['site_logo_path', 'images/Logo-Master-Software.ico', 'text', 'Логотип сайта'],
            ['site_favicon_path', 'images/Logo-Master-Software.ico', 'text', 'Фавикон сайта'],
            ['oauth_vk_client_id', '', 'text', 'VK Client ID'],
            ['oauth_vk_client_secret', '', 'text', 'VK Client Secret'],
            ['oauth_ok_client_id', '', 'text', 'OK Client ID'],
            ['oauth_ok_client_secret', '', 'text', 'OK Client Secret'],
            ['oauth_yandex_client_id', '', 'text', 'Yandex Client ID'],
            ['oauth_yandex_client_secret', '', 'text', 'Yandex Client Secret'],
            ['oauth_mail_client_id', '', 'text', 'Mail.ru Client ID'],
            ['oauth_mail_client_secret', '', 'text', 'Mail.ru Client Secret'],
            ['verification_code_expiry', '30', 'number', 'Время жизни кода подтверждения'],
            ['max_login_attempts', '5', 'number', 'Максимум попыток входа'],
            ['lockout_duration', '30', 'number', 'Длительность блокировки']
        ];

        $stmt = $this->pdo->prepare(
            'INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)'
        );
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }
    }

    public function ensureSetting($key, $value, $type = 'text', $description = '') {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM site_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        if ((int) $stmt->fetchColumn() === 0) {
            $insert = $this->pdo->prepare('INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, ?, ?)');
            $insert->execute([$key, $value, $type, $description]);
        }
    }

    private function seedDefaultProducts() {
        $count = (int) $this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        if ($count > 0) {
            return;
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO products (slug, name, tagline, category, price, description, os_support, rating, reviews_count, badge, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)'
        );
        $products = [
            ['nimbus-guard-pro', 'NimbusGuard Pro', 'Антивирус нового поколения с AI-движком', 'Безопасность', 2490, 'Комплексное решение для защиты рабочих станций и серверов.', '["Windows","macOS","Linux"]', 4.9, 2847, 'Хит'],
            ['nimbus-clean-utility', 'NimbusClean Utility', 'Оптимизация и очистка системы', 'Утилиты', 1290, 'Безопасная очистка системного мусора и оптимизация.', '["Windows","macOS"]', 4.7, 1623, null],
            ['nimbus-vault-cloud', 'NimbusVault Cloud', 'Защищённое облачное хранилище', 'Облако', 990, 'Корпоративное облако с end-to-end шифрованием.', '["Windows","macOS","Linux","Android","iOS"]', 4.8, 982, 'Новинка']
        ];
        foreach ($products as $product) {
            $stmt->execute($product);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function __clone() {}

    public function __wakeup() {
        throw new Exception('Cannot unserialize singleton');
    }
}

function db() {
    return Database::getInstance()->getConnection();
}
