<?php
session_set_cookie_params(['lifetime' => 86400, 'path' => '/', 'domain' => '.sederhanaok.com']);
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$conn = new mysqli("db", "root", "root", "db_sederhanaok");

if (isset($_POST['add_to_cart'])) {
    $id = $_POST['menu_id'];
    $jumlah = (int)$_POST['jumlah'];
    
    $res = $conn->query("SELECT * FROM menu WHERE id=$id");
    $item = $res->fetch_assoc();

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['jumlah'] += $jumlah;
    } else {
        $_SESSION['cart'][$id] = [
            'nama' => $item['nama'],
            'harga' => $item['harga'],
            'jumlah' => $jumlah
        ];
    }
    $msg = "Berhasil masuk keranjang!";
}

$minuman = $conn->query("SELECT * FROM menu WHERE kategori='minuman'");
$total_items = array_sum(array_column($_SESSION['cart'], 'jumlah'));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu Minuman - SederhanaOK</title>
    <style>
        .cart-float {
            position: fixed; bottom: 20px; right: 20px;
            background: #007bff; color: white; padding: 15px 25px;
            border-radius: 50px; text-decoration: none; font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <h1>🥤 Menu Minuman</h1>
    <?php if (isset($msg)) echo "<p style='color:green;'><b>$msg</b></p>"; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr><th>Nama Minuman</th><th>Harga</th><th>Jumlah</th><th>Aksi</th></tr>
        <?php while($row = $minuman->fetch_assoc()): ?>
        <tr>
            <td><?= $row['nama'] ?></td>
            <td>Rp <?= number_format($row['harga']) ?></td>
            <form method="POST">
                <td>
                    <input type="hidden" name="menu_id" value="<?= $row['id'] ?>">
                    <input type="number" name="jumlah" value="1" min="1" style="width: 50px;">
                </td>
                <td><button type="submit" name="add_to_cart">+ Keranjang</button></td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="http://makanan.sederhanaok.com/makanan.php"><button>🍔 Ke Menu Makanan</button></a>
    <a href="http://sederhanaok.com/index.php"><button>🏠 Ke Halaman Utama</button></a> 


    <!-- Tombol Keranjang Floating -->
    <a href="http://sederhanaok.com/checkout.php" class="cart-float">
        🛒 Keranjang Saya (<?= $total_items ?>)
    </a>
</body>
</html>
