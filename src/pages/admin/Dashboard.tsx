import { TrendingUp, TrendingDown, Package, Download, Users, CreditCard, ArrowRight, Plus, MoreVertical, AlertCircle, FileText, Headphones } from "lucide-react";
import { stats, salesChart, recentOrders, supportTickets } from "../../data";
import { cn } from "../../utils/cn";

const statusMap: Record<string, { label: string; cls: string }> = {
  paid: { label: "Оплачен", cls: "bg-green-100 text-green-700" },
  pending: { label: "Ожидает", cls: "bg-orange-100 text-orange-700" },
  open: { label: "Открыт", cls: "bg-blue-100 text-blue-700" },
  in_progress: { label: "В работе", cls: "bg-orange-100 text-orange-700" },
  closed: { label: "Закрыт", cls: "bg-slate-100 text-slate-600" },
};

const priorityMap: Record<string, string> = {
  high: "text-red-600",
  medium: "text-orange-600",
  low: "text-slate-500",
};

const statCards = [
  { label: "Продажи сегодня", value: `${stats.totalSales.today.toLocaleString("ru-RU")} ₽`, growth: stats.totalSales.growth, icon: CreditCard, color: "from-blue-500 to-blue-700" },
  { label: "Скачиваний", value: stats.downloads.today.toLocaleString("ru-RU"), growth: stats.downloads.growth, icon: Download, color: "from-cyan-500 to-blue-600" },
  { label: "Новых пользователей", value: stats.newUsers.today.toString(), growth: stats.newUsers.growth, icon: Users, color: "from-indigo-500 to-blue-700" },
  { label: "Активных подписок", value: stats.activeSubscriptions.toLocaleString("ru-RU"), growth: 4.2, icon: Package, color: "from-blue-600 to-indigo-700" },
];

const maxVal = Math.max(...salesChart.map(d => d.value));

export default function Dashboard() {
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h1 className="text-2xl font-extrabold text-slate-900">Дашборд</h1>
          <p className="text-sm text-slate-500 mt-0.5">Сводка по продажам, продуктам и активности за сегодня</p>
        </div>
        <div className="flex gap-2">
          <select className="h-9 px-3 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-700">
            <option>За сегодня</option>
            <option>За неделю</option>
            <option>За месяц</option>
            <option>За год</option>
          </select>
          <button className="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            <Plus className="h-4 w-4" /> Добавить
          </button>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {statCards.map(s => (
          <div key={s.label} className="rounded-2xl border border-slate-200 bg-white p-5">
            <div className="flex items-start justify-between">
              <div className={`h-10 w-10 rounded-xl bg-gradient-to-br ${s.color} flex items-center justify-center`}>
                <s.icon className="h-5 w-5 text-white" />
              </div>
              <span className={cn(
                "inline-flex items-center gap-0.5 text-xs font-bold px-2 py-0.5 rounded-full",
                s.growth >= 0 ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"
              )}>
                {s.growth >= 0 ? <TrendingUp className="h-3 w-3" /> : <TrendingDown className="h-3 w-3" />}
                {Math.abs(s.growth)}%
              </span>
            </div>
            <div className="mt-4 text-2xl font-extrabold text-slate-900">{s.value}</div>
            <div className="text-xs text-slate-500 mt-0.5">{s.label}</div>
          </div>
        ))}
      </div>

      {/* Chart + Recent orders */}
      <div className="grid lg:grid-cols-3 gap-4">
        <div className="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
          <div className="flex items-center justify-between">
            <div>
              <h2 className="text-base font-bold text-slate-900">Продажи за неделю</h2>
              <p className="text-xs text-slate-500">Динамика выручки</p>
            </div>
            <button className="text-slate-400 hover:text-slate-700">
              <MoreVertical className="h-4 w-4" />
            </button>
          </div>
          <div className="mt-6 flex items-end gap-3 h-48">
            {salesChart.map(d => (
              <div key={d.day} className="flex-1 flex flex-col items-center gap-2">
                <div className="text-xs font-bold text-slate-900">{d.value}</div>
                <div
                  className="w-full rounded-t-md bg-gradient-to-t from-blue-600 to-blue-400 transition-all hover:from-blue-700 hover:to-blue-500"
                  style={{ height: `${(d.value / maxVal) * 100}%` }}
                />
                <div className="text-xs text-slate-500">{d.day}</div>
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-2xl border border-slate-200 bg-white p-6">
          <h2 className="text-base font-bold text-slate-900">Активность</h2>
          <div className="mt-4 space-y-3">
            {[
              { t: "Новый заказ #NS-78421", s: "2 мин назад", c: "blue" },
              { t: "Регистрация: Мария К.", s: "8 мин назад", c: "green" },
              { t: "Тикет #T-1284 открыт", s: "15 мин назад", c: "orange" },
              { t: "Обновлён NimbusGuard Pro 12.4.1", s: "1 час назад", c: "blue" },
              { t: "Отзыв: 5★ на NimbusClean", s: "2 часа назад", c: "yellow" },
            ].map((a, i) => (
              <div key={i} className="flex items-start gap-2.5">
                <div className={`h-1.5 w-1.5 rounded-full mt-2 bg-${a.c}-500`} />
                <div className="flex-1 min-w-0">
                  <div className="text-sm text-slate-900 truncate">{a.t}</div>
                  <div className="text-xs text-slate-500">{a.s}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Recent orders */}
      <div className="rounded-2xl border border-slate-200 bg-white">
        <div className="flex items-center justify-between p-5 border-b border-slate-100">
          <h2 className="text-base font-bold text-slate-900">Последние заказы</h2>
          <a href="#" className="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-700">
            Все заказы <ArrowRight className="h-3.5 w-3.5" />
          </a>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead className="bg-slate-50">
              <tr className="text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                <th className="px-5 py-3">ID</th>
                <th className="px-5 py-3">Клиент</th>
                <th className="px-5 py-3">Продукт</th>
                <th className="px-5 py-3">Сумма</th>
                <th className="px-5 py-3">Статус</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {recentOrders.map(o => (
                <tr key={o.id} className="hover:bg-slate-50">
                  <td className="px-5 py-3 font-mono text-xs text-slate-700">{o.id}</td>
                  <td className="px-5 py-3 font-semibold text-slate-900">{o.customer}</td>
                  <td className="px-5 py-3 text-slate-600">{o.product}</td>
                  <td className="px-5 py-3 font-bold text-slate-900">{o.amount.toLocaleString("ru-RU")} ₽</td>
                  <td className="px-5 py-3">
                    <span className={cn("px-2 py-0.5 rounded-full text-xs font-bold", statusMap[o.status].cls)}>
                      {statusMap[o.status].label}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Tickets + Alerts */}
      <div className="grid lg:grid-cols-3 gap-4">
        <div className="lg:col-span-2 rounded-2xl border border-slate-200 bg-white">
          <div className="flex items-center justify-between p-5 border-b border-slate-100">
            <h2 className="text-base font-bold text-slate-900 inline-flex items-center gap-2">
              <Headphones className="h-4 w-4 text-blue-600" /> Активные тикеты
            </h2>
            <a href="#" className="text-sm font-semibold text-blue-600">Все тикеты →</a>
          </div>
          <div className="divide-y divide-slate-100">
            {supportTickets.map(t => (
              <div key={t.id} className="flex items-center gap-3 p-4 hover:bg-slate-50">
                <div className="h-9 w-9 rounded-lg bg-slate-100 flex items-center justify-center">
                  <FileText className="h-4 w-4 text-slate-500" />
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2">
                    <span className="font-mono text-xs text-slate-500">{t.id}</span>
                    <span className={cn("text-xs font-bold", priorityMap[t.priority])}>
                      {t.priority === "high" ? "↑ Высокий" : t.priority === "medium" ? "→ Средний" : "↓ Низкий"}
                    </span>
                  </div>
                  <div className="font-semibold text-slate-900 text-sm truncate">{t.subject}</div>
                  <div className="text-xs text-slate-500">{t.user}</div>
                </div>
                <span className={cn("px-2 py-0.5 rounded-full text-xs font-bold", statusMap[t.status].cls)}>
                  {statusMap[t.status].label}
                </span>
              </div>
            ))}
          </div>
        </div>

        <div className="rounded-2xl border border-orange-200 bg-orange-50 p-5">
          <div className="flex items-start gap-3">
            <div className="h-10 w-10 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0">
              <AlertCircle className="h-5 w-5 text-orange-600" />
            </div>
            <div>
              <h3 className="font-bold text-slate-900">Требуется внимание</h3>
              <p className="text-sm text-slate-700 mt-1">3 тикета с высоким приоритетом ожидают ответа более 1 часа</p>
              <button className="mt-3 h-8 px-3 rounded-md bg-orange-600 text-white text-xs font-semibold hover:bg-orange-700">Открыть</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
