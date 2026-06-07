import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Shield, Cloud, Lock, HardDrive, Mail, ChevronLeft, ChevronRight, Download, Star } from "lucide-react";
import { useProducts } from "../hooks/useData";
import { serverPath } from "../utils/paths";

const productIcons: Record<string, any> = {
  "nimbus-guard-pro": Shield,
  "nimbus-clean-utility": Shield,
  "nimbus-vault-cloud": Cloud,
  "nimbus-backup-enterprise": HardDrive,
  "nimbus-mail-server": Mail,
  "nimbus-vpn-business": Lock,
};

const productGradients: Record<string, string> = {
  "nimbus-guard-pro": "from-blue-600 to-blue-700",
  "nimbus-clean-utility": "from-emerald-500 to-cyan-600",
  "nimbus-vault-cloud": "from-blue-600 to-indigo-700",
  "nimbus-backup-enterprise": "from-blue-700 to-indigo-800",
  "nimbus-mail-server": "from-blue-500 to-blue-700",
  "nimbus-vpn-business": "from-sky-500 to-blue-700",
};

export default function HeroProductCarousel() {
  const products = useProducts();
  const [index, setIndex] = useState(0);
  const items = products.slice(0, 6);
  const len = items.length || 1;

  useEffect(() => {
    if (items.length < 2) return;
    const id = setInterval(() => setIndex(p => (p + 1) % len), 4500);
    return () => clearInterval(id);
  }, [items.length, len]);

  const prev = () => setIndex(p => (p - 1 + len) % len);
  const next = () => setIndex(p => (p + 1) % len);

  if (items.length === 0) {
    return (
      <div className="rounded-2xl border border-slate-200 bg-white shadow-2xl p-10 text-center">
        <div className="text-slate-400 text-sm">Загрузка продуктов...</div>
      </div>
    );
  }

  return (
    <div className="rounded-2xl border border-slate-200 bg-white shadow-2xl overflow-hidden">
      {/* Шапка окна */}
      <div className="flex items-center gap-1.5 px-4 py-3 border-b border-slate-200 bg-slate-50">
        <div className="flex gap-1.5">
          <div className="h-3 w-3 rounded-full bg-red-400" />
          <div className="h-3 w-3 rounded-full bg-yellow-400" />
          <div className="h-3 w-3 rounded-full bg-green-400" />
        </div>
          <div className="ml-2 flex-1 h-6 rounded-md bg-white border border-slate-200 flex items-center px-3 text-xs text-slate-500 truncate">
            {items[index]?.name || ""}
          </div>
      </div>

      <div className="p-6 bg-gradient-to-br from-blue-50/50 to-white relative">
        {/* Прогресс-бар */}
        <div className="flex items-center gap-1 mb-4">
          {items.map((_, i) => (
            <button
              key={i}
              onClick={() => setIndex(i)}
              className="h-1 flex-1 rounded-full transition-all"
              style={{
                backgroundColor: i === index ? "var(--ps-primary, #0056D2)" : "#cbd5e1",
              }}
              aria-label={`Продукт ${i + 1}`}
            />
          ))}
        </div>

        {/* Жёсткая высота слайдера — все слайды одинакового размера, без «дёрганья» */}
        <div className="relative" style={{ height: "320px" }}>
          {items.map((p, i) => {
            const I = productIcons[p.slug] || Shield;
            const grad = productGradients[p.slug] || "from-blue-600 to-blue-700";
            return (
              <div
                key={p.slug}
                className="absolute inset-0 transition-opacity duration-500 flex flex-col"
                style={{ opacity: i === index ? 1 : 0, pointerEvents: i === index ? "auto" : "none" }}
              >
                {/* Иконка-заголовок — фиксированная высота */}
                <div className={`h-28 w-full rounded-xl bg-gradient-to-br ${grad} flex items-center justify-center relative overflow-hidden flex-shrink-0`}>
                  {p.coverImage ? (
                    <img src={serverPath(p.coverImage)} alt={p.name} className="absolute inset-0 w-full h-full object-contain p-1" />
                  ) : (
                    <>
                      <div className="absolute inset-0 grid-pattern opacity-30" />
                      <I className="h-14 w-14 text-white relative" strokeWidth={1.5} />
                    </>
                  )}
                </div>

                {/* Бейдж + рейтинг — фиксированная высота строки */}
                <div className="mt-3 flex items-center gap-1 h-6 flex-shrink-0">
                  {p.badge ? (
                    <span className="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-orange-100 text-orange-700">
                      {p.badge}
                    </span>
                  ) : (
                    <span className="text-[10px] text-transparent">.</span>
                  )}
                  <div className="flex items-center gap-0.5 ml-auto">
                    <Star className="h-3 w-3 fill-orange-500 text-orange-500" />
                    <span className="text-xs font-semibold text-slate-900">{p.rating}</span>
                  </div>
                </div>

                {/* Название — одна строка */}
                <h3 className="text-base font-extrabold text-slate-900 truncate flex-shrink-0">{p.name}</h3>

                {/* Описание — ровно 2 строки */}
                <p
                  className="mt-1 text-xs text-slate-600 flex-shrink-0"
                  style={{
                    display: "-webkit-box",
                    WebkitLineClamp: 2,
                    WebkitBoxOrient: "vertical",
                    overflow: "hidden",
                    height: "32px",
                  }}
                >
                  {p.tagline}
                </p>

                {/* Цена + кнопка — всегда внизу через mt-auto */}
                <div className="mt-auto flex items-end justify-between pt-2 flex-shrink-0">
                  <div className="h-12 flex flex-col justify-end">
                    {p.oldPrice ? (
                      <div className="text-xs text-slate-400 line-through leading-none">{p.oldPrice.toLocaleString("ru-RU")} ₽</div>
                    ) : (
                      <div className="text-xs leading-none text-transparent">.</div>
                    )}
                    <div className="text-xl font-extrabold text-slate-900 leading-tight mt-1">{p.price.toLocaleString("ru-RU")} ₽</div>
                  </div>
                  <Link
                    to={"/products/" + p.slug}
                    className="inline-flex items-center gap-1.5 h-9 px-4 rounded-lg text-white text-sm font-semibold transition flex-shrink-0"
                    style={{ backgroundColor: "var(--ps-primary, #0056D2)" }}
                    onMouseEnter={e => (e.currentTarget.style.backgroundColor = "var(--ps-dark, #0043A3)")}
                    onMouseLeave={e => (e.currentTarget.style.backgroundColor = "var(--ps-primary, #0056D2)")}
                  >
                    <Download className="h-3.5 w-3.5" /> Подробнее
                  </Link>
                </div>
              </div>
            );
          })}
        </div>

        {/* Индикатор + стрелки внизу */}
        <div className="mt-4 flex items-center justify-center gap-4">
          <button
            onClick={prev}
            aria-label="Предыдущий"
            className="h-8 w-8 rounded-full border border-slate-200 bg-white flex items-center justify-center hover:bg-slate-50 transition"
          >
            <ChevronLeft className="h-4 w-4 text-slate-700" />
          </button>
          <div className="text-xs text-slate-500">
            {index + 1} из {items.length}
          </div>
          <button
            onClick={next}
            aria-label="Следующий"
            className="h-8 w-8 rounded-full border border-slate-200 bg-white flex items-center justify-center hover:bg-slate-50 transition"
          >
            <ChevronRight className="h-4 w-4 text-slate-700" />
          </button>
        </div>
      </div>
    </div>
  );
}
