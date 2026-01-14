<?php
$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. QUERY AMBIL DATA MAKANAN
$query_makanan = "SELECT * FROM products WHERE category_id = 'makanan'";
$result_makanan = mysqli_query($conn, $query_makanan);

// 3. QUERY AMBIL DATA MINUMAN
$query_minuman = "SELECT * FROM products WHERE category_id = 'minuman'";
$result_minuman = mysqli_query($conn, $query_minuman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Kami - Rasa Nusantara</title>
    <link rel="stylesheet" href="css/menu.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="decorative-wave"></div>

    <main class="paper-container">
        
        <nav>
            <a href="index.php" class="logo">NASGOR.NGAWI</a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="menu.php" class="active">Menu</a>
                <a href="antrian.php" class="active">Antrian</a>
                <a href="feedback.php" class="active">Feedback</a>
                <a href="form.php" class="btn-pesan">Pesan</a>
            </div>
        </nav>

        <header class="menu-header">
            <span class="category-tag">OUR SELECTION</span>
            <h1>Daftar Menu<br>Pilihan.</h1>
            <p>Dikurasi dengan cita rasa autentik dan bahan premium.</p>
        </header>

        <section class="menu-section">
            <div class="section-title">
                <h2>Signature Fried Rice</h2>
                <div class="line"></div>
            </div>

            <div class="menu-grid">
                <?php 
                // Cek apakah ada data makanan
                if (mysqli_num_rows($result_makanan) > 0) {
                    while($makanan = mysqli_fetch_assoc($result_makanan)) { 
                ?>
                    
                <div class="menu-card">
                    <div class="card-image">
                        <img src="<?= $makanan['img']; ?>" alt="<?= $makanan['name']; ?>">
                    </div>
                    <div class="card-info">
                        <div class="info-top">
                            <h3><?= $makanan['name']; ?></h3>
                            <span class="price"><?= number_format($makanan['price'] / 1000, 0); ?>K</span>
                        </div>
                        <p class="desc"><?= $makanan['description']; ?></p>
                    </div>
                </div>

                <?php 
                    } // Tutup While Makanan
                } else {
                    echo "<p>Menu makanan sedang kosong.</p>";
                }
                ?>
            </div>
        </section>

        <section class="menu-section">
            <div class="section-title">
                <h2>Refreshments</h2>
                <div class="line"></div>
            </div>
            
            <div class="beverage-list">
                <?php 
                // Cek apakah ada data minuman
                if (mysqli_num_rows($result_minuman) > 0) {
                    while($minuman = mysqli_fetch_assoc($result_minuman)) { 
                ?>

                <div class="bev-item">
                    <div class="bev-details">
                        <h4><?= $minuman['name']; ?></h4>
                        <p><?= $minuman['description']; ?></p>
                    </div>
                    <span class="bev-price"><?= number_format($minuman['price'] / 1000, 0); ?>K</span>
                </div>

                <?php 
                    } // Tutup While Minuman
                } else {
                    echo "<p>Menu minuman sedang kosong.</p>";
                }
                ?>
            </div>
        </section>

        <footer>
            <p>© 2024 UMKM Kelompok 5.</p>
        </footer>
    </main>

</body>
</html>