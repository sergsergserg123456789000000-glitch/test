import { Routes, Route, useLocation } from "react-router-dom";
import { useEffect } from "react";
import PublicLayout from "./components/PublicLayout";
import AdminLayout from "./components/AdminLayout";
import Home from "./pages/Home";
import Products from "./pages/Products";
import ProductDetail from "./pages/ProductDetail";
import Pricing from "./pages/Pricing";
import Solutions from "./pages/Solutions";
import Blog from "./pages/Blog";
import BlogPost from "./pages/BlogPost";
import Support from "./pages/Support";
import About from "./pages/About";
import Contact from "./pages/Contact";
import Login from "./pages/Login";
import Account from "./pages/Account";
import Checkout from "./pages/Checkout";
import AdminLogin from "./pages/admin/AdminLogin";
import Dashboard from "./pages/admin/Dashboard";
import AdminProducts from "./pages/admin/AdminProducts";
import AdminContent from "./pages/admin/AdminContent";
import AdminLicenses from "./pages/admin/AdminLicenses";
import AdminSettings from "./pages/admin/AdminSettings";
import NotFound from "./pages/NotFound";

function ScrollToTop() {
  const { pathname } = useLocation();
  useEffect(() => {
    window.scrollTo({ top: 0, behavior: "instant" as ScrollBehavior });
  }, [pathname]);
  return null;
}

export default function App() {
  return (
    <>
      <ScrollToTop />
      <Routes>
        <Route element={<PublicLayout />}>
          <Route path="/" element={<Home />} />
          <Route path="/products" element={<Products />} />
          <Route path="/products/:slug" element={<ProductDetail />} />
          <Route path="/pricing" element={<Pricing />} />
          <Route path="/solutions" element={<Solutions />} />
          <Route path="/blog" element={<Blog />} />
          <Route path="/blog/:slug" element={<BlogPost />} />
          <Route path="/support" element={<Support />} />
          <Route path="/about" element={<About />} />
          <Route path="/contact" element={<Contact />} />
          <Route path="/login" element={<Login />} />
          <Route path="/account" element={<Account />} />
          <Route path="/checkout" element={<Checkout />} />
        </Route>

        <Route path="/admin/login" element={<AdminLogin />} />
        <Route element={<AdminLayout />}>
          <Route path="/admin" element={<Dashboard />} />
          <Route path="/admin/products" element={<AdminProducts />} />
          <Route path="/admin/content" element={<AdminContent />} />
          <Route path="/admin/licenses" element={<AdminLicenses />} />
          <Route path="/admin/settings" element={<AdminSettings />} />
        </Route>

        <Route path="*" element={<NotFound />} />
      </Routes>
    </>
  );
}
