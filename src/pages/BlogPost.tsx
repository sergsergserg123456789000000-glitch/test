import { Link, useParams } from "react-router-dom";
import { ChevronRight, Calendar, Clock, Share2, Bookmark, ThumbsUp } from "lucide-react";
import { useBlogPostDetail } from "../hooks/useData";
import { serverPath } from "../utils/paths";

function ContentLine({ line, i }: { line: string; i: number }) {
  const trimmed = line.trim();
  if (!trimmed) return null;
  const imgMatch = trimmed.match(/^!\[([^\]]*)\]\(([^)]+)\)$/);
  if (imgMatch) {
    return (
      <figure key={i} className="my-8">
        <img src={imgMatch[2]} alt={imgMatch[1] || ""} className="w-full rounded-xl border border-slate-200 max-h-[500px] object-contain bg-slate-50" />
        {imgMatch[1] && <figcaption className="text-center text-sm text-slate-400 mt-2">{imgMatch[1]}</figcaption>}
      </figure>
    );
  }
  if (trimmed.startsWith("## ")) return <h2 key={i} className="text-2xl font-extrabold text-slate-900 mt-10 mb-4">{trimmed.slice(3)}</h2>;
  if (trimmed.startsWith("### ")) return <h3 key={i} className="text-xl font-bold text-slate-900 mt-8 mb-3">{trimmed.slice(4)}</h3>;
  if (trimmed.startsWith("> ")) return <blockquote key={i} className="my-6 border-l-4 border-blue-500 pl-4 italic text-slate-700">«{trimmed.slice(2)}»</blockquote>;
  if (trimmed.startsWith("- ")) return <li key={i} className="flex items-start gap-2 text-slate-700 ml-4 mb-1"><span className="text-blue-600 font-bold flex-shrink-0">✓</span> {trimmed.slice(2)}</li>;
  return <p key={i} className="text-slate-700 leading-relaxed mb-4">{trimmed}</p>;
}

export default function BlogPostPage() {
  const { slug } = useParams();
  const post = useBlogPostDetail(slug);

  if (!post) {
    return <div className="mx-auto max-w-3xl px-4 py-20 text-center"><h1 className="text-3xl font-bold">Статья не найдена</h1><Link to="/blog" className="mt-4 inline-block text-blue-600">← К блогу</Link></div>;
  }

  return (
    <article className="bg-white">
      <div className="bg-slate-50 border-b border-slate-200">
        <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-4">
          <nav className="text-sm text-slate-500 flex items-center gap-1.5">
            <Link to="/" className="hover:text-blue-600">Главная</Link><ChevronRight className="h-3.5 w-3.5" />
            <Link to="/blog" className="hover:text-blue-600">Блог</Link><ChevronRight className="h-3.5 w-3.5" />
            <span className="text-slate-900 line-clamp-1">{post.title}</span>
          </nav>
        </div>
      </div>

      <header className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
        <span className="inline-block px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{post.category || "Без категории"}</span>
        <h1 className="mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">{post.title}</h1>
        <p className="mt-4 text-lg text-slate-600">{post.excerpt}</p>
        <div className="mt-6 flex flex-wrap items-center gap-4 text-sm text-slate-500">
          <div className="flex items-center gap-2"><div className="h-9 w-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center font-bold">{post.author[0] || "A"}</div><div className="font-semibold text-slate-900">{post.author}</div></div>
          <span className="text-slate-300">|</span>
          <span className="flex items-center gap-1"><Calendar className="h-4 w-4" /> {post.date}</span>
          <span className="flex items-center gap-1"><Clock className="h-4 w-4" /> {post.readTime} мин чтения</span>
        </div>
      </header>

      {/* Обложка */}
      {post.coverImage ? (
        <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 mb-12">
          <img src={serverPath(post.coverImage)} alt={post.title} className="w-full rounded-2xl border border-slate-200 max-h-[420px] object-contain bg-slate-50 mx-auto block" />
        </div>
      ) : (
        <div className="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 mb-12">
          <div className={`rounded-2xl bg-gradient-to-br ${post.cover} h-48 sm:h-64 flex items-center justify-center`}>
            <span className="text-white/20 text-8xl font-black">{post.category?.[0] || "P"}</span>
          </div>
        </div>
      )}

      <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 pb-16">
        <div className="prose prose-slate max-w-none">
          {typeof post.content === "string" ? post.content.split("\n").map((line: string, i: number) => <ContentLine key={i} line={line} i={i} />) : null}
        </div>
        <div className="mt-12 pt-8 border-t border-slate-200 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <button className="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-sm font-medium text-slate-700 transition"><ThumbsUp className="h-4 w-4" /> Полезно</button>
            <button className="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 transition"><Share2 className="h-4 w-4 text-slate-700" /></button>
            <button className="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 transition"><Bookmark className="h-4 w-4 text-slate-700" /></button>
          </div>
        </div>
        <div className="mt-12 rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 to-white p-8">
          <h3 className="text-xl font-extrabold text-slate-900">Попробуйте наши решения</h3>
          <p className="mt-2 text-slate-600">30 дней бесплатно, без привязки карты</p>
          <Link to="/products" className="mt-4 inline-flex items-center gap-2 h-10 px-5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Смотреть продукты →</Link>
        </div>
      </div>
    </article>
  );
}
