import { useState } from "react";
import { Link } from "react-router-dom";
import { ChevronRight, Search, MessageSquare, BookOpen, FileText, Mail, Phone, Loader2 } from "lucide-react";
import { faqItems } from "../data";
import { cn } from "../utils/cn";

const articles = [
  { cat: "Начало работы", items: ["Установка NimbusGuard Pro", "Первая настройка системы", "Активация лицензии", "Импорт пользователей"] },
  { cat: "Безопасность", items: ["Настройка брандмауэра", "Сканирование по расписанию", "Карантин и восстановление", "Белый список приложений"] },
  { cat: "Администрирование", items: ["Подключение Active Directory", "Настройка политик безопасности", "Создание отчётов", "Резервное копирование"] },
  { cat: "Устранение неполадок", items: ["Не обновляются базы", "Высокая нагрузка CPU", "Конфликты с антивирусами", "Ошибки активации"] },
];

export default function Support() {
  const [openIdx, setOpenIdx] = useState<number | null>(0);
  const [search, setSearch] = useState("");
  const [ticket, setTicket] = useState({ subject: "", message: "" });
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  const filteredFaq = faqItems.filter(f => f.q.toLowerCase().includes(search.toLowerCase()) || f.a.toLowerCase().includes(search.toLowerCase()));

  const submitTicket = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setTimeout(() => {
      setSubmitting(false);
      setSubmitted(true);
      setTicket({ subject: "", message: "" });
    }, 1200);
  };

  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
          <nav className="text-sm text-slate-500 flex items-center gap-2">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">Поддержка</span>
          </nav>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">Центр поддержки</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl">
            Найдите ответы, изучите документацию или свяжитесь с нашей командой поддержки.
          </p>
          <div className="mt-8 relative max-w-2xl">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Опишите вашу проблему или введите код ошибки..."
              className="w-full h-14 pl-12 pr-4 rounded-xl border border-slate-200 bg-white text-base focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition shadow-sm"
            />
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid sm:grid-cols-3 gap-4">
            {[
              { icon: BookOpen, title: "База знаний", desc: "350+ статей и инструкций", count: "350+" },
              { icon: MessageSquare, title: "Тикет-система", desc: "Среднее время ответа: 18 мин", count: "18 мин" },
              { icon: FileText, title: "Документация", desc: "Полные руководства пользователя", count: "API" },
            ].map(c => (
              <div key={c.title} className="rounded-2xl border border-slate-200 bg-white p-6 hover:border-blue-300 transition">
                <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center">
                  <c.icon className="h-6 w-6 text-blue-600" />
                </div>
                <h3 className="mt-4 text-lg font-bold text-slate-900">{c.title}</h3>
                <p className="text-sm text-slate-500 mt-1">{c.desc}</p>
                <div className="mt-4 text-xs font-bold text-blue-600 uppercase tracking-wider">{c.count}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-12 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-extrabold text-slate-900">Популярные статьи</h2>
          <div className="mt-8 grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {articles.map(cat => (
              <div key={cat.cat} className="rounded-xl border border-slate-200 bg-white p-5">
                <h3 className="font-bold text-slate-900">{cat.cat}</h3>
                <ul className="mt-3 space-y-2">
                  {cat.items.map(item => (
                    <li key={item}>
                      <a href="#" className="text-sm text-slate-600 hover:text-blue-600 transition">{item}</a>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16">
        <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-extrabold text-slate-900 text-center">Частые вопросы</h2>
          <div className="mt-8 space-y-3">
            {filteredFaq.map((f, i) => (
              <div key={f.q} className="rounded-xl border border-slate-200 bg-white overflow-hidden">
                <button
                  onClick={() => setOpenIdx(openIdx === i ? null : i)}
                  className="w-full px-5 py-4 text-left flex items-center justify-between hover:bg-slate-50"
                >
                  <span className="font-semibold text-slate-900">{f.q}</span>
                  <ChevronRight className={cn("h-4 w-4 text-slate-400 transition", openIdx === i && "rotate-90")} />
                </button>
                {openIdx === i && (
                  <div className="px-5 pb-4 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-3">
                    {f.a}
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-8">
            <div>
              <h2 className="text-3xl font-extrabold text-slate-900">Свяжитесь с нами</h2>
              <p className="mt-3 text-slate-600">Наши специалисты готовы помочь вам 24/7</p>
              <div className="mt-8 space-y-4">
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <Phone className="h-5 w-5 text-blue-600" />
                  </div>
                  <div>
                    <div className="font-semibold text-slate-900">8 (800) 555-12-34</div>
                    <div className="text-sm text-slate-500">Бесплатно по России, 24/7</div>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <Mail className="h-5 w-5 text-blue-600" />
                  </div>
                  <div>
                    <div className="font-semibold text-slate-900">support@nimbussoft.ru</div>
                    <div className="text-sm text-slate-500">Ответ в течение 1 часа</div>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <MessageSquare className="h-5 w-5 text-blue-600" />
                  </div>
                  <div>
                    <div className="font-semibold text-slate-900">Онлайн-чат</div>
                    <div className="text-sm text-slate-500">Среднее время ответа: 18 мин</div>
                  </div>
                </div>
              </div>
            </div>

            <form onSubmit={submitTicket} className="rounded-2xl border border-slate-200 bg-white p-6">
              <h3 className="font-bold text-slate-900">Создать тикет</h3>
              {submitted ? (
                <div className="mt-6 rounded-xl bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                  ✓ Тикет успешно создан. Номер: #T-1285. Мы свяжемся с вами в ближайшее время.
                </div>
              ) : (
                <>
                  <div className="mt-4 space-y-3">
                    <input
                      required
                      value={ticket.subject}
                      onChange={e => setTicket({ ...ticket, subject: e.target.value })}
                      placeholder="Тема обращения"
                      className="w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    />
                    <textarea
                      required
                      value={ticket.message}
                      onChange={e => setTicket({ ...ticket, message: e.target.value })}
                      placeholder="Опишите вашу проблему..."
                      rows={5}
                      className="w-full p-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none"
                    />
                    <button
                      type="submit"
                      disabled={submitting}
                      className="w-full h-10 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-60"
                    >
                      {submitting ? <><Loader2 className="h-4 w-4 animate-spin" /> Отправка...</> : "Отправить тикет"}
                    </button>
                  </div>
                </>
              )}
            </form>
          </div>
        </div>
      </section>
    </>
  );
}
