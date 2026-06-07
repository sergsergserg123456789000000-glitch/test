import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Mail, Lock, Eye, EyeOff, Loader2, ArrowRight, User } from "lucide-react";
import { serverPath } from "../utils/paths";

type OAuthProvider = { key: string; label: string; href: string };

export default function Login() {
  const [tab, setTab] = useState<"login" | "register">("login");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [oauthProviders, setOauthProviders] = useState<OAuthProvider[]>([]);

  useEffect(() => {
    fetch(serverPath("api/oauth-providers.php"))
      .then(r => r.json())
      .then(d => { if (d.providers) setOauthProviders(d.providers); })
      .catch(() => {});
  }, []);

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    window.location.href = serverPath(tab === "login" ? "login.php" : "register.php");
  };

  return (
    <div className="min-h-screen flex">
      <div className="hidden lg:flex flex-1 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white p-12 relative overflow-hidden">
        <div className="absolute -top-20 -right-20 h-80 w-80 rounded-full bg-cyan-400/20 blur-3xl" />
        <div className="absolute -bottom-20 -left-20 h-80 w-80 rounded-full bg-blue-400/30 blur-3xl" />
        <div className="relative z-10 flex flex-col justify-between w-full">
          <Link to="/" className="flex items-center gap-2">
            <img src={serverPath("site-assets.php?type=logo")} alt="PROFESSIONAL SOFTWARE" className="h-9 w-9 rounded-xl bg-white object-contain" />
            <span className="text-lg font-extrabold">PROFESSIONAL SOFTWARE</span>
          </Link>

          <div>
            <h2 className="text-4xl font-extrabold">Личный кабинет</h2>
            <p className="mt-3 text-blue-100 text-lg max-w-md">Управляйте лицензиями, скачивайте обновления и продлевайте подписку в одном месте.</p>
            <div className="mt-8 space-y-3">
              {["Все ваши лицензии в одном месте", "Мгновенный доступ к обновлениям", "Автоматическое продление подписки", "Приоритетная поддержка 24/7"].map(b => (
                <div key={b} className="flex items-center gap-2 text-sm">
                  <div className="h-5 w-5 rounded-full bg-white/20 flex items-center justify-center text-xs">✓</div>
                  {b}
                </div>
              ))}
            </div>
          </div>

          <div className="text-sm text-blue-200">© 2010–2026 PROFESSIONAL SOFTWARE</div>
        </div>
      </div>

      <div className="flex-1 flex items-center justify-center p-6 lg:p-12">
        <div className="w-full max-w-md">
          <Link to="/" className="lg:hidden flex items-center gap-2 mb-8">
            <img src={serverPath("site-assets.php?type=logo")} alt="PROFESSIONAL SOFTWARE" className="h-9 w-9 rounded-xl bg-white object-contain" />
            <span className="text-lg font-extrabold">PROFESSIONAL SOFTWARE</span>
          </Link>

          <h1 className="text-3xl font-extrabold text-slate-900">
            {tab === "login" ? "С возвращением" : "Создать аккаунт"}
          </h1>
          <p className="mt-2 text-slate-600">
            {tab === "login" ? "Войдите, чтобы управлять лицензиями" : "Зарегистрируйтесь за 30 секунд"}
          </p>

          <div className="mt-6 flex rounded-xl border border-slate-200 bg-slate-50 p-1">
            {(["login", "register"] as const).map(t => (
              <button
                key={t}
                onClick={() => setTab(t)}
                className={`flex-1 h-9 rounded-lg text-sm font-semibold transition ${tab === t ? "bg-white text-slate-900 shadow-sm" : "text-slate-600"}`}
              >
                {t === "login" ? "Вход" : "Регистрация"}
              </button>
            ))}
          </div>

          <form onSubmit={submit} className="mt-6 space-y-3">
            {tab === "register" && (
              <div>
                <label className="text-sm font-semibold text-slate-900">Имя</label>
                <div className="mt-1 relative">
                  <User className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <input
                    required
                    placeholder="Иван Петров"
                    className="w-full h-10 pl-10 pr-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  />
                </div>
              </div>
            )}
            <div>
              <label className="text-sm font-semibold text-slate-900">Email</label>
              <div className="mt-1 relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input
                  type="email"
                  required
                  value={email}
                  onChange={e => setEmail(e.target.value)}
                  placeholder="you@company.ru"
                  className="w-full h-10 pl-10 pr-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                />
              </div>
            </div>
            <div>
              <div className="flex items-center justify-between">
                <label className="text-sm font-semibold text-slate-900">Пароль</label>
                {tab === "login" && <a href="#" className="text-xs text-blue-600 hover:underline">Забыли пароль?</a>}
              </div>
              <div className="mt-1 relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                <input
                  type={show ? "text" : "password"}
                  required
                  value={password}
                  onChange={e => setPassword(e.target.value)}
                  placeholder="••••••••"
                  className="w-full h-10 pl-10 pr-10 rounded-lg border border-slate-200 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                />
                <button type="button" onClick={() => setShow(!show)} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                  {show ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>

            {tab === "login" && (
              <label className="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" className="rounded" defaultChecked /> Запомнить меня
              </label>
            )}

            <button
              type="submit"
              disabled={loading}
              className="w-full h-11 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-60"
            >
              {loading ? <><Loader2 className="h-4 w-4 animate-spin" /> Вход...</> : <>{tab === "login" ? "Войти" : "Создать аккаунт"} <ArrowRight className="h-4 w-4" /></>}
            </button>
          </form>

          {oauthProviders.length > 0 && (
          <>
          <div className="mt-6 flex items-center gap-3">
            <div className="flex-1 h-px bg-slate-200" />
            <span className="text-xs text-slate-500">или через</span>
            <div className="flex-1 h-px bg-slate-200" />
          </div>
          <div className={`mt-4 grid gap-2`} style={{ gridTemplateColumns: `repeat(${oauthProviders.length}, 1fr)` }}>
            {oauthProviders.map(p => (
              <a key={p.key} href={serverPath(p.href)} className="h-10 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition inline-flex items-center justify-center">
                {p.label}
              </a>
            ))}
          </div>
          </>
          )}

          <p className="mt-6 text-center text-sm text-slate-600">
            {tab === "login" ? "Нет аккаунта?" : "Уже есть аккаунт?"}{" "}
            <button onClick={() => setTab(tab === "login" ? "register" : "login")} className="text-blue-600 font-semibold hover:underline">
              {tab === "login" ? "Зарегистрироваться" : "Войти"}
            </button>
          </p>
        </div>
      </div>
    </div>
  );
}
