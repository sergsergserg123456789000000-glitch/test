import { Outlet, Link, NavLink } from "react-router-dom";
import { Shield, LayoutDashboard, Package, FileText, Key, Settings, LogOut, Bell, Search, ChevronDown, BarChart3, Users, ShoppingCart, Headphones } from "lucide-react";
import { cn } from "../utils/cn";

const navItems = [
  { to: "/admin", label: "Дашборд", icon: LayoutDashboard, end: true },
  { to: "/admin/products", label: "Продукты", icon: Package },
  { to: "/admin/content", label: "Контент", icon: FileText },
  { to: "/admin/licenses", label: "Лицензии", icon: Key },
  { to: "/admin/settings", label: "Настройки", icon: Settings },
];

export default function AdminLayout() {
  return (
    <div className="min-h-screen bg-slate-100 flex">
      {/* Sidebar */}
      <aside className="w-64 bg-slate-900 text-slate-200 flex flex-col flex-shrink-0">
        <div className="h-16 flex items-center gap-2 px-5 border-b border-slate-800">
          <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700">
            <Shield className="h-5 w-5 text-white" />
          </div>
          <div className="leading-none">
            <div className="text-sm font-extrabold text-white">NimbusSoft</div>
            <div className="text-[10px] text-slate-400 tracking-widest">ADMIN PANEL</div>
          </div>
        </div>

        <div className="px-3 py-4">
          <div className="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-2">Основное</div>
          <nav className="space-y-1">
            {navItems.map(item => (
              <NavLink
                key={item.to}
                to={item.to}
                end={item.end}
                className={({ isActive }) =>
                  cn(
                    "flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition",
                    isActive ? "bg-blue-600 text-white shadow-lg shadow-blue-600/20" : "text-slate-300 hover:bg-slate-800 hover:text-white"
                  )
                }
              >
                <item.icon className="h-4 w-4" />
                {item.label}
              </NavLink>
            ))}
          </nav>
        </div>

        <div className="px-3 py-4 border-t border-slate-800">
          <div className="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-2">Аналитика</div>
          <nav className="space-y-1">
            {[
              { icon: BarChart3, label: "Отчёты" },
              { icon: Users, label: "Пользователи" },
              { icon: ShoppingCart, label: "Заказы" },
              { icon: Headphones, label: "Тикеты" },
            ].map(item => (
              <button key={item.label} className="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition text-left">
                <item.icon className="h-4 w-4" />
                {item.label}
              </button>
            ))}
          </nav>
        </div>

        <div className="mt-auto p-3 border-t border-slate-800">
          <div className="flex items-center gap-2 px-2 py-2 rounded-lg bg-slate-800/50">
            <div className="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xs font-bold">АИ</div>
            <div className="flex-1 min-w-0">
              <div className="text-xs font-semibold text-white truncate">Алексей Иванов</div>
              <div className="text-[10px] text-slate-400">Администратор</div>
            </div>
            <Link to="/admin/login" className="text-slate-400 hover:text-white">
              <LogOut className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </aside>

      {/* Main */}
      <div className="flex-1 flex flex-col min-w-0">
        <header className="h-16 bg-white border-b border-slate-200 flex items-center gap-4 px-6">
          <div className="flex-1 max-w-md relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
            <input
              placeholder="Поиск по админ-панели..."
              className="w-full h-9 pl-10 pr-4 rounded-lg bg-slate-100 text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/20 outline-none"
            />
          </div>
          <button className="relative h-9 w-9 inline-flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
            <Bell className="h-4 w-4" />
            <span className="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-orange-500" />
          </button>
          <Link to="/" className="text-sm font-semibold text-slate-700 hover:text-blue-600">На сайт →</Link>
          <button className="flex items-center gap-2 h-9 px-3 rounded-lg hover:bg-slate-100">
            <div className="h-7 w-7 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xs font-bold">АИ</div>
            <ChevronDown className="h-3.5 w-3.5 text-slate-500" />
          </button>
        </header>

        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
