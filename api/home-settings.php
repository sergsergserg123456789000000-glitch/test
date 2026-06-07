<?php
/**
 * API: настройки блоков главной страницы и цветовой схемы
 * Используется фронтендом для динамического управления контентом
 */
require_once __DIR__ . '/../config.php';
require_once INCLUDES_DIR . '/db.php';
require_once INCLUDES_DIR . '/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache');

echo json_encode(array(
    'colors' => array(
        'primary'    => getSetting('color_primary',    '#0056D2'),
        'cta'        => getSetting('color_cta',        '#FF6B00'),
        'text'       => getSetting('color_text',       '#1A1A1A'),
        'bg'         => getSetting('color_bg',         '#FFFFFF'),
        'bg_mode'    => getSetting('site_bg_mode',    'color'),
        'bg_image'   => getSetting('site_bg_image',    ''),
        'bg_opacity' => (int)getSetting('site_bg_opacity', '0'),
    ),
    'blocks' => array(
        'hero'        => getSetting('block_hero_enabled',       '1') === '1',
        'trusted'     => getSetting('block_trusted_enabled',    '1') === '1',
        'products'    => getSetting('block_products_enabled',   '1') === '1',
        'features'    => getSetting('block_features_enabled',   '1') === '1',
        'solutions'   => getSetting('block_solutions_enabled',  '1') === '1',
        'testimonials'=> getSetting('block_testimonials_enabled','1') === '1',
        'map'         => getSetting('block_map_enabled',        '1') === '1',
        'cta'         => getSetting('block_cta_enabled',        '1') === '1',
        'blog'        => getSetting('block_blog_enabled',       '1') === '1',
    ),
    'hero' => array(
        'badge'    => getSetting('hero_badge',    'Версия 12.4 с обновлённым AI-движком'),
        'title1'   => getSetting('hero_title1',  'Профессиональное ПО'),
        'title2'   => getSetting('hero_title2',  'для вашего бизнеса'),
        'subtitle' => getSetting('hero_subtitle', 'Российский разработчик антивирусов, утилит, облачных и корпоративных решений. Защищаем 2.5 млн пользователей в 40+ странах мира.'),
        'btn1'     => getSetting('hero_btn1',    'Смотреть продукты'),
        'btn2'     => getSetting('hero_btn2',    'Сравнить версии'),
        'stat1'    => getSetting('hero_stat1',   '2.5M+'),
        'stat2'    => getSetting('hero_stat2',   '15 лет'),
        'reviews'  => getSetting('hero_reviews', '2 847 отзывов'),
    ),
    'trusted' => array(
        'title'    => getSetting('trusted_title', 'Нам доверяют компании из разных отраслей'),
        'logos'    => getSetting('trusted_logos', 'Альфа-Банк, СберМаркет, Тинькофф, М.Видео, DNS, Ситилинк, ВкусВилл, Ozon'),
    ),
    'cta' => array(
        'title'    => getSetting('cta_title',    'Попробуйте бесплатно 30 дней'),
        'subtitle' => getSetting('cta_subtitle', 'Полный функционал всех продуктов, без ограничений. Без привязки карты. Установка за 2 минуты.'),
        'btn1'     => getSetting('cta_btn1',     'Скачать бесплатно'),
        'btn2'     => getSetting('cta_btn2',     'Связаться с нами'),
    ),
    'products' => array(
        'label'    => getSetting('products_label',    'Наши продукты'),
        'title'    => getSetting('products_title',    'Решения для любых задач'),
        'subtitle' => getSetting('products_subtitle', 'От защиты домашнего ПК до комплексной безопасности корпоративной сети на тысячи устройств.'),
    ),
    'features' => array(
        'label'    => getSetting('features_label',    'Почему мы'),
        'title'    => getSetting('features_title',    'Технологии, которым доверяют'),
        'subtitle' => getSetting('features_subtitle', 'Каждый продукт создан инженерами с опытом 15+ лет в индустрии и проходит многоступенчатое тестирование.'),
    ),
    'solutions' => array(
        'label'    => getSetting('solutions_label',    'Отраслевые решения'),
        'title'    => getSetting('solutions_title',    'Подходим под вашу индустрию'),
        'subtitle' => getSetting('solutions_subtitle', 'Готовые отраслевые кейсы, учитывающие специфику бизнеса и требования регуляторов.'),
    ),
    'testimonials' => array(
        'label'    => getSetting('testimonials_label', 'Отзывы'),
        'title'    => getSetting('testimonials_title', 'Нам доверяют тысячи'),
    ),
    'map' => array(
        'label'    => getSetting('map_label',    'Офис компании'),
        'title'    => getSetting('map_title',    'Приезжайте в гости'),
        'subtitle' => getSetting('map_subtitle', 'Загляните в наш офис в Санкт-Петербурге — будем рады обсудить сотрудничество лично'),
    ),
    'blog' => array(
        'label'    => getSetting('blog_label', 'Блог'),
        'title'    => getSetting('blog_title', 'Последние статьи'),
    ),
), JSON_UNESCAPED_UNICODE);
