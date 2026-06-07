import { useState, useEffect } from "react";
import { serverPath } from "../utils/paths";

// =============================================
// ТИПЫ
// =============================================
export type Product = {
  id: string;
  slug: string;
  name: string;
  tagline: string;
  category: string;
  price: number;
  oldPrice?: number;
  rating: number;
  reviews: number;
  badge?: string;
  description: string;
  features: string[];
  os: string[];
  requirements: { os: string; cpu: string; ram: string; disk: string };
  versions: { version: string; date: string; size: string; isCurrent?: boolean }[];
  changelog: { version: string; date: string; changes: string[] }[];
  screenshots: { title: string; color: string; path?: string }[];
  coverImage?: string;
};

export type BlogPost = {
  id: string;
  slug: string;
  title: string;
  excerpt: string;
  content: string;
  category: string;
  author: string;
  date: string;
  readTime: number;
  cover: string;
  coverImage?: string;
};

// =============================================
// API → Product (безопасное преобразование)
// =============================================
function toProduct(p: any): Product {
  const versions = Array.isArray(p.versions) ? p.versions : [];
  return {
    id: String(p.id || ""),
    slug: p.slug || "",
    name: p.name || "",
    tagline: p.tagline || "",
    category: p.category || "",
    price: Number(p.price) || 0,
    oldPrice: p.oldPrice ? Number(p.oldPrice) : undefined,
    rating: Number(p.rating) || 5,
    reviews: Number(p.reviews) || 0,
    badge: p.badge || undefined,
    description: p.description || "",
    features: Array.isArray(p.features) ? p.features : [],
    os: Array.isArray(p.os) ? p.os : [],
    requirements: {
      os: (p.requirements && p.requirements.os) || "",
      cpu: (p.requirements && p.requirements.cpu) || "",
      ram: (p.requirements && p.requirements.ram) || "",
      disk: (p.requirements && p.requirements.disk) || "",
    },
    versions: versions.map(function(v: any) {
      return {
        version: v.version || "",
        date: v.date || "",
        size: v.size || "",
        isCurrent: Boolean(v.isCurrent),
      };
    }),
    changelog: versions
      .filter(function(v: any) { return v.changelog; })
      .map(function(v: any) {
        var changes = [];
        if (v.changelog && Array.isArray(v.changelog.changes)) {
          changes = v.changelog.changes;
        } else if (typeof v.changelog === "string") {
          changes = v.changelog.split("\n").filter(Boolean);
        }
        return { version: v.version || "", date: v.date || "", changes: changes };
      }),
    screenshots: Array.isArray(p.gallery)
      ? p.gallery.map(function(path: string) { return { title: "Скриншот", color: "from-blue-500 to-blue-700", path: path }; })
      : [],
    coverImage: p.coverImage || undefined,
  };
}

// =============================================
// API → BlogPost
// =============================================
function toBlogPost(p: any): BlogPost {
  return {
    id: String(p.id || ""),
    slug: p.slug || "",
    title: p.title || "",
    excerpt: p.excerpt || "",
    content: p.content || "",
    category: p.category || "",
    author: p.author || "",
    date: p.date || "",
    readTime: Number(p.readTime) || 5,
    cover: "from-blue-500 to-indigo-700",
    coverImage: p.coverImage || undefined,
  };
}

// =============================================
// ХУКИ — данные ТОЛЬКО из БД через API
// =============================================

export function useProductDetail(slug: string | undefined): Product | null {
  const [item, setItem] = useState<Product | null>(null);

  useEffect(() => {
    if (!slug) return;
    var url = serverPath("api/products.php?slug=" + encodeURIComponent(slug));
    fetch(url)
      .then(function(r) { return r.json(); })
      .then(function(d) {
        if (d && !d.error && d.id) {
          setItem(toProduct(d));
        }
      })
      .catch(function() {});
  }, [slug]);

  return item;
}

export function useProducts(): Product[] {
  const [items, setItems] = useState<Product[]>([]);

  useEffect(() => {
    var url = serverPath("api/products.php");
    console.log("[useProducts] fetching:", url);

    fetch(url)
      .then(function(r) {
        console.log("[useProducts] status:", r.status, "content-type:", r.headers.get("content-type"));
        if (!r.ok) throw new Error("HTTP " + r.status);
        var ct = r.headers.get("content-type") || "";
        if (ct.indexOf("json") === -1) {
          // Сервер вернул HTML вместо JSON — значит .htaccess редиректит
          throw new Error("API вернул не JSON, а HTML. Проверьте что файл api/products.php существует на сервере. URL: " + url);
        }
        return r.json();
      })
      .then(function(d) {
        console.log("[useProducts] data:", d);
        if (d && Array.isArray(d.products)) {
          setItems(d.products.map(toProduct));
          console.log("[useProducts] loaded", d.products.length, "products");
        } else if (d && d.error) {
          console.error("[useProducts] API error:", d.error);
        }
      })
      .catch(function(err) {
        console.error("[useProducts] fetch failed:", err);
      });
  }, []);

  return items;
}

export function useBlogPostDetail(slug: string | undefined): BlogPost | null {
  const [item, setItem] = useState<BlogPost | null>(null);
  useEffect(() => {
    if (!slug) return;
    fetch(serverPath("api/blog.php?slug=" + encodeURIComponent(slug)))
      .then(function(r) { return r.json(); })
      .then(function(d) { if (d && d.id) setItem(toBlogPost(d)); })
      .catch(function() {});
  }, [slug]);
  return item;
}

export function useBlogPosts(): BlogPost[] {
  const [items, setItems] = useState<BlogPost[]>([]);

  useEffect(() => {
    var url = serverPath("api/blog.php");
    console.log("[useBlogPosts] fetching:", url);

    fetch(url)
      .then(function(r) {
        if (!r.ok) throw new Error("HTTP " + r.status);
        var ct = r.headers.get("content-type") || "";
        if (ct.indexOf("json") === -1) {
          throw new Error("API вернул не JSON. URL: " + url);
        }
        return r.json();
      })
      .then(function(d) {
        console.log("[useBlogPosts] data:", d);
        if (d && Array.isArray(d.posts)) {
          setItems(d.posts.map(toBlogPost));
        }
      })
      .catch(function(err) {
        console.error("[useBlogPosts] fetch failed:", err);
      });
  }, []);

  return items;
}

// Статические данные, которые НЕ хранятся в БД
export { faqItems, solutions } from "../data";
