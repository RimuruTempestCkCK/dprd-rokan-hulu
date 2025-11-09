<?php 
session_start();
require '../koneksi.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Proteksi halaman: hanya dewan
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'dewan') {
    header('Location: ../login.php');
    exit();
}

// ==== CETAK PDF ====
if (isset($_GET['action']) && $_GET['action'] === 'cetak' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM rapat WHERE id = ?");
    $stmt->execute([$id]);
    $rapat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rapat) {
        die("Data rapat tidak ditemukan.");
    }

    // Konfigurasi DOMPDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Template PDF DPRD Rokan Hulu (dinamis, font 12pt, jarak lebih rapat)
    $logoPath = '../img/logo.png';
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoSrc = 'data:image/png;base64,' . $logoData;

    // Nomor surat otomatis
    $tahun = date('Y', strtotime($rapat['tanggal']));
    $no_surat = "UND-{$rapat['id']}/DPRD-ROHUL-UM/{$tahun}";
    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: DejaVu Sans, sans-serif; 
                line-height: 1.2;   /* lebih rapat */
                padding: 30px; 
                font-size: 12pt;    /* font 12pt */
            }

            .kop {
                width: 100%;
                text-align: center;
                border-bottom: 3px solid #000;
                padding-bottom: 6px;
                margin-bottom: 15px;
            }

            .kop .text {
                text-align: center;
                line-height: 1.1;
            }

            .kop .text h2 {
                margin: 0;
                font-size: 16pt;
                font-weight: bold;
            }

            .kop .text h3 {
                margin: 0;
                font-size: 12pt;
                font-weight: bold;
            }

            .kop .text p {
                margin: 1px 0;
                font-size: 10pt;
            }

            .judul {
                text-align: center;
                font-size: 12pt;
                font-weight: bold;
                margin-top: 10px;
                margin-bottom: 15px;
                text-decoration: underline;
            }

            table {
                width: 100%;
                margin-top: 10px;
                border-collapse: collapse;
            }

            td {
                padding: 2px 0;
                vertical-align: top;
            }

            .footer {
                margin-top: 30px;
                text-align: right;
                font-weight: bold;
                margin-right: 20px;
            }

            .footer .nama {
                margin-top: 50px;
            }
        </style>
    </head>
    <body>

        <div class="kop" style="width:100%; border-bottom:3px solid #000; padding-bottom:6px; margin-bottom:15px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <!-- Logo di kiri -->
                    <td style="width:80px; text-align:center; vertical-align:middle;">
                        <img src="'.$logoSrc.'" alt="Logo DPRD" style="width:70px; height:auto;">
                    </td>
                    <!-- Teks di kanan -->
                    <td style="text-align:center; vertical-align:middle;">
                        <h2 style="margin:0; font-size:16pt;">DEWAN PERWAKILAN RAKYAT DAERAH</h2>
                        <h3 style="margin:0; font-size:12pt;">KABUPATEN ROKAN HULU</h3>
                        <p style="margin:1px 0; font-size:10pt;">Jl. Panglima Sulung Nomor : 09 Pasir Pengaraian</p>
                        <p style="margin:1px 0; font-size:10pt;">Telp. (0762) - 91460 Faks (0762) 91460 | Kode pos: 28557</p>
                        <p style="margin:1px 0; font-size:10pt;">Website : www.dprd-rohul.go.id | Email: dprdrohul@gmail.com</p>
                        <p style="margin:1px 0; font-size:10pt;">Pasir Pengaraian, '.date('d F Y').'</p>
                    </td>
                </tr>
            </table>
        </div>

        <p>Kepada Yth,<br>
        Sdr. ....</p>
        <p>Nomor : '.$no_surat.'<br>
        Sifat : Penting<br>
        Lamp. : -<br>
        Perihal : UNDANGAN</p>

        <p>Sehubungan dengan akan dilaksanakannya rapat sebagaimana rincian berikut:</p>

        <table>
            <tr><td width="160"><strong>Judul Rapat</strong></td><td>: '.htmlspecialchars($rapat['judul']).'</td></tr>
            <tr><td><strong>Jenis</strong></td><td>: '.htmlspecialchars($rapat['jenis']).'</td></tr>
            <tr><td><strong>Hari/Tanggal</strong></td><td>: '.date('l, d F Y', strtotime($rapat['tanggal'])).'</td></tr>
            <tr><td><strong>Waktu</strong></td><td>: '.date('H:i', strtotime($rapat['tanggal'])).' WIB</td></tr>
            <tr><td><strong>Tema</strong></td><td>: '.htmlspecialchars($rapat['isi']).'</td></tr>
            <tr><td><strong>Status</strong></td><td>: '.htmlspecialchars($rapat['status']).'</td></tr>
        </table>

        <p>Diharapkan kehadiran Saudara tepat waktu untuk kelancaran jalannya rapat. Atas perhatian dan kerja samanya, kami ucapkan terima kasih.</p>

        <div class="footer">
            Hormat kami,<br>
            <span>Ketua DPRD Kabupaten Rokan Hulu</span>
            <div class="nama">
                <strong>Hj. SUMIARTINI</strong>
            </div>
        </div>

    </body>
    </html>';

    // Buat PDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Undangan_Rapat_{$rapat['id']}.pdf", ["Attachment" => false]);
    exit();
}

// ==== TAMPILAN DAFTAR RAPAT ====
$stmt = $pdo->query("SELECT * FROM rapat ORDER BY tanggal ASC");
$rapats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Rapat - Dewan</title>
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
                <div class="user-profile"><div class="user-avatar">DW</div><span>Dewan</span></div>
                <a href="../logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </header>

        <main class="content">
            <h1 class="page-title">Jadwal Rapat</h1>

            <div class="table-container">
                <div class="table-header">
                    <h3>Daftar Rapat</h3>
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
                            <th>Hasil Rapat</th> 
                            <th>Action</th>
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
                                        <a href="?action=cetak&id=<?= $rapat['id'] ?>" target="_blank" 
                                        style="
                                            display: inline-flex; 
                                            align-items: center; 
                                            gap: 5px; 
                                            background-color: #E74C3C; 
                                            color: #fff; 
                                            padding: 5px 12px; 
                                            border-radius: 5px; 
                                            text-decoration: none; 
                                            font-weight: 500; 
                                            font-size: 14px;
                                            transition: 0.3s;
                                        "
                                        onmouseover="this.style.backgroundColor='#C0392B'; this.style.boxShadow='0 3px 8px rgba(0,0,0,0.2)';"
                                        onmouseout="this.style.backgroundColor='#E74C3C'; this.style.boxShadow='none';"
                                        >
                                            <i class="fa-solid fa-file-pdf" style="font-size:14px;"></i> Cetak
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





<script src="../script.js"></script>

<script>

const closeBtn = modal.querySelector('.close-btn');
const modalTitle = document.getElementById('modalTitle');
const rapatForm = document.getElementById('rapatForm');





closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target == modal) modal.style.display = 'none'; }

</script>
</body>
</html>
