import { Link, useParams } from "react-router-dom";
import { Shield, Star, Check, Download, Monitor, Cpu, HardDrive, MemoryStick, ChevronRight, ArrowRight, ShieldCheck, FileText, Globe } from "lucide-react";
import { useProductDetail, useProducts } from "../hooks/useData";
import { cn } from "../utils/cn";
import { useState } from "react";
import { serverPath } from "../utils/paths";

export default function ProductDetail() {
  const { slug } = useParams();
  const product = useProductDetail(slug);
  const products = useProducts();
  const [activeTab, setActiveTab] = useState<"overview" | "versions" | "changelog" | "requirements">("overview");
  const [activeScreenshot, setActiveScreenshot] = useState(0);

  if (!product) {
    return (
      <div className="mx-auto max-w-7xl px-4 py-20 text-center">
        <h1 className="text-3xl font-bold">Продукт не найден</h1>
        <Link to="/products" className="mt-4 inline-block text-blue-600">← К каталогу</Link>
      </div>
    );
  }

  const detectedOS = typeof navigator !== "undefined" && /Mac|iPhone|iPad/.test(navigator.userAgent) ? "macOS" : "Windows";

  return (
    <>
      {/* Breadcrumbs */}
      <div className="bg-slate-50 border-b border-slate-200">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-3">
          <nav className="text-sm text-slate-500 flex items-center gap-1.5">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <Link to="/products" className="hover:text-blue-600">Продукты</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">{product.name}</span>
          </nav>
        </div>
      </div>

      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid lg:grid-cols-2 gap-12 items-start">
            <div>
              <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                {product.category}
              </div>
              <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">{product.name}</h1>
              <p className="mt-3 text-lg text-slate-600">{product.tagline}</p>
              <div className="mt-4 flex items-center gap-3 text-sm">
                <div className="flex items-center gap-1">
                  <Star className="h-4 w-4 fill-orange-500 text-orange-500" />
                  <span className="font-bold text-slate-900">{product.rating}</span>
                </div>
                <span className="text-slate-500">{product.reviews} отзывов</span>
                {product.versions && product.versions.length > 0 && (
                  <>
                    <span className="text-slate-300">|</span>
                    <span className="text-slate-500">Версия {product.versions[0].version}</span>
                  </>
                )}
              </div>
              <p className="mt-6 text-slate-700 leading-relaxed">{product.description}</p>

              <div className="mt-6 grid sm:grid-cols-2 gap-3">
                {(product.features || []).slice(0, 6).map(f => (
                  <div key={f} className="flex items-start gap-2 text-sm">
                    <Check className="h-4 w-4 text-blue-600 flex-shrink-0 mt-0.5" />
                    <span className="text-slate-700">{f}</span>
                  </div>
                ))}
              </div>

              {/* Download block with OS detection */}
              <div className="mt-8 rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-white p-6">
                <div className="text-xs font-bold text-blue-600 uppercase tracking-wider">Скачать</div>
                <div className="mt-2 flex items-center gap-2 text-sm text-slate-600">
                  <Globe className="h-4 w-4" />
                  Мы определили вашу ОС: <strong className="text-slate-900">{detectedOS}</strong>
                </div>
                <div className="mt-4 flex flex-wrap gap-2">
                  {(product.os || []).map(os => (
                    <button
                      key={os}
                      className={cn(
                        "inline-flex items-center gap-1.5 h-10 px-4 rounded-lg text-sm font-semibold transition",
                        os === detectedOS
                          ? "bg-blue-600 text-white shadow-md shadow-blue-500/30"
                          : "bg-white border border-slate-200 text-slate-700 hover:border-blue-300"
                      )}
                    >
                      <Download className="h-4 w-4" /> {os}
                    </button>
                  ))}
                </div>
                {product.versions && product.versions.length > 0 && (
                  <div className="mt-4 flex items-center gap-3 text-xs text-slate-500">
                    <span>{product.versions[0].size}</span>
                    <span>·</span>
                    <span>Версия {product.versions[0].version}</span>
                    <span>·</span>
                    <span>{product.versions[0].date}</span>
                  </div>
                )}
                <Link
                  to="/checkout"
                  className="mt-5 w-full h-12 inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 text-white font-semibold hover:bg-orange-600 transition shadow-lg shadow-orange-500/20"
                >
                  Купить лицензию · от {product.price.toLocaleString("ru-RU")} ₽
                </Link>
                <div className="mt-2 text-center text-xs text-slate-500">Trial 30 дней · Без привязки карты</div>
              </div>
            </div>

            {/* Screenshots gallery */}
            <div>
              {product.screenshots && product.screenshots.length > 0 ? (
                <>
                  <div className={cn("aspect-[4/3] rounded-2xl shadow-2xl flex items-center justify-center relative overflow-hidden bg-white border border-slate-200")}>
                    {product.screenshots[activeScreenshot]?.path ? (
                      <img
                        src={serverPath(product.screenshots[activeScreenshot].path!)}
                        alt={product.screenshots[activeScreenshot]?.title || "Скриншот"}
                        className="w-full h-full object-contain bg-slate-100"
                      />
                    ) : (
                      <div className={cn("w-full h-full bg-gradient-to-br flex items-center justify-center", product.screenshots[activeScreenshot]?.color || "from-blue-500 to-blue-700")}>
                        <div className="absolute inset-0 grid-pattern opacity-30" />
                        <div className="relative text-center text-white">
                          <Shield className="h-20 w-20 mx-auto opacity-80" />
                          <div className="mt-4 text-2xl font-bold">{product.screenshots[activeScreenshot]?.title || "Скриншот"}</div>
                        </div>
                      </div>
                    )}
                    <div className="absolute bottom-4 left-4 right-4 flex items-center gap-2 px-4 py-2 rounded-lg bg-white/20 backdrop-blur text-white text-sm">
                      <Monitor className="h-4 w-4" />
                      <span>Скриншот {activeScreenshot + 1} из {product.screenshots.length}</span>
                    </div>
                  </div>
                  <div className="mt-4 grid grid-cols-4 gap-2">
                    {product.screenshots.map((s, i) => (
                      <button
                        key={i}
                        onClick={() => setActiveScreenshot(i)}
                        className={cn(
                          "aspect-[4/3] rounded-lg overflow-hidden flex items-center justify-center text-xs font-bold text-white transition border-2",
                          activeScreenshot === i ? "border-blue-600" : "border-transparent opacity-70 hover:opacity-100"
                        )}
                      >
                        {s.path ? (
                          <img src={serverPath(s.path)} alt="" className="w-full h-full object-contain bg-slate-50" />
                        ) : (
                          <div className={cn("w-full h-full bg-gradient-to-br flex items-center justify-center", s.color || "from-blue-500 to-blue-700")}>{i + 1}</div>
                        )}
                      </button>
                    ))}
                  </div>
                </>
              ) : product.coverImage ? (
                <div className="aspect-[4/3] rounded-2xl shadow-2xl overflow-hidden bg-white border border-slate-200">
                  <img src={serverPath(product.coverImage)} alt={product.name} className="w-full h-full object-contain bg-slate-50" />
                </div>
              ) : (
                <div className="aspect-[4/3] rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-2xl flex items-center justify-center relative overflow-hidden">
                  <div className="absolute inset-0 grid-pattern opacity-30" />
                  <div className="relative text-center text-white">
                    <Shield className="h-24 w-24 mx-auto opacity-80" />
                    <div className="mt-4 text-2xl font-bold">{product.name}</div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* TABS */}
      <section className="border-t border-slate-200 bg-white sticky top-16 z-20">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex gap-1 overflow-x-auto no-scrollbar">
            {[
              { id: "overview", label: "Обзор" },
              { id: "versions", label: "Версии" },
              { id: "changelog", label: "История изменений" },
              { id: "requirements", label: "Системные требования" },
            ].map(t => (
              <button
                key={t.id}
                onClick={() => setActiveTab(t.id as any)}
                className={cn(
                  "px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 transition",
                  activeTab === t.id
                    ? "border-blue-600 text-blue-600"
                    : "border-transparent text-slate-600 hover:text-slate-900"
                )}
              >
                {t.label}
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          {activeTab === "overview" && (
            <div className="grid lg:grid-cols-3 gap-8">
              <div className="lg:col-span-2 prose prose-slate max-w-none">
                <h2 className="text-2xl font-bold text-slate-900">О продукте</h2>
                <p className="text-slate-700 leading-relaxed">{product.description}</p>
                <h3 className="text-xl font-bold text-slate-900 mt-8">Ключевые возможности</h3>
                <ul className="space-y-2">
                  {(product.features || []).map(f => (
                    <li key={f} className="flex items-start gap-2 text-slate-700">
                      <Check className="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                      {f}
                    </li>
                  ))}
                </ul>
              </div>
              <aside className="space-y-4">
                <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                  <div className="text-xs font-bold text-blue-600 uppercase">Безопасность</div>
                  <div className="mt-2 flex items-center gap-2 text-sm text-slate-700">
                    <ShieldCheck className="h-4 w-4 text-green-600" /> Сертифицировано ФСТЭК
                  </div>
                </div>
                <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                  <div className="text-xs font-bold text-blue-600 uppercase">Поддержка</div>
                  <div className="mt-2 text-sm text-slate-700">24/7 на русском языке</div>
                </div>
                <div className="rounded-2xl border border-slate-200 p-6 bg-slate-50">
                  <div className="text-xs font-bold text-blue-600 uppercase">Документация</div>
                  <Link to="/support" className="mt-2 inline-flex items-center gap-1 text-sm text-blue-600 hover:underline">
                    <FileText className="h-4 w-4" /> База знаний →
                  </Link>
                </div>
              </aside>
            </div>
          )}

          {activeTab === "versions" && (
            <div className="overflow-hidden rounded-2xl border border-slate-200">
              <table className="w-full text-sm">
                <thead className="bg-slate-50">
                  <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th className="px-6 py-3">Версия</th>
                    <th className="px-6 py-3">Дата выпуска</th>
                    <th className="px-6 py-3">Размер</th>
                    <th className="px-6 py-3">Статус</th>
                    <th className="px-6 py-3 text-right">Скачать</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-200">
                  {(product.versions || []).map(v => (
                    <tr key={v.version} className="hover:bg-slate-50">
                      <td className="px-6 py-4 font-bold text-slate-900">v{v.version}</td>
                      <td className="px-6 py-4 text-slate-600">{v.date}</td>
                      <td className="px-6 py-4 text-slate-600">{v.size}</td>
                      <td className="px-6 py-4">
                        {v.isCurrent ? (
                          <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Current</span>
                        ) : (
                          <span className="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold">Legacy</span>
                        )}
                      </td>
                      <td className="px-6 py-4 text-right">
                        <button className="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-semibold text-sm">
                          <Download className="h-4 w-4" /> Скачать
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {activeTab === "changelog" && (
            <div className="space-y-6">
              {(product.changelog || []).map(c => (
                <div key={c.version} className="rounded-2xl border border-slate-200 p-6">
                  <div className="flex items-center gap-3">
                    <span className="px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-xs font-bold">v{c.version}</span>
                    <span className="text-sm text-slate-500">{c.date}</span>
                  </div>
                  <ul className="mt-4 space-y-2">
                    {(c.changes || []).map((ch, i) => (
                      <li key={i} className="flex items-start gap-2 text-sm text-slate-700">
                        <span className="text-blue-600 mt-1">•</span> {ch}
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          )}

          {activeTab === "requirements" && (
            <div className="rounded-2xl border border-slate-200 p-6 max-w-2xl">
              <h3 className="text-lg font-bold text-slate-900">Минимальные системные требования</h3>
              <div className="mt-6 space-y-4">
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center"><Monitor className="h-5 w-5 text-blue-600" /></div>
                  <div><div className="text-xs text-slate-500">ОС</div><div className="text-sm font-semibold text-slate-900">{product.requirements?.os || "—"}</div></div>
                </div>
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center"><Cpu className="h-5 w-5 text-blue-600" /></div>
                  <div><div className="text-xs text-slate-500">Процессор</div><div className="text-sm font-semibold text-slate-900">{product.requirements?.cpu || "—"}</div></div>
                </div>
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center"><MemoryStick className="h-5 w-5 text-blue-600" /></div>
                  <div><div className="text-xs text-slate-500">Оперативная память</div><div className="text-sm font-semibold text-slate-900">{product.requirements?.ram || "—"}</div></div>
                </div>
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center"><HardDrive className="h-5 w-5 text-blue-600" /></div>
                  <div><div className="text-xs text-slate-500">Свободное место</div><div className="text-sm font-semibold text-slate-900">{product.requirements?.disk || "—"}</div></div>
                </div>
              </div>
            </div>
          )}
        </div>
      </section>

      {/* Related */}
      <section className="py-16 bg-slate-50">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h2 className="text-2xl font-extrabold text-slate-900">Похожие продукты</h2>
          <div className="mt-6 grid sm:grid-cols-3 gap-4">
            {products.filter(p => p.id !== product.id).slice(0, 3).map(p => (
              <Link key={p.id} to={`/products/${p.slug}`} className="group rounded-xl bg-white p-5 border border-slate-200 hover:shadow-md transition">
                <div className="flex items-center gap-3">
                  <div className="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white"><Shield className="h-5 w-5" /></div>
                  <div className="flex-1 min-w-0">
                    <div className="font-bold text-slate-900 group-hover:text-blue-600 transition truncate">{p.name}</div>
                    <div className="text-xs text-slate-500 truncate">{p.tagline}</div>
                  </div>
                </div>
                <div className="mt-3 flex items-center justify-between text-sm">
                  <span className="font-bold text-slate-900">{p.price.toLocaleString("ru-RU")} ₽</span>
                  <ArrowRight className="h-4 w-4 text-blue-600 group-hover:translate-x-1 transition" />
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
