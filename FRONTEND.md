# 🎨 Frontend Guide (Vue.js)

Project ini dilengkapi dengan Frontend Single Page Application (SPA) menggunakan Vue.js 3.

## 🛠️ Stack

-   **Framework**: Vue 3 (Composition API)
-   **State Management**: Pinia
-   **Routing**: Vue Router
-   **Styling**: TailwindCSS
-   **HTTP Client**: Axios

## 📂 Struktur Folder

Kode frontend berada di dalam folder `frontend/frontend-starter`.

```
frontend/frontend-starter/
├── src/
│   ├── api.js              # Konfigurasi Axios
│   ├── stores/             # Pinia Stores (Auth)
│   ├── router/             # Vue Router Config
│   ├── views/              # Page Components
│   │   ├── auth/           # Login & Register
│   │   ├── dashboard/      # Dashboard Views
│   │   └── ...
│   └── App.vue             # Main Component
```

## 🚀 Cara Menjalankan

Masuk ke folder frontend dan jalankan development server:

```bash
cd frontend/frontend-starter
npm install
npm run dev
```

Akses aplikasi di: `http://localhost:5173`

## 🔑 Fitur Frontend

-   **Auth**: Login & Register (terintegrasi dengan API v1)
-   **Dashboard**: Halaman yang dilindungi login.
-   **Profile**: Update profil user (Nama, Email, Avatar, Bio).
-   **Admin**: Create user baru (hanya untuk role admin).
