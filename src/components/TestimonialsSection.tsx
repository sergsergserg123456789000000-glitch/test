import { useState, useEffect } from "react";
import { Star, ChevronLeft, ChevronRight, Plus, X, Check, Loader2 } from "lucide-react";
import { serverPath } from "../utils/paths";

type Testimonial = { id: number; name: string; role: string; text: string; rating: number; avatar: string };

const FALLBACK: Testimonial[] = [
  { id: 1, name: "Алексей Морозов", role: "CTO, АльфаТех", text: "За 2 года использования NimbusGuard Pro не было ни одного инцидента с вирусами на 250+ рабочих станциях.", rating: 5, avatar: "А" },
  { id: 2, name: "Мария Соколова", role: "IT-директор, Северные сети", text: "Перешли с импортного решения за один weekend. Поддержка отвечает за минуты, а не дни.", rating: 5, avatar: "М" },
  { id: 3, name: "Дмитрий Иванов", role: "Фрилансер", text: "NimbusClean — лучшая утилита для очистки, что я пробовал. Ускорила мой старый ноутбук в 2 раза.", rating: 5, avatar: "Д" },
];

function Card({ t }: { t: Testimonial }) {
  return (
    <div className="rounded-2xl bg-white p-6 border border-slate-200 h-full flex flex-col">
      <div className="flex items-center gap-0.5">
        {[1, 2, 3, 4, 5].map(i => (
          <Star key={i} className={`h-4 w-4 ${i <= t.rating ? "fill-orange-500 text-orange-500" : "text-slate-300"}`} />
        ))}
      </div>
      <p className="mt-4 text-slate-700 leading-relaxed flex-1">«{t.text}»</p>
      <div className="mt-6 flex items-center gap-3">
        <div className="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold">
          {t.avatar}
        </div>
        <div>
          <div className="text-sm font-semibold text-slate-900">{t.name}</div>
          <div className="text-xs text-slate-500">{t.role}</div>
        </div>
      </div>
    </div>
  );
}

export default function TestimonialsSection({ label, title }: { label: string; title: string }) {
  const [items, setItems] = useState<Testimonial[]>(FALLBACK);
  const [page, setPage] = useState(0);
  const [perPage, setPerPage] = useState(3);
  const [modal, setModal] = useState(false);
  const [form, setForm] = useState({ name: "", role: "", text: "", rating: 5 });
  const [sending, setSending] = useState(false);
  const [done, setDone] = useState(false);

  useEffect(() => {
    fetch(serverPath("api/testimonials.php"))
      .then(r => r.json())
      .then(d => { if (d?.testimonials?.length) setItems(d.testimonials); })
      .catch(() => {});
  }, []);

  useEffect(() => {
    const calc = () => setPerPage(window.innerWidth < 768 ? 1 : 3);
    calc();
    window.addEventListener("resize", calc);
    return () => window.removeEventListener("resize", calc);
  }, []);

  const isCarousel = items.length > 3;
  const totalPages = Math.ceil(items.length / perPage);
  const visible = isCarousel ? items.slice(page * perPage, page * perPage + perPage) : items;

  const next = () => setPage(p => (p + 1) % totalPages);
  const prev = () => setPage(p => (p - 1 + totalPages) % totalPages);

  // Автопрокрутка карусели
  useEffect(() => {
    if (!isCarousel) return;
    const id = setInterval(() => setPage(p => (p + 1) % totalPages), 6000);
    return () => clearInterval(id);
  }, [isCarousel, totalPages]);

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setSending(true);
    fetch(serverPath("api/testimonials.php"), {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    })
      .then(r => r.json())
      .then(() => { setSending(false); setDone(true); })
      .catch(() => { setSending(false); setDone(true); });
  };

  return (
    <>
      <div className="text-center max-w-2xl mx-auto">
        <div className="text-sm font-bold text-blue-600 uppercase tracking-wider">{label}</div>
        <h2 className="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900">{title}</h2>
      </div>

      {isCarousel ? (
        <div className="mt-12 relative">
          {/* Стрелка ВЛЕВО — по бокам карусели, вертикально по центру */}
          <button
            onClick={prev}
            aria-label="Назад"
            style={{ position: "absolute", left: "-24px", top: "50%", transform: "translateY(-50%)", zIndex: 10 }}
            className="h-12 w-12 rounded-full border border-slate-200 bg-white shadow-lg items-center justify-center hover:bg-slate-50 hover:scale-110 transition flex"
          >
            <ChevronLeft className="h-6 w-6 text-slate-700" />
          </button>

          {/* Стрелка ВПРАВО — по бокам карусели */}
          <button
            onClick={next}
            aria-label="Вперёд"
            style={{ position: "absolute", right: "-24px", top: "50%", transform: "translateY(-50%)", zIndex: 10 }}
            className="h-12 w-12 rounded-full border border-slate-200 bg-white shadow-lg items-center justify-center hover:bg-slate-50 hover:scale-110 transition flex"
          >
            <ChevronRight className="h-6 w-6 text-slate-700" />
          </button>

          <div className="grid gap-6 px-10" style={{ gridTemplateColumns: `repeat(${perPage}, minmax(0, 1fr))` }}>
            {visible.map(t => <Card key={t.id} t={t} />)}
          </div>

          {/* Точки-индикаторы */}
          <div className="mt-8 flex items-center justify-center gap-3">
            <div className="flex gap-1.5">
              {Array.from({ length: totalPages }).map((_, i) => (
                <button
                  key={i}
                  onClick={() => setPage(i)}
                  aria-label={`Страница ${i + 1}`}
                  className="h-2 rounded-full transition-all"
                  style={{
                    width: i === page ? 24 : 8,
                    backgroundColor: i === page ? "var(--ps-primary, #0056D2)" : "#cbd5e1",
                  }}
                />
              ))}
            </div>
          </div>
        </div>
      ) : (
        <div className="mt-12 grid md:grid-cols-3 gap-6">
          {visible.map(t => <Card key={t.id} t={t} />)}
        </div>
      )}

      <div className="mt-10 text-center">
        <button
          onClick={() => { setModal(true); setDone(false); }}
          className="inline-flex items-center gap-2 h-11 px-6 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
        >
          <Plus className="h-4 w-4" /> Оставить отзыв
        </button>
      </div>

      {modal && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4" onClick={() => setModal(false)}>
          <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" />
          <div onClick={e => e.stopPropagation()} className="relative w-full max-w-md rounded-2xl bg-white shadow-2xl p-8">
            <button onClick={() => setModal(false)} className="absolute top-4 right-4 h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100">
              <X className="h-4 w-4" />
            </button>

            {done ? (
              <div className="text-center py-6">
                <div className="h-14 w-14 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                  <Check className="h-7 w-7 text-green-600" />
                </div>
                <h3 className="mt-4 text-xl font-bold text-slate-900">Спасибо за отзыв!</h3>
                <p className="mt-2 text-sm text-slate-600">Ваш отзыв отправлен на модерацию и появится после проверки.</p>
                <button onClick={() => setModal(false)} className="mt-6 h-10 px-5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Закрыть</button>
              </div>
            ) : (
              <>
                <h3 className="text-xl font-extrabold text-slate-900">Оставить отзыв</h3>
                <p className="mt-1 text-sm text-slate-500">Поделитесь вашим мнением о наших продуктах</p>
                <form onSubmit={submit} className="mt-5 space-y-3">
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Ваше имя *</label>
                    <input required value={form.name} onChange={e => setForm({ ...form, name: e.target.value })}
                      className="mt-1 w-full h-11 px-4 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none" placeholder="Иван Петров" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Должность / компания</label>
                    <input value={form.role} onChange={e => setForm({ ...form, role: e.target.value })}
                      className="mt-1 w-full h-11 px-4 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none" placeholder="CTO, Компания" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Оценка</label>
                    <div className="mt-1 flex gap-1">
                      {[1, 2, 3, 4, 5].map(i => (
                        <button key={i} type="button" onClick={() => setForm({ ...form, rating: i })}>
                          <Star className={`h-7 w-7 ${i <= form.rating ? "fill-orange-500 text-orange-500" : "text-slate-300"}`} />
                        </button>
                      ))}
                    </div>
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Отзыв *</label>
                    <textarea required rows={4} value={form.text} onChange={e => setForm({ ...form, text: e.target.value })}
                      className="mt-1 w-full p-3 rounded-xl border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none resize-none" placeholder="Ваш отзыв..." />
                  </div>
                  <button type="submit" disabled={sending}
                    className="w-full h-12 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-60">
                    {sending ? <><Loader2 className="h-4 w-4 animate-spin" /> Отправка...</> : "Отправить отзыв"}
                  </button>
                </form>
              </>
            )}
          </div>
        </div>
      )}
    </>
  );
}
