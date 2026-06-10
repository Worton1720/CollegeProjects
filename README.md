# CollegeProjects

Монорепозиторий учебных проектов. Каждый проект живёт в **отдельной ветке** —
эта ветка `main` служит **навигатором** (роутером) по темам и технологиям.

> Чтобы открыть проект, переключитесь на его ветку:
> ```bash
> git switch <branch>
> # например: git switch videohub
> ```

---

## 🗂️ Проекты по темам

| Тема / Stack | Проект | Ветка | Описание |
|---|---|---|---|
| **PHP + Laravel + Vue 3** | VideoHub | [`videohub`](../../tree/videohub) | Платформа для хостинга и стриминга видео: загрузка (Cloudinary), комментарии, лайки, поиск, роли и админка (Sanctum). |
| **PHP + PostgreSQL** | МирИгрушек | [`exam-php-postgres`](../../tree/exam-php-postgres) | Магазин игрушек: список товаров, авторизация по ролям, CRUD товаров и заказов, SQL-схема `schema.sql`. |
| **PHP + SQLite + Vanilla JS** | Comfort-Rest | [`comfort-rest`](../../tree/comfort-rest) | Система администрирования турфирмы «Комфорт-отдых»: управление турами, клиентами и странами. Bootstrap 5. |
| **React + Node/Express** | Recipe Manager | [`react-recipe-manager`](../../tree/react-recipe-manager) | Менеджер кулинарных рецептов: создание, фильтрация по категориям, REST API на Express + SQLite, UI на React + Styled Components. |
| **Android / Kotlin** | Weather App | [`android-project`](../../tree/android-project) | Погодное приложение: прогноз по дням, поиск по городу, MVVM + Coroutines + Retrofit, Navigation Component. |
| **Unity / C#** | Unity College | [`unity-college`](../../tree/unity-college) | Учебный Unity-проект для уроков колледжа: сцены, ассеты и шейдеры (ShaderLab). |
| **ASP.NET** | — | [`asp.net`](../../tree/asp.net) | Зарезервированная ветка под проект на ASP.NET. |

---

## 🧭 Навигация по стеку

- **Backend (PHP):** `videohub` · `exam-php-postgres` · `comfort-rest`
- **Backend (Node.js):** `react-recipe-manager`
- **Backend (.NET):** `asp.net`
- **Frontend (Vue):** `videohub`
- **Frontend (React):** `react-recipe-manager`
- **Frontend (Vanilla JS):** `comfort-rest`
- **Mobile (Android/Kotlin):** `android-project`
- **Game (Unity/C#):** `unity-college`
- **Базы данных:** SQLite (`videohub`, `comfort-rest`, `react-recipe-manager`) · PostgreSQL (`exam-php-postgres`)

---

## ℹ️ Как устроен репозиторий

- `main` — этот навигатор. Кода проектов здесь нет, только маршрутизация по веткам.
- Каждая остальная ветка — самостоятельный проект со своим `README.md` и инструкцией по запуску.

Полный список веток: [Branches](../../branches).
