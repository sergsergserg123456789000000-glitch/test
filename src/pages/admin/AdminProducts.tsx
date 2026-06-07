import { useState } from "react";
import { Plus, Edit, Trash2, Search, Eye, Star, Upload, X } from "lucide-react";
import { products } from "../../data";
import { cn } from "../../utils/cn";

export default function AdminProducts() {
  const [search, setSearch] = useState("");
  const [editing, setEditing] = useState<string | null>(null);
  const [creating, setCreating] = useState(false);

  const filtered = products.filter(p => p.name.toLowerCase().includes(search.toLowerCase()));

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900">Продукты</h1>
          <p className="text-sm text-slate-500 mt-0.5">Управление каталогом продуктов, версиями и загрузками</p>
        </div>
        <button onClick={() => setCreating(true)} className="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
          <Plus className="h-4 w-4" /> Создать продукт
        </button>
      </div>

      <div className="rounded-2xl border border-slate-200 bg-white">
        <div className="p-4 border-b border-slate-100 flex flex-col sm:flex-row gap-2">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Поиск продукта..."
              className="w-full h-9 pl-10 pr-4 rounded-lg bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-blue-500 outline-none"
            />
          </div>
          <div className="flex gap-2">
            {["Все", "Активные", "Черновики", "Архив"].map((t, i) => (
              <button key={t} className={cn("h-9 px-3 rounded-lg text-sm font-semibold border", i === 0 ? "bg-blue-600 text-white border-blue-600" : "bg-white text-slate-700 border-slate-200 hover:border-blue-300")}>
                {t}
              </button>
            ))}
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-slate-50">
              <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <th className="px-5 py-3">Продукт</th>
                <th className="px-5 py-3">Категория</th>
                <th className="px-5 py-3">Версия</th>
                <th className="px-5 py-3">Цена</th>
                <th className="px-5 py-3">Скачиваний</th>
                <th className="px-5 py-3">Рейтинг</th>
                <th className="px-5 py-3 text-right">Действия</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map(p => (
                <tr key={p.id} className="hover:bg-slate-50">
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-3">
                      <div className="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xs font-bold">N</div>
                      <div>
                        <div className="font-semibold text-slate-900">{p.name}</div>
                        <div className="text-xs text-slate-500 line-clamp-1">{p.tagline}</div>
                      </div>
                      {p.badge && <span className="px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">{p.badge}</span>}
                    </div>
                  </td>
                  <td className="px-5 py-3 text-slate-600">{p.category}</td>
                  <td className="px-5 py-3 font-mono text-xs text-slate-700">v{p.versions[0].version}</td>
                  <td className="px-5 py-3 font-bold text-slate-900">{p.price.toLocaleString("ru-RU")} ₽</td>
                  <td className="px-5 py-3 text-slate-600">{Math.floor(p.reviews * 12.5).toLocaleString("ru-RU")}</td>
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-1">
                      <Star className="h-3.5 w-3.5 fill-orange-500 text-orange-500" />
                      <span className="font-semibold">{p.rating}</span>
                    </div>
                  </td>
                  <td className="px-5 py-3 text-right">
                    <div className="flex items-center justify-end gap-1">
                      <button onClick={() => setEditing(p.id)} className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" title="Редактировать">
                        <Edit className="h-3.5 w-3.5" />
                      </button>
                      <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" title="Просмотр">
                        <Eye className="h-3.5 w-3.5" />
                      </button>
                      <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" title="Загрузить версию">
                        <Upload className="h-3.5 w-3.5" />
                      </button>
                      <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50" title="Удалить">
                        <Trash2 className="h-3.5 w-3.5" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Edit / Create modal */}
      {(editing || creating) && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div className="sticky top-0 bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
              <h2 className="text-lg font-bold text-slate-900">{creating ? "Создать продукт" : "Редактировать продукт"}</h2>
              <button onClick={() => { setEditing(null); setCreating(false); }} className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                <X className="h-4 w-4" />
              </button>
            </div>
            <div className="p-6 space-y-4">
              <div className="grid sm:grid-cols-2 gap-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Название *</label>
                  <input defaultValue={editing ? products.find(p => p.id === editing)?.name : ""} placeholder="NimbusGuard Pro" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">URL (slug) *</label>
                  <input placeholder="nimbus-guard-pro" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                  <p className="mt-1 text-xs text-slate-500">site.com/products/<strong>nimbus-guard-pro</strong></p>
                </div>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Краткое описание</label>
                <input placeholder="Антивирус нового поколения с AI" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Полное описание</label>
                <textarea rows={4} placeholder="Подробное описание продукта..." className="mt-1 w-full p-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none resize-none" />
              </div>
              <div className="grid sm:grid-cols-3 gap-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Категория</label>
                  <select className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm bg-white focus:border-blue-500 outline-none">
                    <option>Безопасность</option><option>Утилиты</option><option>Облако</option><option>Бизнес</option>
                  </select>
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Цена (₽)</label>
                  <input type="number" placeholder="2490" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Версия</label>
                  <input placeholder="12.4.1" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Дистрибутив</label>
                <div className="mt-1 rounded-lg border-2 border-dashed border-slate-200 p-6 text-center hover:border-blue-400 hover:bg-blue-50/30 transition cursor-pointer">
                  <Upload className="h-8 w-8 text-slate-400 mx-auto" />
                  <p className="mt-2 text-sm text-slate-600">Перетащите .exe / .msi / .dmg сюда или нажмите для выбора</p>
                  <p className="mt-1 text-xs text-slate-400">Максимум 500 МБ. Хеш-сумма рассчитается автоматически.</p>
                </div>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Changelog</label>
                <textarea rows={3} placeholder="Что нового в этой версии..." className="mt-1 w-full p-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none resize-none" />
              </div>
              <div className="grid sm:grid-cols-2 gap-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Meta title (SEO)</label>
                  <input placeholder="NimbusGuard Pro — Антивирус с AI" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Meta description (SEO)</label>
                  <input placeholder="До 160 символов" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
              </div>
            </div>
            <div className="sticky bottom-0 bg-slate-50 border-t border-slate-200 px-6 py-3 flex items-center justify-end gap-2">
              <button onClick={() => { setEditing(null); setCreating(false); }} className="h-9 px-4 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">Отмена</button>
              <button className="h-9 px-4 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Сохранить</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
