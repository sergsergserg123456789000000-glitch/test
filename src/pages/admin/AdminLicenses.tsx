import { useState } from "react";
import { Plus, Copy, Search, Download, Ban, RefreshCw, Key, Check, Lock } from "lucide-react";
import { userLicenses } from "../../data";
import { cn } from "../../utils/cn";

const allLicenses = [
  ...userLicenses,
  { product: "NimbusBackup Enterprise", key: "NBB-9H2K-7F4L-3M8N-1X5V", status: "active", expires: "01 марта 2027", seats: "1 сервер" },
  { product: "NimbusMail Server", key: "NBM-4T6Y-8U9I-2O1P-5A7S", status: "active", expires: "10 июля 2026", seats: "50 ящиков" },
  { product: "NimbusGuard Pro", key: "NBS-3D5F-7G9H-1J2K-4L6M", status: "active", expires: "25 января 2027", seats: "10 устройств" },
  { product: "NimbusVPN Business", key: "NBV-8N0P-2Q4R-6S8T-1U3V", status: "blocked", expires: "—", seats: "5 устройств" },
];

export default function AdminLicenses() {
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<"all" | "active" | "expired" | "blocked">("all");
  const [copied, setCopied] = useState<string | null>(null);
  const [showGen, setShowGen] = useState(false);

  const filtered = allLicenses.filter(l => {
    if (filter !== "all" && l.status !== filter) return false;
    if (search && !l.key.toLowerCase().includes(search.toLowerCase()) && !l.product.toLowerCase().includes(search.toLowerCase())) return false;
    return true;
  });

  const copy = (k: string) => {
    navigator.clipboard?.writeText(k);
    setCopied(k);
    setTimeout(() => setCopied(null), 1500);
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900">Лицензии</h1>
          <p className="text-sm text-slate-500 mt-0.5">Управление лицензионными ключами, активациями и blacklist</p>
        </div>
        <div className="flex gap-2">
          <button className="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">
            <Download className="h-4 w-4" /> Экспорт CSV
          </button>
          <button onClick={() => setShowGen(true)} className="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            <Plus className="h-4 w-4" /> Сгенерировать ключи
          </button>
        </div>
      </div>

      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {[
          { l: "Всего ключей", v: allLicenses.length.toString(), c: "text-slate-900" },
          { l: "Активных", v: allLicenses.filter(l => l.status === "active").length.toString(), c: "text-green-600" },
          { l: "Истёкших", v: allLicenses.filter(l => l.status === "expired").length.toString(), c: "text-red-600" },
          { l: "Заблокировано", v: allLicenses.filter(l => l.status === "blocked").length.toString(), c: "text-slate-500" },
        ].map(s => (
          <div key={s.l} className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="text-xs text-slate-500">{s.l}</div>
            <div className={cn("text-2xl font-extrabold mt-1", s.c)}>{s.v}</div>
          </div>
        ))}
      </div>

      <div className="rounded-2xl border border-slate-200 bg-white">
        <div className="p-4 border-b border-slate-100 flex flex-col lg:flex-row gap-2">
          <div className="relative flex-1 max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
              value={search}
              onChange={e => setSearch(e.target.value)}
              placeholder="Поиск по ключу или продукту..."
              className="w-full h-9 pl-10 pr-4 rounded-lg bg-slate-50 border border-slate-200 text-sm focus:bg-white focus:border-blue-500 outline-none"
            />
          </div>
          <div className="flex gap-1.5">
            {[
              { id: "all" as const, label: "Все" },
              { id: "active" as const, label: "Активные" },
              { id: "expired" as const, label: "Истёкшие" },
              { id: "blocked" as const, label: "Заблокированы" },
            ].map(f => (
              <button
                key={f.id}
                onClick={() => setFilter(f.id)}
                className={cn("h-9 px-3 rounded-lg text-sm font-semibold border", filter === f.id ? "bg-blue-600 text-white border-blue-600" : "bg-white text-slate-700 border-slate-200 hover:border-blue-300")}
              >
                {f.label}
              </button>
            ))}
          </div>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-slate-50">
              <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <th className="px-5 py-3">Ключ</th>
                <th className="px-5 py-3">Продукт</th>
                <th className="px-5 py-3">Статус</th>
                <th className="px-5 py-3">Срок</th>
                <th className="px-5 py-3">Тип</th>
                <th className="px-5 py-3 text-right">Действия</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {filtered.map((l, i) => (
                <tr key={l.key} className="hover:bg-slate-50">
                  <td className="px-5 py-3">
                    <div className="flex items-center gap-2">
                      <Key className="h-3.5 w-3.5 text-slate-400" />
                      <code className="font-mono text-xs text-slate-700">{l.key}</code>
                      <button onClick={() => copy(l.key)} className="text-slate-400 hover:text-blue-600">
                        {copied === l.key ? <Check className="h-3.5 w-3.5 text-green-600" /> : <Copy className="h-3.5 w-3.5" />}
                      </button>
                    </div>
                  </td>
                  <td className="px-5 py-3">
                    <div className="font-semibold text-slate-900">{l.product}</div>
                    <div className="text-xs text-slate-500">{l.seats}</div>
                  </td>
                  <td className="px-5 py-3">
                    {l.status === "active" && <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Активен</span>}
                    {l.status === "expired" && <span className="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold">Истёк</span>}
                    {l.status === "blocked" && <span className="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-xs font-bold">Заблокирован</span>}
                  </td>
                  <td className="px-5 py-3 text-slate-600 text-xs">{l.expires}</td>
                  <td className="px-5 py-3 text-xs text-slate-500">{i % 3 === 0 ? "Trial" : i % 3 === 1 ? "Pro" : "Enterprise"}</td>
                  <td className="px-5 py-3 text-right">
                    <div className="flex items-center justify-end gap-1">
                      <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" title="Продлить">
                        <RefreshCw className="h-3.5 w-3.5" />
                      </button>
                      {l.status !== "blocked" ? (
                        <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50" title="Заблокировать">
                          <Ban className="h-3.5 w-3.5" />
                        </button>
                      ) : (
                        <button className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-green-500 hover:bg-green-50" title="Разблокировать">
                          <Check className="h-3.5 w-3.5" />
                        </button>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Generate modal */}
      {showGen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-md w-full p-6">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-bold text-slate-900 inline-flex items-center gap-2"><Lock className="h-4 w-4 text-blue-600" /> Генерация ключей</h2>
              <button onClick={() => setShowGen(false)} className="h-8 w-8 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">×</button>
            </div>
            <div className="mt-4 space-y-3">
              <div>
                <label className="text-sm font-semibold text-slate-900">Продукт</label>
                <select className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm bg-white">
                  <option>NimbusGuard Pro</option>
                  <option>NimbusClean Utility</option>
                  <option>NimbusBackup Enterprise</option>
                </select>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Количество</label>
                <input type="number" defaultValue={10} className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Срок действия</label>
                <select className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm bg-white">
                  <option>1 год</option>
                  <option>2 года</option>
                  <option>Бессрочно</option>
                  <option>Trial 30 дней</option>
                </select>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Привязать к пользователю (опционально)</label>
                <input placeholder="email@example.com" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
              </div>
            </div>
            <div className="mt-5 flex justify-end gap-2">
              <button onClick={() => setShowGen(false)} className="h-9 px-4 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50">Отмена</button>
              <button className="h-9 px-4 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Сгенерировать</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
