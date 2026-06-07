import { Link } from "react-router-dom";
import { Award, TrendingUp, Heart, Target, Eye, ChevronRight, Briefcase } from "lucide-react";

const timeline = [
  { year: "2010", title: "Основание компании", desc: "Трое инженеров основали PROFESSIONAL SOFTWARE в Санкт-Петербурге" },
  { year: "2014", title: "Первый миллион пользователей", desc: "Первый продукт-антивирус — установлен на 1 млн устройств" },
  { year: "2018", title: "Выход на международный рынок", desc: "Представительства в Европе, Азии и Латинской Америке" },
  { year: "2020", title: "Запуск облачной платформы", desc: "Enterprise-решения для бизнеса по всему миру" },
  { year: "2023", title: "AI-движок 2.0", desc: "Внедрение нейросетевого детектора угроз" },
  { year: "2026", title: "Новая R&D-лаборатория", desc: "200+ специалистов в R&D, фокус на AI и облако" },
];

const team = [
  { name: "Алексей Иванов", role: "CEO & Основатель", initials: "АИ" },
  { name: "Мария Петрова", role: "CTO", initials: "МП" },
  { name: "Дмитрий Соколов", role: "CISO", initials: "ДС" },
  { name: "Елена Кузнецова", role: "COO", initials: "ЕК" },
];

const stats = [
  { v: "5M+", l: "Пользователей" },
  { v: "80+", l: "Стран" },
  { v: "15 лет", l: "На рынке" },
  { v: "450+", l: "Сотрудников" },
];

export default function About() {
  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-16">
          <nav className="text-sm text-slate-500 flex items-center gap-2">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">О компании</span>
          </nav>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">PROFESSIONAL SOFTWARE — создаём ПО, которому доверяют</h1>
          <p className="mt-4 text-lg text-slate-600 max-w-3xl">
            Российский разработчик профессионального программного обеспечения.
            С 2010 года мы защищаем и ускоряем IT-инфраструктуру миллионов пользователей и тысяч компаний.
          </p>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {stats.map(s => (
              <div key={s.l} className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
                <div className="text-3xl sm:text-4xl font-extrabold gradient-text">{s.v}</div>
                <div className="mt-1 text-sm text-slate-600">{s.l}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-6">
            {[
              { icon: Target, title: "Миссия", desc: "Сделать профессиональное ПО доступным каждому — от студента до крупной корпорации." },
              { icon: Eye, title: "Видение", desc: "Мир, в котором технологии работают на людей. Безопасность, стабильность и приватность — без компромиссов." },
              { icon: Heart, title: "Ценности", desc: "Честность, инновации, забота о клиенте. Эти принципы определяют каждое наше решение." },
            ].map(b => (
              <div key={b.title} className="rounded-2xl border border-slate-200 bg-white p-7">
                <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center">
                  <b.icon className="h-6 w-6 text-blue-600" />
                </div>
                <h3 className="mt-4 text-xl font-extrabold text-slate-900">{b.title}</h3>
                <p className="mt-2 text-slate-600 leading-relaxed">{b.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto">
            <h2 className="text-3xl font-extrabold text-slate-900">Наша история</h2>
            <p className="mt-3 text-slate-600">Путь от небольшого стартапа до международной компании</p>
          </div>
          <div className="mt-12 relative">
            <div className="absolute left-1/2 -translate-x-1/2 h-full w-px bg-slate-200 hidden md:block" />
            <div className="space-y-8">
              {timeline.map((t, i) => (
                <div key={t.year} className="grid md:grid-cols-2 gap-8 items-center">
                  <div className={i % 2 === 1 ? "md:order-2" : ""}>
                    <div className="text-3xl font-extrabold text-blue-600">{t.year}</div>
                    <h3 className="mt-1 text-xl font-bold text-slate-900">{t.title}</h3>
                    <p className="mt-1 text-slate-600">{t.desc}</p>
                  </div>
                  <div className={`hidden md:block ${i % 2 === 1 ? "md:order-1" : ""}`}>
                    <div className="h-32 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                      <Award className="h-10 w-10 text-blue-600" />
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto">
            <h2 className="text-3xl font-extrabold text-slate-900">Руководство</h2>
            <p className="mt-3 text-slate-600">Команда, которая стоит за PROFESSIONAL SOFTWARE</p>
          </div>
          <div className="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {team.map(m => (
              <div key={m.name} className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
                <div className="h-20 w-20 mx-auto rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-2xl font-bold">
                  {m.initials}
                </div>
                <h3 className="mt-4 font-bold text-slate-900">{m.name}</h3>
                <p className="text-sm text-slate-500">{m.role}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-2 gap-6">
            <div className="rounded-2xl border border-slate-200 bg-white p-8">
              <Briefcase className="h-8 w-8 text-blue-600" />
              <h3 className="mt-4 text-2xl font-extrabold text-slate-900">Стать партнёром</h3>
              <p className="mt-2 text-slate-600">Партнёрская программа для интеграторов и реселлеров с маржой до 40%.</p>
              <Link to="/contact" className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
                Подать заявку →
              </Link>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-8">
              <TrendingUp className="h-8 w-8 text-blue-600" />
              <h3 className="mt-4 text-2xl font-extrabold text-slate-900">Карьера в PROFESSIONAL SOFTWARE</h3>
              <p className="mt-2 text-slate-600">Открытые вакансии в Санкт-Петербурге. Удалёнка приветствуется.</p>
              <a href="#" className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
                12 открытых вакансий →
              </a>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
