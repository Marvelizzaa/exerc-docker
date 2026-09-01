<?php
$host = 'db'; // Nama service di docker-compose
$user = 'user_app';
$pass = 'userpassword';
$dbname = 'db_tertes';

// Koneksi ke Database MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 1. Proses Input Data Saat Form Disubmit
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];

    if (!empty($nama) && !empty($kelas)) {
        $stmt = $conn->prepare("INSERT INTO siswa (nama, kelas) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $kelas);
        $stmt->execute();
        header("Location: index.php"); // Refresh halaman
        exit();
    }
}

// 2. Ambil Data dari Database
$result = $conn->query("SELECT * FROM siswa ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengujian Database Docker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; line-height: 1.6; }
        .card { background: #f4f4f4; padding: 20px; border-radius: 8px; margin-bottom: 20px; max-width: 500px; }
        input[type=text] { width: 100%; padding: 8px; margin: 5px 0 15px; display: block; box-sizing: border-box; }
        button, input[type=submit] { background-color: #04AA6D; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; max-width: 600px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <h2>Form Input Data (Pengujian DB Docker)</h2>
    
    <!-- Form Input Data -->
    <div class="card">
        <form action="" method="POST">
            <label>Nama:</label>
            <input type="text" name="nama" required placeholder="Masukkan Nama">
            
            <label>Kelas:</label>
            <input type="text" name="kelas" required placeholder="Masukkan Kelas">
            
            <input type="submit" name="submit" value="Simpan ke Database">
        </form>
    </div>

    <hr>

    <h3>Data Terimpan di Database</h3>
    <!-- Tombol Refresh / Tampilkan Data -->
    <a href="index.php"><button type="button">🔄 Tampilkan / Refresh Data</button></a>
    <br><br>

    <!-- Tabel Menampilkan Data -->
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>Waktu Input</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center;">Belum ada data. Silakan inputkan data di atas!</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>
