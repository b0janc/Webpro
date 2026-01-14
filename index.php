<?php

$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. AMBIL DATA DARI DATABASE (Misal kita ambil 3 produk pertama untuk ditampilkan)
$query = "SELECT * FROM products LIMIT 3";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nasgor Kelompok 5</title>
    <link rel="stylesheet" href="css/landpage.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="bg-wave"></div>

    <div class="main-wrapper">
        <nav>
            <a href="#" class="logo">NASGOR.NGAWI</a>
            <div class="nav-links">
                <a href="menu.php">Menu</a>
                <a href="antrian.php">Antrian</a>
                <a href="biodata.html">Tentang</a>
                <a href="feedback.php">Feedback</a>
                <a href="form.php" class="btn-contact">Pesan</a>
            </div>
        </nav>

        <header class="hero">
            <div class="hero-text">
                <span class="subtitle">AUTHENTIC TASTE</span>
                <h1>Modern<br>Ngawi<br>Fried Rice.</h1>
                <p>Perpaduan cita rasa tradisional dengan penyajian modern yang estetik.</p>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1603133872878-684f208fb84b?q=80&w=1000&auto=format&fit=crop" alt="Nasi Goreng Aesthetic">
            </div>
        </header>

        <section id="menu" class="content-section">
            <div class="section-header">
                <h2>Our Signature</h2>
                <p>Menu pilihan dikurasi dengan bahan premium.</p>
            </div>

            <div class="grid-container">
                
                <?php 
                // 3. LOOPING DATA (Foreach)
                // Kode ini akan mengulang <div> grid-item sebanyak jumlah data di database
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) { 
                ?>

                <div class="grid-item">
                    <div class="image-box">
                        <img src="<?= $row['img']; ?>" alt="<?= $row['name']; ?>">
                    </div>
                    <div class="text-box">
                        <h3><?= $row['name']; ?></h3>
                        
                        <p class="desc"><?= $row['description']; ?></p>
                        
                        <span class="price">IDR <?= number_format($row['price'] / 1000, 0); ?>K</span>
                    </div>
                </div>

                <?php 
                    } 
                } else {
                    echo "<p>Belum ada menu yang tersedia.</p>";
                }
                ?>

            </div>
        </section>

        <footer>
            <p>© 2024 Nasgor Kelompok 5. Design inspired by Modern Trends.</p>
        </footer>
    </div>

</body>
</html>