# Alur Login dan Sistem Role (Akses)

Dokumen ini menjelaskan alur login dan cara kerja sistem role (hak akses) pada aplikasi ini secara ringkas, agar mudah dipahami oleh developer baru atau AI Assistant.

## 1. Alur Login (Login Flow)

Proses login ditangani oleh method `login` di dalam `AuthController.php`. Berikut adalah urutan proses yang terjadi ketika user melakukan request login:

1. **Validasi Kredensial**: Sistem mengecek kombinasi `email` dan `password` menggunakan `Auth::attempt()`. Jika salah, akan mengembalikan response `401 Unauthorized`.
2. **Ambil Data User**: Jika login berhasil, sistem mengambil data lengkap model `User` berdasarkan email yang diinputkan.
3. **Generate Token**: Sistem membuat token otentikasi menggunakan Laravel Sanctum (`$user->createToken()`).
4. **Proses Single Sign-On (SSO)**:
   Sistem mengirim _HTTP POST Request_ ke URL SSO eksternal (terdapat di konfigurasi `services.sso.url`), mengirimkan `id_user` dan `id_organization` untuk memvalidasi atau sinkronisasi sesi.
5. **Cek Grup (Role) User**:
   Sistem mengecek user masuk ke grup mana melalui tabel `sys_user_groups`. Jika user memiliki lebih dari satu grup, sistem akan mengambil grup **pertama**.
6. **Ambil Menu & Hak Akses (Permissions)**:
   Sistem memanggil method `getmenu($id_group, $id_organization)`. Fungsi ini akan mencari daftar menu dan aksi (seperti _create, read, update, delete_) apa saja yang boleh diakses oleh grup tersebut berdasarkan koneksi di tabel `sys_group_permissions`.
7. **Simpan ke Cache (Performa)**:
   Hasil mapping menu dan akses disimpan sementara (`Cache::put()`) dengan durasi 6 jam. Ini bertujuan agar aplikasi tidak perlu melakukan query berat ke database setiap kali user pindah halaman.
8. **Response API**:
   Sistem mengembalikan response JSON berisi pesan sukses, token otentikasi, struktur menu berserta hak aksesnya, data SSO, dan detail profil user.

---

## 2. Cara Kerja Sistem Role / Akses (RBAC)

Aplikasi ini menggunakan konsep _Role-Based Access Control_ (RBAC) dengan pemetaan yang cukup terperinci. Sistem ini menghubungkan **User**, **Grup/Role**, **Menu**, dan **Aksi (Permission)** melalui beberapa tabel di database.

Berikut adalah hubungan (_Foreign Keys_) dari tabel-tabel migrasinya:

- **`sys_groups`**: Menyimpan daftar grup atau role (misal: Admin, Staff, Manager). [Primary Key: `id_sys_group`]
- **`sys_user_groups`**: Tabel _junction_ yang menghubungkan User dengan Grup.
    - `id_sys_group` berelasi (`references`) ke tabel `sys_groups`.
    - Artinya, satu User bisa memiliki banyak Grup, dan satu Grup bisa berisi banyak User.
- **`sys_menu`**: Menyimpan daftar navigasi menu aplikasi. [Primary Key: `id_sys_menu`]
- **`sys_permissions`**: Menyimpan daftar kode _action_ yang bisa dilakukan (contoh kode: `create`, `update`, `delete`, `approve`). [Primary Key: `id_sys_permission`]

- **`sys_menu_permissions`**: Tabel _junction_ yang memetakan aksi apa saja yang tersedia untuk suatu menu.
    - `id_sys_menu` berelasi ke `sys_menu`.
    - `id_sys_permission` berelasi ke `sys_permissions`.
    - Contoh: Menu "Dashboard" hanya punya aksi `read`. Menu "User" punya aksi `read`, `create`, `update`.

- **`sys_group_permissions`**: Tabel _junction_ final yang menentukan **Grup mana** yang diizinkan untuk melakukan aksi di **Menu apa**.
    - `id_sys_group` berelasi ke `sys_groups`.
    - `id_sys_menu_permission` berelasi ke `sys_menu_permissions`.
    - Sistem query akses di controller `getmenu` mengandalkan koneksi relasional ini agar saat mengecek hak akses, sistem tinggal melakukan `LEFT JOIN` dari tabel menu hingga group permission, tersaring oleh klausa `WHERE sgp.id_sys_group = ?`.

### Kesimpulan

Secara sederhana: User -> masuk ke Grup (Role) -> Grup diizinkan melakukan Aksi (Permission) tertentu -> Aksi tersebut tertaut/terikat ke Menu tertentu di tabel `sys_group_permissions`.
