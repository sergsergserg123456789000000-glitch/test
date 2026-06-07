export type Product = {
  id: string;
  slug: string;
  name: string;
  tagline: string;
  category: "Безопасность" | "Утилиты" | "Облако" | "Бизнес";
  price: number;
  oldPrice?: number;
  rating: number;
  reviews: number;
  badge?: "Хит" | "Новинка" | "Скидка";
  description: string;
  features: string[];
  os: ("Windows" | "macOS" | "Linux" | "Android" | "iOS")[];
  requirements: { os: string; cpu: string; ram: string; disk: string };
  versions: { version: string; date: string; size: string; isCurrent?: boolean }[];
  changelog: { version: string; date: string; changes: string[] }[];
  screenshots: { title: string; color: string }[];
};

export const products: Product[] = [
  {
    id: "1",
    slug: "nimbus-guard-pro",
    name: "NimbusGuard Pro",
    tagline: "Антивирус нового поколения с AI-движком",
    category: "Безопасность",
    price: 2490,
    oldPrice: 3490,
    rating: 4.9,
    reviews: 2847,
    badge: "Хит",
    description:
      "NimbusGuard Pro — комплексное решение для защиты рабочих станций и серверов. Использует нейросетевой движок для обнаружения угроз нулевого дня, поведенческий анализ и защиту от программ-вымогателей.",
    features: [
      "Защита от вирусов, троянов, шпионского ПО и программ-вымогателей",
      "Веб-антифишинг и блокировка вредоносных сайтов",
      "AI-детектор угроз нулевого дня с точностью 99.8%",
      "Брандмауэр нового поколения и IDS/IPS",
      "Защита платежей и конфиденциальных данных",
      "Централизованная консоль управления для IT-отдела",
    ],
    os: ["Windows", "macOS", "Linux"],
    requirements: { os: "Windows 10/11, macOS 12+, Ubuntu 20.04+", cpu: "Intel Core i3 / AMD Ryzen 3", ram: "4 ГБ", disk: "2 ГБ" },
    versions: [
      { version: "12.4.1", date: "12 мая 2026", size: "184 МБ", isCurrent: true },
      { version: "12.3.0", date: "18 апреля 2026", size: "181 МБ" },
      { version: "12.2.0", date: "27 марта 2026", size: "179 МБ" },
      { version: "11.8.5", date: "12 января 2026", size: "165 МБ" },
    ],
    changelog: [
      { version: "12.4.1", date: "12 мая 2026", changes: ["Улучшен AI-движок: +12% к детекции фишинга", "Исправлен конфликт с VPN-клиентами", "Оптимизировано потребление RAM на 18%"] },
      { version: "12.3.0", date: "18 апреля 2026", changes: ["Добавлена защита от новых ransomware-семейств", "Новая тема интерфейса", "Поддержка Windows 11 24H2"] },
    ],
    screenshots: [
      { title: "Главное окно", color: "from-blue-500 to-blue-700" },
      { title: "Сканирование системы", color: "from-cyan-500 to-blue-600" },
      { title: "Веб-защита", color: "from-indigo-500 to-blue-700" },
      { title: "Отчёты", color: "from-sky-500 to-blue-600" },
    ],
  },
  {
    id: "2",
    slug: "nimbus-clean-utility",
    name: "NimbusClean Utility",
    tagline: "Оптимизация и очистка системы",
    category: "Утилиты",
    price: 1290,
    rating: 4.7,
    reviews: 1623,
    description:
      "Безопасная очистка системного мусора, управление автозагрузкой, удаление вредоносных расширений браузера и тонкая настройка конфиденциальности Windows.",
    features: [
      "Удаление временных файлов и кэша всех браузеров",
      "Управление программами автозагрузки с рекомендациями",
      "Деинсталлятор с зачисткой остаточных файлов и записей реестра",
      "Настройка приватности Windows (телеметрия, реклама)",
      "Поиск дубликатов файлов с визуализацией",
      "Планировщик автоматического обслуживания",
    ],
    os: ["Windows", "macOS"],
    requirements: { os: "Windows 10/11, macOS 11+", cpu: "Intel Core i3 / Apple M1", ram: "2 ГБ", disk: "500 МБ" },
    versions: [
      { version: "5.2.0", date: "02 мая 2026", size: "62 МБ", isCurrent: true },
      { version: "5.1.4", date: "14 марта 2026", size: "60 МБ" },
      { version: "5.0.0", date: "08 января 2026", size: "58 МБ" },
    ],
    changelog: [
      { version: "5.2.0", date: "02 мая 2026", changes: ["Добавлен модуль очистки мессенджеров", "Улучшен алгоритм поиска дубликатов", "Полная поддержка Apple Silicon"] },
    ],
    screenshots: [
      { title: "Сводка", color: "from-emerald-500 to-blue-600" },
      { title: "Очистка", color: "from-teal-500 to-cyan-600" },
      { title: "Автозагрузка", color: "from-sky-500 to-blue-600" },
    ],
  },
  {
    id: "3",
    slug: "nimbus-vault-cloud",
    name: "NimbusVault Cloud",
    tagline: "Защищённое облачное хранилище для команд",
    category: "Облако",
    price: 990,
    rating: 4.8,
    reviews: 982,
    badge: "Новинка",
    description:
      "Корпоративное облако с end-to-end шифрованием, разграничением прав доступа, аудитом действий и интеграцией с Active Directory / LDAP.",
    features: [
      "End-to-end шифрование данных по стандарту AES-256",
      "Совместная работа с документами в реальном времени",
      "SSO через Active Directory, Google Workspace, Okta",
      "Полный аудит-лог всех действий с экспортом",
      "Синхронизация между устройствами без лимитов",
      "Хранение в дата-центрах на территории РФ",
    ],
    os: ["Windows", "macOS", "Linux", "Android", "iOS"],
    requirements: { os: "Любая ОС с доступом в интернет", cpu: "—", ram: "—", disk: "—" },
    versions: [
      { version: "2.0.1", date: "20 мая 2026", size: "Web / Mobile", isCurrent: true },
      { version: "2.0.0", date: "01 апреля 2026", size: "Web / Mobile" },
    ],
    changelog: [
      { version: "2.0.1", date: "20 мая 2026", changes: ["Улучшена производительность синхронизации в 2 раза", "Новый веб-интерфейс", "Исправлены ошибки доступа в Safari"] },
    ],
    screenshots: [
      { title: "Файловый менеджер", color: "from-blue-600 to-indigo-700" },
      { title: "Команды", color: "from-violet-500 to-blue-700" },
      { title: "Аудит", color: "from-blue-500 to-cyan-600" },
    ],
  },
  {
    id: "4",
    slug: "nimbus-backup-enterprise",
    name: "NimbusBackup Enterprise",
    tagline: "Резервное копирование для бизнеса",
    category: "Бизнес",
    price: 4990,
    rating: 4.6,
    reviews: 412,
    description:
      "Корпоративное решение для бэкапа серверов, баз данных, виртуальных машин и рабочих станций. Инкрементальные копии, дедупликация и мгновенное восстановление.",
    features: [
      "Бэкап серверов Windows Server, Linux, FreeBSD",
      "Поддержка СУБД: PostgreSQL, MySQL, MS SQL, 1C",
      "Дедупликация и компрессия данных — экономия до 70%",
      "Мгновенное восстановление виртуальных машин",
      "Шифрование и хранение в 3-х географически распределённых ЦОД",
      "REST API для интеграции с вашими системами",
    ],
    os: ["Windows", "Linux"],
    requirements: { os: "Windows Server 2016+, Ubuntu 20.04+", cpu: "Intel Xeon / AMD EPYC", ram: "8 ГБ", disk: "10 ГБ + место под бэкапы" },
    versions: [
      { version: "8.1.2", date: "08 мая 2026", size: "320 МБ", isCurrent: true },
      { version: "8.0.0", date: "12 февраля 2026", size: "310 МБ" },
    ],
    changelog: [
      { version: "8.1.2", date: "08 мая 2026", changes: ["Поддержка PostgreSQL 16", "Улучшена дедупликация: -25% места", "Новый веб-интерфейс администратора"] },
    ],
    screenshots: [
      { title: "Дашборд", color: "from-blue-700 to-indigo-800" },
      { title: "Политики", color: "from-cyan-600 to-blue-700" },
      { title: "Восстановление", color: "from-sky-600 to-blue-800" },
    ],
  },
  {
    id: "5",
    slug: "nimbus-mail-server",
    name: "NimbusMail Server",
    tagline: "Корпоративная почтовая система",
    category: "Бизнес",
    price: 3490,
    rating: 4.5,
    reviews: 218,
    description:
      "Защищённый почтовый сервер с антиспамом, календарями, контактами и интеграцией с мобильными устройствами по протоколам Exchange ActiveSync / IMAP.",
    features: [
      "Собственный почтовый сервер на вашем оборудовании",
      "Антиспам с точностью 99.5% (самообучающиеся модели)",
      "Push-синхронизация почты, календарей, контактов",
      "Шифрование TLS 1.3 для всего трафика",
      "Веб-клиент с поддержкой совместной работы",
      "Миграция с MS Exchange и других решений «под ключ»",
    ],
    os: ["Linux", "Windows"],
    requirements: { os: "Ubuntu 22.04 / Debian 12 / Windows Server 2019+", cpu: "Intel Xeon / 4+ ядра", ram: "16 ГБ", disk: "100 ГБ SSD" },
    versions: [
      { version: "4.5.0", date: "01 мая 2026", size: "480 МБ", isCurrent: true },
    ],
    changelog: [
      { version: "4.5.0", date: "01 мая 2026", changes: ["Улучшен антиспам: -40% ложных срабатываний", "Добавлен модуль DLP", "Новый веб-клиент на React"] },
    ],
    screenshots: [
      { title: "Веб-клиент", color: "from-blue-500 to-blue-700" },
      { title: "Календарь", color: "from-indigo-500 to-blue-700" },
    ],
  },
  {
    id: "6",
    slug: "nimbus-vpn-business",
    name: "NimbusVPN Business",
    tagline: "Корпоративный VPN с централизованным управлением",
    category: "Безопасность",
    price: 1990,
    rating: 4.7,
    reviews: 654,
    badge: "Скидка",
    description:
      "Безопасный доступ сотрудников к корпоративным ресурсам из любой точки мира. WireGuard®-протокол, kill-switch, split-tunneling и admin-панель.",
    features: [
      "Современный протокол WireGuard® — максимальная скорость",
      "Централизованное управление пользователями и политиками",
      "Kill-switch и защита от утечек DNS/IPv6",
      "Split-tunneling для оптимизации трафика",
      "2FA для подключения к корпоративной сети",
      "Серверы в 40+ странах, включая РФ и СНГ",
    ],
    os: ["Windows", "macOS", "Linux", "Android", "iOS"],
    requirements: { os: "Любая современная ОС", cpu: "—", ram: "—", disk: "—" },
    versions: [
      { version: "3.2.1", date: "15 мая 2026", size: "45 МБ", isCurrent: true },
    ],
    changelog: [
      { version: "3.2.1", date: "15 мая 2026", changes: ["Добавлены серверы в Казахстане и Узбекистане", "Улучшена стабильность на мобильных сетях"] },
    ],
    screenshots: [
      { title: "Подключение", color: "from-blue-600 to-cyan-700" },
      { title: "Карта серверов", color: "from-sky-500 to-blue-700" },
    ],
  },
];

export type BlogPost = {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  category: string;
  date: string;
  author: string;
  readTime: number;
  cover: string;
  content: { type: "h2" | "p" | "quote" | "list"; text?: string; items?: string[] }[];
};

export const blogPosts: BlogPost[] = [
  {
    id: "1",
    slug: "ai-antivirus-future-2026",
    title: "AI в антивирусах: почему сигнатурный подход устарел",
    excerpt: "Разбираемся, как нейросети изменили индустрию кибербезопасности и почему классические базы вирусов больше не работают против современных угроз.",
    category: "Безопасность",
    date: "18 мая 2026",
    author: "Алексей Петров",
    readTime: 8,
    cover: "from-blue-500 to-indigo-700",
    content: [
      { type: "p", text: "Ещё 5 лет назад антивирусная индустрия строилась вокруг сигнатур — уникальных цифровых отпечатков известных вредоносных программ. Сегодня этот подход катастрофически отстаёт от реальности." },
      { type: "h2", text: "Что не так с сигнатурами" },
      { type: "p", text: "По данным лаборатории NimbusSoft, ежедневно появляется более 450 000 новых образцов вредоносного ПО. Даже при автоматизированной обработке классические базы попросту не успевают обновляться." },
      { type: "quote", text: "К 2026 году AI-движки обнаруживают 99.8% ранее неизвестных угроз. Сигнатурный подход покрывает лишь 47%." },
      { type: "h2", text: "Как работает AI-детектор" },
      { type: "list", items: [
        "Поведенческий анализ запущенных процессов в реальном времени",
        "Статический анализ кода без его исполнения (песочница наоборот)",
        "Графовые нейросети для анализа связей между файлами",
        "Обучение на телеметрии 250+ млн защищённых устройств",
      ] },
      { type: "p", text: "В NimbusGuard Pro 12 мы применили все четыре подхода одновременно, что позволило поднять детекцию на 18% по сравнению с предыдущим поколением." },
    ],
  },
  {
    id: "2",
    slug: "remote-work-security",
    title: "7 правил безопасности для удалённых сотрудников",
    excerpt: "Простые, но критически важные правила, которые должен знать каждый сотрудник, работающий из дома или в кафе.",
    category: "Безопасность",
    date: "12 мая 2026",
    author: "Мария Соколова",
    readTime: 6,
    cover: "from-cyan-500 to-blue-700",
    content: [
      { type: "p", text: "Удалёнка никуда не уйдёт — и это хорошо. Но она же открывает новые вектора атак для злоумышленников." },
      { type: "h2", text: "Главные угрозы" },
      { type: "list", items: [
        "Открытые Wi-Fi сети в кафе и коворкингах",
        "Использование личных устройств для рабочих задач (BYOD)",
        "Фишинг через мессенджеры",
        "Утечка данных через скриншоты",
      ] },
    ],
  },
  {
    id: "3",
    slug: "company-news-q2-2026",
    title: "NimbusSoft открывает R&D-центр в Иннополисе",
    excerpt: "Расширяем присутствие в России: новый центр разработки на 200+ специалистов, фокус на AI и облачных технологиях.",
    category: "Компания",
    date: "05 мая 2026",
    author: "Пресс-служба",
    readTime: 4,
    cover: "from-blue-600 to-cyan-700",
    content: [
      { type: "p", text: "Сегодня мы официально открываем двери нового R&D-центра в Иннополисе. Это позволит нам ускорить разработку AI-движков и облачных продуктов." },
      { type: "h2", text: "Что это значит для клиентов" },
      { type: "list", items: [
        "Более частые релизы с улучшениями AI",
        "Локальная поддержка на русском языке 24/7",
        "Соответствие требованиям регуляторов РФ",
      ] },
    ],
  },
  {
    id: "4",
    slug: "cloud-storage-encryption",
    title: "End-to-end шифрование: что это и зачем нужно",
    excerpt: "Простым языком о сложной криптографии: как работает E2EE, чем оно отличается от обычного шифрования, и почему это важно для бизнеса.",
    category: "Облако",
    date: "28 апреля 2026",
    author: "Дмитрий Иванов",
    readTime: 10,
    cover: "from-indigo-500 to-blue-700",
    content: [
      { type: "p", text: "End-to-end шифрование (E2EE) — единственный способ гарантировать, что ваши данные не прочитает никто, кроме вас и тех, кому вы их отправили." },
    ],
  },
  {
    id: "5",
    slug: "performance-optimization-tips",
    title: "Как ускорить старый компьютер: 10 проверенных способов",
    excerpt: "Не спешите покупать новый ПК — иногда достаточно правильно настроить систему. Делимся лайфхаками от наших инженеров.",
    category: "Утилиты",
    date: "20 апреля 2026",
    author: "Сергей Кузнецов",
    readTime: 7,
    cover: "from-sky-500 to-blue-600",
    content: [
      { type: "p", text: "Старый компьютер можно заставить работать быстрее. Вот 10 способов, которые реально работают." },
    ],
  },
  {
    id: "6",
    slug: "release-notes-12-4",
    title: "NimbusGuard Pro 12.4 — что нового",
    excerpt: "Большое обновление нашего флагманского антивируса: улучшенный AI, новый интерфейс и поддержка Windows 11 24H2.",
    category: "Релизы",
    date: "12 мая 2026",
    author: "Команда разработки",
    readTime: 5,
    cover: "from-blue-700 to-indigo-800",
    content: [
      { type: "p", text: "NimbusGuard Pro 12.4 — крупное обновление, которое мы готовили почти полгода." },
    ],
  },
];

export const faqItems = [
  { q: "Как купить лицензию?", a: "Выберите продукт, нажмите «Купить», заполните данные и оплатите удобным способом. Ключ придёт на e-mail мгновенно после оплаты." },
  { q: "Можно ли перенести лицензию на другой ПК?", a: "Да, деактивируйте лицензию на старом устройстве в личном кабинете и активируйте на новом — количество переносов не ограничено." },
  { q: "Есть ли пробный период?", a: "Да, для всех продуктов доступен бесплатный trial на 30 дней. Полный функционал, без ограничений." },
  { q: "Какие способы оплаты вы принимаете?", a: "Банковские карты, СБП, ЮMoney, оплата по счёту для юридических лиц, а также криптовалюта для зарубежных клиентов." },
  { q: "Что делать, если забыл ключ активации?", a: "Зайдите в личный кабинет — все ключи хранятся там. Если нет доступа — напишите в поддержку, восстановим по e-mail." },
  { q: "Работает ли NimbusGuard на Linux?", a: "Да, поддерживаются Ubuntu 20.04+, Debian 11+, Fedora 36+, CentOS Stream 9. Интерфейс идентичен Windows-версии." },
  { q: "Возможна ли скидка для образовательных учреждений?", a: "Да, для школ, ВУЗов и некоммерческих организаций мы предоставляем скидку до 70%. Запросите коммерческое предложение." },
  { q: "Как стать партнёром?", a: "Заполните форму в разделе «О компании» → «Стать партнёром». Мы свяжемся с вами в течение 2 рабочих дней." },
];

export const solutions = [
  { industry: "Финансы и банки", title: "Защита финансовых данных", description: "Соответствие требованиям ЦБ РФ, ФСТЭК и международным стандартам PCI DSS.", icon: "Banknote" },
  { industry: "Медицина", title: "Защита персональных данных пациентов", description: "HIPAA-совместимые решения, шифрование медицинских записей и контроль доступа.", icon: "Heart" },
  { industry: "Государственный сектор", title: "Импортозамещение ПО", description: "Реестр отечественного ПО, сертификация ФСТЭК, поддержка на территории РФ.", icon: "Building" },
  { industry: "Образование", title: "Лицензии для школ и ВУЗов", description: "Специальные условия для образовательных учреждений, централизованное управление.", icon: "GraduationCap" },
  { industry: "Розничная торговля", title: "Безопасность кассовых систем", description: "Защита POS-терминалов, соответствие 54-ФЗ, защита платежных данных.", icon: "ShoppingCart" },
  { industry: "Промышленность", title: "Защита АСУ ТП и SCADA", description: "Изолированные сегменты сети, мониторинг OT-инфраструктуры, реагирование на инциденты.", icon: "Factory" },
];

export const userLicenses = [
  { product: "NimbusGuard Pro", key: "NBS-7A2D-9KFH-3XM8-PQR4", status: "active", expires: "12 мая 2027", seats: "5 устройств" },
  { product: "NimbusClean Utility", key: "NBC-2X9K-M4P7-L8D1-WVJ3", status: "active", expires: "08 марта 2027", seats: "1 устройство" },
  { product: "NimbusVault Cloud", key: "NBV-5T6Y-8H2N-4B1V-CX9Z", status: "active", expires: "30 июня 2026", seats: "1 ТБ" },
  { product: "NimbusGuard Pro (legacy)", key: "NBS-1Q3W-5E7R-9T2Y-4U6I", status: "expired", expires: "15 января 2026", seats: "3 устройства" },
];

export const stats = {
  totalSales: { today: 184720, month: 5_842_100, growth: 12.4 },
  downloads: { today: 1_847, month: 58_421, growth: 8.2 },
  newUsers: { today: 67, month: 1_842, growth: -2.1 },
  activeSubscriptions: 24_847,
};

export const salesChart = [
  { day: "Пн", value: 142 },
  { day: "Вт", value: 189 },
  { day: "Ср", value: 234 },
  { day: "Чт", value: 198 },
  { day: "Пт", value: 287 },
  { day: "Сб", value: 156 },
  { day: "Вс", value: 98 },
];

export const recentOrders = [
  { id: "#NS-78421", customer: "ООО «АльфаТех»", product: "NimbusGuard Pro × 25", amount: 62_250, status: "paid" },
  { id: "#NS-78420", customer: "Иван Петров", product: "NimbusClean Utility × 1", amount: 1_290, status: "paid" },
  { id: "#NS-78419", customer: "ПАО «Северные сети»", product: "NimbusBackup Enterprise × 1", amount: 124_900, status: "pending" },
  { id: "#NS-78418", customer: "Мария Соколова", product: "NimbusVault Cloud × 1", amount: 990, status: "paid" },
  { id: "#NS-78417", customer: "ГБОУ «Школа №1254»", product: "NimbusGuard Pro × 100", amount: 99_600, status: "paid" },
];

export const supportTickets = [
  { id: "#T-1284", user: "Алексей Морозов", subject: "Не активируется ключ", status: "open", priority: "high" },
  { id: "#T-1283", user: "Елена Никитина", subject: "Ошибка при обновлении", status: "in_progress", priority: "medium" },
  { id: "#T-1282", user: "Дмитрий Соколов", subject: "Возврат средств", status: "open", priority: "low" },
  { id: "#T-1281", user: "ООО «ТехноСтрой»", subject: "Настройка SSO", status: "closed", priority: "medium" },
  { id: "#T-1280", user: "Игорь Васильев", subject: "Вопрос по лицензии", status: "closed", priority: "low" },
];
