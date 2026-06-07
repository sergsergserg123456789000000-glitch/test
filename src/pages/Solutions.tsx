import { Link } from "react-router-dom";
import { Building2, GraduationCap, Banknote, ShoppingCart, Factory, ShieldCheck, Award, Globe, Check, ArrowRight } from "lucide-react";
import { solutions } from "../data";

const solutionIcons: Record<string, any> = { Banknote, Building2, GraduationCap, ShoppingCart, Factory };

const certifications = [
  { name: "ФСТЭК", desc: "Сертификат соответствия" },
  { name: "ISO 27001", desc: "Международный стандарт" },
  { name: "PCI DSS", desc: "Безопасность платежей" },
  { name: "ГОСТ Р", desc: "Российские стандарты" },
  { name: "152-ФЗ", desc: "Персональные данные" },
  { name: "GDPR", desc: "Защита данных ЕС" },
];

const caseStudies = [
  {
    industry: "Банковский сектор",
    company: "Крупный федеральный банк",
    title: "Защита 12 000 рабочих станций и 850 серверов",
    metrics: [
      { v: "12 000+", l: "защищённых устройств" },
      { v: "99.99%", l: "uptime системы" },
      { v: "0", l: "инцидентов за 3 года" },
    ],
    quote: "Переход на NimbusSoft сократил наши затраты на 40% при значительном росте уровня защиты.",
    author: "Алексей Петров, CISO",
  },
  {
    industry: "Промышленность",
    company: "Нефтегазовая корпорация",
    title: "Защита АСУ ТП и промышленных сетей",
    metrics: [
      { v: "45", l: "производственных площадок" },
      { v: "3 мес.", l: "от пилотного проекта до production" },
      { v: "24/7", l: "SOC-мониторинг" },
    ],
    quote: "Изоляция сегментов SCADA и поведенческий анализ дали нам уверенность в защите критической инфраструктуры.",
    author: "Игорь Сидоров, директор по ИБ",
  },
];

export default function Solutions() {
  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12 text-center">
          <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900">Отраслевые решения</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl mx-auto">
            Готовые кейсы для вашей индустрии, учитывающие отраслевую специфику и регуляторные требования
          </p>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {solutions.map(s => {
              const Icon = solutionIcons[s.icon] || Building2;
              return (
                <div key={s.industry} className="rounded-2xl border border-slate-200 bg-white p-7 hover:shadow-xl hover:shadow-blue-500/10 transition">
                  <div className="flex items-center gap-3">
                    <div className="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                      <Icon className="h-6 w-6" />
                    </div>
                    <div className="text-xs font-bold text-blue-600 uppercase tracking-wider">{s.industry}</div>
                  </div>
                  <h3 className="mt-4 text-xl font-bold text-slate-900">{s.title}</h3>
                  <p className="mt-2 text-sm text-slate-600 leading-relaxed">{s.description}</p>
                  <ul className="mt-4 space-y-1.5">
                    {["Соответствие отраслевым стандартам", "Готовые шаблоны политик", "Сертифицированные специалисты"].map(item => (
                      <li key={item} className="flex items-start gap-2 text-sm text-slate-700">
                        <Check className="h-4 w-4 text-blue-600 flex-shrink-0 mt-0.5" /> {item}
                      </li>
                    ))}
                  </ul>
                  <button className="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:gap-2 transition-all">
                    Узнать подробнее <ArrowRight className="h-4 w-4" />
                  </button>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      <section className="py-20 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-extrabold text-slate-900 text-center">Кейсы внедрения</h2>
          <div className="mt-12 space-y-6">
            {caseStudies.map((c, i) => (
              <div key={i} className="rounded-2xl border border-slate-200 bg-white p-8 lg:p-10 grid lg:grid-cols-3 gap-8">
                <div className="lg:col-span-2">
                  <div className="text-xs font-bold text-blue-600 uppercase tracking-wider">{c.industry}</div>
                  <h3 className="mt-2 text-2xl font-extrabold text-slate-900">{c.title}</h3>
                  <div className="text-sm text-slate-500 mt-1">{c.company}</div>
                  <blockquote className="mt-6 border-l-4 border-blue-500 pl-4 italic text-slate-700">
                    «{c.quote}»
                    <div className="mt-2 not-italic text-sm font-semibold text-slate-900">— {c.author}</div>
                  </blockquote>
                </div>
                <div className="grid grid-cols-3 lg:grid-cols-1 gap-4">
                  {c.metrics.map(m => (
                    <div key={m.l} className="rounded-xl bg-slate-50 p-4 text-center">
                      <div className="text-2xl font-extrabold text-blue-600">{m.v}</div>
                      <div className="text-xs text-slate-600 mt-1">{m.l}</div>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto">
            <h2 className="text-3xl font-extrabold text-slate-900">Сертификаты и соответствие стандартам</h2>
            <p className="mt-3 text-slate-600">Соответствуем требованиям регуляторов и международным стандартам</p>
          </div>
          <div className="mt-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            {certifications.map(c => (
              <div key={c.name} className="rounded-xl border border-slate-200 p-5 text-center hover:border-blue-300 transition">
                <ShieldCheck className="h-8 w-8 text-blue-600 mx-auto" />
                <div className="mt-2 text-base font-bold text-slate-900">{c.name}</div>
                <div className="text-xs text-slate-500 mt-1">{c.desc}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 bg-gradient-to-br from-blue-600 to-indigo-800 text-white">
        <div className="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
          <Globe className="h-12 w-12 mx-auto opacity-80" />
          <h2 className="mt-4 text-3xl sm:text-4xl font-extrabold">Обсудим ваш проект?</h2>
          <p className="mt-3 text-blue-100 max-w-xl mx-auto">
            Наши инженеры подготовят индивидуальное решение и рассчитают стоимость с учётом особенностей вашей инфраструктуры.
          </p>
          <div className="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <Link to="/contact" className="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-white text-blue-700 font-semibold hover:bg-blue-50 transition">
              Связаться с нами
            </Link>
            <button className="inline-flex items-center justify-center gap-2 h-12 px-6 rounded-xl bg-white/10 backdrop-blur border border-white/20 text-white font-semibold hover:bg-white/20 transition">
              <Award className="h-4 w-4" /> Запросить демо
            </button>
          </div>
        </div>
      </section>
    </>
  );
}
