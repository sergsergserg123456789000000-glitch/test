import { useState } from "react";
import { Link } from "react-router-dom";
import { User, Key, Download, CreditCard, Settings, LogOut, Copy, Check, Calendar, Shield, RefreshCw, FileText } from "lucide-react";
import { userLicenses } from "../data";
import { cn } from "../utils/cn";

const tabs = [
  { id: "overview", label: "Обзор", icon: User },
  { id: "licenses", label: "Лицензии", icon: Key },
  { id: "downloads", label: "Загрузки", icon: Download },
  { id: "billing", label: "Платежи", icon: CreditCard },
  { id: "settings", label: "Настройки", icon: Settings },
];

const downloads = [
  { product: "NimbusGuard Pro", version: "12.4.1", size: "184 МБ", os: "Windows" },
  { product: "NimbusGuard Pro", version: "12.4.1", size: "176 МБ", os: "macOS" },
  { product: "NimbusClean Utility", version: "5.2.0", size: "62 МБ", os: "Windows" },
  { product: "NimbusClean Utility", version: "5.2.0", size: "58 МБ", os: "macOS" },
  { product: "NimbusVPN Business", version: "3.2.1", size: "45 МБ", os: "Windows" },
  { product: "NimbusVPN Business", version: "3.2.1", size: "42 МБ", os: "macOS" },
  { product: "NimbusVPN Business", version: "3.2.1", size: "38 МБ", os: "Linux" },
];

const invoices = [
  { id: "NS-78421", date: "12 мая 2026", amount: "2 490 ₽", status: "paid", product: "NimbusGuard Pro" },
  { id: "NS-76920", date: "12 апреля 2026", amount: "1 290 ₽", status: "paid", product: "NimbusClean Utility" },
  { id: "NS-75120", date: "12 марта 2026", amount: "990 ₽", status: "paid", product: "NimbusVault Cloud" },
  { id: "NS-73821", date: "12 февраля 2026", amount: "1 990 ₽", status: "paid", product: "NimbusVPN Business" },
];

export default function Account() {
  const [tab, setTab] = useState("overview");
  const [copiedKey, setCopiedKey] = useState<string | null>(null);

  const copyKey = (k: string) => {
    navigator.clipboard?.writeText(k);
    setCopiedKey(k);
    setTimeout(() => setCopiedKey(null), 1500);
  };

  return (
    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
      <div className="grid lg:grid-cols-4 gap-6">
        {/* Sidebar */}
        <aside className="lg:col-span-1">
          <div className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-center gap-3">
              <div className="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold">
                ИП
              </div>
              <div className="min-w-0">
                <div className="font-bold text-slate-900 truncate">Иван Петров</div>
                <div className="text-xs text-slate-500 truncate">ivan.petrov@mail.ru</div>
              </div>
            </div>
            <div className="mt-4 pt-4 border-t border-slate-100">
              <div className="text-xs text-slate-500">Активных лицензий</div>
              <div className="text-2xl font-extrabold text-slate-900">3</div>
            </div>
          </div>

          <nav className="mt-4 rounded-2xl border border-slate-200 bg-white p-2">
            {tabs.map(t => (
              <button
                key={t.id}
                onClick={() => setTab(t.id)}
                className={cn(
                  "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition",
                  tab === t.id ? "bg-blue-50 text-blue-600" : "text-slate-700 hover:bg-slate-50"
                )}
              >
                <t.icon className="h-4 w-4" />
                {t.label}
              </button>
            ))}
            <div className="mt-2 pt-2 border-t border-slate-100">
              <button className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                <LogOut className="h-4 w-4" />
                Выйти
              </button>
            </div>
          </nav>
        </aside>

        {/* Content */}
        <div className="lg:col-span-3 space-y-4">
          {tab === "overview" && (
            <>
              <div className="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-800 p-6 text-white">
                <div className="text-sm font-semibold text-blue-100 uppercase tracking-wider">Добро пожаловать</div>
                <h2 className="mt-1 text-2xl font-extrabold">Иван, у вас 3 активных лицензии</h2>
                <p className="mt-1 text-sm text-blue-100">Общий срок действия истекает 30 июня 2027</p>
                <div className="mt-4 flex gap-2">
                  <Link to="/products" className="inline-flex items-center gap-1 h-9 px-4 rounded-lg bg-white text-blue-700 text-sm font-semibold hover:bg-blue-50">
                    Купить ещё
                  </Link>
                </div>
              </div>

              <div className="grid sm:grid-cols-3 gap-3">
                {[
                  { l: "Активные лицензии", v: "3", c: "text-green-600" },
                  { l: "Доступно загрузок", v: "12", c: "text-blue-600" },
                  { l: "Потрачено за год", v: "4 770 ₽", c: "text-slate-900" },
                ].map(s => (
                  <div key={s.l} className="rounded-2xl border border-slate-200 bg-white p-5">
                    <div className="text-xs text-slate-500">{s.l}</div>
                    <div className={cn("text-2xl font-extrabold mt-1", s.c)}>{s.v}</div>
                  </div>
                ))}
              </div>

              <div className="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 className="font-bold text-slate-900">Активные лицензии</h3>
                <div className="mt-4 space-y-2">
                  {userLicenses.filter(l => l.status === "active").map(l => (
                    <div key={l.key} className="flex items-center gap-3 p-3 rounded-lg border border-slate-100">
                      <div className="h-9 w-9 rounded-lg bg-blue-50 flex items-center justify-center"><Shield className="h-4 w-4 text-blue-600" /></div>
                      <div className="flex-1 min-w-0">
                        <div className="font-semibold text-slate-900 text-sm">{l.product}</div>
                        <div className="text-xs text-slate-500">{l.seats} · до {l.expires}</div>
                      </div>
                      <button className="inline-flex items-center gap-1 h-8 px-3 rounded-md border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        <RefreshCw className="h-3.5 w-3.5" /> Продлить
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            </>
          )}

          {tab === "licenses" && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="text-xl font-extrabold text-slate-900">Все лицензии</h2>
              <div className="mt-4 space-y-3">
                {userLicenses.map(l => (
                  <div key={l.key} className={cn("rounded-xl border p-4", l.status === "active" ? "border-slate-200 bg-white" : "border-slate-200 bg-slate-50 opacity-75")}>
                    <div className="flex items-start justify-between gap-4">
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2">
                          <div className="font-bold text-slate-900">{l.product}</div>
                          {l.status === "active" ? (
                            <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Активна</span>
                          ) : (
                            <span className="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold">Истекла</span>
                          )}
                        </div>
                        <div className="mt-2 flex items-center gap-2 text-sm">
                          <code className="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded">{l.key}</code>
                          <button onClick={() => copyKey(l.key)} className="text-slate-400 hover:text-blue-600">
                            {copiedKey === l.key ? <Check className="h-4 w-4 text-green-600" /> : <Copy className="h-4 w-4" />}
                          </button>
                        </div>
                        <div className="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                          <span className="flex items-center gap-1"><Calendar className="h-3 w-3" /> До {l.expires}</span>
                          <span>·</span>
                          <span>{l.seats}</span>
                        </div>
                      </div>
                      {l.status === "active" ? (
                        <button className="inline-flex items-center gap-1 h-9 px-3 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                          <RefreshCw className="h-3.5 w-3.5" /> Продлить
                        </button>
                      ) : (
                        <button className="inline-flex items-center gap-1 h-9 px-3 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                          <RefreshCw className="h-3.5 w-3.5" /> Продлить
                        </button>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {tab === "downloads" && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="text-xl font-extrabold text-slate-900">Загрузки</h2>
              <p className="mt-1 text-sm text-slate-500">Все доступные вам дистрибутивы</p>
              <div className="mt-4 divide-y divide-slate-100">
                {downloads.map((d, i) => (
                  <div key={i} className="flex items-center gap-3 py-3">
                    <div className="h-9 w-9 rounded-lg bg-blue-50 flex items-center justify-center"><Download className="h-4 w-4 text-blue-600" /></div>
                    <div className="flex-1 min-w-0">
                      <div className="font-semibold text-slate-900 text-sm">{d.product}</div>
                      <div className="text-xs text-slate-500">v{d.version} · {d.size} · {d.os}</div>
                    </div>
                    <button className="inline-flex items-center gap-1 h-8 px-3 rounded-md bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                      <Download className="h-3.5 w-3.5" /> Скачать
                    </button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {tab === "billing" && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="text-xl font-extrabold text-slate-900">История платежей</h2>
              <div className="mt-4 overflow-hidden rounded-xl border border-slate-200">
                <table className="w-full text-sm">
                  <thead className="bg-slate-50">
                    <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                      <th className="px-4 py-3">Счёт</th>
                      <th className="px-4 py-3">Дата</th>
                      <th className="px-4 py-3">Продукт</th>
                      <th className="px-4 py-3">Сумма</th>
                      <th className="px-4 py-3 text-right">Действие</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {invoices.map(inv => (
                      <tr key={inv.id}>
                        <td className="px-4 py-3 font-mono text-xs text-slate-700">{inv.id}</td>
                        <td className="px-4 py-3 text-slate-600">{inv.date}</td>
                        <td className="px-4 py-3 text-slate-700">{inv.product}</td>
                        <td className="px-4 py-3 font-bold text-slate-900">{inv.amount}</td>
                        <td className="px-4 py-3 text-right">
                          <button className="text-blue-600 hover:underline text-sm font-semibold inline-flex items-center gap-1">
                            <FileText className="h-3.5 w-3.5" /> PDF
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {tab === "settings" && (
            <div className="rounded-2xl border border-slate-200 bg-white p-6">
              <h2 className="text-xl font-extrabold text-slate-900">Настройки профиля</h2>
              <div className="mt-6 space-y-4 max-w-lg">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Имя</label>
                  <input defaultValue="Иван Петров" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Email</label>
                  <input defaultValue="ivan.petrov@mail.ru" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Телефон</label>
                  <input defaultValue="+7 (999) 123-45-67" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="flex items-center gap-2 text-sm">
                    <input type="checkbox" defaultChecked /> Получать новости о продуктах
                  </label>
                </div>
                <div>
                  <label className="flex items-center gap-2 text-sm">
                    <input type="checkbox" /> Двухфакторная аутентификация
                  </label>
                </div>
                <button className="h-10 px-5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                  Сохранить изменения
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
