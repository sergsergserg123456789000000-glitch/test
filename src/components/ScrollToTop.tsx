import { useState, useEffect } from "react";
import { ArrowUp } from "lucide-react";

export default function ScrollTopButton() {
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const onScroll = () => setVisible(window.scrollY > 400);
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  const scrollUp = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <button
      onClick={scrollUp}
      aria-label="Наверх"
      className={`fixed bottom-6 right-6 z-50 group transition-all duration-300 ${
        visible ? "opacity-100 translate-y-0 pointer-events-auto" : "opacity-0 translate-y-4 pointer-events-none"
      }`}
    >
      <span
        className="flex h-12 w-12 items-center justify-center rounded-full text-white shadow-lg transition-transform duration-200 group-hover:scale-110 group-active:scale-95"
        style={{
          backgroundColor: "var(--ps-primary, #0056D2)",
          boxShadow: "0 10px 25px -5px var(--ps-primary, #0056D2)",
        }}
      >
        {/* Пульсирующее кольцо в цвете гаммы */}
        <span
          className="absolute inset-0 rounded-full animate-ping opacity-20"
          style={{ backgroundColor: "var(--ps-primary, #0056D2)" }}
        />
        <ArrowUp className="relative h-5 w-5" strokeWidth={2.5} />
      </span>
    </button>
  );
}
