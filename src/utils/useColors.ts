import { useEffect } from "react";
import { serverPath } from "./paths";

function hexToRgb(hex: string) {
  const m = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
  if (!m) return null;
  return { r: parseInt(m[1], 16), g: parseInt(m[2], 16), b: parseInt(m[3], 16) };
}

function shade(hex: string, pct: number): string {
  const c = hexToRgb(hex);
  if (!c) return hex;
  const t = pct > 0 ? 255 : 0;
  const p = Math.abs(pct) / 100;
  const r = Math.round(c.r + (t - c.r) * p);
  const g = Math.round(c.g + (t - c.g) * p);
  const b = Math.round(c.b + (t - c.b) * p);
  return `#${((r << 16) | (g << 8) | b).toString(16).padStart(6, "0")}`;
}

export function useSiteColors() {
  useEffect(() => {
    fetch(serverPath("api/home-settings.php"))
      .then((r) => r.json())
      .then((d: any) => {
        if (!d?.colors) return;
        const root = document.documentElement;
        const c = d.colors;

        root.setAttribute("data-theme", "custom");

        if (c.primary) {
          root.style.setProperty("--ps-primary", c.primary);
          root.style.setProperty("--ps-light", shade(c.primary, 20));
          root.style.setProperty("--ps-dark", shade(c.primary, -20));
          root.style.setProperty("--ps-50", shade(c.primary, 92));
          root.style.setProperty("--ps-100", shade(c.primary, 80));
        }
        if (c.cta) {
          root.style.setProperty("--ps-cta", c.cta);
          root.style.setProperty("--ps-cta-dark", shade(c.cta, -15));
        }
        if (c.text) {
          root.style.setProperty("--ps-text", c.text);
        }
        const bgColor = c.bg || "#FFFFFF";
        const bgMode = c.bg_mode || "color";
        root.style.setProperty("--ps-bg", bgColor);

        // Убираем старый бэкграунд-стиль
        const oldEl = document.getElementById("ps-bg-style");
        if (oldEl) oldEl.remove();

        const el = document.createElement("style");
        el.id = "ps-bg-style";

        if (bgMode === "image" && c.bg_image) {
          const op = Math.max(0, Math.min(100, parseInt(c.bg_opacity) || 0)) / 100;
          const whiteOverlay = 1 - op;
          const imageUrl = serverPath(c.bg_image);
          root.style.setProperty(
            "--ps-bg-layer",
            `linear-gradient(rgba(255,255,255,${whiteOverlay}), rgba(255,255,255,${whiteOverlay})), url('${imageUrl}') center / cover fixed no-repeat`
          );
          el.textContent = `body,.site-shell{background:var(--ps-bg-layer)!important;}`;
        } else {
          root.style.setProperty("--ps-bg-layer", bgColor);
          el.textContent = `body,.site-shell{background:${bgColor}!important;}`;
        }
        document.head.appendChild(el);
      })
      .catch(() => {});
  }, []);
}
