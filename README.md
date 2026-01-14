# 🍚 Sistem Informasi UMKM Nasi Goreng K5 (Webpro)

![Project Status](https://img.shields.io/badge/Status-Completed-success)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1)
![Design](https://img.shields.io/badge/Design-Earthy%20Minimalist-D6D2CA)

Project ini adalah aplikasi web pemesanan makanan (Point of Sales / Kiosk Mode) untuk UMKM "Nasi Goreng K5". Dibangun menggunakan PHP Native dan MySQL dengan antarmuka modern bertema **"Earthy Minimalist / Editorial"**.

Aplikasi ini mencakup sisi **Pelanggan (Kiosk)** untuk pemesanan mandiri dan sisi **Admin (Kitchen Display)** untuk memantau pesanan secara real-time.

## ✨ Fitur Utama

### 🖥️ Sisi Pelanggan (Client/Kiosk)
- **Modern Landing Page:** Tampilan beranda yang estetis dan responsif.
- **Kiosk Ordering System:**
  - Pemilihan menu dinamis berdasarkan kategori (Makanan, Minuman, Snack).
  - Sistem Keranjang Belanja (Add/Remove item) menggunakan Session.
  - Perhitungan Total Harga otomatis.
- **Feedback System:** Formulir ulasan pelanggan dengan rating bintang.
- **Daftar Menu & Biodata Tim:** Halaman informasi statis yang menarik.

### 🛡️ Sisi Admin (Dashboard)
- **Secure Login:** Sistem autentikasi admin.
- **Kitchen Display System (Real-time):** Dashboard otomatis refresh setiap 30 detik untuk cek pesanan baru.
- **Manajemen Status Pesanan:**
  - Ubah status: *Pending* ➝ *Cooking* ➝ *Completed*.
- **Hapus Pesanan:** Fitur pembatalan/penghapusan data pesanan.
- **Monitoring Feedback:** Melihat ulasan dan rating yang masuk dari pelanggan.

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP (Native)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3 (Custom Variables), JavaScript
- **Icons:** [Phosphor Icons](https://phosphoricons.com/)
- **Font:** Manrope & Plus Jakarta Sans (via Google Fonts)

---

## 🚀 Cara Instalasi & Menjalankan

Ikuti langkah-langkah ini untuk menjalankan project di komputer lokal (Localhost):

### 1. Persiapan Lingkungan
Pastikan Anda sudah menginstall **XAMPP** atau aplikasi server lokal sejenis (Laragon/MAMP).

### 2. Clone Repository
```bash
git clone [https://github.com/b0janc/Webpro.git](https://github.com/b0janc/Webpro.git)

-- 1. Tabel Kategori
CREATE TABLE categories (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100),
    icon VARCHAR(50)
);

-- 2. Tabel Produk
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id VARCHAR(50),
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,0),
    img VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 3. Tabel Admin
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 4. Tabel Pesanan
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) DEFAULT 'Guest',
    total_price DECIMAL(10,0),
    status ENUM('pending', 'cooking', 'completed', 'cancelled') DEFAULT 'pending',
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabel Detail Pesanan
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_name VARCHAR(100),
    price DECIMAL(10,0),
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- 6. Tabel Feedback
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100),
    rating INT,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- INSERT DATA DUMMY
INSERT INTO categories VALUES 
('makanan', 'Makanan', 'ph-bowl-food'), 
('minuman', 'Minuman', 'ph-brandy'), 
('snack', 'Snack', 'ph-cookie');

INSERT INTO products (category_id, name, description, price, img) VALUES
('makanan', 'Nasgor Jawa', 'Ayam kampung, manis gurih.', 25000, '[https://images.unsplash.com/photo-1603133872878-684f208fb84b](https://images.unsplash.com/photo-1603133872878-684f208fb84b)'),
('minuman', 'Es Teh Solo', 'Wangi melati autentik.', 8000, '[https://images.unsplash.com/photo-1544025162-d76690b67f1c](https://images.unsplash.com/photo-1544025162-d76690b67f1c)');

INSERT INTO admins (username, password) VALUES ('admin', 'admin123');

Webpro/
├── css/                  # File Styling (CSS)
│   ├── landpage.css
│   ├── form.css
│   ├── feedback.css
│   └── ...
├── images/               # Aset Gambar (Jika ada)
├── admin.php             # Dashboard Admin
├── admin_feedback.php    # Halaman Admin Feedback
├── checkout.php          # Proses Checkout
├── display.php           # Halaman Pemesanan (Kiosk)
├── feedback.php          # Halaman Feedback User
├── index.php             # Landing Page
├── login.php             # Login Page
├── submit_feedback.php   # Logika submit ulasan
└── README.md             # Dokumentasi Project