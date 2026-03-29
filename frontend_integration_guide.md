# Panduan Integrasi Frontend ke Backend Manajemen Proker

Dokumen ini menjelaskan tahapan dasar bagaimana menghubungkan aplikasi Frontend (seperti React, Vue, dst) ke API Backend Manajemen Proker ini.

## 1. Aturan CORS (Cross-Origin Resource Sharing)

Agar penarikan API dari Frontend tidak diblokir oleh browser (yang menghasilkan pesan _error CORS merah_ di console), backend ini sudah dikonfigurasi secara spesifik:

- **URL Frontend yang Diizinkan:** Secara bawaan (_default_), backend akan menerima request dari origin `http://localhost:5174` (port bawahan Vite/Vue/React terbaru).
- **Cara Menyesuaikan URL:** Jika frontend Anda menggunakan port yang berbeda (misalnya `http://localhost:3000`), Anda **wajib** mengubah nilai variabel `FRONTEND_URL` di dalam file `.env` di backend dan me-restart server backend Anda.
    ```env
    FRONTEND_URL=http://localhost:3000
    ```
- **Aturan Headers/Methods:** Semua HTTP Method (GET, POST, PUT, DELETE) diizinkan selama URL origin-nya dikenali.

## 2. Cara Kerja Akses Autentikasi (Bearer Token)

Aplikasi API ini dikunci menggunakan skema **Bearer Token** (lewat Laravel Sanctum). Artinya, pengunjung umum tidak bisa begitu saja membaca data. Frontend harus "Buktikan Identitas" di setiap _request_ yang dikirim dengan melampirkan sebuah **Kunci (Token)**.

### A. Alur Login (Mendapatkan Token)

Untuk mendapatkan Token, Frontend harus melakukan POST request ke rute login. Rute ini bersifat publik (bebas diakses).

- **Endpoint:** `POST /api/login`
- **Body Request:**
    ```json
    {
        "email": "user@example.com",
        "password": "password123"
    }
    ```
    Jika email dan password benar, backend akan merespon dengan data _user_ dan sebuah teks panjang bernama `token`. Tugas Frontend adalah **menyimpan** teks `token` tersebut, entah di dalam `localStorage`, `Cookies`, atau `State Management` aplikasi.

### B. Mengakses Endpoint Lain (Menggunakan Token)

Setelah token disimpan, gunakan token tadi pada setiap request ke endpoint lainnya yang dibatasi otorisasinya (seperti mengambil list menu, data user, dsb).

Caranya, lampirkan token tersebut melalui mekanisme HTTP **Headers**, dengan menyelipkan `Authorization`.

**Format Wajib Headers:**

```json
{
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer <TOKEN_YANG_DISIMPAN_SEBELUMNYA>"
}
```

> Ingat, kata `Bearer ` wajib dituliskan sebelum kunci tokennya, lengkap dengan spasi.

## 3. Contoh Mini Kode Frontend (Fetch API)

Berikut adalah contoh praktis bagi programmer frontend (bisa dijalankan di browser console):

```javascript
// --- PROSES LOGIN ---
async function login() {
    const respon = await fetch("http://localhost:8000/api/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body: JSON.stringify({ email: "tester@mail.com", password: "rahasia" }),
    });
    const hasil = await respon.json();

    // Simpan token di local storage agar tidak hilang saat reload
    localStorage.setItem("kunci_api", hasil.token);
    console.log("Token disimpan:", hasil.token);
}

// --- PROSES AMBIL DATA SETELAH LOGIN ---
async function getMenu() {
    // Ambil kunci token dari storage
    const token = localStorage.getItem("kunci_api");

    const respon = await fetch("http://localhost:8000/api/sys-menus", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            Authorization: `Bearer ${token}`, // Lampirkan Kunci!
        },
    });

    const hasilData = await respon.json();
    console.log("Data menu:", hasilData);
}
```

### Ringkasan Untuk Frontend / Junior Programmer:

1. Gagal CORS? Pastikan URL frontend Anda tertulis di file `.env` backend sebagai `FRONTEND_URL`.
2. Ditolak / _Unauthorized_ (401)? Pastikan Anda menyisipkan `Authorization: Bearer <token_anda>` pada Request Headers.
3. Token didapat dari balasan respons saat hit endpoint `/api/login`. Panggil ini pertama kali!
