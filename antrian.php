<?php
// 1. KONEKSI DATABASE
$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. QUERY DATABASE
$query_pending = mysqli_query($conn, "SELECT * FROM orders WHERE status = 'pending' ORDER BY id ASC");
$query_cooking = mysqli_query($conn, "SELECT * FROM orders WHERE status = 'cooking' ORDER BY id ASC");
$query_ready   = mysqli_query($conn, "SELECT * FROM orders WHERE status = 'completed' ORDER BY id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5"> 
    <title>Antrian Pesanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        :root {
            --bg-body: #0f172a;       
            --bg-panel: #1e293b;      
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            
            --color-pending: #64748b; 
            --color-cooking: #f59e0b; 
            --color-ready: #10b981;   
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-body);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* HEADER ATAS */
        .top-bar {
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: rgba(15, 23, 42, 0.95);
        }
        
        /* Tombol Kembali Style */
        .btn-back {
            text-decoration: none;
            color: var(--text-primary);
            background: rgba(255,255,255,0.1);
            padding: 8px 12px;
            border-radius: 8px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(-3px);
        }

        .brand-container {
            display: flex;
            align-items: center;
        }

        .brand { font-weight: 800; font-size: 1.2rem; letter-spacing: 1px; }
        .clock { font-weight: 600; color: var(--text-secondary); font-variant-numeric: tabular-nums; }

        /* GRID UTAMA */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr; 
            gap: 2px; 
            height: 100%;
            background: rgba(255,255,255,0.05); 
        }

        /* PANEL */
        .panel {
            background: var(--bg-body);
            display: flex;
            flex-direction: column;
            padding: 0 20px;
            position: relative;
        }

        /* HEADER PER KOLOM */
        .panel-header {
            padding: 25px 0;
            text-align: center;
            position: sticky;
            top: 0;
            background: var(--bg-body);
            z-index: 10;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            margin-bottom: 20px;
        }

        .panel-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border-radius: 50px;
        }

        /* Warna Badge Judul */
        .h-pending { color: var(--color-pending); background: rgba(100, 116, 139, 0.1); }
        .h-cooking { color: var(--color-cooking); background: rgba(245, 158, 11, 0.1); }
        .h-ready { color: var(--color-ready); background: rgba(16, 185, 129, 0.1); }

        /* LIST CONTAINER */
        .list-container {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 30px;
        }
        .list-container::-webkit-scrollbar { width: 0; }

        /* KARTU ORDER */
        .card {
            background: var(--bg-panel);
            margin-bottom: 15px;
            padding: 20px 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.03);
            position: relative;
            overflow: hidden;
        }

        .card-number { font-size: 2.2rem; font-weight: 800; line-height: 1; }
        .card-name { font-size: 1.1rem; font-weight: 600; color: var(--text-secondary); text-align: right; }
        .card-hash { font-size: 1rem; color: #475569; font-weight: 400; margin-right: 5px; }

        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.95); }
            100% { opacity: 1; transform: scale(1); }
        }
        .card { animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

        /* STYLE KHUSUS PER STATUS */
        .pending-col .card { opacity: 0.7; }
        .pending-col .card-number { color: var(--color-pending); }

        .cooking-col .card { border-left: 4px solid var(--color-cooking); }
        .cooking-col .card-number { color: var(--text-primary); }
        .cooking-col .status-dot {
            height: 8px; width: 8px; background: var(--color-cooking);
            border-radius: 50%; display: inline-block; margin-right: 10px;
            box-shadow: 0 0 10px var(--color-cooking);
            animation: pulse 2s infinite;
        }

        .ready-col .card {
            background: var(--color-ready);
            color: #064e3b; 
            border: none;
            transform: scale(1.02);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }
        .ready-col .card-number { color: #fff; font-size: 3rem; }
        .ready-col .card-name { color: #d1fae5; font-size: 1.3rem; }
        .ready-col .card-hash { color: rgba(255,255,255,0.6); }

        .empty {
            text-align: center; color: #475569; margin-top: 50px;
            font-style: italic; font-size: 0.9rem;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

    </style>
</head>
<body>

    <div class="top-bar">
        <div class="brand-container">
            <a href="index.php" class="btn-back">
                <i class="ph ph-arrow-left"></i> Kembali
            </a>
            <div class="brand">ANTRIAN PESANAN</div>
        </div>
        <div class="clock" id="jam">00:00</div>
    </div>

    <div class="main-grid">
        
        <div class="panel pending-col">
            <div class="panel-header">
                <span class="panel-title h-pending"><i class="ph ph-hourglass"></i> Menunggu</span>
            </div>
            <div class="list-container">
                <?php if(mysqli_num_rows($query_pending) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_pending)): ?>
                        <div class="card">
                            <div><span class="card-hash">#</span><span class="card-number"><?= $row['id'] ?></span></div>
                            <div class="card-name"><?= htmlspecialchars(substr($row['customer_name'], 0, 15)) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty">Tidak ada antrian</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel cooking-col">
            <div class="panel-header">
                <span class="panel-title h-cooking"><i class="ph ph-fire"></i> Dimasak</span>
            </div>
            <div class="list-container">
                <?php if(mysqli_num_rows($query_cooking) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_cooking)): ?>
                        <div class="card">
                            <div>
                                <span class="status-dot"></span>
                                <span class="card-hash">#</span><span class="card-number"><?= $row['id'] ?></span>
                            </div>
                            <div class="card-name"><?= htmlspecialchars($row['customer_name']) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty">Dapur standby</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel ready-col">
            <div class="panel-header">
                <span class="panel-title h-ready"><i class="ph ph-check-circle"></i> Siap Diambil</span>
            </div>
            <div class="list-container">
                <?php if(mysqli_num_rows($query_ready) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($query_ready)): ?>
                        <div class="card">
                            <div><span class="card-hash">#</span><span class="card-number"><?= $row['id'] ?></span></div>
                            <div class="card-name"><?= htmlspecialchars($row['customer_name']) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty">Belum ada pesanan selesai</div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('jam').textContent = timeString;
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>

</body>
</html>