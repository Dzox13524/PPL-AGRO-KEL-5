# Dokumentasi API - Hidara (PPL Agro)

Dokumentasi resmi REST API untuk modul **Autentikasi** dan **Manajemen Pengguna (CRUD Users)**.

---

## 📌 Informasi Umum

- **Base URL**: `http://localhost:8000/api`
- **Format Pertukaran Data**: `application/json`
- **Header Wajib**:
  ```http
  Accept: application/json
  Content-Type: application/json
  ```
- **Header Autentikasi (Endpoint Terproteksi)**:
  ```http
  Authorization: Bearer <access_token>
  ```

---

## 🔑 Akun Awal (Seeder)

Untuk keperluan pengujian backend, akun owner pertama dapat dibuat/di-seed melalui perintah:
```bash
php artisan db:seed
```

- **Username**: `owner`
- **Password**: `owner123`
- **Role**: `owner`

> **Catatan:** Sistem ini tidak menyediakan registrasi publik (`register`). Seluruh pembuatan akun baru dilakukan oleh pengguna dengan role `owner`.

---

## 👥 Hak Akses (Role-Based Access Control)

| Role | Deskripsi | Hak Akses |
| :--- | :--- | :--- |
| **`owner`** | Pemilik / Administrator utama sistem | Login, Logout, Cek profil (`/me`), dan **penuh akses CRUD User** |
| **`karyawan`** | Karyawan / Staf operasional | Login, Logout, Cek profil (`/me`). **Dilarang** mengakses CRUD User (`403 Forbidden`) |

---

## 📑 Daftar Endpoint

### 1. Autentikasi (`/api`)

| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | Publik | Masuk ke sistem dan mendapatkan token Bearer |
| `POST` | `/api/logout` | `auth:sanctum` | Keluar dari sistem dan mencabut token aktif |
| `GET` | `/api/me` | `auth:sanctum` | Mendapatkan data akun pengguna yang sedang login |

---

### 2. Manajemen Pengguna (`/api/users`)

> ⚠️ **Hanya Role `owner`**: Endpoint ini dilindungi middleware `auth:sanctum` dan `role.owner`.

| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/users` | Owner | Menampilkan seluruh daftar akun pengguna |
| `POST` | `/api/users` | Owner | Menambahkan akun pengguna baru (`owner`/`karyawan`) |
| `GET` | `/api/users/{id}` | Owner | Menampilkan detail akun pengguna berdasarkan ID |
| `PUT` / `PATCH` | `/api/users/{id}` | Owner | Memperbarui username, role, dan/atau password |
| `DELETE` | `/api/users/{id}` | Owner | Menghapus akun pengguna *(tidak dapat menghapus diri sendiri)* |

---

## 📖 Rincian Endpoint & Contoh Payload

### 1. Login

Mengautentikasi pengguna menggunakan username dan password.

- **URL**: `POST /api/login`
- **Akses**: Publik
- **Request Body**:
  | Field | Tipe | Wajib | Keterangan |
  | :--- | :--- | :--- | :--- |
  | `username` | string | Ya | Username akun yang terdaftar |
  | `password` | string | Ya | Password akun |

#### Contoh Request:
```bash
curl -X POST "http://localhost:8000/api/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "owner",
    "password": "owner123"
  }'
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Login berhasil.",
  "access_token": "1|qXyZ123456789abcdef...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "username": "owner",
    "role": "owner",
    "created_at": "2026-10-12T04:43:00.000000Z",
    "edited_at": "2026-10-12T04:43:00.000000Z"
  }
}
```

#### Respon Gagal (`401 Unauthorized`):
```json
{
  "message": "Username atau password yang Anda masukkan salah."
}
```

---

### 2. Logout

Mencabut access token yang sedang digunakan.

- **URL**: `POST /api/logout`
- **Akses**: Terautentikasi (`auth:sanctum`)
- **Headers**: `Authorization: Bearer <token>`

#### Contoh Request:
```bash
curl -X POST "http://localhost:8000/api/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..."
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Logout berhasil. Token telah dicabut."
}
```

---

### 3. Profil Pengguna (`/me`)

Mendapatkan informasi pengguna yang saat ini sedang login.

- **URL**: `GET /api/me`
- **Akses**: Terautentikasi (`auth:sanctum`)
- **Headers**: `Authorization: Bearer <token>`

#### Contoh Request:
```bash
curl -X GET "http://localhost:8000/api/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..."
```

#### Respon Berhasil (`200 OK`):
```json
{
  "user": {
    "id": 1,
    "username": "owner",
    "role": "owner",
    "created_at": "2026-10-12T04:43:00.000000Z",
    "edited_at": "2026-10-12T04:43:00.000000Z"
  }
}
```

---

### 4. Daftar Seluruh Pengguna

Mengambil daftar seluruh akun yang tersimpan di sistem.

- **URL**: `GET /api/users`
- **Akses**: Owner saja (`auth:sanctum`, `role.owner`)
- **Headers**: `Authorization: Bearer <token>`

#### Contoh Request:
```bash
curl -X GET "http://localhost:8000/api/users" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..."
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Daftar pengguna berhasil diambil.",
  "data": [
    {
      "id": 2,
      "username": "budi_karyawan",
      "role": "karyawan",
      "created_at": "2026-10-12T04:45:00.000000Z",
      "edited_at": "2026-10-12T04:45:00.000000Z"
    },
    {
      "id": 1,
      "username": "owner",
      "role": "owner",
      "created_at": "2026-10-12T04:43:00.000000Z",
      "edited_at": "2026-10-12T04:43:00.000000Z"
    }
  ]
}
```

#### Respon Jika Diakses Karyawan (`403 Forbidden`):
```json
{
  "message": "Akses ditolak. Hanya owner yang diizinkan mengelola akun pengguna."
}
```

---

### 5. Tambah Pengguna Baru

Membuat akun pengguna baru (bisa role `owner` atau `karyawan`).

- **URL**: `POST /api/users`
- **Akses**: Owner saja (`auth:sanctum`, `role.owner`)
- **Headers**: `Authorization: Bearer <token>`
- **Request Body**:
  | Field | Tipe | Wajib | Aturan & Keterangan |
  | :--- | :--- | :--- | :--- |
  | `username` | string | Ya | Min. 3 karakter, Max. 50 karakter, Unique (belum terdaftar) |
  | `password` | string | Ya | Min. 6 karakter, Max. 255 karakter |
  | `role` | string | Ya | Harus salah satu dari: `owner` atau `karyawan` |

#### Contoh Request:
```bash
curl -X POST "http://localhost:8000/api/users" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..." \
  -d '{
    "username": "siti_karyawan",
    "password": "password123",
    "role": "karyawan"
  }'
```

#### Respon Berhasil (`201 Created`):
```json
{
  "message": "Akun pengguna berhasil dibuat.",
  "data": {
    "id": 3,
    "username": "siti_karyawan",
    "role": "karyawan",
    "created_at": "2026-10-12T04:47:00.000000Z",
    "edited_at": "2026-10-12T04:47:00.000000Z"
  }
}
```

#### Respon Validasi Gagal (`422 Unprocessable Content`):
```json
{
  "message": "Username sudah digunakan oleh akun lain. (and 1 more error)",
  "errors": {
    "username": [
      "Username sudah digunakan oleh akun lain."
    ],
    "password": [
      "Password minimal terdiri dari 6 karakter."
    ]
  }
}
```

---

### 6. Detail Pengguna

Melihat informasi akun tertentu berdasarkan ID.

- **URL**: `GET /api/users/{id}`
- **Akses**: Owner saja (`auth:sanctum`, `role.owner`)
- **Headers**: `Authorization: Bearer <token>`

#### Contoh Request:
```bash
curl -X GET "http://localhost:8000/api/users/3" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..."
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Detail pengguna berhasil diambil.",
  "data": {
    "id": 3,
    "username": "siti_karyawan",
    "role": "karyawan",
    "created_at": "2026-10-12T04:47:00.000000Z",
    "edited_at": "2026-10-12T04:47:00.000000Z"
  }
}
```

#### Respon Pengguna Tidak Ditemukan (`404 Not Found`):
```json
{
  "message": "Pengguna tidak ditemukan."
}
```

---

### 7. Perbarui Pengguna

Mengubah data akun pengguna (username, role, dan/atau password).

- **URL**: `PUT /api/users/{id}` atau `PATCH /api/users/{id}`
- **Akses**: Owner saja (`auth:sanctum`, `role.owner`)
- **Headers**: `Authorization: Bearer <token>`
- **Request Body**:
  | Field | Tipe | Wajib | Aturan & Keterangan |
  | :--- | :--- | :--- | :--- |
  | `username` | string | Ya | Min. 3 karakter, Max. 50 karakter, Unique (kecuali username milik user ini sendiri) |
  | `role` | string | Ya | Harus salah satu dari: `owner` atau `karyawan` |
  | `password` | string | Tidak | Opsional. Minimal 6 karakter. Jika diisi, password akan diganti baru |

#### Contoh Request:
```bash
curl -X PUT "http://localhost:8000/api/users/3" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..." \
  -d '{
    "username": "siti_supervisor",
    "role": "karyawan",
    "password": "passwordbaru456"
  }'
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Akun pengguna berhasil diperbarui.",
  "data": {
    "id": 3,
    "username": "siti_supervisor",
    "role": "karyawan",
    "created_at": "2026-10-12T04:47:00.000000Z",
    "edited_at": "2026-10-12T04:50:00.000000Z"
  }
}
```

---

### 8. Hapus Pengguna

Menghapus akun pengguna dari database.

- **URL**: `DELETE /api/users/{id}`
- **Akses**: Owner saja (`auth:sanctum`, `role.owner`)
- **Headers**: `Authorization: Bearer <token>`

> 🛡️ **Proteksi Keamanan**: Owner tidak diizinkan menghapus akun miliknya sendiri yang sedang login.

#### Contoh Request:
```bash
curl -X DELETE "http://localhost:8000/api/users/3" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|qXyZ123456789abcdef..."
```

#### Respon Berhasil (`200 OK`):
```json
{
  "message": "Akun pengguna berhasil dihapus."
}
```

#### Respon Gagal Menghapus Akun Sendiri (`400 Bad Request`):
```json
{
  "message": "Anda tidak dapat menghapus akun Anda sendiri."
}
```

---

## 🚦 Ringkasan Status Code HTTP

| Status Code | Makna | Kondisi Terjadinya |
| :--- | :--- | :--- |
| `200 OK` | Sukses | Permintaan login, logout, get data, update, atau delete berhasil |
| `201 Created` | Berhasil Dibuat | Akun pengguna baru berhasil ditambahkan |
| `400 Bad Request` | Permintaan Tidak Valid | Percobaan menghapus akun milik sendiri |
| `401 Unauthorized` | Belum Login / Kredensial Salah | Token tidak disertakan/kadaluarsa, atau username/password login salah |
| `403 Forbidden` | Akses Ditolak | Pengguna selain role `owner` mencoba mengakses endpoint manajemen user |
| `404 Not Found` | Data Tidak Ditemukan | User ID yang dicari tidak ada di database |
| `422 Unprocessable Content` | Validasi Input Gagal | Input tidak memenuhi syarat (contoh: username duplikat, password < 6 karakter) |
