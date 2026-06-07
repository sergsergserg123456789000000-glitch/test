import { useState } from "react";
import { Link } from "react-router-dom";
import { Check, X, Shield, Calculator, ChevronRight, Star, Sparkles, ArrowRight } from "lucide-react";
import { useProducts } from "../hooks/useData";
import { cn } from "../utils/cn";

const plans = [
  {
    name: "Free",
    price: 0,
    description: "Базовый антивирус для домашнего использования",
    features: [
      { yes: true, label: "Защита от вирусов и шпионского ПО" },
      { yes: true, label: "1 устройство" },
      { yes: true, label: "Обновления баз" },
      { yes: false, label: "AI-детектор угроз" },
      { yes: false, label: "Защита платежей" },
      { yes: false, label: "Брандмауэр" },
      { yes: false, label: "Приоритетная поддержка" },
    ],
    cta: "Скачать бесплатно",
    style: "outline" as const,
  },
  {
    name: "Pro",
    price: 2490,
    period: "/год",
    popular: true,
    description: "Полная защита для одного ПК",
    features: [
      { yes: true, label: "Все из Free" },
      { yes: true, label: "AI-детектор угроз" },
      { yes: true, label: "Защита платежей" },
      { yes: true, label: "Брандмауэр" },
      { yes: true, label: "1 устройство" },
      { yes: true, label: "Email-поддержка" },
      { yes: false, label: "Семейная защита" },
    ],
    cta: "Купить Pro",
    style: "primary" as const,
  },
  {
    name: "Family",
    price: 4490,
    period: "/год",
    description: "Защита всей семьи — до 5 устройств",
    features: [
      { yes: true, label: "Все из Pro" },
      { yes: true, label: "5 устройств" },
      { yes: true, label: "Родительский контроль" },
      { yes: true, label: "Защита веб-камеры" },
      { yes: true, label: "VPN 500 МБ/день" },
      { yes: true, label: "Резервное копирование 50 ГБ" },
      { yes: true, label: "Приоритетная поддержка 24/7" },
    ],
    cta: "Купить Family",
    style: "outline" as const,
  },
];

export default function Pricing() {
  const products = useProducts();
  const [seats, setSeats] = useState(10);
  const [years, setYears] = useState(1);

  const baseEnterprise = 1990;
  const enterprisePrice = baseEnterprise * seats * years * 0.85;

  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12 text-center">
          <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
            <Sparkles className="h-3.5 w-3.5" /> Скидка 30% на годовую подписку
          </div>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">Простые и честные цены</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl mx-auto">
            Выберите подходящий тариф. Без скрытых платежей, бесплатный trial 30 дней.
          </p>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-3 gap-6">
            {plans.map(plan => (
              <div
                key={plan.name}
                className={cn(
                  "rounded-2xl p-8 border-2 flex flex-col",
                  plan.popular ? "border-blue-600 shadow-xl shadow-blue-500/10 bg-gradient-to-b from-blue-50/30 to-white" : "border-slate-200 bg-white"
                )}
              >
                {plan.popular && (
                  <div className="self-start mb-4 inline-flex items-center gap-1 rounded-full bg-blue-600 text-white text-xs font-bold px-2.5 py-0.5">
                    <Star className="h-3 w-3 fill-white" /> Популярный
                  </div>
                )}
                <h3 className="text-2xl font-extrabold text-slate-900">{plan.name}</h3>
                <p className="mt-1 text-sm text-slate-500">{plan.description}</p>
                <div className="mt-6 flex items-baseline gap-1">
                  <span className="text-5xl font-extrabold text-slate-900">{plan.price.toLocaleString("ru-RU")}</span>
                  <span className="text-slate-500 font-medium">₽{plan.period || ""}</span>
                </div>
                <Link
                  to="/checkout"
                  className={cn(
                    "mt-6 inline-flex items-center justify-center gap-2 h-12 rounded-xl font-semibold transition",
                    plan.style === "primary"
                      ? "bg-blue-600 text-white hover:bg-blue-700"
                      : "border border-slate-200 text-slate-900 hover:border-blue-300"
                  )}
                >
                  {plan.cta} <ArrowRight className="h-4 w-4" />
                </Link>
                <ul className="mt-6 space-y-3 flex-1">
                  {plan.features.map(f => (
                    <li key={f.label} className="flex items-start gap-2 text-sm">
                      {f.yes ? (
                        <Check className="h-4 w-4 text-blue-600 flex-shrink-0 mt-0.5" />
                      ) : (
                        <X className="h-4 w-4 text-slate-300 flex-shrink-0 mt-0.5" />
                      )}
                      <span className={f.yes ? "text-slate-700" : "text-slate-400 line-through"}>{f.label}</span>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Calculator */}
      <section className="py-20 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div>
              <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                <Calculator className="h-3.5 w-3.5" /> Калькулятор
              </div>
              <h2 className="mt-3 text-3xl sm:text-4xl font-extrabold text-slate-900">Рассчитайте стоимость для бизнеса</h2>
              <p className="mt-3 text-slate-600">Корпоративные тарифы со скидкой от объёма. От 15% скидки при покупке от 10 лицензий.</p>

              <div className="mt-8 space-y-6">
                <div>
                  <div className="flex items-center justify-between mb-2">
                    <label className="text-sm font-semibold text-slate-900">Количество лицензий</label>
                    <span className="text-2xl font-extrabold text-blue-600">{seats}</span>
                  </div>
                  <input
                    type="range"
                    min={5}
                    max={500}
                    step={5}
                    value={seats}
                    onChange={e => setSeats(parseInt(e.target.value))}
                    className="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                  />
                  <div className="mt-1 flex justify-between text-xs text-slate-500">
                    <span>5</span><span>500</span>
                  </div>
                </div>
                <div>
                  <div className="flex items-center justify-between mb-2">
                    <label className="text-sm font-semibold text-slate-900">Срок лицензии</label>
                    <span className="text-2xl font-extrabold text-blue-600">{years} {years === 1 ? "год" : years < 5 ? "года" : "лет"}</span>
                  </div>
                  <div className="grid grid-cols-3 gap-2">
                    {[1, 2, 3].map(y => (
                      <button
                        key={y}
                        onClick={() => setYears(y)}
                        className={cn(
                          "h-10 rounded-lg text-sm font-semibold border transition",
                          years === y ? "bg-blue-600 text-white border-blue-600" : "bg-white text-slate-700 border-slate-200 hover:border-blue-300"
                        )}
                      >
                        {y} {y === 1 ? "год" : "года"}
                      </button>
                    ))}
                  </div>
                </div>
              </div>
            </div>

            <div className="rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-white p-8">
              <div className="text-sm font-semibold text-blue-600 uppercase tracking-wider">Ваш расчёт</div>
              <div className="mt-4 space-y-3 text-sm">
                <div className="flex items-center justify-between">
                  <span className="text-slate-600">Базовая цена</span>
                  <span className="font-semibold">{baseEnterprise.toLocaleString("ru-RU")} ₽ × {seats}</span>
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-slate-600">Срок</span>
                  <span className="font-semibold">× {years} {years === 1 ? "год" : "года"}</span>
                </div>
                <div className="flex items-center justify-between text-green-600">
                  <span>Скидка за объём (15%)</span>
                  <span className="font-semibold">−15%</span>
                </div>
                <div className="pt-3 border-t border-blue-200 flex items-end justify-between">
                  <span className="text-slate-600">Итого</span>
                  <div className="text-right">
                    <div className="text-3xl font-extrabold text-slate-900">{enterprisePrice.toLocaleString("ru-RU")} ₽</div>
                    <div className="text-xs text-slate-500">{(enterprisePrice / seats).toFixed(0)} ₽ за лицензию</div>
                  </div>
                </div>
              </div>
              <Link to="/contact" className="mt-6 w-full h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
                Запросить КП
              </Link>
              <div className="mt-3 text-center text-xs text-slate-500">С НДС · Без скрытых платежей</div>
            </div>
          </div>
        </div>
      </section>

      {/* Comparison table */}
      <section className="py-20">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-extrabold text-slate-900 text-center">Сравнение продуктов</h2>
          <p className="mt-2 text-slate-600 text-center">Подробное сравнение всех продуктов линейки</p>

          <div className="mt-10 overflow-hidden rounded-2xl border border-slate-200">
            <table className="w-full text-sm">
              <thead className="bg-slate-50">
                <tr>
                  <th className="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Продукт</th>
                  <th className="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Категория</th>
                  <th className="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Цена</th>
                  <th className="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-500">Рейтинг</th>
                  <th className="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-slate-500">Действие</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-200">
                {products.map(p => (
                  <tr key={p.id} className="hover:bg-slate-50">
                    <td className="px-6 py-4">
                      <Link to={`/products/${p.slug}`} className="flex items-center gap-3">
                        <div className="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white"><Shield className="h-4 w-4" /></div>
                        <div>
                          <div className="font-bold text-slate-900">{p.name}</div>
                          <div className="text-xs text-slate-500 line-clamp-1">{p.tagline}</div>
                        </div>
                      </Link>
                    </td>
                    <td className="px-6 py-4 text-slate-600">{p.category}</td>
                    <td className="px-6 py-4 font-bold text-slate-900">{p.price.toLocaleString("ru-RU")} ₽</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-1">
                        <Star className="h-3.5 w-3.5 fill-orange-500 text-orange-500" />
                        <span className="font-semibold">{p.rating}</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 text-right">
                      <Link to={`/products/${p.slug}`} className="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-semibold">
                        Подробнее <ChevronRight className="h-4 w-4" />
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="py-20 bg-slate-50">
        <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-extrabold text-slate-900 text-center">Частые вопросы</h2>
          <div className="mt-10 space-y-3">
            {[
              { q: "Какие способы оплаты доступны?", a: "Карты Visa/MC/МИР, СБП, ЮMoney, оплата по счёту для юрлиц, криптовалюта." },
              { q: "Можно ли вернуть деньги?", a: "Да, в течение 30 дней с момента покупки — без объяснения причин." },
              { q: "Что входит в trial?", a: "Полный функционал выбранного продукта на 30 дней. Никаких ограничений." },
              { q: "Есть ли скидки для образования?", a: "Да, до 70% для школ, ВУЗов и некоммерческих организаций. Запросите КП." },
            ].map(f => (
              <details key={f.q} className="group rounded-xl border border-slate-200 bg-white p-5">
                <summary className="cursor-pointer font-semibold text-slate-900 flex items-center justify-between">
                  {f.q}
                  <ChevronRight className="h-4 w-4 group-open:rotate-90 transition" />
                </summary>
                <p className="mt-3 text-sm text-slate-600">{f.a}</p>
              </details>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
