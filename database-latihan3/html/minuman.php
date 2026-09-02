<?php
session_set_cookie_params(['lifetime' => 86400, 'path' => '/', 'domain' => '.sederhanaok.com']);
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$conn = new mysqli("db", "root", "root", "db_sederhanaok");

// Proses Checkout dari Modal Pop-up
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
    foreach ($_SESSION['cart'] as $menu_id => $item) {
        $jumlah = $item['jumlah'];
        $conn->query("INSERT INTO pesanan (nama_pelanggan, menu_id, jumlah) VALUES ('$nama_pelanggan', '$menu_id', '$jumlah')");
    }
    $_SESSION['cart'] = [];
    $success_msg = "Pesanan berhasil disimpan!";
}

// Tambah ke Keranjang
if (isset($_POST['add_to_cart'])) {
    $id = $_POST['menu_id'];
    $jumlah = (int)$_POST['jumlah'];
    $res = $conn->query("SELECT * FROM menu WHERE id=$id");
    $item = $res->fetch_assoc();

    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['jumlah'] += $jumlah;
    } else {
        $_SESSION['cart'][$id] = ['nama' => $item['nama'], 'harga' => $item['harga'], 'jumlah' => $jumlah];
    }
}

// Query khusus kategori minuman
$minuman = $conn->query("SELECT * FROM menu WHERE kategori='minuman'");
$total_items = array_sum(array_column($_SESSION['cart'], 'jumlah'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu Minuman - SederhanaOK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen p-6 animate__animated animate__fadeIn">

    <div class="max-w-4xl mx-auto bg-slate-800 rounded-2xl p-6 shadow-2xl border border-slate-700">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-sky-400 flex items-center gap-2">🥤 Menu Minuman</h1>
            <span class="text-xs bg-sky-500/20 text-sky-300 px-3 py-1 rounded-full border border-sky-500/30">minuman.sederhanaok.com</span>
        </div>

        <?php if (isset($success_msg)): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-4 rounded-xl mb-4 text-center font-semibold animate__animated animate__bounceIn">
                ✅ <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <!-- Table Menu Minuman -->
        <div class="overflow-x-auto rounded-xl border border-slate-700">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-700/50 text-slate-300">
                        <th class="p-4">Nama Minuman</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                    <?php while($row = $minuman->fetch_assoc()): ?>
                    <tr class="hover:bg-slate-700/30 transition-all duration-200">
                        <td class="p-4 font-medium"><?= $row['nama'] ?></td>
                        <td class="p-4 text-sky-400 font-semibold">Rp <?= number_format($row['harga']) ?></td>
                        <td class="p-4 text-center">
                            <form method="POST" class="flex justify-center items-center gap-2">
                                <input type="hidden" name="menu_id" value="<?= $row['id'] ?>">
                                <input type="number" name="jumlah" value="1" min="1" class="w-16 bg-slate-900 border border-slate-600 rounded-lg px-2 py-1 text-center text-white focus:outline-none focus:border-sky-400 transition">
                                <button type="submit" name="add_to_cart" class="bg-sky-500 hover:bg-sky-400 active:scale-95 text-slate-950 font-bold px-4 py-1.5 rounded-lg shadow-md transition-all duration-200 flex items-center gap-1">
                                    + Keranjang
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Navigasi -->
        <div class="flex gap-4 mt-6">
            <a href="http://sederhanaok.com" class="bg-slate-700 hover:bg-slate-600 active:scale-95 px-5 py-2.5 rounded-xl font-medium transition duration-200">🏠 Ke Halaman Utama</a>
            <a href="http://makanan.sederhanaok.com/makanan.php" class="bg-amber-600 hover:bg-amber-500 active:scale-95 px-5 py-2.5 rounded-xl font-medium transition duration-200">🍔 Ke Menu Makanan</a>
        </div>
    </div>

    <!-- Tombol Floating Keranjang -->
    <button onclick="toggleModal()" class="fixed bottom-6 right-6 bg-sky-500 hover:bg-sky-400 active:scale-90 text-slate-950 font-bold px-6 py-3.5 rounded-full shadow-2xl flex items-center gap-3 transition-all duration-300 animate__animated animate__pulse animate__infinite">
        <span class="text-xl">🛒</span>
        <span>Keranjang Saya</span>
        <span class="bg-slate-950 text-sky-400 text-xs px-2.5 py-1 rounded-full"><?= $total_items ?></span>
    </button>

    <!-- Modal Pop-up Checkout -->
    <div id="cartModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex justify-center items-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-slate-800 border border-slate-700 w-full max-w-lg rounded-2xl p-6 shadow-2xl relative animate__animated animate__zoomIn">
            <button onclick="toggleModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl font-bold">✕</button>
            <h2 class="text-2xl font-bold text-sky-400 mb-4 border-b border-slate-700 pb-2">🛒 Keranjang Belanja</h2>
            
            <div class="max-h-60 overflow-y-auto mb-4 border border-slate-700 rounded-xl p-2">
                <?php 
                $grand_total = 0;
                if (!empty($_SESSION['cart'])): 
                    foreach ($_SESSION['cart'] as $item):
                        $sub = $item['harga'] * $item['jumlah'];
                        $grand_total += $sub;
                ?>
                    <div class="flex justify-between items-center p-2 border-b border-slate-700/50">
                        <div>
                            <p class="font-semibold text-slate-200"><?= $item['nama'] ?></p>
                            <p class="text-xs text-slate-400">Rp <?= number_format($item['harga']) ?> x <?= $item['jumlah'] ?></p>
                        </div>
                        <span class="font-bold text-sky-400">Rp <?= number_format($sub) ?></span>
                    </div>
                <?php endforeach; else: ?>
                    <p class="text-center text-slate-400 py-4">Keranjang masih kosong!</p>
                <?php endif; ?>
            </div>

            <div class="flex justify-between items-center text-lg font-bold mb-6">
                <span>Total Bayar:</span>
                <span class="text-emerald-400 text-xl">Rp <?= number_format($grand_total) ?></span>
            </div>

            <?php if (!empty($_SESSION['cart'])): ?>
                <form method="POST">
                    <label class="block text-sm font-medium mb-2 text-slate-300">Nama Pemesan:</label>
                    <input type="text" name="nama_pelanggan" required placeholder="Masukkan Nama Anda" class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-2.5 mb-4 text-white focus:outline-none focus:border-sky-400 transition">
                    <button type="submit" name="checkout" class="w-full bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-slate-950 font-bold py-3 rounded-xl transition duration-200 shadow-lg">
                        🚀 Simpan & Checkout Now!
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function toggleModal() {
            document.getElementById('cartModal').classList.toggle('hidden');
        }
    </script>
</body>
</html>