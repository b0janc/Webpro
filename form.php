<?php
session_start(); // Mulai session untuk menyimpan keranjang belanja

// ==========================================
// 1. KONEKSI DATABASE
// ==========================================
$host = "localhost";
$user = "root";
$pass = "";     // Sesuaikan password database Anda
$db   = "db_kiosk";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// ==========================================
// 2. LOGIKA CART (TAMBAH/KURANG ITEM)
// ==========================================

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle request POST (Tambah ke Keranjang)
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $id = $_POST['product_id'];
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $img = $_POST['product_img'];

    // Cek apakah item sudah ada di cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['qty'] += 1;
            $found = true;
            break;
        }
    }
    // Jika belum ada, masukkan baru
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'img' => $img,
            'qty' => 1
        ];
    }
    // Redirect agar tidak resubmit saat refresh
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit;
}

// Handle request POST (Kurangi/Hapus Item)
if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    $id = $_POST['product_id'];
    foreach ($_SESSION['cart'] as $key => &$item) {
        if ($item['id'] == $id) {
            $item['qty'] -= 1;
            if ($item['qty'] <= 0) {
                unset($_SESSION['cart'][$key]); // Hapus jika 0
            }
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit;
}

// ==========================================
// 3. FETCH DATA DARI DATABASE
// ==========================================

// Helper Function: Format Harga
function formatPrice($number) {
    if ($number >= 1000) {
        return ($number / 1000) . 'K';
    }
    return $number;
}

// Ambil Kategori
$categories_result = mysqli_query($conn, "SELECT * FROM categories");

// Cek Kategori Aktif
$active_cat = isset($_GET['cat']) ? $_GET['cat'] : 'makanan';

// Ambil Produk Berdasarkan Kategori Aktif
$stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ?");
$stmt->bind_param("s", $active_cat);
$stmt->execute();
$products_result = $stmt->get_result();

// Hitung Total Keranjang (TANPA PAJAK)
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['qty']);
}
// $tax = $subtotal * 0.1; // <-- Dihapus
$total = $subtotal; // Total sekarang sama dengan subtotal
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nasgor Ngawi</title>
    <link rel="stylesheet" href="css/form.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <div class="bg-decoration"></div>

    <div class="kiosk-wrapper">
        
        <nav class="sidebar">
            <div class="brand">
                <div class="logo-box"></div>
                <span>K5</span>
            </div>
            
            <div class="menu-categories">
                <?php while($cat = mysqli_fetch_assoc($categories_result)): ?>
                    <a href="?cat=<?= $cat['id'] ?>" class="cat-item <?= ($active_cat == $cat['id']) ? 'active' : '' ?>">
                        <i class="ph <?= $cat['icon'] ?>"></i>
                        <span><?= $cat['name'] ?></span>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="settings">
                <i class="ph ph-gear"></i>
            </div>
        </nav>

        <main class="menu-content">
            <header class="content-header">
                <div>
                    <h1>Menu <?= ucfirst($active_cat) ?></h1>
                    <p>Rasa legendaris, penyajian modern.</p>
                </div>
            </header>

            <div class="products-grid">
                <?php 
                if ($products_result->num_rows > 0):
                    while($product = $products_result->fetch_assoc()): 
                ?>
                    <div class="product-card">
                        <div class="img-container">
                            <img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>">
                        </div>
                        <div class="card-details">
                            <h3><?= $product['name'] ?></h3>
                            <p class="desc"><?= $product['description'] ?></p>
                            <div class="price-action">
                                <span class="price"><?= formatPrice($product['price']) ?></span>
                                
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= $product['name'] ?>">
                                    <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                                    <input type="hidden" name="product_img" value="<?= $product['img'] ?>">
                                    <button type="submit" class="btn-add" style="cursor:pointer;">+</button>
                                </form>

                            </div>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else: ?>
                    <p style="color: #888;">Belum ada menu di kategori ini.</p>
                <?php endif; ?>
            </div>
        </main>

        <aside class="cart-panel">
            <div class="cart-header">
                <h2>Pesanan</h2>
                <span class="badge">Dining In</span>
            </div>

            <div class="cart-items">
                <?php if(!empty($_SESSION['cart'])): ?>
                    <?php foreach($_SESSION['cart'] as $c_item): ?>
                    <div class="cart-item">
                        <div class="item-img">
                            <img src="<?= $c_item['img'] ?>">
                        </div>
                        <div class="item-info">
                            <h4><?= $c_item['name'] ?></h4>
                            <span class="item-price"><?= formatPrice($c_item['price']) ?></span>
                        </div>
                        
                        <div class="qty-control">
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= $c_item['id'] ?>">
                                <button type="submit">-</button>
                            </form>
                            
                            <span><?= $c_item['qty'] ?></span>
                            
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= $c_item['id'] ?>">
                                <button type="submit">+</button>
                            </form>
                        </div>

                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align:center; margin-top:50px; color:#aaa;">
                        <i class="ph ph-shopping-cart" style="font-size: 32px; margin-bottom:10px;"></i>
                        <p>Keranjang kosong</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cart-footer">
                <div class="summary">
                    <div class="row">
                        <span>Subtotal</span>
                        <span><?= formatPrice($subtotal) ?></span>
                    </div>
                    
                    <div class="row total">
                        <span>Total</span>
                        <span><?= formatPrice($total) ?></span>
                    </div>
                </div>
                <a href="checkout.php" class="btn-checkout" style="display:block; text-align:center; text-decoration:none;">Bayar Sekarang</a>
            </div>
        </aside>

    </div>
</body>
</html>