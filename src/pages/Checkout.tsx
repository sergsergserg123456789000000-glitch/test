import { useState } from "react";
import { Link } from "react-router-dom";
import { Shield, CreditCard, Check, Lock, ArrowRight, Loader2, ChevronRight } from "lucide-react";
import { useProducts } from "../hooks/useData";
import { cn } from "../utils/cn";

export default function Checkout() {
  const products = useProducts();
  const product = products[0];
  const [payment, setPayment] = useState<"card" | "sbp" | "invoice">("card");
  const [loading, setLoading] = useState(false);
  const [done, setDone] = useState(false);
  const [seats, setSeats] = useState(1);
  const [years, setYears] = useState(1);

  if (!product) {
    return (
      <div className="mx-auto max-w-3xl px-4 py-20 text-center">
        <h1 className="text-2xl font-bold text-slate-900">Загрузка...</h1>
      </div>
    );
  }

  const price = product.price * seats * years;

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => {
      setLoading(false);
      setDone(true);
    }, 1500);
  };

  return (
    <>
      <div className="bg-slate-50 border-b border-slate-200">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
          <nav className="text-sm text-slate-500 flex items-center gap-1.5">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <Link to="/products" className="hover:text-blue-600">Продукты</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <Link to={`/products/${product.slug}`} className="hover:text-blue-600">{product.name}</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">Оформление</span>
          </nav>
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <h1 className="text-3xl font-extrabold text-slate-900">Оформление заказа</h1>

        {done ? (
          <div className="mt-8 max-w-2xl mx-auto rounded-2xl border-2 border-green-200 bg-green-50 p-8 text-center">
            <div className="h-16 w-16 mx-auto rounded-full bg-green-100 flex items-center justify-center">
              <Check className="h-8 w-8 text-green-600" />
            </div>
            <h2 className="mt-4 text-2xl font-extrabold text-slate-900">Заказ оплачен</h2>
            <p className="mt-2 text-slate-600">Спасибо! Лицензионный ключ отправлен на ваш email. Также вы можете найти его в личном кабинете.</p>
            <div className="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
              <Link to="/account" className="h-11 px-5 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                В личный кабинет <ArrowRight className="h-4 w-4" />
              </Link>
              <Link to="/products" className="h-11 px-5 inline-flex items-center justify-center gap-2 rounded-lg border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">
                Продолжить покупки
              </Link>
            </div>
          </div>
        ) : (
          <div className="mt-8 grid lg:grid-cols-3 gap-6">
            <form onSubmit={submit} className="lg:col-span-2 space-y-4">
              <div className="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 className="text-lg font-bold text-slate-900">1. Тариф</h2>
                <div className="mt-4 grid sm:grid-cols-3 gap-3">
                  {[1, 3, 5].map(s => (
                    <button
                      key={s}
                      type="button"
                      onClick={() => setSeats(s)}
                      className={cn("rounded-xl border-2 p-4 text-left transition", seats === s ? "border-blue-600 bg-blue-50/30" : "border-slate-200 hover:border-slate-300")}
                    >
                      <div className="text-xs font-semibold text-slate-500 uppercase">{s} {s === 1 ? "устройство" : s < 5 ? "устройства" : "устройств"}</div>
                      <div className="mt-1 text-xl font-extrabold text-slate-900">{(product.price * s).toLocaleString("ru-RU")} ₽</div>
                      {s >= 3 && <div className="text-xs text-green-600 font-bold">Скидка {s === 3 ? "10%" : "20%"}</div>}
                    </button>
                  ))}
                </div>
                <div className="mt-3 flex gap-2">
                  {[1, 2, 3].map(y => (
                    <button
                      key={y}
                      type="button"
                      onClick={() => setYears(y)}
                      className={cn("h-9 px-4 rounded-lg text-sm font-semibold border transition", years === y ? "bg-blue-600 text-white border-blue-600" : "bg-white border-slate-200 text-slate-700 hover:border-blue-300")}
                    >
                      {y} {y === 1 ? "год" : "года"}
                    </button>
                  ))}
                </div>
              </div>

              <div className="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 className="text-lg font-bold text-slate-900">2. Контактные данные</h2>
                <div className="mt-4 grid sm:grid-cols-2 gap-3">
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Имя *</label>
                    <input required className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none" placeholder="Иван" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Фамилия *</label>
                    <input required className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none" placeholder="Петров" />
                  </div>
                  <div className="sm:col-span-2">
                    <label className="text-sm font-semibold text-slate-900">Email *</label>
                    <input type="email" required className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none" placeholder="you@email.com" />
                  </div>
                  {payment === "invoice" && (
                    <>
                      <div>
                        <label className="text-sm font-semibold text-slate-900">Компания</label>
                        <input className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" placeholder="ООО «Ромашка»" />
                      </div>
                      <div>
                        <label className="text-sm font-semibold text-slate-900">ИНН</label>
                        <input className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" placeholder="7701234567" />
                      </div>
                    </>
                  )}
                </div>
              </div>

              <div className="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 className="text-lg font-bold text-slate-900">3. Способ оплаты</h2>
                <div className="mt-4 grid grid-cols-3 gap-2">
                  {[
                    { id: "card" as const, label: "Картой" },
                    { id: "sbp" as const, label: "СБП" },
                    { id: "invoice" as const, label: "По счёту" },
                  ].map(p => (
                    <button
                      key={p.id}
                      type="button"
                      onClick={() => setPayment(p.id)}
                      className={cn("h-10 rounded-lg text-sm font-semibold border transition", payment === p.id ? "bg-blue-600 text-white border-blue-600" : "bg-white border-slate-200 text-slate-700 hover:border-blue-300")}
                    >
                      {p.label}
                    </button>
                  ))}
                </div>
                {payment === "card" && (
                  <div className="mt-4 space-y-3">
                    <div>
                      <label className="text-sm font-semibold text-slate-900">Номер карты *</label>
                      <div className="mt-1 relative">
                        <CreditCard className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                        <input required placeholder="0000 0000 0000 0000" className="w-full h-10 pl-10 pr-3 rounded-lg border border-slate-200 text-sm font-mono focus:border-blue-500 outline-none" />
                      </div>
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                      <div>
                        <label className="text-sm font-semibold text-slate-900">MM / ГГ *</label>
                        <input required placeholder="12 / 28" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm font-mono focus:border-blue-500 outline-none" />
                      </div>
                      <div>
                        <label className="text-sm font-semibold text-slate-900">CVC *</label>
                        <input required placeholder="•••" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm font-mono focus:border-blue-500 outline-none" />
                      </div>
                    </div>
                  </div>
                )}
                {payment === "sbp" && (
                  <div className="mt-4 rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-slate-700">
                    После нажатия «Оплатить» вы получите QR-код для оплаты через приложение вашего банка.
                  </div>
                )}
                {payment === "invoice" && (
                  <div className="mt-4 rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-slate-700">
                    Счёт для оплаты будет отправлен на указанный email. Действует 5 рабочих дней.
                  </div>
                )}
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full h-12 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 transition inline-flex items-center justify-center gap-2 disabled:opacity-60 shadow-lg shadow-orange-500/20"
              >
                {loading ? <><Loader2 className="h-4 w-4 animate-spin" /> Обработка...</> : <><Lock className="h-4 w-4" /> Оплатить {price.toLocaleString("ru-RU")} ₽</>}
              </button>
              <p className="text-center text-xs text-slate-500">Нажимая «Оплатить», вы соглашаетесь с офертой. Данные защищены по 152-ФЗ.</p>
            </form>

            <aside className="lg:col-span-1">
              <div className="rounded-2xl border border-slate-200 bg-white p-6 sticky top-20">
                <h3 className="text-sm font-bold text-slate-500 uppercase tracking-wider">Ваш заказ</h3>
                <div className="mt-4 flex items-center gap-3">
                  <div className="h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white">
                    <Shield className="h-6 w-6" />
                  </div>
                  <div>
                    <div className="font-bold text-slate-900">{product.name}</div>
                    <div className="text-xs text-slate-500">Лицензия на {seats} {seats === 1 ? "устройство" : "устройств"}</div>
                  </div>
                </div>
                <div className="mt-6 space-y-2 text-sm">
                  <div className="flex justify-between"><span className="text-slate-500">Подписка</span><span className="text-slate-900">{product.price.toLocaleString("ru-RU")} ₽ × {seats}</span></div>
                  {seats >= 3 && (
                    <div className="flex justify-between text-green-600">
                      <span>Скидка</span>
                      <span>−{seats === 3 ? "10" : "20"}%</span>
                    </div>
                  )}
                  <div className="flex justify-between"><span className="text-slate-500">Срок</span><span className="text-slate-900">{years} {years === 1 ? "год" : "года"}</span></div>
                </div>
                <div className="mt-4 pt-4 border-t border-slate-200 flex items-end justify-between">
                  <span className="text-sm text-slate-500">Итого</span>
                  <div className="text-right">
                    <div className="text-2xl font-extrabold text-slate-900">{price.toLocaleString("ru-RU")} ₽</div>
                    <div className="text-xs text-slate-500">с НДС</div>
                  </div>
                </div>
                <div className="mt-4 pt-4 border-t border-slate-200 space-y-2 text-xs text-slate-500">
                  {["30 дней гарантия возврата", "Моментальная активация", "Поддержка 24/7", "Без скрытых платежей"].map(b => (
                    <div key={b} className="flex items-center gap-1.5"><Check className="h-3.5 w-3.5 text-green-600" /> {b}</div>
                  ))}
                </div>
              </div>
            </aside>
          </div>
        )}
      </div>
    </>
  );
}
