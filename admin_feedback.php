<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "db_kiosk");

// Logic Hapus Feedback (Opsional)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM feedback WHERE id='$id'");
    header("Location: admin_feedback.php");
}

$feedbacks = mysqli_query($conn, "SELECT * FROM feedback ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Feedback List</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Menggunakan style dasar yang sama dengan admin.php agar konsisten */
        :root { --bg-dark: #54524A; --bg-card: #FDFCF8; --text-dark: #2D2D2A; }
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Manrope', sans-serif; }
        body { background-color: #f4f4f4; display: flex; min-height: 100vh; }

        .sidebar { width: 250px; background: var(--bg-dark); color: white; padding: 30px; position: fixed; height: 100%; display:flex; flex-direction:column; justify-content:space-between; }
        .sidebar h2 { margin-bottom: 40px; font-weight: 800; letter-spacing: 1px; }
        .menu a { display: flex; align-items: center; gap: 10px; color: #ccc; text-decoration: none; padding: 15px 0; border-bottom: 1px solid #666; }
        .menu a:hover, .menu a.active { color: white; font-weight: 700; }

        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        h1 { color: var(--text-dark); margin-bottom: 30px; }

        /* TABEL STYLE */
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        th, td { padding: 15px 20px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; font-weight: 700; color: var(--text-dark); }
        .rating-star { color: #D3A84C; }
        .btn-delete { color: red; text-decoration: none; font-weight: 700; font-size: 0.9rem; }
        .btn-delete:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <h2>K5 ADMIN</h2>
            <div class="menu">
                <a href="admin.php"><i class="ph ph-squares-four"></i> Pesanan Aktif</a>
                <a href="#" class="active"><i class="ph ph-chat-text"></i> Feedback Masuk</a>
                <a href="#"><i class="ph ph-chart-pie-slice"></i> Laporan</a>
            </div>
        </div>
        <a href="admin.php?action=logout" style="color: #EF4444; text-decoration: none; font-weight: 700;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Feedback Pelanggan</h1>

        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Nama Pelanggan</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($feedbacks)): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td class="rating-star">
                        <?= $row['rating'] ?> <i class="ph-fill ph-star"></i>
                    </td>
                    <td style="color: #555;"><?= htmlspecialchars($row['comment']) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Hapus ulasan ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>