import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import { Shield, Mail, Lock, Loader2, Eye, EyeOff, ArrowRight, Smartphone } from "lucide-react";

export default function AdminLogin() {
  const [email, setEmail] = useState("admin@nimbussoft.ru");
  const [password, setPassword] = useState("••••••••");
  const [code, setCode] = useState("");
  const [show, setShow] = useState(false);
  const [loading, setLoading] = useState(false);
  const [step, setStep] = useState<"login" | "2fa">("login");
  const navigate = useNavigate();

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setTimeout(() => {
      if (step === "login") {
        setLoading(false);
        setStep("2fa");
      } else {
        setLoading(false);
        navigate("/admin");
      }
    }, 1000);
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center p-6 relative overflow-hidden">
      <div className="absolute inset-0 grid-pattern opacity-10" />
      <div className="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl" />

      <div className="relative w-full max-w-md">
        <Link to="/" className="flex items-center gap-2 justify-center mb-8">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700">
            <Shield className="h-6 w-6 text-white" />
          </div>
          <div className="leading-none">
            <div className="text-xl font-extrabold text-white">NimbusSoft</div>
            <div className="text-[10px] text-slate-400 tracking-widest">ADMIN PANEL</div>
          </div>
        </Link>

        <div className="rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur p-8 shadow-2xl">
          <h1 className="text-2xl font-extrabold text-white">{step === "login" ? "Вход в админ-панель" : "Двухфакторная аутентификация"}</h1>
          <p className="mt-1 text-sm text-slate-400">
            {step === "login" ? "Введите учётные данные администратора" : "Введите код из приложения-аутентификатора"}
          </p>

          <form onSubmit={submit} className="mt-6 space-y-3">
            {step === "login" ? (
              <>
                <div>
                  <label className="text-sm font-semibold text-slate-200">Email</label>
                  <div className="mt-1 relative">
                    <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input
                      type="email"
                      required
                      value={email}
                      onChange={e => setEmail(e.target.value)}
                      className="w-full h-10 pl-10 pr-3 rounded-lg bg-slate-900/50 border border-slate-700 text-white text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    />
                  </div>
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-200">Пароль</label>
                  <div className="mt-1 relative">
                    <Lock className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input
                      type={show ? "text" : "password"}
                      required
                      value={password}
                      onChange={e => setPassword(e.target.value)}
                      className="w-full h-10 pl-10 pr-10 rounded-lg bg-slate-900/50 border border-slate-700 text-white text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                    />
                    <button type="button" onClick={() => setShow(!show)} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                      {show ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                    </button>
                  </div>
                </div>
              </>
            ) : (
              <div>
                <label className="text-sm font-semibold text-slate-200">Код подтверждения</label>
                <div className="mt-1 relative">
                  <Smartphone className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                  <input
                    type="text"
                    required
                    maxLength={6}
                    value={code}
                    onChange={e => setCode(e.target.value)}
                    placeholder="000000"
                    className="w-full h-12 pl-10 pr-3 rounded-lg bg-slate-900/50 border border-slate-700 text-white text-2xl font-mono tracking-widest text-center focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none"
                  />
                </div>
                <p className="mt-2 text-xs text-slate-400">Откройте Google Authenticator / Яндекс.Ключ и введите 6-значный код</p>
              </div>
            )}

            <button
              type="submit"
              disabled={loading}
              className="w-full h-11 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition inline-flex items-center justify-center gap-2 disabled:opacity-60"
            >
              {loading ? <><Loader2 className="h-4 w-4 animate-spin" /> Проверка...</> : <>{step === "login" ? "Продолжить" : "Войти"} <ArrowRight className="h-4 w-4" /></>}
            </button>
          </form>

          <div className="mt-6 pt-6 border-t border-slate-700 text-center text-xs text-slate-500">
            Вход только для администраторов. Все попытки логируются.
          </div>
        </div>

        <div className="mt-4 text-center">
          <Link to="/" className="text-sm text-slate-400 hover:text-white">← Вернуться на сайт</Link>
        </div>
      </div>
    </div>
  );
}
