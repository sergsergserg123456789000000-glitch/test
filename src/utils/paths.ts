/**
 * Базовый серверный путь.
 * Вычисляется в index.html ДО загрузки React и сохраняется в window.__PS_BASE.
 * Это гарантирует правильный путь даже при F5 на /master/products/xxx.
 *
 * /master/products/nimbus-vault-cloud → /master
 * /master/ → /master
 * / → ""
 */

declare global {
  interface Window {
    __PS_BASE?: string;
  }
}

export function getBasePath(): string {
  // Берём из глобальной переменной, установленной в HTML
  if (typeof window !== "undefined" && typeof window.__PS_BASE === "string") {
    return window.__PS_BASE;
  }
  return "";
}

export function serverPath(path: string): string {
  return getBasePath() + "/" + path.replace(/^\//, "");
}
