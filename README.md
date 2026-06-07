# PROFESSIONAL SOFTWARE — Корпоративный сайт

## 📋 Описание

Полноценный корпоративный сайт с серверным хранением данных на PHP + MySQL.

## 🚀 Установка

### 1. Требования
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache с mod_rewrite

### 2. База данных

```bash
# Создать базу данных
mysql -u root -p -e "CREATE DATABASE professional_software CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Импортировать структуру
mysql -u root -p professional_software < database.sql
```

### 3. Настройка

Отредактируйте `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'professional_software');
define('DB_USER', 'ваш_пользователь');
define('DB_PASS', 'ваш_пароль');

// SMTP для отправки email
define('SMTP_HOST', 'smtp.yandex.ru');
define('SMTP_USER', 'info@mastersoftware.ru');
define('SMTP_PASS', 'ваш_пароль');

// OAuth (получить в разработчиках соцсетей)
define('VK_CLIENT_ID', '...');
define('VK_CLIENT_SECRET', '...');
define('OK_CLIENT_ID', '...');
define('OK_CLIENT_SECRET', '...');
define('YANDEX_CLIENT_ID', '...');
define('YANDEX_CLIENT_SECRET', '...');
```

### 4. Права доступа

```bash
chmod 755 uploads/
chmod 644 config.php
chown -R www-data:www-data /path/to/site
```

### 5. Администратор по умолчанию

```
Email: admin@mastersoftware.ru
Пароль: admin123
```

**Сразу смените пароль после первого входа!**

## 📁 Структура

```
/
├── admin/              # Админ-панель
│   ├── login.php       # Вход с 2FA
│   ├── index.php       # Дашборд
│   └── templates/      # Шаблоны админки
├── auth/               # OAuth авторизация
│   ├── vk/             # ВКонтакте
│   ├── ok/             # Одноклассники
│   └── yandex/         # Яндекс
├── includes/           # Библиотеки
│   ├── db.php          # Подключение к БД
│   └── functions.php   # Функции
├── templates/          # Email шаблоны
├── uploads/            # Загруженные файлы
├── config.php          # Конфигурация
├── database.sql        # Дамп БД
├── register.php        # Регистрация (double opt-in)
├── login.php           # Вход пользователя
└── .htaccess           # Защита и редиректы
```

## 🔐 Безопасность

### .htaccess защита
- `admin/.htaccess` — доступ только к login.php и index.php
- `includes/.htaccess` — полный запрет доступа
- `uploads/.htaccess` — запрет выполнения PHP
- Корневой `.htaccess` — защита конфигов, логов, .git

### Функции безопасности
- CSRF токены для всех форм
- Prepared statements (защита от SQL injection)
- Хэширование паролей (bcrypt, cost=12)
- 2FA для администраторов
- Блокировка после 5 неудачных попыток входа
- Логирование всех действий администраторов
- Rate limiting (через mod_evasive)

## 📧 Регистрация пользователей

### Double Opt-In
1. Пользователь заполняет форму
2. Отправка 6-значного кода на email
3. Ввод кода на сайте (не ссылка!)
4. Активация аккаунта

### Соцсети (OAuth)
- ВКонтакте — email автоматически верифицирован
- Одноклассники — требуется подтверждение email
- Яндекс — email автоматически верифицирован

## 🎯 API Endpoints (для фронтенда)

```
POST /api/register.php      # Регистрация
POST /api/verify-code.php   # Подтверждение кода
POST /api/login.php         # Вход
POST /api/logout.php        # Выход
GET  /api/user.php          # Данные пользователя
POST /api/order.php         # Создание заказа
GET  /api/products.php      # Список продуктов
GET  /api/blog.php          # Статьи блога
POST /api/contact.php       # Форма контактов
POST /api/callback.php      # Обратный звонок
```

## 📊 Админ-панель

### Разделы
- **Дашборд** — статистика, продажи, тикеты
- **Продукты** — CRUD, версии, загрузка файлов
- **Заказы** — управление, статусы, оплаты
- **Лицензии** — генерация ключей, blacklist
- **Блог** — статьи, категории, SEO
- **Тикеты** — поддержка, приоритеты
- **Пользователи** — управление, роли
- **Настройки** — сайт, email, OAuth

## 🛠 Технологии

- **Backend:** PHP 8.0+
- **Database:** MySQL 5.7+
- **Frontend:** React + Vite + Tailwind CSS
- **Email:** PHPMailer + SMTP
- **OAuth:** VK, OK, Yandex API
- **Security:** CSRF, 2FA, bcrypt, prepared statements

## 📞 Контакты

**PROFESSIONAL SOFTWARE**
- Адрес: 198334, г. Санкт-Петербург, пр. Ветеранов, д. 140, офис 1
- Телефон: +7 (812) 945-31-43
- Email: info@mastersoftware.ru
- Сайт: mastersoftware.ru
