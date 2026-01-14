<?php
session_start();

// 1. KONEKSI DATABASE (Sama seperti sebelumnya)
$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. CEK KERANJANG
if (empty($_SESSION['cart'])) {
    echo "<script>alert('Keranjang kosong!'); window.location='index.php';</script>";
    exit;
}

// ========================================================
// 3. TANGKAP NAMA PEMESAN (BAGIAN PENTING)
// ========================================================
// Kita cek: Apakah ada data 'customer_name' yang dikirim via POST?
// Jika ADA, pakai nama itu. Jika TIDAK ADA, pakai 'Pelanggan Kiosk'.
$nama_pemesan = isset($_POST['customer_name']) && !empty($_POST['customer_name']) 
                ? mysqli_real_escape_string($conn, $_POST['customer_name']) 
                : 'Pelanggan Kiosk';

// 4. HITUNG TOTAL HARGA
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_price += ($item['price'] * $item['qty']);
}

// 5. SIMPAN KE TABEL ORDERS
// Perhatikan kolom 'customer_name' diisi variabel $nama_pemesan
$query_order = "INSERT INTO orders (customer_name, total_price, status, order_date) 
                VALUES ('$nama_pemesan', '$total_price', 'pending', NOW())";

if (mysqli_query($conn, $query_order)) {
    
    // Dapatkan ID Order yang baru saja dibuat
    $order_id = mysqli_insert_id($conn);

    // 6. SIMPAN DETAIL ITEM KE TABEL ORDER_ITEMS
    foreach ($_SESSION['cart'] as $item) {
        $prod_name = $item['name'];
        $prod_price = $item['price'];
        $prod_qty = $item['qty'];

        $query_item = "INSERT INTO order_items (order_id, product_name, price, quantity) 
                       VALUES ('$order_id', '$prod_name', '$prod_price', '$prod_qty')";
        mysqli_query($conn, $query_item);
    }

    // 7. KOSONGKAN KERANJANG & REDIRECT SUKSES
    unset($_SESSION['cart']);
    
    // Tampilkan struk atau pesan sukses
    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Pesanan Berhasil</title>
        <link href='https://fonts.googleapis.com/css2?family=Manrope:wght@400;700&display=swap' rel='stylesheet'>
        <style>
            body { font-family: 'Manrope', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f4f4f4; margin: 0; }
            .success-card { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 400px; width: 90%; }
            .icon { font-size: 60px; color: #4CAF50; margin-bottom: 20px; }
            h1 { color: #333; margin-bottom: 10px; font-size: 24px; }
            p { color: #666; line-height: 1.6; }
            .btn { display: inline-block; margin-top: 20px; padding: 12px 30px; background: #333; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='success-card'>
            <div class='icon'>✅</div>
            <h1>Pesanan Diterima!</h1>
            <p>Terima kasih, <strong>$nama_pemesan</strong>.<br>Nomor Antrian: <strong>#$order_id</strong></p>
            <p>Mohon tunggu, pesanan Anda sedang disiapkan di dapur.</p>
            <a href='feedback.php' class='btn'>Kembali ke Menu</a>
        </div>
    </body>
    </html>
    ";

} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
?>