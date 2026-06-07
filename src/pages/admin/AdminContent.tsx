import { useState } from "react";
import { Plus, Edit, Trash2, Search, Eye, FileText, Bold, Italic, List, Link as LinkIcon, Image as ImageIcon, Code, Save, X, Tag } from "lucide-react";
import { blogPosts } from "../../data";
import { cn } from "../../utils/cn";

export default function AdminContent() {
  const [tab, setTab] = useState<"posts" | "pages" | "media">("posts");
  const [search, setSearch] = useState("");
  const [editing, setEditing] = useState<string | null>("new");

  const filtered = blogPosts.filter(p => p.title.toLowerCase().includes(search.toLowerCase()));

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900">Контент</h1>
          <p className="text-sm text-slate-500 mt-0.5">Управление статьями блога, страницами и медиафайлами</p>
        </div>
        <button onClick={() => setEditing("new")} className="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
          <Plus className="h-4 w-4" /> Создать материал
        </button>
      </div>

      <div className="flex border-b border-slate-200">
        {[
          { id: "posts" as const, label: "Статьи блога", count: blogPosts.length },
          { id: "pages" as const, label: "Страницы", count: 8 },
          { id: "media" as const, label: "Медиа-библиотека", count: 124 },
        ].map(t => (
          <button
            key={t.id}
            onClick={() => setTab(t.id)}
            className={cn(
              "px-4 py-3 text-sm font-semibold border-b-2 -mb-px",
              tab === t.id ? "border-blue-600 text-blue-600" : "border-transparent text-slate-600 hover:text-slate-900"
            )}
          >
            {t.label} <span className="ml-1 text-xs text-slate-400">({t.count})</span>
          </button>
        ))}
      </div>

      {tab === "posts" && (
        <div className="rounded-2xl border border-slate-200 bg-white">
          <div className="p-4 border-b border-slate-100">
            <div className="relative max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
              <input
                value={search}
                onChange={e => setSearch(e.target.value)}
                placeholder="Поиск статьи..."
                className="w-full h-9 pl-10 pr-4 rounded-lg bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-blue-500 outline-none"
              />
            </div>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-slate-50">
                <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                  <th className="px-5 py-3">Заголовок</th>
                  <th className="px-5 py-3">Категория</th>
                  <th className="px-5 py-3">Автор</th>
                  <th className="px-5 py-3">Дата</th>
                  <th className="px-5 py-3">Статус</th>
                  <th className="px-5 py-3 text-right">Действия</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {filtered.map(p => (
                  <tr key={p.id} className="hover:bg-slate-50">
                    <td className="px-5 py-3">
                      <div className="flex items-center gap-3">
                        <div className={`h-9 w-12 rounded bg-gradient-to-br ${p.cover} flex-shrink-0`} />
                        <div>
                          <div className="font-semibold text-slate-900 line-clamp-1">{p.title}</div>
                          <div className="text-xs text-slate-500 line-clamp-1">{p.excerpt}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-5 py-3"><span className="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{p.category}</span></td>
                    <td className="px-5 py-3 text-slate-600">{p.author}</td>
                    <td className="px-5 py-3 text-slate-600">{p.date}</td>
                    <td className="px-5 py-3">
                      <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Опубликовано</span>
                    </td>
                    <td className="px-5 py-3 text-right">
                      <div className="flex items-center justify-end gap-1">
                        <button onClick={() => setEditing(p.id)} className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"><Edit className="h-3.5 w-3.5" /></button>
                        <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"><Eye className="h-3.5 w-3.5" /></button>
                        <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50"><Trash2 className="h-3.5 w-3.5" /></button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {tab === "pages" && (
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            {["О компании", "Контакты", "Решения", "Карьера", "Партнёрам", "Юридическая информация", "Политика конфиденциальности", "Пользовательское соглашение"].map(p => (
              <div key={p} className="rounded-xl border border-slate-200 p-4 hover:border-blue-300 hover:shadow-sm transition cursor-pointer">
                <FileText className="h-5 w-5 text-blue-600" />
                <div className="mt-2 font-semibold text-slate-900">{p}</div>
                <div className="text-xs text-slate-500 mt-0.5">Обновлено 12 мая 2026</div>
                <div className="mt-3 flex gap-1">
                  <button className="text-xs text-blue-600 hover:underline font-semibold">Редактировать</button>
                  <span className="text-slate-300">·</span>
                  <button className="text-xs text-slate-500 hover:underline">Просмотр</button>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {tab === "media" && (
        <div className="rounded-2xl border border-slate-200 bg-white p-5">
          <div className="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
            {Array.from({ length: 12 }).map((_, i) => (
              <div key={i} className="aspect-square rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center text-slate-400">
                <ImageIcon className="h-8 w-8" />
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Editor modal */}
      {editing && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-5xl w-full max-h-[92vh] overflow-hidden flex flex-col">
            <div className="border-b border-slate-200 px-6 py-4 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <h2 className="text-lg font-bold text-slate-900">Редактор статьи</h2>
                <span className="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-xs font-bold">Черновик</span>
              </div>
              <div className="flex items-center gap-2">
                <button className="h-9 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 inline-flex items-center gap-1">
                  <Save className="h-4 w-4" /> Сохранить
                </button>
                <button className="h-9 px-3 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700">Опубликовать</button>
                <button onClick={() => setEditing(null)} className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                  <X className="h-4 w-4" />
                </button>
              </div>
            </div>
            <div className="flex-1 overflow-y-auto">
              <div className="grid lg:grid-cols-4 gap-6 p-6">
                <div className="lg:col-span-3">
                  <input
                    defaultValue="AI в антивирусах: почему сигнатурный подход устарел"
                    placeholder="Заголовок статьи..."
                    className="w-full text-2xl font-extrabold text-slate-900 outline-none placeholder-slate-300"
                  />
                  <input
                    defaultValue="Разбираемся, как нейросети изменили индустрию кибербезопасности..."
                    placeholder="Краткое описание (excerpt)..."
                    className="mt-2 w-full text-base text-slate-600 outline-none placeholder-slate-300"
                  />

                  <div className="mt-4 rounded-xl border border-slate-200">
                    <div className="flex items-center gap-1 p-2 border-b border-slate-100">
                      {[Bold, Italic, List, LinkIcon, ImageIcon, Code].map((Icon, i) => (
                        <button key={i} className="h-8 w-8 inline-flex items-center justify-center rounded text-slate-500 hover:bg-slate-100">
                          <Icon className="h-4 w-4" />
                        </button>
                      ))}
                      <select className="ml-2 h-8 px-2 rounded text-sm border border-slate-200 bg-white">
                        <option>Параграф</option><option>Заголовок H2</option><option>Заголовок H3</option><option>Цитата</option>
                      </select>
                    </div>
                    <textarea
                      rows={14}
                      defaultValue="Ещё 5 лет назад антивирусная индустрия строилась вокруг сигнатур — уникальных цифровых отпечатков известных вредоносных программ. Сегодня этот подход катастрофически отстаёт от реальности."
                      className="w-full p-4 outline-none resize-none text-slate-700 leading-relaxed"
                    />
                  </div>

                  <div className="mt-6 rounded-xl border-2 border-dashed border-slate-200 p-6 text-center">
                    <ImageIcon className="h-8 w-8 text-slate-400 mx-auto" />
                    <p className="mt-2 text-sm text-slate-600">Перетащите обложку статьи</p>
                    <p className="mt-1 text-xs text-slate-400">Рекомендуем 1200×630px, WebP/JPG</p>
                  </div>
                </div>

                <aside className="space-y-4">
                  <div className="rounded-xl border border-slate-200 p-4">
                    <h3 className="text-sm font-bold text-slate-900">Параметры</h3>
                    <div className="mt-3 space-y-3">
                      <div>
                        <label className="text-xs font-semibold text-slate-600">Категория</label>
                        <select className="mt-1 w-full h-9 px-2 rounded-lg border border-slate-200 text-sm bg-white">
                          <option>Безопасность</option><option>Облако</option><option>Утилиты</option><option>Компания</option>
                        </select>
                      </div>
                      <div>
                        <label className="text-xs font-semibold text-slate-600">Автор</label>
                        <select className="mt-1 w-full h-9 px-2 rounded-lg border border-slate-200 text-sm bg-white">
                          <option>Алексей Петров</option>
                        </select>
                      </div>
                      <div>
                        <label className="text-xs font-semibold text-slate-600">URL (slug)</label>
                        <input defaultValue="ai-antivirus-future-2026" className="mt-1 w-full h-9 px-2 rounded-lg border border-slate-200 text-sm" />
                      </div>
                      <div>
                        <label className="text-xs font-semibold text-slate-600 inline-flex items-center gap-1"><Tag className="h-3 w-3" /> Теги</label>
                        <div className="mt-1 flex flex-wrap gap-1">
                          {["AI", "антивирус", "кибербезопасность"].map(t => (
                            <span key={t} className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 text-xs">
                              {t} <X className="h-3 w-3 cursor-pointer" />
                            </span>
                          ))}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="rounded-xl border border-slate-200 p-4">
                    <h3 className="text-sm font-bold text-slate-900">SEO</h3>
                    <div className="mt-3 space-y-3">
                      <div>
                        <label className="text-xs font-semibold text-slate-600">Meta title</label>
                        <input defaultValue="AI в антивирусах 2026 — NimbusSoft" className="mt-1 w-full h-9 px-2 rounded-lg border border-slate-200 text-sm" />
                      </div>
                      <div>
                        <label className="text-xs font-semibold text-slate-600">Meta description</label>
                        <textarea rows={2} defaultValue="Разбираемся, как нейросети изменили индустрию..." className="mt-1 w-full p-2 rounded-lg border border-slate-200 text-sm resize-none" />
                        <div className="mt-1 text-xs text-slate-400">87 / 160</div>
                      </div>
                    </div>
                  </div>

                  <div className="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <h3 className="text-sm font-bold text-slate-900">Предпросмотр</h3>
                    <p className="mt-1 text-xs text-slate-600">Посмотрите, как статья будет выглядеть в блоге перед публикацией</p>
                    <button className="mt-3 h-8 px-3 rounded-md bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 inline-flex items-center gap-1 w-full justify-center">
                      <Eye className="h-3.5 w-3.5" /> Открыть предпросмотр
                    </button>
                  </div>
                </aside>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
