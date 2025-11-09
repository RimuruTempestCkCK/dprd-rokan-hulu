<?php 
session_start();
require '../koneksi.php';

// Proteksi halaman: hanya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// --- Hapus user ---
if (isset($_GET['hapus'])) {
    $idHapus = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$idHapus]);
    header("Location: kelola_user.php");
    exit();
}

// --- Proses form tambah/edit ---
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? '';

    if (!$username || !$role || (!$id && !$password)) {
        $error = "Username, password, dan role harus diisi!";
    } else {
        if ($id) {
            // Edit user
            if ($password) {
                $stmt = $pdo->prepare("UPDATE users SET username=?, password=?, role=? WHERE id=?");
                $stmt->execute([$username, $password, $role, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username=?, role=? WHERE id=?");
                $stmt->execute([$username, $role, $id]);
            }
            $success = "User berhasil diupdate!";
        } else {
            // Tambah user
            $stmt = $pdo->prepare("INSERT INTO users (username,password,role) VALUES (?,?,?)");
            $stmt->execute([$username, $password, $role]);
            $success = "User berhasil ditambahkan!";
        }
    }
}

// Ambil semua user dari database
$stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style_dashboard.css">
<style>
/* ===== TOMBOL ===== */
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

/* ===== MODAL ===== */
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

/* ===== ALERT ===== */
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
    table-layout: fixed; /* Supaya kolom rapi */
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
/* Kolom Hasil Rapat wrap dan justify */
.table-container table td.rapat-hasil {
    text-align: justify;
    max-width: 300px;
    word-wrap: break-word;
    white-space: normal;
}
</style>

</head>
<body>
<div class="container">
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search..." id="searchInput"></div>
            <div class="header-right">
                <div class="user-profile"><div class="user-avatar">AD</div><span>Admin</span></div>
                <a href="../logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </header>

        <main class="content">
            <h1 class="page-title">Kelola User</h1>

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
                    <h3>Daftar User</h3>
                    <button class="btn-primary" id="btnTambah"><i class="fas fa-plus"></i> Tambah User</button>
                </div>

                <table>
                    <thead>
                        <tr><th>ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if($users): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td>
                                        <button class="btn-action btn-edit" 
                                            data-id="<?= $user['id'] ?>" 
                                            data-username="<?= $user['username'] ?>" 
                                            data-role="<?= $user['role'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="?hapus=<?= $user['id'] ?>" class="btn-action" onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">Belum ada user.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h3 id="modalTitle">Tambah User</h3>
        <form id="userForm" method="POST">
            <input type="hidden" name="id" id="userId">
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="password" name="password" id="password" placeholder="Password" >
            <select name="role" id="role" required>
                <option value="">Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="dewan">Dewan</option>
            </select>
            <button type="submit">Simpan</button>
        </form>
    </div>
</div>

<?php
// Hapus user
if (isset($_GET['hapus'])) {
    $idHapus = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$idHapus]);
    header("Location: kelola_user.php");
    exit();
}
?>


<script src="../script.js"></script>

<script>
const modal = document.getElementById('userModal');
const btnTambah = document.getElementById('btnTambah');
const closeBtn = modal.querySelector('.close-btn');
const modalTitle = document.getElementById('modalTitle');
const userForm = document.getElementById('userForm');

// Tambah User
btnTambah.onclick = () => {
    modal.style.display = 'block';
    modalTitle.textContent = 'Tambah User';
    userForm.id.value = '';
    userForm.username.value = '';
    userForm.password.value = '';
    userForm.role.value = '';
}

// Edit User
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.onclick = () => {
        modal.style.display = 'block';
        modalTitle.textContent = 'Edit User';
        userForm.id.value = btn.dataset.id;
        userForm.username.value = btn.dataset.username;
        userForm.password.value = '';
        userForm.role.value = btn.dataset.role;
    }
});

// Tutup modal
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
