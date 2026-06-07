CREATE TABLE IF NOT EXISTS users (
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
);

CREATE INDEX IF NOT EXISTS idx_users_verification_code ON users(verification_code);
CREATE INDEX IF NOT EXISTS idx_users_social ON users(social_provider, social_id);

CREATE TABLE IF NOT EXISTS admin_users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  name TEXT NOT NULL,
  role TEXT DEFAULT 'admin' CHECK (role IN ('super_admin', 'admin', 'editor', 'support')),
  two_factor_secret TEXT,
  two_factor_enabled INTEGER DEFAULT 0,
  last_login TEXT,
  last_ip TEXT,
  failed_login_attempts INTEGER DEFAULT 0,
  locked_until TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  is_active INTEGER DEFAULT 1
);

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL,
  tagline TEXT,
  category TEXT NOT NULL,
  price REAL NOT NULL,
  old_price REAL,
  description TEXT,
  features TEXT,
  os_support TEXT,
  requirements TEXT,
  rating REAL DEFAULT 5.0,
  reviews_count INTEGER DEFAULT 0,
  badge TEXT,
  cover_image TEXT,
  is_active INTEGER DEFAULT 1,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_category ON products(category);
CREATE INDEX IF NOT EXISTS idx_products_active ON products(is_active);

CREATE TABLE IF NOT EXISTS product_images (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  image_path TEXT NOT NULL,
  image_type TEXT DEFAULT 'screenshot',
  sort_order INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_product_images_product ON product_images(product_id);

CREATE TABLE IF NOT EXISTS product_categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  sort_order INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

INSERT OR IGNORE INTO product_categories (name, slug, sort_order) VALUES ('Безопасность', 'bezopasnost', 0);
INSERT OR IGNORE INTO product_categories (name, slug, sort_order) VALUES ('Утилиты', 'utility', 1);
INSERT OR IGNORE INTO product_categories (name, slug, sort_order) VALUES ('Облако', 'oblako', 2);
INSERT OR IGNORE INTO product_categories (name, slug, sort_order) VALUES ('Бизнес', 'biznes', 3);

CREATE TABLE IF NOT EXISTS product_versions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  product_id INTEGER NOT NULL,
  version TEXT NOT NULL,
  release_date TEXT NOT NULL,
  file_size TEXT,
  download_url TEXT,
  file_hash TEXT,
  is_current INTEGER DEFAULT 0,
  changelog TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS licenses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  license_key TEXT NOT NULL UNIQUE,
  product_id INTEGER NOT NULL,
  user_id INTEGER,
  status TEXT DEFAULT 'active' CHECK (status IN ('active', 'expired', 'blocked', 'trial')),
  seats INTEGER DEFAULT 1,
  activated_at TEXT,
  expires_at TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(product_id) REFERENCES products(id),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS idx_licenses_user ON licenses(user_id);
CREATE INDEX IF NOT EXISTS idx_licenses_status ON licenses(status);

CREATE TABLE IF NOT EXISTS orders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_number TEXT NOT NULL UNIQUE,
  user_id INTEGER,
  customer_name TEXT NOT NULL,
  customer_email TEXT NOT NULL,
  customer_company TEXT,
  total_amount REAL NOT NULL,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'paid', 'cancelled', 'refunded')),
  payment_method TEXT,
  payment_id TEXT,
  paid_at TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  order_id INTEGER NOT NULL,
  product_id INTEGER NOT NULL,
  quantity INTEGER DEFAULT 1,
  price REAL NOT NULL,
  license_id INTEGER,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY(product_id) REFERENCES products(id),
  FOREIGN KEY(license_id) REFERENCES licenses(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS blog_posts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  slug TEXT NOT NULL UNIQUE,
  title TEXT NOT NULL,
  excerpt TEXT,
  content TEXT,
  category TEXT,
  author_id INTEGER,
  author_name TEXT,
  cover_image TEXT,
  meta_title TEXT,
  meta_description TEXT,
  tags TEXT,
  is_published INTEGER DEFAULT 0,
  published_at TEXT,
  views_count INTEGER DEFAULT 0,
  cover_image TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS support_tickets (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  ticket_number TEXT NOT NULL UNIQUE,
  user_id INTEGER,
  subject TEXT NOT NULL,
  message TEXT NOT NULL,
  status TEXT DEFAULT 'open' CHECK (status IN ('open', 'in_progress', 'closed', 'pending')),
  priority TEXT DEFAULT 'medium' CHECK (priority IN ('low', 'medium', 'high', 'critical')),
  assigned_to INTEGER,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP,
  closed_at TEXT,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS contact_forms (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  company TEXT,
  phone TEXT,
  subject TEXT NOT NULL,
  message TEXT NOT NULL,
  is_processed INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS callback_requests (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  phone TEXT NOT NULL,
  is_called INTEGER DEFAULT 0,
  called_at TEXT,
  notes TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS email_logs (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  recipient TEXT NOT NULL,
  subject TEXT NOT NULL,
  template TEXT,
  status TEXT DEFAULT 'pending' CHECK (status IN ('sent', 'failed', 'pending')),
  error_message TEXT,
  sent_at TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_activity_log (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  admin_id INTEGER NOT NULL,
  action TEXT NOT NULL,
  table_name TEXT,
  record_id INTEGER,
  old_values TEXT,
  new_values TEXT,
  ip_address TEXT,
  user_agent TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS visitors (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  visitor_id TEXT NOT NULL,
  session_id TEXT NOT NULL UNIQUE,
  ip_address TEXT,
  user_agent TEXT,
  country TEXT,
  country_code TEXT,
  region TEXT,
  city TEXT,
  isp TEXT,
  browser TEXT,
  os TEXT,
  device_type TEXT,
  referer TEXT,
  source TEXT,
  first_url TEXT,
  pages_count INTEGER DEFAULT 1,
  duration_seconds INTEGER DEFAULT 0,
  created_ts INTEGER DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  last_activity TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_visitors_visitor_id ON visitors(visitor_id);
CREATE INDEX IF NOT EXISTS idx_visitors_created ON visitors(created_at);
CREATE INDEX IF NOT EXISTS idx_visitors_country ON visitors(country);

CREATE TABLE IF NOT EXISTS visitor_pageviews (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  visitor_id INTEGER NOT NULL,
  url TEXT,
  page_title TEXT,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY(visitor_id) REFERENCES visitors(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_pageviews_visitor ON visitor_pageviews(visitor_id);
CREATE INDEX IF NOT EXISTS idx_pageviews_created ON visitor_pageviews(created_at);

CREATE TABLE IF NOT EXISTS site_settings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  setting_key TEXT NOT NULL UNIQUE,
  setting_value TEXT,
  setting_type TEXT DEFAULT 'text' CHECK (setting_type IN ('text', 'number', 'boolean', 'json')),
  description TEXT,
  updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
