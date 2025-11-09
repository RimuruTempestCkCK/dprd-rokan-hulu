-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Okt 2025 pada 12.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dpr`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `rapat`
--

CREATE TABLE `rapat` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `jenis` enum('Terbuka','Private') NOT NULL,
  `tanggal` datetime DEFAULT NULL,
  `isi` text NOT NULL,
  `status` enum('Belum','Selesai') DEFAULT 'Belum',
  `hasil` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rapat`
--

INSERT INTO `rapat` (`id`, `judul`, `jenis`, `tanggal`, `isi`, `status`, `hasil`) VALUES
(1, 'Rapat 1', 'Private', '2025-10-18 22:00:00', 'ini adalah tema rapat', 'Selesai', 'Semua milestone sesuai target. Setiap deliverable telah selesai tepat waktu sesuai jadwal yang telah ditetapkan, seluruh tim berhasil memenuhi target masing-masing, kualitas pekerjaan sudah diperiksa dan divalidasi, komunikasi antar tim berjalan lancar tanpa kendala berarti, risiko yang diprediksi telah diminimalkan, setiap dokumen dan laporan pendukung sudah lengkap, anggaran digunakan sesuai rencana, progres proyek tercatat dengan rapi, dan semua keputusan rapat telah disosialisasikan kepada pihak terkait agar tidak ada kesalahan komunikasi. Semua milestone sesuai target. Setiap deliverable telah selesai tepat waktu sesuai jadwal yang telah ditetapkan, seluruh tim berhasil memenuhi target masing-masing, kualitas pekerjaan sudah diperiksa dan divalidasi, komunikasi antar tim berjalan lancar tanpa kendala berarti, risiko yang diprediksi telah diminimalkan, setiap dokumen dan laporan pendukung sudah lengkap, anggaran digunakan sesuai rencana, progres proyek tercatat dengan rapi, dan semua keputusan rapat telah disosialisasikan kepada pihak terkait agar tidak ada kesalahan komunikasi.'),
(3, 'vasffas', 'Private', '2025-10-18 23:21:00', 'dvasvasf', 'Selesai', 'Semua milestone sesuai target. Setiap deliverable telah selesai tepat waktu sesuai jadwal yang telah ditetapkan, seluruh tim berhasil memenuhi target masing-masing, kualitas pekerjaan sudah diperiksa dan divalidasi, komunikasi antar tim berjalan lancar tanpa kendala berarti, risiko yang diprediksi telah diminimalkan, setiap dokumen dan laporan pendukung sudah lengkap, anggaran digunakan sesuai rencana, progres proyek tercatat dengan rapi, dan semua keputusan rapat telah disosialisasikan kepada pihak terkait agar tidak ada kesalahan komunikasi. Semua milestone sesuai target. Setiap deliverable telah selesai tepat waktu sesuai jadwal yang telah ditetapkan, seluruh tim berhasil memenuhi target masing-masing, kualitas pekerjaan sudah diperiksa dan divalidasi, komunikasi antar tim berjalan lancar tanpa kendala berarti, risiko yang diprediksi telah diminimalkan, setiap dokumen dan laporan pendukung sudah lengkap, anggaran digunakan sesuai rencana, progres proyek tercatat dengan rapi, dan semua keputusan rapat telah disosialisasikan kepada pihak terkait agar tidak ada kesalahan komunikasi.'),
(4, 'Rapat Peresmian Gedung', 'Terbuka', '2025-10-18 22:47:00', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'Selesai', 'It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.'),
(5, 'Rapat Perencanaan Keuangan', 'Terbuka', '2025-10-18 22:47:00', 'Dasar Keuangan', 'Belum', 'sdasd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dewan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'admin', 'admin'),
(2, 'dewan', 'dewan', 'dewan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `rapat`
--
ALTER TABLE `rapat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `rapat`
--
ALTER TABLE `rapat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
