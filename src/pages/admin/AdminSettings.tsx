import { useState } from "react";
import { Save, Globe, Phone, Bell, Code, Palette } from "lucide-react";
import { Shield } from "lucide-react";
import { cn } from "../../utils/cn";

const tabs = [
  { id: "general" as const, label: "Общие", icon: Globe },
  { id: "contacts" as const, label: "Контакты", icon: Phone },
  { id: "seo" as const, label: "SEO", icon: Code },
  { id: "appearance" as const, label: "Внешний вид", icon: Palette },
  { id: "integrations" as const, label: "Интеграции", icon: Bell },
  { id: "security" as const, label: "Безопасность", icon: Shield },
];

export default function AdminSettings() {
  const [tab, setTab] = useState<"general" | "contacts" | "seo" | "appearance" | "integrations" | "security">("general");
  const [saved, setSaved] = useState(false);

  const save = () => {
    setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-extrabold text-slate-900">Настройки сайта</h1>
        <p className="text-sm text-slate-500 mt-0.5">Глобальные параметры, контент и интеграции</p>
      </div>

      <div className="grid lg:grid-cols-4 gap-6">
        <aside className="lg:col-span-1">
          <nav className="rounded-2xl border border-slate-200 bg-white p-2 space-y-0.5">
            {tabs.map(t => (
              <button
                key={t.id}
                onClick={() => setTab(t.id)}
                className={cn(
                  "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-left",
                  tab === t.id ? "bg-blue-50 text-blue-600" : "text-slate-700 hover:bg-slate-50"
                )}
              >
                <t.icon className="h-4 w-4" />
                {t.label}
              </button>
            ))}
          </nav>
        </aside>

        <div className="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-6 space-y-6">
          {tab === "general" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Информация о компании</h2>
                <p className="text-sm text-slate-500">Основные данные, отображаемые на сайте</p>
              </div>
              <div className="grid sm:grid-cols-2 gap-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Название компании</label>
                  <input defaultValue="NimbusSoft" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Юридическое название</label>
                  <input defaultValue='ООО "Нимбуссофт"' className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Год основания</label>
                  <input type="number" defaultValue="2010" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Часовой пояс</label>
                  <select className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm bg-white">
                    <option>Europe/Moscow (UTC+3)</option>
                    <option>Asia/Almaty (UTC+6)</option>
                  </select>
                </div>
                <div className="sm:col-span-2">
                  <label className="text-sm font-semibold text-slate-900">Слоган</label>
                  <input defaultValue="Профессиональное ПО для вашего бизнеса" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm focus:border-blue-500 outline-none" />
                </div>
              </div>
            </>
          )}

          {tab === "contacts" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Контактная информация</h2>
                <p className="text-sm text-slate-500">Эти данные отображаются в шапке, подвале и странице контактов</p>
              </div>
              <div className="grid sm:grid-cols-2 gap-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Телефон поддержки</label>
                  <input defaultValue="8 (800) 555-12-34" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Email отдела продаж</label>
                  <input defaultValue="sales@nimbussoft.ru" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div className="sm:col-span-2">
                  <label className="text-sm font-semibold text-slate-900">Адрес главного офиса</label>
                  <input defaultValue="Москва, Пресненская наб. 12, башня «Федерация»" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Telegram</label>
                  <input defaultValue="@nimbussoft_official" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">VK</label>
                  <input defaultValue="vk.com/nimbussoft" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                </div>
              </div>
            </>
          )}

          {tab === "seo" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Глобальные SEO-настройки</h2>
                <p className="text-sm text-slate-500">Meta-теги по умолчанию и аналитика</p>
              </div>
              <div className="space-y-3">
                <div>
                  <label className="text-sm font-semibold text-slate-900">Meta title (по умолчанию)</label>
                  <input defaultValue="NimbusSoft — Профессиональное ПО для бизнеса" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                  <div className="mt-1 text-xs text-slate-400">52 / 60 символов</div>
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Meta description</label>
                  <textarea rows={2} defaultValue="Российский разработчик антивирусов, утилит, облачных решений для бизнеса и частных пользователей." className="mt-1 w-full p-3 rounded-lg border border-slate-200 text-sm resize-none" />
                  <div className="mt-1 text-xs text-slate-400">116 / 160 символов</div>
                </div>
                <div>
                  <label className="text-sm font-semibold text-slate-900">Open Graph изображение</label>
                  <div className="mt-1 rounded-lg border-2 border-dashed border-slate-200 p-6 text-center">
                    <div className="h-24 w-40 mx-auto rounded bg-gradient-to-br from-blue-500 to-blue-700" />
                    <button className="mt-2 text-xs text-blue-600 font-semibold">Заменить изображение</button>
                  </div>
                </div>
                <div className="grid sm:grid-cols-2 gap-3">
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Google Analytics 4 ID</label>
                    <input placeholder="G-XXXXXXXXXX" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Яндекс.Метрика ID</label>
                    <input placeholder="12345678" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">Google Tag Manager ID</label>
                    <input placeholder="GTM-XXXXXXX" className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm" />
                  </div>
                  <div>
                    <label className="text-sm font-semibold text-slate-900">robots.txt</label>
                    <button className="mt-1 w-full h-10 px-3 rounded-lg border border-slate-200 text-sm text-left text-slate-500">Редактировать</button>
                  </div>
                </div>
              </div>
            </>
          )}

          {tab === "appearance" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Внешний вид</h2>
                <p className="text-sm text-slate-500">Брендирование, логотип и цветовая схема</p>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Логотип</label>
                <div className="mt-1 rounded-lg border-2 border-dashed border-slate-200 p-6 text-center">
                  <div className="inline-flex items-center gap-2">
                    <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white">
                      <Shield className="h-5 w-5" />
                    </div>
                    <span className="font-extrabold text-slate-900">NimbusSoft</span>
                  </div>
                  <button className="mt-3 ml-3 text-xs text-blue-600 font-semibold">Заменить</button>
                </div>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">Основной цвет</label>
                <div className="mt-2 flex items-center gap-2">
                  {[
                    { c: "#0056D2", name: "Синий" },
                    { c: "#1A57E6", name: "Ярко-синий" },
                    { c: "#7C3AED", name: "Фиолетовый" },
                    { c: "#16A34A", name: "Зелёный" },
                    { c: "#DC2626", name: "Красный" },
                  ].map((c, i) => (
                    <button
                      key={c.c}
                      className={cn("h-10 w-10 rounded-xl ring-2 ring-offset-2 transition", i === 0 ? "ring-blue-600" : "ring-transparent hover:ring-slate-300")}
                      style={{ backgroundColor: c.c }}
                      title={c.name}
                    />
                  ))}
                  <input type="color" defaultValue="#0056D2" className="h-10 w-10 rounded-xl cursor-pointer" />
                  <input defaultValue="#0056D2" className="h-10 w-24 px-2 rounded-lg border border-slate-200 text-sm font-mono" />
                </div>
              </div>
              <div>
                <label className="text-sm font-semibold text-slate-900">CTA цвет (Купить/Скачать)</label>
                <div className="mt-2 flex items-center gap-2">
                  {[
                    { c: "#FF6B00", name: "Оранжевый" },
                    { c: "#E63946", name: "Красный" },
                    { c: "#16A34A", name: "Зелёный" },
                  ].map((c, i) => (
                    <button key={c.c} className={cn("h-10 w-10 rounded-xl ring-2 ring-offset-2 transition", i === 0 ? "ring-orange-500" : "ring-transparent hover:ring-slate-300")} style={{ backgroundColor: c.c }} title={c.name} />
                  ))}
                </div>
              </div>
            </>
          )}

          {tab === "integrations" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Интеграции</h2>
                <p className="text-sm text-slate-500">Платёжные системы, CRM, email-рассылки</p>
              </div>
              {[
                { name: "ЮKassa", desc: "Приём платежей", status: "active", color: "purple" },
                { name: "AmoCRM", desc: "Передача лидов", status: "active", color: "blue" },
                { name: "Unisender", desc: "Email-рассылки", status: "active", color: "orange" },
                { name: "Bitrix24", desc: "Альтернативная CRM", status: "inactive", color: "blue" },
                { name: "Telegram Bot", desc: "Уведомления", status: "inactive", color: "cyan" },
                { name: "Slack", desc: "Оповещения команды", status: "inactive", color: "violet" },
              ].map(int => (
                <div key={int.name} className="flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                  <div className="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-700">
                    {int.name[0]}
                  </div>
                  <div className="flex-1">
                    <div className="font-semibold text-slate-900">{int.name}</div>
                    <div className="text-xs text-slate-500">{int.desc}</div>
                  </div>
                  {int.status === "active" ? (
                    <>
                      <span className="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">Подключено</span>
                      <button className="text-sm text-blue-600 hover:underline font-semibold">Настроить</button>
                    </>
                  ) : (
                    <button className="h-8 px-3 rounded-md border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">Подключить</button>
                  )}
                </div>
              ))}
            </>
          )}

          {tab === "security" && (
            <>
              <div>
                <h2 className="text-lg font-bold text-slate-900">Безопасность</h2>
                <p className="text-sm text-slate-500">2FA, лимиты входа, история активности</p>
              </div>
              <div className="space-y-3">
                <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                  <input type="checkbox" defaultChecked className="mt-1" />
                  <div>
                    <div className="font-semibold text-slate-900">Двухфакторная аутентификация (2FA)</div>
                    <div className="text-sm text-slate-500">Все администраторы должны использовать 2FA</div>
                  </div>
                </label>
                <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                  <input type="checkbox" defaultChecked className="mt-1" />
                  <div>
                    <div className="font-semibold text-slate-900">Защита от перебора паролей</div>
                    <div className="text-sm text-slate-500">Блокировка после 5 неудачных попыток входа на 15 минут</div>
                  </div>
                </label>
                <label className="flex items-start gap-3 rounded-xl border border-slate-200 p-4">
                  <input type="checkbox" defaultChecked className="mt-1" />
                  <div>
                    <div className="font-semibold text-slate-900">Cloudflare DDoS protection</div>
                    <div className="text-sm text-slate-500">Активна на уровне CDN</div>
                  </div>
                </label>
                <div>
                  <label className="text-sm font-semibold text-slate-900">IP Whitelist для админки</label>
                  <textarea
                    rows={3}
                    defaultValue={"192.168.1.0/24\n10.0.0.5"}
                    placeholder="Каждый IP или подсеть с новой строки"
                    className="mt-1 w-full p-3 rounded-lg border border-slate-200 text-sm font-mono resize-none"
                  />
                </div>
              </div>
            </>
          )}

          <div className="pt-4 border-t border-slate-200 flex items-center justify-between">
            <div className="text-sm text-slate-500">
              {saved && <span className="text-green-600 font-semibold">✓ Настройки сохранены</span>}
            </div>
            <button onClick={save} className="h-10 px-5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 inline-flex items-center gap-1.5">
              <Save className="h-4 w-4" /> Сохранить изменения
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
