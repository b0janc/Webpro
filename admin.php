<?php
session_start(); // 1. Mulai Session

// 2. CEK KEAMANAN: Jika belum login, tendang ke login.php
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// 3. KONEKSI DATABASE (SESUAIKAN DENGAN HOSTING)
$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// -- LOGIKA UPDATE STATUS --
if (isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    // Ambil value langsung dari tombol yang diklik (cooking/completed)
    $status = $_POST['update_status']; 
    
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$id'");
    
    // Refresh halaman agar data terupdate
    header("Location: admin.php");
    exit;
}

// -- LOGIKA HAPUS PESANAN --
if (isset($_POST['delete_order'])) {
    $id = $_POST['order_id'];
    
    // Hapus detail item terlebih dahulu (Foreign Key)
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id='$id'");
    
    // Hapus data pesanan utama
    mysqli_query($conn, "DELETE FROM orders WHERE id='$id'");
    
    header("Location: admin.php");
    exit;
}

// -- LOGOUT LOGIC --
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// -- AMBIL DATA PESANAN --
// Mengambil semua pesanan kecuali yang dibatalkan, urut dari yang terbaru
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE status != 'cancelled' ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - K5</title>
    <meta http-equiv="refresh" content="30"> 
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        :root {
            --bg-dark: #1F2937;
            --bg-card: #FDFCF8;
            --text-dark: #2D2D2A;
            --status-pending: #F59E0B;   /* Kuning */
            --status-cooking: #3B82F6;   /* Biru */
            --status-completed: #10B981; /* Hijau */
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Manrope', sans-serif; }
        body { background-color: #f3f4f6; display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 260px; background-color: var(--bg-dark); color: white;
            padding: 30px; position: fixed; height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
            z-index: 100;
        }
        .sidebar h2 { margin-bottom: 40px; font-weight: 800; letter-spacing: 1px; color: #fbbf24; }
        .menu a {
            display: flex; align-items: center; gap: 12px;
            color: #9ca3af; text-decoration: none; padding: 15px 0;
            border-bottom: 1px solid #374151; transition: 0.3s;
            font-size: 16px;
        }
        .menu a:hover, .menu a.active { color: white; font-weight: 700; padding-left: 5px; }
        
        .logout-btn {
            display: block; text-align: center; background: #EF4444; color: white;
            padding: 12px; text-decoration: none; border-radius: 8px; font-weight: 700;
            transition: 0.3s;
        }
        .logout-btn:hover { background: #dc2626; }

        /* MAIN CONTENT */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #111827; font-size: 28px; font-weight: 800; }

        /* ORDER CARDS GRID */
        .order-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;
        }
        .order-card {
            background: white; border-radius: 16px; padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 6px solid gray;
            transition: transform 0.2s;
        }
        .order-card:hover { transform: translateY(-5px); }
        
        .status-pending { border-left-color: var(--status-pending); }
        .status-cooking { border-left-color: var(--status-cooking); background-color: #eff6ff; }
        .status-completed { border-left-color: var(--status-completed); opacity: 0.7; background-color: #f0fdf4; }

        .card-header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .order-id { font-weight: 800; font-size: 1.4rem; color: #111; }
        .customer-name { font-size: 1rem; font-weight: 600; color: #4b5563; margin-top: 4px; display: block; }
        .order-time { font-size: 0.85rem; color: #9ca3af; margin-top: 5px; display: block;}
        
        .items-list { margin-bottom: 20px; background: #f9fafb; padding: 15px; border-radius: 8px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.95rem; border-bottom: 1px dashed #e5e7eb; padding-bottom: 5px; }
        .item-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .qty { font-weight: 800; color: var(--text-dark); margin-right: 10px; min-width: 25px; display: inline-block; }
        
        .card-actions { display: flex; gap: 10px; margin-top: 15px; }
        
        button {
            flex: 1; padding: 12px; border: none; border-radius: 8px;
            cursor: pointer; font-weight: 700; color: white; transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        button:hover { opacity: 0.9; }
        .btn-cook { background-color: var(--status-cooking); }
        .btn-done { background-color: var(--status-completed); }
        .btn-cancel { background-color: #EF4444; flex: 0.4; } /* Tombol hapus lebih kecil */
        
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; color: white; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px; height: fit-content;}
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <h2>K5 ADMIN</h2>
            <div class="menu">
                <a href="#" class="active"><i class="ph ph-squares-four"></i> Kitchen Display</a>
                <a href="#"><i class="ph ph-chart-pie-slice"></i> Laporan (Soon)</a>
            </div>
        </div>
        
        <a href="?action=logout" class="logout-btn">
            <i class="ph ph-sign-out"></i> Logout
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Dapur & Pesanan</h1>
                <p>Halo, <b><?= isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin' ?></b>! Semangat masak hari ini.</p>
            </div>
            <div style="text-align:right;">
                <h3 style="font-size: 1.2rem;"><?= date('d M Y') ?></h3>
                <span id="jam" style="font-size: 1.5rem; font-weight: 800; color: var(--status-cooking);"><?= date('H:i') ?></span>
            </div>
        </div>

        <div class="order-grid">
            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                
                <div class="order-card status-<?= $order['status'] ?>">
                    <div class="card-header">
                        <div>
                            <div class="order-id">#<?= $order['id'] ?></div>
                            <span class="customer-name">
                                <i class="ph ph-user"></i> <?= htmlspecialchars($order['customer_name']) ?>
                            </span>
                            <span class="order-time">
                                <i class="ph ph-clock"></i> <?= date('H:i', strtotime($order['order_date'])) ?> WIB
                            </span>
                        </div>
                        <div>
                            <?php 
                                $color = '#ccc';
                                $label = $order['status'];
                                if($order['status']=='pending') { $color = 'var(--status-pending)'; $label = 'BARU'; }
                                if($order['status']=='cooking') { $color = 'var(--status-cooking)'; $label = 'DIMASAK'; }
                                if($order['status']=='completed') { $color = 'var(--status-completed)'; $label = 'SELESAI'; }
                            ?>
                            <span class="badge" style="background:<?= $color ?>"><?= $label ?></span>
                        </div>
                    </div>

                    <div class="items-list">
                        <?php
                        $oid = $order['id'];
                        $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = '$oid'");
                        if(mysqli_num_rows($items) > 0) {
                            while($item = mysqli_fetch_assoc($items)):
                        ?>
                            <div class="item-row">
                                <span><span class="qty"><?= $item['quantity'] ?>x</span> <?= $item['product_name'] ?></span>
                            </div>
                        <?php 
                            endwhile;
                        } else {
                            echo "<small style='color:red;'>Item tidak ditemukan (Error DB)</small>";
                        }
                        ?>
                    </div>

                    <form method="POST" class="card-actions">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <?php if($order['status'] == 'pending'): ?>
                            <button type="submit" name="update_status" value="cooking" class="btn-cook">
                                <i class="ph ph-fire"></i> Mulai Masak
                            </button>
                        <?php elseif($order['status'] == 'cooking'): ?>
                            <button type="submit" name="update_status" value="completed" class="btn-done">
                                <i class="ph ph-check"></i> Selesai
                            </button>
                        <?php endif; ?>

                        <button type="submit" name="delete_order" class="btn-cancel" onclick="return confirm('Yakin ingin menghapus pesanan #<?= $order['id'] ?>?')">
                            <i class="ph ph-trash"></i>
                        </button>
                    </form>

                </div>

            <?php endwhile; ?>
            
            <?php if(mysqli_num_rows($orders) == 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #888;">
                    <i class="ph ph-coffee" style="font-size: 48px; margin-bottom: 10px;"></i>
                    <h3>Belum ada pesanan masuk.</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Update Jam Realtime
        setInterval(() => {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('jam').textContent = timeString;
        }, 1000);
    </script>

</body>
</html>