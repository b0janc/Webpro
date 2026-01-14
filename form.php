<?php
session_start(); // Mulai session untuk menyimpan keranjang belanja

// ==========================================
// 1. KONEKSI DATABASE
// ==========================================
$conn = mysqli_connect("sql100.byetcluster.xyz", "alcy_40850935", "BMwCgSa9B2iBMDl", "alcy_40850935_db_kiosk");

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

// Hitung Total Keranjang
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] * $item['qty']);
}
$total = $subtotal;
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
    
    <style>
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            background: #f9f9f9;
            box-sizing: border-box; /* Agar padding tidak merusak lebar */
        }
        .input-group input:focus {
            outline: none;
            border-color: #333;
            background: #fff;
        }
        /* Mengubah tombol link jadi tombol submit asli */
        button.btn-checkout {
            width: 100%;
            border: none;
            cursor: pointer;
            font-family: 'Manrope', sans-serif;
            font-size: 16px;
        }
    </style>
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

                <form action="checkout.php" method="POST" id="checkoutForm">
                    
                    <div class="input-group">
                        <label for="customer_name">Nama Pemesan</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Ketik nama anda..." required autocomplete="off">
                    </div>

                    <button type="submit" class="btn-checkout">Bayar Sekarang</button>
                </form>

            </div>
        </aside>

    </div>

    <script>
        // 1. Saat halaman selesai dimuat, cek apakah ada nama yang tersimpan
        document.addEventListener("DOMContentLoaded", function() {
            var savedName = localStorage.getItem("customerName");
            if (savedName) {
                // Jika ada, isi kembali kolom input
                document.getElementById("customer_name").value = savedName;
            }
        });

        // 2. Setiap kali user mengetik, simpan hurufnya ke memori browser
        document.getElementById("customer_name").addEventListener("input", function() {
            localStorage.setItem("customerName", this.value);
        });

        // 3. (Opsional) Reset nama setelah tombol bayar ditekan agar bersih untuk pelanggan berikutnya
        document.getElementById("checkoutForm").addEventListener("submit", function() {
            // localStorage.removeItem("customerName"); // Uncomment baris ini jika ingin auto-reset
        });
    </script>

</body>
</html>