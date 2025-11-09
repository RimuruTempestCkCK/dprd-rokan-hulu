<?php 
session_start();
require '../koneksi.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// --- Hapus rapat ---
if (isset($_GET['hapus'])) {
    $idHapus = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM rapat WHERE id=?");
    $stmt->execute([$idHapus]);
    header("Location: kelola_rapat.php");
    exit();
}

// --- Proses form tambah/edit ---
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $judul = trim($_POST['judul'] ?? '');
    $jenis = $_POST['jenis'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $isi = trim($_POST['isi'] ?? '');
    $status = $_POST['status'] ?? 'Belum';
    $hasil = trim($_POST['hasil'] ?? '');

    $now = date('Y-m-d H:i:s');

    if (!$judul || !$jenis || !$tanggal || !$isi) {
        $error = "Semua field harus diisi!";
    } else {
        if ($id) {
    // Edit rapat
    if ($status === 'Selesai' && empty($hasil)) {
        // admin bisa isi hasil rapat
        $stmt = $pdo->prepare("UPDATE rapat SET judul=?, jenis=?, tanggal=?, isi=?, status=?, hasil=? WHERE id=?");
        $stmt->execute([$judul, $jenis, $tanggal, $isi, $status, $hasil, $id]);
        $success = "Rapat berhasil diupdate!";
    } else {
        // status "Belum" atau sudah ada hasil
        $stmt = $pdo->prepare("UPDATE rapat SET judul=?, jenis=?, tanggal=?, isi=?, status=?, hasil=? WHERE id=?");
        $stmt->execute([$judul, $jenis, $tanggal, $isi, $status, $hasil, $id]);
        $success = "Rapat berhasil diupdate!";
    }
}
 else {
            // Tambah rapat baru, hasil kosong dan status 'Belum'
            $stmt = $pdo->prepare("INSERT INTO rapat (judul, jenis, tanggal, isi, status, hasil) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$judul, $jenis, $tanggal, $isi, 'Belum', '']);
            $success = "Rapat berhasil ditambahkan!";
        }
    }
}

// Ambil semua rapat
$stmt = $pdo->query("SELECT * FROM rapat ORDER BY id ASC");
$rapats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Jadwal Rapat - Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../style_dashboard.css">
<style>
.btn-action { 
    display:inline-flex; 
    gap:5px; 
    border:1px solid #667eea; 
    color:#667eea; 
    padding:5px 10px; 
    border-radius:5px; 
    font-size:13px; 
    text-decoration:none; 
    cursor:pointer; 
    transition:all 0.3s;
}
.btn-action:hover { 
    background:#667eea; 
    color:white; 
}
.btn-primary { 
    display:inline-flex; 
    gap:5px; 
    background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); 
    color:white; 
    border:none; 
    padding:8px 15px; 
    border-radius:8px; 
    cursor:pointer; 
    text-decoration:none; 
    font-size:14px; 
    transition:all 0.3s;
}
.btn-primary:hover { 
    transform:translateY(-2px); 
    box-shadow:0 5px 15px rgba(102,126,234,0.3); 
}

.modal { 
    display:none; 
    position:fixed; 
    z-index:10000; 
    left:0; top:0; 
    width:100%; height:100%; 
    overflow:auto; 
    background:rgba(0,0,0,0.5);
}
.modal-content { 
    background:white; 
    margin:10% auto; 
    padding:20px; 
    border-radius:10px; 
    width:90%; max-width:400px; 
    position:relative; 
}
.close-btn { 
    position:absolute; top:10px; right:15px; 
    font-size:20px; cursor:pointer; color:#333; 
}
.modal-content h3 { margin-bottom:15px; }
.modal-content input, .modal-content select, .modal-content textarea { 
    width:100%; 
    padding:10px; 
    margin-bottom:15px; 
    border-radius:5px; 
    border:1px solid #ccc; 
}
.modal-content button { 
    width:100%; 
    padding:10px; 
    border:none; 
    border-radius:8px; 
    background:#667eea; 
    color:white; 
    font-size:14px; 
    cursor:pointer; 
}
.modal-content button:hover { background:#564fd3; }

.alert { 
    padding:10px 15px; 
    border-radius:5px; 
    margin-bottom:15px; 
}
.alert-success { background:#d4edda; color:#155724; }
.alert-error { background:#f8d7da; color:#721c24; }

.close-alert {
    float: right;
    cursor: pointer;
    font-weight: bold;
    margin-left: 10px;
}
.close-alert:hover { color: #000; }

/* ===== TABEL RAPAT ===== */
.table-container table {
    width: 100%;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
}

.table-container table th, 
.table-container table td {
    border: 1px solid #ddd;
    padding: 12px 15px;
    vertical-align: top;
}

.table-container table th {
    background-color: #667eea;
    color: white;
    text-align: left;
}

.table-container table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.table-container table tr:hover {
    background-color: #f1f1f1;
}

.table-container table td.rapat-hasil {
    text-align: justify;       /* Rata kiri-kanan */
    max-width: 300px;          /* Lebar kolom maksimal */
    word-wrap: break-word;     /* Pecah kata bila terlalu panjang */
    white-space: normal;       /* Pastikan teks wrap */
}
</style>

</head>
<body>
<div class="container">
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <div class="main-content">
        <header class="header">
            <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search..." id="searchInput"></div>
            <div class="header-right">
                <div class="user-profile"><div class="user-avatar">AD</div><span>Admin</span></div>
                <a href="../logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </header>

        <main class="content">
            <h1 class="page-title">Kelola Jadwal Rapat</h1>

            <?php if($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
        <span class="close-alert">&times;</span>
    </div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
        <span class="close-alert">&times;</span>
    </div>
<?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h3>Daftar Rapat</h3>
                    <button class="btn-primary" id="btnTambah"><i class="fas fa-plus"></i> Tambah Rapat</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Tanggal</th>
                            <th>Tema</th>
                            <th>Status</th>
                            <th>Hasil Rapat</th> <!-- Tambah kolom hasil -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($rapats): ?>
                            <?php foreach ($rapats as $rapat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($rapat['id']) ?></td>
                                    <td><?= htmlspecialchars($rapat['judul']) ?></td>
                                    <td><?= htmlspecialchars($rapat['jenis']) ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($rapat['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($rapat['isi']) ?></td>
                                    <td><?= htmlspecialchars($rapat['status']) ?></td>
                                    <td class="rapat-hasil">
                                        <?php 
                                        if($rapat['status'] === 'Selesai') {
                                            echo htmlspecialchars($rapat['hasil']);
                                        } else {
                                            echo "<em>Rapat belum selesai</em>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <button class="btn-action btn-edit" 
                                            data-id="<?= $rapat['id'] ?>" 
                                            data-judul="<?= htmlspecialchars($rapat['judul']) ?>"
                                            data-jenis="<?= $rapat['jenis'] ?>"
                                            data-tanggal="<?= date('Y-m-d\TH:i', strtotime($rapat['tanggal'])) ?>"
                                            data-isi="<?= htmlspecialchars($rapat['isi']) ?>"
                                            data-status="<?= $rapat['status'] ?>"
                                            data-hasil="<?= htmlspecialchars($rapat['hasil']) ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?hapus=<?= $rapat['id'] ?>" class="btn-action" onclick="return confirm('Yakin ingin menghapus rapat ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align:center;">Belum ada rapat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>


            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah/Edit Rapat -->
<div id="rapatModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3 id="modalTitle">Tambah Rapat</h3>
        <form id="rapatForm" method="POST">
            <input type="hidden" name="id" id="rapatId">
            <input type="text" name="judul" id="judul" placeholder="Judul Rapat" required>
            <select name="jenis" id="jenis" required>
                <option value="">Pilih Jenis</option>
                <option value="Terbuka">Terbuka</option>
                <option value="Private">Private</option>
            </select>
            <input type="datetime-local" name="tanggal" id="tanggal" required>
            <textarea name="isi" id="isi" placeholder="Tema Rapat" rows="4" required></textarea>
            <!-- Hanya muncul saat edit rapat -->
            <select name="status" id="status">
                <option value="Belum">Belum</option>
                <option value="Selesai">Selesai</option>
            </select>

            <textarea name="hasil" id="hasil" placeholder="Hasil Rapat" rows="4"></textarea>

            <button type="submit">Simpan</button>
        </form>
    </div>
</div>



<script src="../script.js"></script>

<script>
const modal = document.getElementById('rapatModal');
const btnTambah = document.getElementById('btnTambah');
const closeBtn = modal.querySelector('.close-btn');
const modalTitle = document.getElementById('modalTitle');
const rapatForm = document.getElementById('rapatForm');

// Tambah Rapat
btnTambah.onclick = () => {
    modal.style.display = 'block';
    modalTitle.textContent = 'Tambah Rapat';
    rapatForm.id.value = '';
    rapatForm.judul.value = '';
    rapatForm.jenis.value = '';
    rapatForm.tanggal.value = '';
    rapatForm.isi.value = '';
    rapatForm.status.value = 'Belum';
    rapatForm.hasil.value = '';
    rapatForm.status.disabled = false; // biarkan bisa diubah
    rapatForm.hasil.disabled = false;  // biarkan bisa diisi
}

// Edit Rapat
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.onclick = () => {
        modal.style.display = 'block';
        modalTitle.textContent = 'Edit Rapat';
        rapatForm.id.value = btn.dataset.id;
        rapatForm.judul.value = btn.dataset.judul;
        rapatForm.jenis.value = btn.dataset.jenis;
        rapatForm.tanggal.value = btn.dataset.tanggal;
        rapatForm.isi.value = btn.dataset.isi;
        rapatForm.status.value = btn.dataset.status;
        rapatForm.hasil.value = btn.dataset.hasil;

        // tidak menonaktifkan field status & hasil
        rapatForm.status.disabled = false;
        rapatForm.hasil.disabled = false;
    }
});



closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target == modal) modal.style.display = 'none'; }

// Tutup alert saat tombol close diklik
document.querySelectorAll('.close-alert').forEach(btn => {
    btn.addEventListener('click', function() {
        this.parentElement.style.display = 'none';
    });
});
</script>
</body>
</html>
