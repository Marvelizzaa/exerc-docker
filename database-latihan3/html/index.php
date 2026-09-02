<?php
session_set_cookie_params(['lifetime' => 86400, 'path' => '/', 'domain' => '.sederhanaok.com']);
session_start();
$conn = new mysqli("db", "root", "root", "db_sederhanaok");

$sql = "SELECT p.nama_pelanggan, m.nama, m.harga, p.jumlah, (m.harga * p.jumlah) AS subtotal 
        FROM pesanan p JOIN menu m ON p.menu_id = m.id ORDER BY p.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SederhanaOK - Dashboard Utama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen p-6 animate__animated animate__fadeIn">

    <div class="max-w-5xl mx-auto bg-slate-800 rounded-2xl p-8 shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-amber-400 mb-2">🍜 Restoran SederhanaOK</h1>
            <p class="text-slate-400">Sistem Pemesanan Multi-Subdomain & Database Centralized</p>
        </div>

        <!-- Tombol Menu Subdomain -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <a href="http://makanan.sederhanaok.com/makanan.php" class="bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 p-6 rounded-2xl text-center transition-all duration-300 hover:scale-[1.02] active:scale-95 group">
                <span class="text-4xl block mb-2 group-hover:bounce">🍔</span>
                <span class="text-xl font-bold text-amber-400">Subdomain Makanan</span>
                <span class="block text-xs text-slate-400 mt-1">makanan.sederhanaok.com</span>
            </a>
            <a href="http://minuman.sederhanaok.com/minuman.php" class="bg-sky-500/10 border border-sky-500/30 hover:bg-sky-500/20 p-6 rounded-2xl text-center transition-all duration-300 hover:scale-[1.02] active:scale-95 group">
                <span class="text-4xl block mb-2 group-hover:bounce">🥤</span>
                <span class="text-xl font-bold text-sky-400">Subdomain Minuman</span>
                <span class="block text-xs text-slate-400 mt-1">minuman.sederhanaok.com</span>
            </a>
        </div>

        <hr class="border-slate-700 mb-8">

        <!-- Rekap Transaksi Database -->
        <h2 class="text-2xl font-bold text-slate-200 mb-4 flex items-center gap-2">
            📊 Rekap Transaksi Database (SUM)
        </h2>

        <div class="overflow-x-auto rounded-xl border border-slate-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-700/50 text-slate-300">
                        <th class="p-4">Pemesan</th>
                        <th class="p-4">Menu</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Qty</th>
                        <th class="p-4">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php 
                    $total_db = 0;
                    if ($result && $result->num_rows > 0):
                        while($row = $result->fetch_assoc()):
                            $total_db += $row['subtotal'];
                    ?>
                    <tr class="hover:bg-slate-700/30 transition duration-150">
                        <td class="p-4 font-semibold text-emerald-400"><?= $row['nama_pelanggan'] ?></td>
                        <td class="p-4"><?= $row['nama'] ?></td>
                        <td class="p-4 text-slate-400">Rp <?= number_format($row['harga']) ?></td>
                        <td class="p-4"><?= $row['jumlah'] ?></td>
                        <td class="p-4 font-bold text-amber-400">Rp <?= number_format($row['subtotal']) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="p-4 text-center text-slate-400">Belum ada transaksi tercatat di database.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl flex justify-between items-center">
            <span class="text-lg font-bold text-emerald-300">Total Omset Keseluruhan (SUM):</span>
            <span class="text-2xl font-extrabold text-emerald-400">Rp <?= number_format($total_db) ?></span>
        </div>
    </div>
</body>
</html>
