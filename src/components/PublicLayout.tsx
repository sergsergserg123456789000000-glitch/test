import { Outlet, Link, useLocation, NavLink } from "react-router-dom";
import { useState, useEffect } from "react";
import { Menu, X, Search, User, ChevronDown, Mail, Phone, MapPin } from "lucide-react";
import { cn } from "../utils/cn";
import { serverPath } from "../utils/paths";
import ScrollTopButton from "./ScrollToTop";
import { useSiteColors } from "../utils/useColors";

const navItems = [
  { label: "Продукты", path: "/products" },
  { label: "Решения", path: "/solutions" },
  { label: "Цены", path: "/pricing" },
  { label: "Блог", path: "/blog" },
  { label: "Поддержка", path: "/support" },
  { label: "Компания", path: "/about" },
];

export default function PublicLayout() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileOpen, setMobileOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [productsOpen, setProductsOpen] = useState(false);
  const location = useLocation();
  useSiteColors();

  useEffect(() => {
    setMobileOpen(false);
    setSearchOpen(false);
    setProductsOpen(false);
  }, [location.pathname]);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 10);
    onScroll();
    window.addEventListener("scroll", onScroll);
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <div className="site-shell min-h-screen text-slate-900">
      {/* HEADER */}
      <header
        className={cn(
          "fixed top-0 left-0 right-0 z-50 transition-all duration-300",
          scrolled ? "glass border-b border-slate-200/70 shadow-sm" : "bg-white/80 backdrop-blur"
        )}
      >
        <div className="mx-auto flex h-16 max-w-7xl items-center gap-8 px-4 sm:px-6 lg:px-8">
          <Link to="/" className="flex items-center gap-2 group">
            <div className="relative">
              <img
                src={serverPath("site-assets.php?type=logo")}
                alt="PROFESSIONAL SOFTWARE"
                className="h-9 w-9 rounded-xl object-contain bg-white shadow-lg shadow-blue-500/20"
              />
            </div>
            <div className="flex flex-col leading-none">
              <span className="text-lg font-extrabold tracking-tight text-slate-900">PROFESSIONAL</span>
              <span className="text-[10px] font-bold text-blue-600 tracking-[0.3em]">SOFTWARE</span>
            </div>
          </Link>

          <nav className="hidden lg:flex items-center gap-1">
            <div
              className="relative"
              onMouseEnter={() => setProductsOpen(true)}
              onMouseLeave={() => setProductsOpen(false)}
            >
              <button className="flex items-center gap-1 px-3 py-2 text-sm font-medium text-slate-700 hover:text-blue-600 transition">
                Продукты <ChevronDown className="h-4 w-4" />
              </button>
              {productsOpen && (
                <div className="absolute top-full left-0 pt-2 w-[560px]">
                  <div className="rounded-2xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-200/50">
                    <div className="grid grid-cols-2 gap-2">
                      {[
                        { name: "Guard Pro", desc: "Антивирус с AI", path: "/products/nimbus-guard-pro" },
                        { name: "Clean Utility", desc: "Оптимизация системы", path: "/products/nimbus-clean-utility" },
                        { name: "Vault Cloud", desc: "Облачное хранилище", path: "/products/nimbus-vault-cloud" },
                        { name: "Backup Enterprise", desc: "Резервное копирование", path: "/products/nimbus-backup-enterprise" },
                        { name: "Mail Server", desc: "Почтовый сервер", path: "/products/nimbus-mail-server" },
                        { name: "VPN Business", desc: "Корпоративный VPN", path: "/products/nimbus-vpn-business" },
                      ].map(p => (
                        <Link
                          key={p.path}
                          to={p.path}
                          className="group flex items-start gap-3 rounded-xl p-3 hover:bg-blue-50 transition"
                        >
                          <div className="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            PS
                          </div>
                          <div>
                            <div className="text-sm font-semibold text-slate-900 group-hover:text-blue-600">{p.name}</div>
                            <div className="text-xs text-slate-500">{p.desc}</div>
                          </div>
                        </Link>
                      ))}
                    </div>
                    <div className="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                      <span className="text-xs text-slate-500">Бесплатный trial 30 дней · без привязки карты</span>
                      <Link to="/products" className="text-sm font-semibold text-blue-600 hover:text-blue-700">
                        Все продукты →
                      </Link>
                    </div>
                  </div>
                </div>
              )}
            </div>
            {navItems.slice(1).map(item => (
              <NavLink
                key={item.path}
                to={item.path}
                className={({ isActive }) =>
                  cn(
                    "px-3 py-2 text-sm font-medium transition rounded-lg",
                    isActive ? "text-blue-600" : "text-slate-700 hover:text-blue-600"
                  )
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>

          <div className="ml-auto flex items-center gap-2">
            <button
              onClick={() => setSearchOpen(!searchOpen)}
              className="hidden md:flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"
              aria-label="Поиск"
            >
              <Search className="h-4 w-4" />
            </button>
            <Link
              to="/login"
              className="hidden md:flex h-9 items-center gap-1.5 px-3 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition"
            >
              <User className="h-4 w-4" />
              Войти
            </Link>
            <Link
              to="/contact"
              className="hidden sm:inline-flex h-9 items-center px-4 rounded-lg bg-orange-500 text-sm font-semibold text-white shadow-sm hover:bg-orange-600 transition"
            >
              Связаться
            </Link>
            <button
              className="lg:hidden flex h-9 w-9 items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100"
              onClick={() => setMobileOpen(!mobileOpen)}
            >
              {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </button>
          </div>
        </div>

        {searchOpen && (
          <div className="border-t border-slate-200 bg-white">
            <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input
                  autoFocus
                  type="text"
                  placeholder="Найти продукт, статью, ошибку..."
                  className="w-full h-12 pl-10 pr-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
                />
              </div>
            </div>
          </div>
        )}

        {mobileOpen && (
          <div className="lg:hidden border-t border-slate-200 bg-white">
            <nav className="px-4 py-3 space-y-1">
              <Link to="/products" className="block px-3 py-2 rounded-lg text-sm font-semibold text-slate-900 hover:bg-slate-50">Продукты</Link>
              {navItems.slice(1).map(item => (
                <Link
                  key={item.path}
                  to={item.path}
                  className="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                  {item.label}
                </Link>
              ))}
              <div className="pt-3 mt-2 border-t border-slate-100 flex gap-2">
                <Link to="/login" className="flex-1 h-10 inline-flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 text-sm font-semibold">
                  <User className="h-4 w-4" /> Войти
                </Link>
                <Link
                  to="/contact"
                  className="flex-1 h-10 inline-flex items-center justify-center rounded-lg bg-orange-500 text-sm font-semibold text-white"
                >
                  Связаться
                </Link>
              </div>
            </nav>
          </div>
        )}
      </header>

      <main className="pt-16">
        <Outlet />
      </main>

      <Footer />
      <ScrollTopButton />
    </div>
  );
}

function Footer() {
  return (
    <footer className="mt-32 border-t border-slate-200 bg-slate-50">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div className="grid grid-cols-2 lg:grid-cols-6 gap-8">
          <div className="col-span-2">
            <div className="flex items-center gap-2">
              <img
                src={serverPath("site-assets.php?type=logo")}
                alt="PROFESSIONAL SOFTWARE"
                className="h-9 w-9 rounded-xl object-contain bg-white"
              />
              <div className="flex flex-col leading-none">
                <span className="text-lg font-extrabold tracking-tight text-slate-900">PROFESSIONAL</span>
                <span className="text-[10px] font-bold text-blue-600 tracking-[0.3em]">SOFTWARE</span>
              </div>
            </div>
            <p className="mt-4 text-sm text-slate-600 max-w-sm">
              Российский производитель профессионального программного обеспечения для бизнеса и частных пользователей с 2010 года.
            </p>
            <div className="mt-6 space-y-2.5 text-sm">
              <a href="tel:+78129453143" className="flex items-center gap-2 text-slate-700 hover:text-blue-600 transition">
                <Phone className="h-4 w-4 text-slate-400" /> +7 (812) 945-31-43
              </a>
              <a href="mailto:info@mastersoftware.ru" className="flex items-center gap-2 text-slate-700 hover:text-blue-600 transition">
                <Mail className="h-4 w-4 text-slate-400" /> info@mastersoftware.ru
              </a>
              <div className="flex items-start gap-2 text-slate-600">
                <MapPin className="h-4 w-4 text-slate-400 mt-0.5 flex-shrink-0" /> 198334, г. Санкт-Петербург, пр. Ветеранов, 140, офис 1
              </div>
            </div>
            <div className="mt-6 flex items-center gap-3">
              {[
                { name: "VK", url: "https://vk.com/professionalsoftware" },
                { name: "TG", url: "https://t.me/professionalsoftware" },
                { name: "WA", url: "https://wa.me/79219453143" },
              ].map(s => (
                <a
                  key={s.name}
                  href={s.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-300 transition text-[10px] font-bold"
                  title={s.name}
                >
                  {s.name}
                </a>
              ))}
            </div>
          </div>

          {[
            { title: "Продукты", links: ["Guard Pro", "Clean Utility", "Vault Cloud", "Backup Enterprise", "Mail Server", "VPN Business"] },
            { title: "Компания", links: ["О нас", "mastersoftware.ru", "Партнёрам", "Пресса", "Контакты"] },
            { title: "Поддержка", links: ["База знаний", "Связаться", "Статус систем", "Скачать", "Сообщить об уязвимости"] },
          ].map(col => (
            <div key={col.title}>
              <h4 className="text-sm font-bold text-slate-900">{col.title}</h4>
              <ul className="mt-4 space-y-2.5">
                {col.links.map(l => (
                  <li key={l}><a href="#" className="text-sm text-slate-600 hover:text-blue-600 transition">{l}</a></li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="mt-12 pt-8 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p className="text-sm text-slate-500">© 2010–2026 PROFESSIONAL SOFTWARE. Все права защищены.</p>
          <div className="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-slate-500">
            <a href="#" className="hover:text-blue-600">Политика конфиденциальности</a>
            <a href="#" className="hover:text-blue-600">Пользовательское соглашение</a>
            <a href="#" className="hover:text-blue-600">Карта сайта</a>
          </div>
        </div>
      </div>
    </footer>
  );
}
