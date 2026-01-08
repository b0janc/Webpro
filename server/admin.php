<?php
// --- BAGIAN 1: LOGIKA PHP (BACKEND) ---
session_start();
include 'koneksi.php';

// A. Logika Hapus Data
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM pesanan WHERE id='$id'");
    header("Location: admin.php"); // Refresh halaman
    exit();
}

// B. Logika Update Status (Saat dropdown diganti)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'update_status') {
    $id = $_GET['id'];
    $status_baru = $_GET['status'];
    mysqli_query($koneksi, "UPDATE pesanan SET status='$status_baru' WHERE id='$id'");
    header("Location: admin.php"); // Refresh halaman
    exit();
}

// C. Logika Statistik (Hitung Angka untuk Kartu Atas)
$tgl_ini = date('Y-m-d');

// 1. Hitung Total Pesanan Hari Ini
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal) = '$tgl_ini'");
$d_total = mysqli_fetch_assoc($q_total);
$jml_pesanan = $d_total['total'];

// 2. Hitung Pesanan Pending (Yang belum selesai)
$q_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status != 'done'");
$d_pending = mysqli_fetch_assoc($q_pending);
$jml_pending = $d_pending['total'];

// 3. Hitung Pendapatan Hari Ini
$q_omset = mysqli_query($koneksi, "SELECT SUM(harga) as total FROM pesanan WHERE DATE(tanggal) = '$tgl_ini'");
$d_omset = mysqli_fetch_assoc($q_omset);
$jml_omset = $d_omset['total'] ?? 0; // Jika tidak ada pesanan, set 0
?>