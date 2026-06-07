import { Link } from "react-router-dom";
import { Shield } from "lucide-react";

export default function NotFound() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-slate-50 to-white px-4">
      <div className="text-center max-w-lg">
        <div className="h-20 w-20 mx-auto rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center shadow-lg shadow-blue-500/30">
          <Shield className="h-10 w-10 text-white" />
        </div>
        <h1 className="mt-6 text-7xl sm:text-9xl font-extrabold gradient-text">404</h1>
        <h2 className="mt-4 text-2xl sm:text-3xl font-extrabold text-slate-900">Страница не найдена</h2>
        <p className="mt-3 text-slate-600">Возможно, страница была перемещена или удалена. Вернитесь на главную или посмотрите каталог продуктов.</p>
        <div className="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
          <Link to="/" className="h-11 px-5 inline-flex items-center justify-center rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">
            На главную
          </Link>
          <Link to="/products" className="h-11 px-5 inline-flex items-center justify-center rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition">
            Каталог продуктов
          </Link>
        </div>
      </div>
    </div>
  );
}
