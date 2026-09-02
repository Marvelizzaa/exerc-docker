<?php
$host = 'db';
$user = 'user_app';
$pass = 'userpassword';
$dbname = 'db_tertes';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// 1. PROSES HAPUS DATA
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM siswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

// 2. PROSES SIMPAN / UPDATE DATA
if (isset($_POST['submit'])) {
    $id = $_POST['id_siswa'] ?? '';
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $absen = (int)$_POST['absen'];
    $tugas = (int)$_POST['nilai_tugas'];
    $uts = (int)$_POST['nilai_uts'];
    $uas = (int)$_POST['nilai_uas'];

    if (!empty($nama) && !empty($kelas)) {
        if (!empty($id)) {
            // Update Data
            $stmt = $conn->prepare("UPDATE siswa SET nama=?, kelas=?, absen=?, nilai_tugas=?, nilai_uts=?, nilai_uas=? WHERE id=?");
            $stmt->bind_param("ssiiiii", $nama, $kelas, $absen, $tugas, $uts, $uas, $id);
        } else {
            // Insert Data Baru
            $stmt = $conn->prepare("INSERT INTO siswa (nama, kelas, absen, nilai_tugas, nilai_uts, nilai_uas) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiii", $nama, $kelas, $absen, $tugas, $uts, $uas);
        }
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}

// 3. AMBIL DATA UNTUK EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM siswa WHERE id = $id_edit");
    if ($res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}

// 4. AMBIL SEMUA DATA
$result = $conn->query("SELECT * FROM siswa ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Rapor & Kehadiran</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-frost: #e0eaf5;
            --card-bg: #f0f5fa;
            --panel-border: #4a6fa5;
            --text-main: #2c3e50;
            --accent-blue: #5b9bd5;
            --accent-hover: #417bb8;
            --danger-red: #e74c3c;
            --warning-orange: #f39c12;
            --shadow-doff: 4px 4px 0px #34495e;
            --shadow-doff-sm: 2px 2px 0px #34495e;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-frost);
            color: var(--text-main);
            margin: 0;
            padding: 30px 15px;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 950px;
        }

        .header-title {
            font-family: 'Fredoka', cursive;
            font-size: 2.2rem;
            color: #2c3e50;
            text-shadow: 2px 2px 0px #aed6f1;
            margin-bottom: 20px;
            text-align: center;
        }

        .card {
            background-color: var(--card-bg);
            border: 3px solid var(--panel-border);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--shadow-doff);
            margin-bottom: 25px;
        }

        .card-title {
            font-family: 'Fredoka', cursive;
            font-size: 1.3rem;
            margin-top: 0;
            margin-bottom: 15px;
            color: #34495e;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        input[type="text"], input[type="number"] {
            font-family: 'Inter', sans-serif;
            padding: 8px 12px;
            border: 2px solid var(--panel-border);
            border-radius: 10px;
            background-color: #ffffff;
            font-size: 0.9rem;
            outline: none;
        }

        .btn {
            font-family: 'Fredoka', cursive;
            font-size: 0.95rem;
            background-color: var(--accent-blue);
            color: white;
            padding: 10px 16px;
            border: 2px solid var(--panel-border);
            border-radius: 10px;
            cursor: pointer;
            box-shadow: var(--shadow-doff-sm);
            text-decoration: none;
            display: inline-block;
        }

        .btn:active {
            transform: translate(2px, 2px);
            box-shadow: 0px 0px 0px #34495e;
        }

        .btn-danger { background-color: var(--danger-red); }
        .btn-warning { background-color: var(--warning-orange); }
        .btn-print { background-color: #27ae60; }

        /* Filter Section */
        .filter-container {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-container input, .filter-container select {
            flex: 1;
            min-width: 150px;
        }

        /* Table */
        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 3px solid var(--panel-border);
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 2px solid var(--bg-frost);
            font-size: 0.9rem;
        }

        th {
            background-color: #d4e6f1;
            font-family: 'Fredoka', cursive;
            border-bottom: 3px solid var(--panel-border);
        }

        .badge {
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1.5px solid var(--panel-border);
            font-size: 0.8rem;
        }

        .status-pass { background-color: #a3e4d7; color: #1e8449; }
        .status-fail { background-color: #f8d7da; color: #b03a2e; }

        /* CSS KHUSUS PRINT */
        @media print {
            body { background: white; padding: 0; }
            .no-print, .card-form, .filter-container, .action-col { display: none !important; }
            .card { border: none; box-shadow: none; padding: 0; }
            .header-title { font-size: 1.8rem; margin-bottom: 10px; }
            table { border: 1px solid #000; }
            th, td { border: 1px solid #000; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-title">❄️ Web App Rapor & Absensi Siswa</div>

    <!-- FORM INPUT / EDIT DATA -->
    <div class="card card-form no-print">
        <div class="card-title">
            <?= $edit_data ? '✏️ Edit Data Siswa' : '📝 Input Data Siswa Baru' ?>
        </div>
        <form action="index.php" method="POST">
            <input type="hidden" name="id_siswa" value="<?= $edit_data['id'] ?? '' ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Siswa:</label>
                    <input type="text" name="nama" value="<?= $edit_data['nama'] ?? '' ?>" required placeholder="Contoh: Fauzan">
                </div>
                <div class="form-group">
                    <label>Kelas:</label>
                    <input type="text" name="kelas" value="<?= $edit_data['kelas'] ?? '' ?>" required placeholder="Contoh: XII SIJA 1">
                </div>
                <div class="form-group">
                    <label>Kehadiran (%):</label>
                    <input type="number" name="absen" min="0" max="100" value="<?= $edit_data['absen'] ?? '' ?>" required placeholder="0-100">
                </div>
                <div class="form-group">
                    <label>Nilai Tugas:</label>
                    <input type="number" name="nilai_tugas" min="0" max="100" value="<?= $edit_data['nilai_tugas'] ?? '' ?>" required placeholder="0-100">
                </div>
                <div class="form-group">
                    <label>Nilai UTS:</label>
                    <input type="number" name="nilai_uts" min="0" max="100" value="<?= $edit_data['nilai_uts'] ?? '' ?>" required placeholder="0-100">
                </div>
                <div class="form-group">
                    <label>Nilai UAS:</label>
                    <input type="number" name="nilai_uas" min="0" max="100" value="<?= $edit_data['nilai_uas'] ?? '' ?>" required placeholder="0-100">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit" name="submit" class="btn">💾 <?= $edit_data ? 'Update Data' : 'Simpan Data' ?></button>
                <?php if ($edit_data): ?>
                    <a href="index.php" class="btn btn-danger">❌ Batal Edit</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- REKAP TABEL DATA -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;" class="no-print">
            <div class="card-title" style="margin: 0;">📊 Rekap Nilai Rapor</div>
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak / Simpan PDF</button>
        </div>

        <!-- SEARCH & FILTER SECTION -->
        <div class="filter-container no-print">
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="🔍 Cari Nama / Kelas...">
            <select id="statusFilter" onchange="filterTable()">
                <option value="ALL">-- Semua Status --</option>
                <option value="LULUS">LULUS</option>
                <option value="REMIDI">REMIDI</option>
            </select>
        </div>

        <div class="table-wrapper">
            <table id="dataTable">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Absen</th>
                        <th>Tugas</th>
                        <th>UTS</th>
                        <th>UAS</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="action-col no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $total = ($row['nilai_tugas'] * 0.3) + ($row['nilai_uts'] * 0.3) + ($row['nilai_uas'] * 0.4);
                            $total_fmt = number_format($total, 1);
                            $is_passed = ($total >= 75 && $row['absen'] >= 75);
                            $status_str = $is_passed ? 'LULUS' : 'REMIDI';
                        ?>
                            <tr>
                                <td style="text-align: left;">
                                    <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                    <small style="color: #7f8c8d;"><?= htmlspecialchars($row['kelas']) ?></small>
                                </td>
                                <td><?= $row['absen'] ?>%</td>
                                <td><?= $row['nilai_tugas'] ?></td>
                                <td><?= $row['nilai_uts'] ?></td>
                                <td><?= $row['nilai_uas'] ?></td>
                                <td><strong><?= $total_fmt ?></strong></td>
                                <td data-status="<?= $status_str ?>">
                                    <span class="badge <?= $is_passed ? 'status-pass' : 'status-fail' ?>">
                                        <?= $status_str ?>
                                    </span>
                                </td>
                                <td class="action-col no-print">
                                    <a href="index.php?edit=<?= $row['id'] ?>" class="btn btn-warning" style="padding: 4px 8px; font-size: 0.8rem;">✏️ Edit</a>
                                    <a href="index.php?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;">🗑️ Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="padding: 20px; color: #7f8c8d;">Belum ada data nilai.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- JAVASCRIPT UNTUK FILTER & SEARCH REALTIME -->
<script>
function filterTable() {
    const searchVal = document.getElementById("searchInput").value.toLowerCase();
    const statusVal = document.getElementById("statusFilter").value;
    const rows = document.querySelectorAll("#dataTable tbody tr");

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusTd = row.querySelector("td[data-status]");
        const rowStatus = statusTd ? statusTd.getAttribute("data-status") : "";

        const matchesSearch = text.includes(searchVal);
        const matchesStatus = (statusVal === "ALL" || rowStatus === statusVal);

        if (matchesSearch && matchesStatus) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

</body>
</html>
