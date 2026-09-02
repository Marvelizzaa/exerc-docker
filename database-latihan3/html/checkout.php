<?php
session_set_cookie_params(['lifetime' => 86400, 'path' => '/', 'domain' => '.sederhanaok.com']);
session_start();
$conn = new mysqli("db", "root", "root", "db_sederhanaok");

$cart = $_SESSION['cart'] ?? [];
$success = false;

// Proses Simpan ke Database MySQL saat Klik PESAN!
if (isset($_POST['checkout']) && !empty($cart)) {
    $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
    
    foreach ($cart as $menu_id => $item) {
        $jumlah = $item['jumlah'];
        $conn->query("INSERT INTO pesanan (nama_pelanggan, menu_id, jumlah) VALUES ('$nama_pelanggan', '$menu_id', '$jumlah')");
    }
    
    // Kosongkan Keranjang setelah sukses simpan ke DB
    $_SESSION['cart'] = [];
    $success = true;
}
?>
<!DOCTYPE html>
<html>
<head><title>Keranjang & Pembayaran - SederhanaOK</title></head>
<body>
    <h1>🛒 Ringkasan Keranjang Belanja</h1>

    <?php if ($success): ?>
        <h2 style="color: green;">✅ Pesanan Berhasil Disimpan ke Database!</h2>
        <p>Terima Kasih atas pesanan Anda.</p>
        <a href="http://sederhanaok.com/index.php"><button>Lihat Halaman Utama</button></a>
    <?php else: ?>

        <table border="1" cellpadding="8" cellspacing="0">
            <tr bgcolor="#f2f2f2"><th>Nama Item</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr>
            <?php 
            $grand_total = 0;
            if (!empty($cart)):
                foreach ($cart as $item):
                    $subtotal = $item['harga'] * $item['jumlah'];
                    $grand_total += $subtotal;
            ?>
            <tr>
                <td><?= $item['nama'] ?></td>
                <td>Rp <?= number_format($item['harga']) ?></td>
                <td><?= $item['jumlah'] ?></td>
                <td>Rp <?= number_format($subtotal) ?></td>
            </tr>
            <?php 
                endforeach;
            else:
            ?>
            <tr><td colspan="4" align="center">Keranjang masih kosong!</td></tr>
            <?php endif; ?>
        </table>

        <h3>TOTAL BAYAR: Rp <?= number_format($grand_total) ?></h3>

        <?php if (!empty($cart)): ?>
            <hr>
            <form method="POST">
                <label><b>Nama Pemesan:</b> </label>
                <input type="text" name="nama_pelanggan" required placeholder="Masukkan Nama Anda">
                <br><br>
                <button type="submit" name="checkout" style="padding: 10px 20px; background: green; color: white; font-weight: bold; cursor: pointer;">
                    🚀 PESAN SEKARANG! (Simpan ke DB)
                </button>
            </form>
        <?php endif; ?>

        <br><br>
        <a href="http://makanan.sederhanaok.com/makanan.php"><button>← Tambah Makanan Lagi</button></a>
        <a href="http://minuman.sederhanaok.com/minuman.php"><button>← Tambah Minuman Lagi</button></a>
    <?php endif; ?>
</body>
</html>
