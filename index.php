<?php
// Konfigurasi
$site_title = "Dewan Perwakilan Rakyat";
$site_description = "Lembaga Perwakilan Rakyat Republik Indonesia";

require 'koneksi.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

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
    $logoPath = 'img/logo.png';
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
    <title><?php echo $site_title; ?> - Profil dan Informasi</title>
    <meta name="description" content="<?php echo $site_description; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style_index.css">


</head>
<body>
    <!-- Navigation DPRD Rokan Hulu -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="brand-text">
                    <h1>DEWAN PERWAKILAN RAKYAT DAERAH</h1>
                    <p>KABUPATEN ROKAN HULU</p>
                </div>
            </div>

            <!-- Tombol toggle untuk versi mobile -->
            <button class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Menu navigasi -->
            <ul class="nav-menu" id="navMenu">
                <li><a href="#beranda" class="nav-link active">Beranda</a></li>
                <li><a href="#tentang" class="nav-link">Tentang DPRD</a></li>
                <!-- <li><a href="#anggota" class="nav-link">Anggota</a></li> -->
                <li><a href="#fungsi" class="nav-link">Fungsi & Tugas</a></li>
                <li><a href="#rapat" class="nav-link">Jadwal Rapat</a></li>
                <li><a href="#kontak" class="nav-link">Kontak</a></li>
                <li><a href="login.php" class="btn-login">Login</a></li>
            </ul>
        </div>
    </nav>


    <!-- Hero Section DPRD Rokan Hulu -->
    <section class="hero" id="beranda">
        <div class="hero-content">
            <h1 class="hero-title">Dewan Perwakilan Rakyat Daerah</h1>
            <h2 class="hero-subtitle">Kabupaten Rokan Hulu</h2>
            <p class="hero-description">
                Mewakili suara rakyat, memperjuangkan aspirasi masyarakat Rokan Hulu
            </p>
            <div class="hero-buttons">
                <a href="#tentang" class="btn btn-primary">Pelajari Lebih Lanjut</a>
                <!-- <a href="#anggota" class="btn btn-secondary">Lihat Anggota DPRD</a> -->
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>


    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number" data-target="575">0</div>
                    <div class="stat-label">Anggota DPR</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number" data-target="11">0</div>
                    <div class="stat-label">Komisi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number" data-target="234">0</div>
                    <div class="stat-label">Undang-Undang</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-number" data-target="34">0</div>
                    <div class="stat-label">Provinsi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section DPRD Rokan Hulu -->
    <section class="about-section" id="tentang">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Tentang DPRD Kabupaten Rokan Hulu</h2>
                <div class="title-underline"></div>
                <p class="section-subtitle">
                    Lembaga perwakilan rakyat daerah yang menjalankan fungsi legislasi, anggaran, dan pengawasan
                </p>
            </div>

            <div class="about-content">
                <div class="about-image">
                    <img src="https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=600" alt="Gedung DPRD Rokan Hulu">
                    <div class="about-image-overlay">
                        <i class="fas fa-landmark"></i>
                    </div>
                </div>

                <div class="about-text">
                    <h3>Sejarah dan Peran DPRD Rokan Hulu</h3>
                    <p>
                        Dewan Perwakilan Rakyat Daerah (DPRD) Kabupaten Rokan Hulu adalah lembaga perwakilan rakyat daerah
                        yang berfungsi menjalankan legislasi daerah, mengawasi jalannya pemerintahan daerah, dan menyusun
                        serta menyetujui anggaran daerah.
                    </p>

                    <div class="about-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Fungsi Legislasi</h4>
                                <p>Membentuk peraturan daerah (Perda) bersama eksekutif</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Fungsi Anggaran</h4>
                                <p>Membahas dan menyetujui APBD Kabupaten Rokan Hulu</p>
                            </div>
                        </div>

                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <h4>Fungsi Pengawasan</h4>
                                <p>Mengawasi pelaksanaan peraturan daerah dan kebijakan pemerintah daerah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    
    <!-- Functions Section DPRD Rokan Hulu -->
    <section class="functions-section" id="fungsi">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Fungsi & Tugas DPRD Kabupaten Rokan Hulu</h2>
                <div class="title-underline"></div>
            </div>

            <div class="functions-grid">
                <div class="function-card">
                    <div class="function-icon">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3>Legislasi</h3>
                    <p>
                        Membentuk peraturan daerah (Perda) bersama eksekutif untuk memastikan kepatuhan terhadap kebijakan daerah
                    </p>
                </div>

                <div class="function-card">
                    <div class="function-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Anggaran</h3>
                    <p>
                        Membahas dan menyetujui Anggaran Pendapatan dan Belanja Daerah (APBD) Kabupaten Rokan Hulu
                    </p>
                </div>

                <div class="function-card">
                    <div class="function-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Pengawasan</h3>
                    <p>
                        Mengawasi pelaksanaan peraturan daerah dan kebijakan pemerintah daerah agar sesuai dengan ketentuan
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- Jadwal Rapat Publik -->
    <section class="rapat-section" id="rapat">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Jadwal Rapat DPRD</h2>
                <div class="title-underline"></div>
                <p class="section-subtitle">
                    Transparansi agenda rapat DPRD Kabupaten Rokan Hulu
                </p>
            </div>


            <?php
            require 'koneksi.php';
            $stmt = $pdo->query("SELECT * FROM rapat WHERE jenis = 'Terbuka' ORDER BY tanggal DESC");
            $rapats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="table-container">
                <table class="rapat-table">
                    <thead>
                        <tr>
                            <!-- <th>ID</th> -->
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Tema</th>
                            <th>Status</th>
                            <th>Hasil Rapat</th>
                            <th>Undangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rapats): ?>
                            <?php foreach ($rapats as $rapat): ?>
                                <tr>
                                    <!-- <td><?= htmlspecialchars($rapat['id']) ?></td> -->
                                    <td><?= htmlspecialchars($rapat['judul']) ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($rapat['tanggal'])) ?></td>
                                    <td><?= htmlspecialchars($rapat['isi']) ?></td>
                                    <td><?= htmlspecialchars($rapat['status']) ?></td>
                                    <td class="hasil">
                                        <?= $rapat['status'] === 'Selesai' 
                                            ? htmlspecialchars($rapat['hasil']) 
                                            : '<em>Rapat belum selesai</em>' ?>
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
                            <tr><td colspan="6" style="text-align:center; padding: 30px;">Belum ada rapat publik terjadwal.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Contact Section DPRD Rokan Hulu -->
    <section class="contact-section" id="kontak">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Hubungi Kami</h2>
                <div class="title-underline"></div>
            </div>

            <div class="contact-grid">
                <div class="contact-info">
                    <h3>Informasi Kontak</h3>

                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Alamat</h4>
                            <p>Jl. Panglima Sulung Nomor : 09, Pasir Pengaraian, Kabupaten Rokan Hulu, Riau 28557</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Telepon</h4>
                            <p>(0762) 91460</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p>dprdrohul@gmail.com</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class="fas fa-globe"></i>
                        <div>
                            <h4>Website</h4>
                            <p>www.dprd-rohul.go.id</p>
                        </div>
                    </div>

                    <div class="social-media">
                        <h4>Ikuti Kami</h4>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h3>Kirim Pesan</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" placeholder="Subjek" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" rows="5" placeholder="Pesan" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer DPRD Rokan Hulu -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Dewan Perwakilan Rakyat Daerah</h3>
                    <p>
                        DPRD Kabupaten Rokan Hulu adalah lembaga legislatif daerah yang bertugas
                        membentuk peraturan daerah, menetapkan APBD, dan melakukan pengawasan
                        terhadap kebijakan pemerintah daerah.
                    </p>
                </div>

                <div class="footer-section">
                    <h3>Link Cepat</h3>
                    <ul>
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#tentang">Tentang DPRD</a></li>
                        <li><a href="#anggota">Anggota</a></li>
                        <li><a href="#rapat">Jadwal Rapat</a></li>
                        <li><a href="#kontak">Kontak</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Informasi</h3>
                    <ul>
                        <li><a href="#">Transparansi</a></li>
                        <li><a href="#">Peraturan Daerah</a></li>
                        <li><a href="#">Dokumen</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Dewan Perwakilan Rakyat Daerah Kabupaten Rokan Hulu. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <script>
        // Navbar Toggle for Mobile
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');

        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking on a link
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                const icon = navToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });

        // Active Navigation on Scroll
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }

            // Update active nav link based on scroll position
            const sections = document.querySelectorAll('section');
            const scrollPos = window.scrollY + 100;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');

                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });

        // Smooth Scrolling for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Counter Animation for Stats
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    element.textContent = Math.floor(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            };

            updateCounter();
        }

        // Intersection Observer for Stats Animation
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        animateCounter(counter);
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Fade In Animation on Scroll
        const fadeInObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        const fadeElements = document.querySelectorAll('.member-card, .function-card');
        fadeElements.forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            fadeInObserver.observe(element);
        });

        // Form Submission Handler
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Terima kasih! Pesan Anda telah dikirim.');
                this.reset();
            });
        }

        console.log('Landing Page DPR RI loaded successfully!');
    </script>
</body>
</html>>
