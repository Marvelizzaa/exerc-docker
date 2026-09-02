<?php
session_set_cookie_params(['lifetime' => 86400, 'path' => '/', 'domain' => '.sederhanaok.com']);
session_start();
$conn = new mysqli("db", "root", "root", "db_sederhanaok");

// Query Rekap Pesanan Terakhir dari DB (SUM)
$sql = "SELECT p.nama_pelanggan, m.nama, m.harga, p.jumlah, (m.harga * p.jumlah) AS subtotal 
        FROM pesanan p JOIN menu m ON p.menu_id = m.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head><title>sederhanaok.com</title></head>
<body>
    <h1>Selamat Datang di Restoran sederhanaok.com</h1>
    <p>Silakan pilih menu pesanan Anda:</p>

    <a href="http://makanan.sederhanaok.com/makanan.php"><button style="padding: 10px;">🍔 Menu Makanan</button></a>
    <a href="http://minuman.sederhanaok.com/minuman.php"><button style="padding: 10px;">🥤 Menu Minuman</button></a>
    <a href="http://sederhanaok.com/checkout.php"><button style="padding: 10px;">🛒 Lihat Keranjang Saya</button></a>

    <hr>
    <h2>📊 Data Transaksi Masuk di Database (SUM Query)</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr bgcolor="#f2f2f2">
            <th>Pemesan</th><th>Menu</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th>
        </tr>
        <?php 
        $total_db = 0;
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $total_db += $row['subtotal'];
                echo "<tr>
                    <td>{$row['nama_pelanggan']}</td>
                    <td>{$row['nama']}</td>
                    <td>Rp ".number_format($row['harga'])."</td>
                    <td>{$row['jumlah']}</td>
                    <td>Rp ".number_format($row['subtotal'])."</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5' align='center'>Belum ada transaksi di Database.</td></tr>";
        }
        ?>
    </table>
    <h3>Total Omset Database (SUM): Rp <?= number_format($total_db) ?></h3>
</body>
</html>
