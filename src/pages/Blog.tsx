import { Link } from "react-router-dom";
import { useState } from "react";
import { Search, Shield, ChevronRight, Calendar, Clock, User } from "lucide-react";
import { useBlogPosts } from "../hooks/useData";
import { serverPath } from "../utils/paths";
import { cn } from "../utils/cn";

const categories = ["Все", "Безопасность", "Облако", "Утилиты", "Компания", "Релизы"];

export default function Blog() {
  const blogPosts = useBlogPosts();
  const [cat, setCat] = useState("Все");
  const [q, setQ] = useState("");

  const filtered = blogPosts.filter(p => {
    if (cat !== "Все" && p.category !== cat) return false;
    if (q && !p.title.toLowerCase().includes(q.toLowerCase())) return false;
    return true;
  });

  const featured = blogPosts[0];

  return (
    <>
      <section className="bg-gradient-to-b from-blue-50/40 to-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-12">
          <nav className="text-sm text-slate-500 flex items-center gap-2">
            <Link to="/" className="hover:text-blue-600">Главная</Link>
            <ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900">Блог</span>
          </nav>
          <h1 className="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">Блог NimbusSoft</h1>
          <p className="mt-3 text-lg text-slate-600 max-w-2xl">
            Экспертные статьи о кибербезопасности, обзоры продуктов, новости компании и многое другое.
          </p>
        </div>
      </section>

      {/* Featured */}
      {featured && (
        <section className="pb-8">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <Link to={`/blog/${featured.slug}`} className="group block rounded-2xl overflow-hidden bg-white border border-slate-200 hover:shadow-xl hover:shadow-blue-500/10 transition">
              <div className="grid lg:grid-cols-2">
                <div className={`h-64 lg:h-auto bg-gradient-to-br ${featured.cover} relative`}>
                  <div className="absolute inset-0 flex items-center justify-center">
                    <Shield className="h-24 w-24 text-white/30" />
                  </div>
                  <span className="absolute top-4 left-4 px-3 py-1 rounded-full bg-white/90 backdrop-blur text-xs font-bold text-slate-900">
                    {featured.category}
                  </span>
                  <span className="absolute top-4 right-4 px-3 py-1 rounded-full bg-orange-500 text-xs font-bold text-white uppercase">
                    Главное
                  </span>
                </div>
                <div className="p-8 lg:p-10 flex flex-col justify-center">
                  <div className="flex items-center gap-3 text-xs text-slate-500">
                    <span className="flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> {featured.date}</span>
                    <span>·</span>
                    <span className="flex items-center gap-1"><Clock className="h-3.5 w-3.5" /> {featured.readTime} мин</span>
                    <span>·</span>
                    <span className="flex items-center gap-1"><User className="h-3.5 w-3.5" /> {featured.author}</span>
                  </div>
                  <h2 className="mt-3 text-2xl lg:text-3xl font-extrabold text-slate-900 group-hover:text-blue-600 transition">{featured.title}</h2>
                  <p className="mt-3 text-slate-600">{featured.excerpt}</p>
                  <span className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-blue-600">
                    Читать статью →
                  </span>
                </div>
              </div>
            </Link>
          </div>
        </section>
      )}

      <section className="border-y border-slate-200 bg-white sticky top-16 z-30">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex flex-col lg:flex-row gap-3 lg:items-center">
            <div className="relative flex-1 max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
              <input
                value={q}
                onChange={e => setQ(e.target.value)}
                placeholder="Поиск по статьям..."
                className="w-full h-10 pl-10 pr-4 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition"
              />
            </div>
            <div className="flex items-center gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 lg:mx-0 lg:px-0">
              {categories.map(c => (
                <button
                  key={c}
                  onClick={() => setCat(c)}
                  className={cn(
                    "h-9 px-4 rounded-lg text-sm font-semibold whitespace-nowrap transition border",
                    cat === c ? "bg-blue-600 text-white border-blue-600" : "bg-white text-slate-700 border-slate-200 hover:border-blue-300"
                  )}
                >
                  {c}
                </button>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section className="py-12">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          {filtered.length === 0 ? (
            <div className="text-center py-20">
              <p className="text-slate-500">Статьи не найдены</p>
            </div>
          ) : (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {filtered.slice(1).map(post => (
                <Link key={post.id} to={`/blog/${post.slug}`} className="group rounded-2xl overflow-hidden bg-white border border-slate-200 hover:shadow-xl hover:shadow-blue-500/10 transition flex flex-col">
                  <div className={`h-44 relative overflow-hidden ${!post.coverImage ? 'bg-gradient-to-br ' + post.cover : 'bg-slate-100'}`}>
                    {post.coverImage ? (
                      <img src={serverPath(post.coverImage)} alt={post.title} className="absolute inset-0 w-full h-full object-cover" />
                    ) : (
                      <div className="absolute inset-0 flex items-center justify-center">
                        <Shield className="h-14 w-14 text-white/30" />
                      </div>
                    )}
                    <span className="absolute top-3 left-3 px-2 py-0.5 rounded-full bg-white/90 backdrop-blur text-xs font-bold text-slate-900">
                      {post.category}
                    </span>
                  </div>
                  <div className="p-5 flex-1 flex flex-col">
                    <div className="text-xs text-slate-500 flex items-center gap-2">
                      <span>{post.date}</span>
                      <span>·</span>
                      <span>{post.readTime} мин</span>
                    </div>
                    <h3 className="mt-2 text-lg font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2">{post.title}</h3>
                    <p className="mt-2 text-sm text-slate-600 line-clamp-3 flex-1">{post.excerpt}</p>
                    <div className="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                      <div className="h-6 w-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xs font-bold">
                        {post.author[0]}
                      </div>
                      <span className="text-xs text-slate-500">{post.author}</span>
                    </div>
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      </section>
    </>
  );
}
