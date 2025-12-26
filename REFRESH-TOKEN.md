# 🔄 Refresh Token Mechanism

Mekanisme JWT-style token refresh untuk pengalaman logout yang lebih jarang dan keamanan yang lebih baik.

> 🚧 **Status:** Fitur ini masih dalam tahap **PERENCANAAN (Planned)** dan belum tersedia di versi saat ini. Saat ini, aplikasi menggunakan token Sanctum standar yang _long-lived_ (atau sesuai expiry di config).

---

## 📖 Rencana Alur Kerja (Planned Workflow)

### 1. Login

User login dan menerima dua token:

1.  **Access Token**: Umur pendek (misal: 60 menit). Digunakan untuk akses API.
2.  **Refresh Token**: Umur panjang (misal: 30 hari). Digunakan hanya untuk memperbarui Access Token.

**Response Login (Rencana):**

```json
{
    "code": 200,
    "status": "success",
    "data": {
        "access_token": "short-lived-token-xtz...",
        "refresh_token": "long-lived-token-abc...",
        "expires_in": 3600,
        "token_type": "Bearer"
    }
}
```

### 2. Akses Resource

Client menggunakan `access_token` di header Authorization.

```
Authorization: Bearer short-lived-token-xtz...
```

### 3. Token Expired

Jika `access_token` kadaluarsa, server mengembalikan `401 Unauthorized`.

### 4. Refresh Token

Client menangkap error 401 dan secara otomatis me-request token baru menggunakan endpoint refresh.

**Request:** `POST /api/refresh-token`
**Body:** `{ "refresh_token": "long-lived-token-abc..." }`

**Response:**

```json
{
    "access_token": "new-short-lived-token-fry...",
    "refresh_token": "new-long-lived-token-def..." // (Optional: Refresh Token Rotation)
}
```

---

## ⚙️ Konfigurasi (Rencana)

Konfigurasi akan dilakukan di `config/sanctum_refresh.php` (belum ada).

---

## 🔒 Fitur Keamanan yang Direncanakan

1.  **Token Rotation**: Refresh token ikut diperbarui setiap kali dipakai. Token lama hangus.
2.  **Revocation**: User bisa melihat list device yang login dan me-revoke akses (logout paksa) per device.
3.  **Short Lived Access**: Mengurangi dampak jika access token dicuri.

---

## 📝 Implementasi Client (Frontend)

Contoh logika _interceptor_ di Axios untuk handle refresh token otomatis:

```javascript
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Jika 401 dan belum pernah retry
        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                // Panggil endpoint refresh
                const { data } = await axios.post("/api/refresh-token", {
                    refresh_token: localStorage.getItem("refresh_token"),
                });

                // Simpan token baru
                localStorage.setItem("access_token", data.access_token);

                // Update header request yang gagal tadi
                originalRequest.headers["Authorization"] =
                    "Bearer " + data.access_token;

                // Coba request ulang
                return axios(originalRequest);
            } catch (err) {
                // Refresh gagal (token expired/invalid), logout user
                window.location.href = "/login";
            }
        }
        return Promise.reject(error);
    }
);
```
