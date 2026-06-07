import { useState } from "react";
import { Link } from "react-router-dom";
import { ChevronRight, Mail, Phone, MapPin, MessageSquare, Clock, Loader2, Check, Globe } from "lucide-react";

export default function Contact() {
  const [form, setForm] = useState({ name: "", email: "", company: "", subject: "Запрос на демо", message: "" });
  const [submitting, setSubmitting] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setTimeout(() => {
      setSubmitting(false);
      setSubmitted(true);
    }, 1500);
  };

  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
          <nav className="text-sm text-slate-500 flex items-center gap-2">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">Контакты</span>
          </nav>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">Свяжитесь с нами</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl">
            Расскажите о вашей задаче — мы подберём оптимальное решение и подготовим персональное предложение.
          </p>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
              <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mx-auto">
                <Phone className="h-5 w-5 text-blue-600" />
              </div>
              <a href="tel:+78129453143" className="mt-3 font-bold text-slate-900 hover:text-blue-600 block">+7 (812) 945-31-43</a>
              <div className="text-xs text-slate-500 mt-1">ПН–ПТ 10:00–19:00 МСК</div>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
              <div className="h-12 w-12 rounded-xl bg-green-50 flex items-center justify-center mx-auto">
                <MessageSquare className="h-5 w-5 text-green-600" />
              </div>
              <a href="https://t.me/professionalsoftware" target="_blank" rel="noopener noreferrer" className="mt-3 font-bold text-slate-900 hover:text-blue-600 block">Telegram / WhatsApp</a>
              <a href="https://wa.me/79219453143" target="_blank" rel="noopener noreferrer" className="text-xs text-slate-500 mt-1 hover:text-blue-600 block">+7 (921) 945-31-43</a>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
              <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mx-auto">
                <Mail className="h-5 w-5 text-blue-600" />
              </div>
              <a href="mailto:info@mastersoftware.ru" className="mt-3 font-bold text-slate-900 hover:text-blue-600 block">info@mastersoftware.ru</a>
              <div className="text-xs text-slate-500 mt-1">Ответ в течение 1 часа</div>
            </div>
            <div className="rounded-2xl border border-slate-200 bg-white p-6 text-center">
              <div className="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center mx-auto">
                <Globe className="h-5 w-5 text-blue-600" />
              </div>
              <a href="https://mastersoftware.ru" target="_blank" rel="noopener noreferrer" className="mt-3 font-bold text-slate-900 hover:text-blue-600 block">mastersoftware.ru</a>
              <div className="text-xs text-slate-500 mt-1">Официальный сайт</div>
            </div>
          </div>

          <div className="grid lg:grid-cols-5 gap-8">
            <form onSubmit={submit} className="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-7">
              <h2 className="text-xl font-extrabold text-slate-900">Оставить заявку</h2>
              {submitted ? (
                <div className="mt-6 rounded-xl bg-green-50 border border-green-200 p-6 text-center">
                  <div className="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                    <Check className="h-6 w-6 text-green-600" />
                  </div>
                  <div className="mt-3 font-bold text-slate-900">Заявка отправлена</div>
                  <div className="mt-1 text-sm text-slate-600">Мы свяжемся с вами в течение 1 часа</div>
                </div>
              ) : (
                <div className="mt-6 grid sm:grid-cols-2 gap-4">
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Имя *</label>
                    <input
                      required
                      value={form.name}
                      onChange={e => setForm({ ...form, name: e.target.value })}
                      placeholder="Иван Петров"
                      className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none"
                    />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Email *</label>
                    <input
                      type="email"
                      required
                      value={form.email}
                      onChange={e => setForm({ ...form, email: e.target.value })}
                      placeholder="you@company.ru"
                      className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none"
                    />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Компания</label>
                    <input
                      value={form.company}
                      onChange={e => setForm({ ...form, company: e.target.value })}
                      placeholder='ООО "Рога и копыта"'
                      className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none"
                    />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Тема</label>
                    <select
                      value={form.subject}
                      onChange={e => setForm({ ...form, subject: e.target.value })}
                      className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm bg-white focus:border-blue-500 outline-none"
                    >
                      <option>Запрос на демо</option>
                      <option>Коммерческое предложение</option>
                      <option>Техническая поддержка</option>
                      <option>Партнёрство</option>
                      <option>Другое</option>
                    </select>
                  </div>
                  <div className="sm:col-span-2">
                    <label className="text-sm font-semibold text-slate-900">Сообщение *</label>
                    <textarea
                      required
                      value={form.message}
                      onChange={e => setForm({ ...form, message: e.target.value })}
                      rows={5}
                      placeholder="Расскажите о вашей задаче..."
                      className="mt-1 w-full p-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none resize-none"
                    />
                  </div>
                  <div className="sm:col-span-2">
                    <button
                      type="submit"
                      disabled={submitting}
                      className="w-full h-12 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-60"
                    >
                      {submitting ? <><Loader2 className="h-4 w-4 animate-spin" /> Обработка...</> : "Отправить заявку"}
                    </button>
                    <p className="mt-2 text-xs text-slate-500 text-center">Нажимая «Отправить», вы соглашаетесь с политикой обработки персональных данных</p>
                  </div>
                </div>
              )}
            </form>

            <div className="lg:col-span-2 space-y-3">
              <div className="rounded-2xl border border-slate-200 bg-white p-6">
                <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">Офис в Санкт-Петербурге</div>
                <div className="mt-3 space-y-3 text-sm">
                  <div className="flex items-start gap-2 text-slate-700">
                    <MapPin className="h-4 w-4 text-blue-600 mt-0.5 flex-shrink-0" />
                    <span>198334, г. Санкт-Петербург, пр. Ветеранов, д. 140, офис 1</span>
                  </div>
                  <a href="tel:+78129453143" className="flex items-center gap-2 text-slate-700 hover:text-blue-600">
                    <Phone className="h-4 w-4 text-blue-600" /> +7 (812) 945-31-43
                  </a>
                  <a href="https://wa.me/79219453143" target="_blank" rel="noopener noreferrer" className="flex items-center gap-2 text-slate-700 hover:text-blue-600">
                    <MessageSquare className="h-4 w-4 text-green-600" /> WhatsApp / Telegram: +7 (921) 945-31-43
                  </a>
                  <a href="mailto:info@mastersoftware.ru" className="flex items-center gap-2 text-slate-700 hover:text-blue-600">
                    <Mail className="h-4 w-4 text-blue-600" /> info@mastersoftware.ru
                  </a>
                </div>
                <div className="mt-4 pt-4 border-t border-slate-100 flex items-start gap-2 text-sm text-slate-600">
                  <Clock className="h-4 w-4 text-slate-400 mt-0.5" />
                  <div>
                    <div>Пн–Пт: 10:00–19:00 МСК</div>
                    <div>Сб–Вс: 10:00–17:00 МСК (только мессенджеры)</div>
                  </div>
                </div>
              </div>

              <div className="rounded-2xl overflow-hidden border border-slate-200">
                <iframe
                  src="https://yandex.ru/map-widget/v1/?ll=30.250000%2C59.860000&mode=search&text=Санкт-Петербург%2C%20проспект%20Ветеранов%20140&z=17"
                  width="100%"
                  height="320"
                  frameBorder="0"
                  allowFullScreen
                  className="w-full"
                  title="Офис PROFESSIONAL SOFTWARE в Санкт-Петербурге"
                />
              </div>

              <div className="rounded-2xl border border-blue-200 bg-blue-50 p-5 flex items-start gap-3">
                <div className="h-10 w-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                  <MessageSquare className="h-5 w-5" />
                </div>
                <div>
                  <div className="font-bold text-slate-900">Задайте вопрос в мессенджере</div>
                  <div className="text-xs text-slate-600 mt-1">Среднее время ответа — 5 минут</div>
                  <div className="mt-3 flex gap-2">
                    <a href="https://t.me/professionalsoftware" target="_blank" rel="noopener noreferrer" className="h-8 px-3 rounded-lg bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 inline-flex items-center">
                      Telegram
                    </a>
                    <a href="https://wa.me/79219453143" target="_blank" rel="noopener noreferrer" className="h-8 px-3 rounded-lg bg-green-600 text-white text-xs font-bold hover:bg-green-700 inline-flex items-center">
                      WhatsApp
                    </a>
                    <a href="https://vk.com/professionalsoftware" target="_blank" rel="noopener noreferrer" className="h-8 px-3 rounded-lg bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 inline-flex items-center">
                      VK
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
