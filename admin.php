<?php
session_start(); // 1. Mulai Session

// 2. CEK KEAMANAN: Jika belum login, tendang ke login.php
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "db_kiosk");

// -- LOGIKA UPDATE STATUS --
if (isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['new_status'];
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id='$id'");
    header("Location: admin.php");
}

// -- LOGIKA HAPUS PESANAN (BARU) --
if (isset($_POST['delete_order'])) {
    $id = $_POST['order_id'];
    
    // Hapus detail item terlebih dahulu
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id='$id'");
    
    // Hapus data pesanan utama
    mysqli_query($conn, "DELETE FROM orders WHERE id='$id'");
    
    header("Location: admin.php");
}

// -- LOGOUT LOGIC --
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

// -- AMBIL DATA PESANAN --
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE status != 'cancelled' ORDER BY order_date DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <meta http-equiv="refresh" content="30"> 
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        :root {
            --bg-dark: #54524A;
            --bg-card: #FDFCF8;
            --text-dark: #2D2D2A;
            --status-pending: #F59E0B;
            --status-cooking: #3B82F6;
            --status-completed: #10B981;
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family: 'Manrope', sans-serif; }
        body { background-color: #f4f4f4; display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 250px; background-color: var(--bg-dark); color: white;
            padding: 30px; position: fixed; height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .sidebar h2 { margin-bottom: 40px; font-weight: 800; letter-spacing: 1px; }
        .menu a {
            display: flex; align-items: center; gap: 10px;
            color: #ccc; text-decoration: none; padding: 15px 0;
            border-bottom: 1px solid #666; transition: 0.3s;
        }
        .menu a:hover, .menu a.active { color: white; font-weight: 700; }
        
        .logout-btn {
            display: block; text-align: center; background: #EF4444; color: white;
            padding: 12px; text-decoration: none; border-radius: 8px; font-weight: 700;
        }

        /* MAIN CONTENT */
        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: var(--text-dark); }

        /* ORDER CARDS GRID */
        .order-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;
        }
        .order-card {
            background: white; border-radius: 16px; padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid gray;
        }
        .status-pending { border-left-color: var(--status-pending); }
        .status-cooking { border-left-color: var(--status-cooking); }
        .status-completed { border-left-color: var(--status-completed); opacity: 0.6; }

        .card-header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .order-id { font-weight: 800; font-size: 1.2rem; }
        .order-time { font-size: 0.8rem; color: #888; }
        .items-list { margin-bottom: 20px; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.95rem; }
        .qty { font-weight: 700; color: var(--text-dark); margin-right: 10px; }
        .card-actions { display: flex; gap: 10px; margin-top: 15px; }
        
        button {
            flex: 1; padding: 10px; border: none; border-radius: 8px;
            cursor: pointer; font-weight: 700; color: white; transition: 0.2s;
        }
        .btn-cook { background-color: var(--status-cooking); }
        .btn-done { background-color: var(--status-completed); }
        .btn-cancel { background-color: #EF4444; } /* Merah */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; color: white; text-transform: uppercase; font-weight: 700; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div>
            <h2>K5 ADMIN</h2>
            <div class="menu">
                <a href="#" class="active"><i class="ph ph-squares-four"></i> Pesanan Aktif</a>
                <a href="admin_feedback.php"><i class="ph ph-chart-pie-slice"></i> Laporan</a>
            </div>
        </div>
        
        <a href="?action=logout" class="logout-btn">
            <i class="ph ph-sign-out"></i> Logout
        </a>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Dapur</h1>
                <p>Halo, <b><?= $_SESSION['admin_name'] ?></b>! Pantau pesanan.</p>
            </div>
            <div style="text-align:right;">
                <h3><?= date('d M Y') ?></h3>
                <span id="jam"><?= date('H:i') ?></span>
            </div>
        </div>

        <div class="order-grid">
            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                
                <div class="order-card status-<?= $order['status'] ?>">
                    <div class="card-header">
                        <div>
                            <div class="order-id">#<?= $order['id'] ?></div>
                            <div class="order-time"><?= date('H:i', strtotime($order['order_date'])) ?> WIB</div>
                        </div>
                        <div>
                            <?php 
                                $color = '#ccc';
                                if($order['status']=='pending') $color = 'var(--status-pending)';
                                if($order['status']=='cooking') $color = 'var(--status-cooking)';
                                if($order['status']=='completed') $color = 'var(--status-completed)';
                            ?>
                            <span class="badge" style="background:<?= $color ?>"><?= $order['status'] ?></span>
                        </div>
                    </div>

                    <div class="items-list">
                        <?php
                        $oid = $order['id'];
                        $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id = '$oid'");
                        while($item = mysqli_fetch_assoc($items)):
                        ?>
                            <div class="item-row">
                                <span><span class="qty"><?= $item['quantity'] ?>x</span> <?= $item['product_name'] ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <form method="POST" class="card-actions">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        
                        <?php if($order['status'] == 'pending'): ?>
                            <button type="submit" name="update_status" value="cooking" class="btn-cook">
                                <i class="ph ph-fire"></i> Masak
                            </button>
                        <?php elseif($order['status'] == 'cooking'): ?>
                            <button type="submit" name="update_status" value="completed" class="btn-done">
                                <i class="ph ph-check"></i> Selesai
                            </button>
                        <?php endif; ?>

                        <button type="submit" name="delete_order" class="btn-cancel" onclick="return confirm('Yakin ingin menghapus pesanan ini?')">
                            <i class="ph ph-trash"></i> Hapus
                        </button>
                    </form>

                </div>

            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>