import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import {
  Shield, Lock, Cloud, Zap, Sparkles, Star, ArrowRight,
  Building2, GraduationCap, Banknote, ShoppingCart, Factory,
  ChevronRight, Download, PlayCircle, MapPin, Phone, Mail, MessageSquare, Clock, X
} from "lucide-react";
import { useProducts, useBlogPosts } from "../hooks/useData";
import { solutions } from "../hooks/useData";
import { cn } from "../utils/cn";
import { serverPath } from "../utils/paths";
import TestimonialsSection from "../components/TestimonialsSection";
import HeroProductCarousel from "../components/HeroProductCarousel";

const solutionIcons: Record<string, any> = { Banknote, Building2, GraduationCap, ShoppingCart, Factory };

type Section = { label?: string; title?: string; subtitle?: string };
type HomeSettings = {
  blocks: Record<string, boolean>;
  hero: Record<string, string>;
  trusted: { title: string; logos: string };
  cta: Record<string, string>;
  products: Section;
  features: Section;
  solutions: Section;
  testimonials: Section;
  map: Section;
  blog: Section;
};

const DEFAULT_SETTINGS: HomeSettings = {
  blocks: { hero:true, trusted:true, products:true, features:true, solutions:true, testimonials:true, map:true, cta:true, blog:true },
  hero: {
    badge: "Версия 12.4 с обновлённым AI-движком",
    title1: "Профессиональное ПО", title2: "для вашего бизнеса",
    subtitle: "Российский разработчик антивирусов, утилит, облачных и корпоративных решений. Защищаем 2.5 млн пользователей в 40+ странах мира.",
    btn1: "Смотреть продукты", btn2: "Сравнить версии",
    stat1: "2.5M+", stat2: "15 лет", reviews: "2 847 отзывов",
  },
  trusted: { title: "Нам доверяют компании из разных отраслей", logos: "Альфа-Банк, СберМаркет, Тинькофф, М.Видео, DNS, Ситилинк, ВкусВилл, Ozon" },
  cta: { title: "Попробуйте бесплатно 30 дней", subtitle: "Полный функционал всех продуктов, без ограничений. Без привязки карты. Установка за 2 минуты.", btn1: "Скачать бесплатно", btn2: "Связаться с нами" },
  products: { label: "Наши продукты", title: "Решения для любых задач", subtitle: "От защиты домашнего ПК до комплексной безопасности корпоративной сети на тысячи устройств." },
  features: { label: "Почему мы", title: "Технологии, которым доверяют", subtitle: "Каждый продукт создан инженерами с опытом 15+ лет в индустрии и проходит многоступенчатое тестирование." },
  solutions: { label: "Отраслевые решения", title: "Подходим под вашу индустрию", subtitle: "Готовые отраслевые кейсы, учитывающие специфику бизнеса и требования регуляторов." },
  testimonials: { label: "Отзывы", title: "Нам доверяют тысячи" },
  map: { label: "Офис компании", title: "Приезжайте в гости", subtitle: "Загляните в наш офис в Санкт-Петербурге — будем рады обсудить сотрудничество лично" },
  blog: { label: "Блог", title: "Последние статьи" },
};

function useHomeSettings() {
  const [cfg, setCfg] = useState<HomeSettings>(DEFAULT_SETTINGS);
  useEffect(() => {
    fetch(serverPath("api/home-settings.php"))
      .then(r => r.json())
      .then(d => { if (d && d.blocks) setCfg(d as HomeSettings); })
      .catch(() => {});
  }, []);
  return cfg;
}

function OfficeMapCard() {
  const [showInfo, setShowInfo] = useState(false);

  return (
    <div className="lg:col-span-3 rounded-2xl overflow-hidden border border-slate-200 bg-white min-h-[500px] relative">
      <iframe
        src="https://yandex.ru/map-widget/v1/?ll=30.252290%2C59.839610&mode=search&text=Санкт-Петербург%2C%20проспект%20Ветеранов%2C%20140%2C%20офис%201&z=16&scroll=false"
        width="100%"
        height="100%"
        style={{ minHeight: 500, border: 0 }}
        frameBorder="0"
        allowFullScreen
        title="PROFESSIONAL SOFTWARE — офис в Санкт-Петербурге"
      />

      {/* Кнопка-иконка метки на карте */}
      <button
        onClick={() => setShowInfo(!showInfo)}
        className="absolute z-10 group cursor-pointer"
        style={{ top: "calc(50% - 60px)", left: "50%", transform: "translateX(-50%)" }}
      >
        <div className="relative">
          <div className="absolute -inset-2 rounded-full bg-blue-600/20 animate-ping" />
          <div className="relative h-12 w-12 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/40 ring-4 ring-white group-hover:scale-110 transition-transform">
            <MapPin className="h-6 w-6" fill="white" />
          </div>
          <div className="absolute -bottom-2 left-1/2 -translate-x-1/2 w-3 h-3 bg-blue-600 rotate-45 shadow-lg shadow-blue-600/40" />
        </div>
      </button>

      {/* Табличка — появляется при клике */}
      {showInfo && (
        <div
          className="absolute z-20"
          style={{ top: "calc(50% - 230px)", left: "50%", transform: "translateX(-30px)" }}
        >
          <div className="bg-white rounded-xl shadow-2xl shadow-slate-900/20 border border-slate-200 w-[280px] overflow-hidden animate-in">
            <div className="relative bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 text-white">
              <button
                onClick={(e) => { e.stopPropagation(); setShowInfo(false); }}
                className="absolute top-2 right-2 h-6 w-6 flex items-center justify-center rounded-md text-white/70 hover:text-white hover:bg-white/20 transition"
              >
                <X className="h-3.5 w-3.5" />
              </button>
              <div className="font-extrabold text-sm">PROFESSIONAL SOFTWARE</div>
              <div className="text-blue-100 text-xs mt-0.5">Головной офис · Санкт-Петербург</div>
            </div>
            <div className="p-4 space-y-2.5">
              <div className="flex items-start gap-2 text-xs">
                <MapPin className="h-3.5 w-3.5 text-blue-600 mt-0.5 flex-shrink-0" />
                <span className="text-slate-700">Проспект Ветеранов, 140, офис 1</span>
              </div>
              <a href="tel:+78129453143" className="flex items-center gap-2 text-xs hover:text-blue-600 transition">
                <Phone className="h-3.5 w-3.5 text-blue-600 flex-shrink-0" />
                <span className="text-slate-700">+7 (812) 945-31-43</span>
              </a>
              <a href="mailto:info@mastersoftware.ru" className="flex items-center gap-2 text-xs hover:text-blue-600 transition">
                <Mail className="h-3.5 w-3.5 text-blue-600 flex-shrink-0" />
                <span className="text-slate-700">info@mastersoftware.ru</span>
              </a>
              <div className="flex items-center gap-2 text-xs">
                <Clock className="h-3.5 w-3.5 text-blue-600 flex-shrink-0" />
                <span className="text-slate-700">Пн–Пт 10:00–19:00</span>
              </div>
            </div>
          </div>
          <div className="absolute -bottom-2 left-[28px] w-4 h-4 bg-white border-r border-b border-slate-200 rotate-45" />
        </div>
      )}

      {/* Подсказка */}
      {!showInfo && (
        <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-10">
          <div className="bg-slate-900/80 backdrop-blur text-white text-xs font-semibold px-3 py-1.5 rounded-full animate-pulse pointer-events-none">
            ← Нажмите на метку на карте
          </div>
        </div>
      )}
    </div>
  );
}

export default function Home() {
  const products = useProducts();
  const blogPosts = useBlogPosts();
  const cfg = useHomeSettings();
  const h = cfg.hero;
  const t = cfg.trusted;
  const c = cfg.cta;
  const b = cfg.blocks;
  const trustedList = t.logos.split(",").map(l => l.trim()).slice(0, 8);

  return (
    <>
      {/* HERO */}
      <section className="relative overflow-hidden" style={b.hero===false?{display:'none'}:{}}>
        <div className="absolute inset-0 grid-pattern opacity-60" />
        <div className="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-blue-400/20 blur-3xl animate-blob" />
        <div className="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-cyan-300/20 blur-3xl animate-blob" style={{ animationDelay: "4s" }} />

        <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-24 lg:pt-24 lg:pb-32">
          <div className="grid lg:grid-cols-12 gap-12 items-center">
            <div className="lg:col-span-7">
              <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                <Sparkles className="h-3.5 w-3.5" />
                {h.badge}
              </div>
              <h1 className="mt-6 text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.05]">
                {h.title1} <br />
                <span className="gradient-text">{h.title2}</span>
              </h1>
              <p className="mt-6 text-lg sm:text-xl text-slate-600 max-w-2xl leading-relaxed">
                {h.subtitle}
              </p>
              <div className="mt-8 flex flex-wrap items-center gap-3">
                <Link
                  to="/products"
                  className="inline-flex items-center gap-2 h-12 px-6 rounded-xl bg-blue-600 text-white font-semibold shadow-lg shadow-blue-500/30 hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-500/40 transition"
                >
                  {h.btn1} <ArrowRight className="h-4 w-4" />
                </Link>
                <Link
                  to="/pricing"
                  className="inline-flex items-center gap-2 h-12 px-6 rounded-xl border border-slate-200 bg-white text-slate-900 font-semibold hover:border-blue-300 hover:text-blue-600 transition"
                >
                  <PlayCircle className="h-4 w-4" /> {h.btn2}
                </Link>
              </div>
              <div className="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4">
                <div className="flex items-center gap-2">
                  <div className="flex -space-x-2">
                    {["А", "М", "Д", "Е"].map(l => (
                      <div key={l} className="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xs font-bold border-2 border-white">
                        {l}
                      </div>
                    ))}
                  </div>
                  <div className="text-sm">
                    <div className="flex items-center gap-0.5">
                      {[1, 2, 3, 4, 5].map(i => <Star key={i} className="h-3.5 w-3.5 fill-orange-500 text-orange-500" />)}
                    </div>
                    <div className="text-xs text-slate-500">2 847 отзывов</div>
                  </div>
                </div>
                <div className="text-sm text-slate-600">
                  <strong className="text-slate-900">2.5M+</strong> пользователей
                </div>
                <div className="text-sm text-slate-600">
                  <strong className="text-slate-900">15 лет</strong> на рынке
                </div>
              </div>
            </div>

              <div className="lg:col-span-5 relative">
              <div className="relative">
                <div className="absolute -inset-4 bg-gradient-to-br from-blue-500/20 to-cyan-400/20 blur-2xl rounded-3xl" />
                <HeroProductCarousel />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* TRUSTED BY */}
      <section className="border-y border-slate-200 bg-slate-50" style={b.trusted===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
          <p className="text-center text-sm font-semibold text-slate-500 uppercase tracking-wider">
            {t.title}
          </p>
          <div className="mt-8 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-6">
            {trustedList.map(p => (
              <div key={p} className="flex items-center justify-center h-12 text-slate-400 hover:text-slate-700 transition font-bold text-sm">
                {p}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* PRODUCTS */}
      <section className="py-20 lg:py-28" style={b.products===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div className="max-w-2xl">
              <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{cfg.products.label}</div>
              <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{cfg.products.title}</h2>
              <p className="mt-3 text-lg text-slate-600">{cfg.products.subtitle}</p>
            </div>
            <Link to="/products" className="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
              Все продукты <ChevronRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {products.slice(0, 6).map(p => (
              <Link
                key={p.id}
                to={`/products/${p.slug}`}
                className="group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10 transition"
              >
                {p.badge && (
                  <span className={cn(
                    "absolute top-4 right-4 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider",
                    p.badge === "Хит" && "bg-orange-100 text-orange-700",
                    p.badge === "Новинка" && "bg-blue-100 text-blue-700",
                    p.badge === "Скидка" && "bg-red-100 text-red-700",
                  )}>
                    {p.badge}
                  </span>
                )}
                <div className="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/20">
                  <Shield className="h-6 w-6 text-white" />
                </div>
                <h3 className="mt-4 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">{p.name}</h3>
                <p className="mt-1 text-sm text-slate-600 line-clamp-2">{p.tagline}</p>
                <div className="mt-4 flex items-center gap-1 text-xs text-slate-500">
                  <Star className="h-3.5 w-3.5 fill-orange-500 text-orange-500" />
                  <span className="font-semibold text-slate-900">{p.rating}</span>
                  <span>({p.reviews})</span>
                </div>
                <div className="mt-auto pt-4 flex items-center justify-between">
                  <div>
                    <div className="text-xs text-slate-500">от</div>
                    <div className="text-xl font-extrabold text-slate-900">{p.price.toLocaleString("ru-RU")} ₽</div>
                  </div>
                  <span className="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 group-hover:translate-x-1 transition">
                    Подробнее <ArrowRight className="h-4 w-4" />
                  </span>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* FEATURES */}
      <section className="py-20 lg:py-28 bg-slate-50" style={b.features===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="max-w-2xl mx-auto text-center">
            <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{cfg.features.label}</div>
            <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{cfg.features.title}</h2>
            <p className="mt-3 text-lg text-slate-600">{cfg.features.subtitle}</p>
          </div>

          <div className="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              { icon: Shield, title: "AI-защита", desc: "Нейросетевой движок обнаруживает 99.8% ранее неизвестных угроз в реальном времени." },
              { icon: Cloud, title: "Облако", desc: "Синхронизация данных между всеми устройствами с end-to-end шифрованием AES-256." },
              { icon: Zap, title: "Скорость", desc: "Минимальное влияние на производительность системы — менее 1% CPU в простое." },
              { icon: Lock, title: "Приватность", desc: "Соответствие 152-ФЗ, GDPR, хранение данных на серверах в России." },
            ].map(f => (
              <div key={f.title} className="rounded-2xl bg-white p-6 border border-slate-200">
                <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center">
                  <f.icon className="h-6 w-6 text-blue-600" />
                </div>
                <h3 className="mt-4 text-lg font-bold text-slate-900">{f.title}</h3>
                <p className="mt-2 text-sm text-slate-600 leading-relaxed">{f.desc}</p>
              </div>
            ))}
          </div>

          <div className="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {[
              { v: "99.8%", l: "детекция угроз" },
              { v: "<1%", l: "потребление CPU" },
              { v: "40+", l: "стран присутствия" },
              { v: "24/7", l: "поддержка" },
            ].map(s => (
              <div key={s.l} className="rounded-2xl bg-white p-6 border border-slate-200 text-center">
                <div className="text-3xl sm:text-4xl font-extrabold gradient-text">{s.v}</div>
                <div className="mt-1 text-sm text-slate-600">{s.l}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* SOLUTIONS */}
      <section className="py-20 lg:py-28" style={b.solutions===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto">
            <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{cfg.solutions.label}</div>
            <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{cfg.solutions.title}</h2>
            <p className="mt-3 text-lg text-slate-600">{cfg.solutions.subtitle}</p>
          </div>

          <div className="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {solutions.map(s => {
              const Icon = solutionIcons[s.icon] || Building2;
              return (
                <Link key={s.industry} to="/solutions" className="group rounded-2xl border border-slate-200 bg-white p-6 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10 transition">
                  <div className="flex items-center gap-3">
                    <div className="h-11 w-11 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-600 transition">
                      <Icon className="h-5 w-5 text-blue-600 group-hover:text-white transition" />
                    </div>
                    <div className="text-xs font-bold text-slate-500 uppercase tracking-wider">{s.industry}</div>
                  </div>
                  <h3 className="mt-4 text-lg font-bold text-slate-900">{s.title}</h3>
                  <p className="mt-2 text-sm text-slate-600">{s.description}</p>
                  <span className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 group-hover:translate-x-1 transition">
                    Изучить <ArrowRight className="h-4 w-4" />
                  </span>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* TESTIMONIALS */}
      <section className="py-20 lg:py-28 bg-slate-50" style={b.testimonials===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <TestimonialsSection label={cfg.testimonials.label || "Отзывы"} title={cfg.testimonials.title || "Нам доверяют тысячи"} />
        </div>
      </section>

      {/* OFFICE MAP */}
      <section className="py-20 lg:py-28 bg-slate-50" style={b.map===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto mb-10">
            <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{cfg.map.label}</div>
            <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{cfg.map.title}</h2>
            <p className="mt-3 text-lg text-slate-600">{cfg.map.subtitle}</p>
          </div>

          <div className="grid lg:grid-cols-5 gap-6">
            <div className="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-8 flex flex-col">
              <div className="inline-flex items-center gap-2 text-xs font-bold text-blue-600 uppercase tracking-wider">
                <div className="h-2 w-2 rounded-full bg-green-500 animate-pulse" /> Открыто
              </div>
              <h3 className="mt-3 text-2xl font-extrabold text-slate-900">PROFESSIONAL SOFTWARE</h3>
              <div className="text-sm text-slate-500 mt-1">Головной офис</div>

              <div className="mt-6 space-y-4 text-sm">
                <div className="flex items-start gap-3">
                  <MapPin className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="font-bold text-slate-900">Адрес</div>
                    <div className="text-slate-600">198334, г. Санкт-Петербург,<br />проспект Ветеранов, 140, офис 1</div>
                  </div>
                </div>
                <a href="tel:+78129453143" className="flex items-start gap-3 hover:text-blue-600 transition">
                  <Phone className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="font-bold text-slate-900">Телефон</div>
                    <div className="text-slate-600">+7 (812) 945-31-43</div>
                  </div>
                </a>
                <a href="mailto:info@mastersoftware.ru" className="flex items-start gap-3 hover:text-blue-600 transition">
                  <Mail className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="font-bold text-slate-900">Email</div>
                    <div className="text-slate-600">info@mastersoftware.ru</div>
                  </div>
                </a>
                <div className="flex items-start gap-3">
                  <MessageSquare className="h-5 w-5 text-green-600 flex-shrink-0 mt-0.5" />
                  <div>
                    <div className="font-bold text-slate-900">Мессенджеры</div>
                    <div className="flex gap-1.5 flex-wrap mt-1">
                      <a href="https://t.me/professionalsoftware" target="_blank" rel="noopener noreferrer" className="text-xs px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 font-bold hover:bg-blue-200">Telegram</a>
                      <a href="https://wa.me/79219453143" target="_blank" rel="noopener noreferrer" className="text-xs px-2 py-0.5 rounded-md bg-green-100 text-green-700 font-bold hover:bg-green-200">WhatsApp</a>
                      <a href="https://vk.com/professionalsoftware" target="_blank" rel="noopener noreferrer" className="text-xs px-2 py-0.5 rounded-md bg-slate-200 text-slate-700 font-bold hover:bg-slate-300">VK</a>
                    </div>
                  </div>
                </div>
              </div>

              <div className="mt-6 pt-5 border-t border-slate-100 flex items-start gap-3">
                <Clock className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                  <div className="font-bold text-slate-900 text-sm">Часы работы</div>
                  <div className="text-sm text-slate-600">Пн–Пт: 10:00–19:00 МСК<br />Сб–Вс: 10:00–17:00 МСК (только мессенджеры)</div>
                </div>
              </div>

              <Link
                to="/contact"
                className="mt-6 h-11 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
              >
                Связаться с нами <ArrowRight className="h-4 w-4" />
              </Link>
            </div>

            <OfficeMapCard />
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20 lg:py-28" style={b.cta===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div
            className="relative overflow-hidden rounded-3xl p-10 lg:p-16"
            style={{
              background:
                "radial-gradient(circle at 88% 8%, var(--ps-light, #3D75F0) 0%, transparent 34%), radial-gradient(circle at 0% 100%, var(--ps-dark, #003278) 0%, transparent 38%), linear-gradient(135deg, var(--ps-dark, #003278) 0%, var(--ps-primary, #0056D2) 48%, var(--ps-light, #3D75F0) 100%)",
            }}
          >
            <div className="absolute -top-20 -right-20 h-80 w-80 rounded-full blur-3xl" style={{ backgroundColor: "color-mix(in srgb, var(--ps-light, #3D75F0) 25%, transparent)" }} />
            <div className="absolute -bottom-20 -left-20 h-80 w-80 rounded-full blur-3xl" style={{ backgroundColor: "color-mix(in srgb, var(--ps-primary, #0056D2) 30%, transparent)" }} />
            <div className="relative grid lg:grid-cols-2 gap-8 items-center">
              <div>
                <h2 className="text-3xl sm:text-4xl font-extrabold text-white">{c.title}</h2>
                <p className="mt-3 text-lg text-blue-100 max-w-md">{c.subtitle}</p>
              </div>
              <div className="flex flex-col sm:flex-row gap-3 lg:justify-end">
                <Link
                  to="/products"
                  className="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/30 hover:bg-orange-600 transition"
                >
                  <Download className="h-4 w-4" /> {c.btn1}
                </Link>
                <Link
                  to="/contact"
                  className="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-white/10 backdrop-blur text-white font-semibold border border-white/20 hover:bg-white/20 transition"
                >
                  {c.btn2}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* BLOG */}
      <section className="py-20 lg:py-28 bg-slate-50" style={b.blog===false?{display:'none'}:{}}>
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
              <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{cfg.blog.label}</div>
              <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{cfg.blog.title}</h2>
            </div>
            <Link to="/blog" className="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
              Все статьи <ChevronRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="mt-12 grid md:grid-cols-3 gap-6">
            {blogPosts.slice(0, 3).map(post => (
              <Link key={post.id} to={`/blog/${post.slug}`} className="group rounded-2xl overflow-hidden bg-white border border-slate-200 hover:shadow-xl hover:shadow-blue-500/10 transition">
                <div
                  className="h-48 relative"
                  style={{
                    background:
                      "radial-gradient(circle at 80% 15%, var(--ps-light, #3D75F0) 0%, transparent 36%), linear-gradient(135deg, var(--ps-dark, #003278) 0%, var(--ps-primary, #0056D2) 52%, var(--ps-light, #3D75F0) 100%)",
                  }}
                >
                  <div className="absolute inset-0 flex items-center justify-center">
                    <Shield className="h-16 w-16 text-white/30" />
                  </div>
                  <span className="absolute top-4 left-4 px-2 py-0.5 rounded-full bg-white/90 backdrop-blur text-xs font-bold text-slate-900">
                    {post.category}
                  </span>
                </div>
                <div className="p-5">
                  <div className="text-xs text-slate-500 flex items-center gap-2">
                    <span>{post.date}</span>
                    <span>·</span>
                    <span>{post.readTime} мин чтения</span>
                  </div>
                  <h3 className="mt-2 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2">{post.title}</h3>
                  <p className="mt-2 text-sm text-slate-600 line-clamp-2">{post.excerpt}</p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
