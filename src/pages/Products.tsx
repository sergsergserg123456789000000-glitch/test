import { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { Shield, Search, Star, Filter, ArrowRight, Download } from "lucide-react";
import { useProducts } from "../hooks/useData";
import { serverPath } from "../utils/paths";
import { cn } from "../utils/cn";

const categories = ["Все", "Безопасность", "Утилиты", "Облако", "Бизнес"] as const;

export default function Products() {
  const products = useProducts();
  const [cat, setCat] = useState<(typeof categories)[number]>("Все");
  const [q, setQ] = useState("");
  const [sort, setSort] = useState("popular");

  const filtered = useMemo(() => {
    let list = products;
    if (cat !== "Все") list = list.filter(p => p.category === cat);
    if (q) list = list.filter(p => p.name.toLowerCase().includes(q.toLowerCase()) || p.tagline.toLowerCase().includes(q.toLowerCase()));
    if (sort === "price-asc") list = [...list].sort((a, b) => a.price - b.price);
    if (sort === "price-desc") list = [...list].sort((a, b) => b.price - a.price);
    if (sort === "rating") list = [...list].sort((a, b) => b.rating - a.rating);
    return list;
  }, [cat, q, sort, products]);

  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
          <nav className="text-sm text-slate-500 flex items-center gap-2">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <span>/</span>
            <span className="text-slate-900">Продукты</span>
          </nav>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">Каталог продуктов</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl">
            6 профессиональных продуктов для защиты и оптимизации IT-инфраструктуры. Trial 30 дней, без привязки карты.
          </p>
        </div>
      </section>

      <section className="border-y border-slate-200 bg-white sticky top-16 z-30">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div className="relative flex-1 max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
              <input
                value={q}
                onChange={e => setQ(e.target.value)}
                placeholder="Поиск продукта..."
                className="w-full h-10 pl-10 pr-4 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
              />
            </div>
            <div className="flex items-center gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 lg:mx-0 lg:px-0">
              {categories.map(c => (
                <button
                  key={c}
                  onClick={() => setCat(c)}
                  className={cn(
                    "h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap transition border",
                    cat === c
                      ? "bg-blue-600 text-white border-blue-600"
                      : "bg-white text-slate-700 border-slate-200 hover:border-blue-300"
                  )}
                >
                  {c}
                </button>
              ))}
            </div>
            <select
              value={sort}
              onChange={e => setSort(e.target.value)}
              className="h-10 px-3 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-700 outline-none focus:border-blue-500"
            >
              <option value="popular">По популярности</option>
              <option value="rating">По рейтингу</option>
              <option value="price-asc">Сначала дешевле</option>
              <option value="price-desc">Сначала дороже</option>
            </select>
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          {filtered.length === 0 ? (
            <div className="text-center py-20">
              <Filter className="h-12 w-12 text-slate-300 mx-auto" />
              <p className="mt-4 text-slate-500">Ничего не найдено</p>
            </div>
          ) : (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {filtered.map(p => (
                <div key={p.id} className="group relative flex flex-col rounded-2xl border border-slate-200 bg-white p-6 hover:border-blue-300 hover:shadow-xl hover:shadow-blue-500/10 transition">
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
                  {p.coverImage ? (
                    <img src={serverPath(p.coverImage)} alt={p.name} className="h-16 w-full rounded-xl object-contain bg-slate-50 shadow-lg shadow-blue-500/20" />
                  ) : (
                    <div className="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/20">
                      <Shield className="h-6 w-6 text-white" />
                    </div>
                  )}
                  <div className="mt-4 text-xs font-semibold text-blue-600 uppercase tracking-wider">{p.category}</div>
                  <h3 className="mt-1 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">{p.name}</h3>
                  <p className="mt-1 text-sm text-slate-600 line-clamp-2">{p.tagline}</p>

                  <div className="mt-3 flex flex-wrap items-center gap-1.5 text-xs">
                    {p.os.slice(0, 3).map(o => (
                      <span key={o} className="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">{o}</span>
                    ))}
                  </div>

                  <div className="mt-3 flex items-center gap-1 text-xs text-slate-500">
                    <Star className="h-3.5 w-3.5 fill-orange-500 text-orange-500" />
                    <span className="font-semibold text-slate-900">{p.rating}</span>
                    <span>({p.reviews})</span>
                  </div>

                  <div className="mt-auto pt-4 flex items-center justify-between border-t border-slate-100">
                    <div>
                      {p.oldPrice && (
                        <div className="text-xs text-slate-400 line-through">{Number(p.oldPrice).toLocaleString("ru-RU")} ₽</div>
                      )}
                      <div className="text-xl font-extrabold text-slate-900">{p.price.toLocaleString("ru-RU")} ₽</div>
                    </div>
                    <div className="flex gap-1.5">
                      <Link to={`/products/${p.slug}`} className="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-200 hover:bg-slate-50 transition" title="Подробнее">
                        <ArrowRight className="h-4 w-4 text-slate-700" />
                      </Link>
                      <Link to="/checkout" className="h-9 px-3 inline-flex items-center gap-1 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                        <Download className="h-3.5 w-3.5" /> Купить
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </section>
    </>
  );
}
