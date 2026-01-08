<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "db_kiosk");

if (empty($_SESSION['cart'])) {
    header("Location: form.php"); // Kembali jika keranjang kosong
    exit;
}

// 1. Hitung Total
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['qty']);
}
$total = $subtotal + ($subtotal * 0.1); // + Pajak 10%

// 2. Simpan ke Tabel 'orders'
$queryOrder = "INSERT INTO orders (customer_name, total_price, status) VALUES ('Pelanggan Kiosk', '$total', 'pending')";
if (mysqli_query($conn, $queryOrder)) {
    $order_id = mysqli_insert_id($conn); // Ambil ID pesanan yang baru dibuat

    // 3. Simpan Detail Item ke 'order_items'
    $queryItem = "INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($queryItem);

    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("isdi", $order_id, $item['name'], $item['price'], $item['qty']);
        $stmt->execute();
    }

    // 4. Kosongkan Keranjang & Redirect
    unset($_SESSION['cart']);
    echo "<script>alert('Pesanan Berhasil Dikirim ke Dapur!'); window.location='feedback.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>