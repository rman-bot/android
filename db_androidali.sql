-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Jan 2026 pada 19.42
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
-- Database: `db_androidali`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_hitung_pendapatan` (IN `p_id` INT)   BEGIN
  UPDATE pendapatan_ojol
  SET 
    total_pemasukan = (goride_pendapatan + gofood_pendapatan + gosend_pendapatan + bonus + tips),
    total_pengeluaran = (bensin + parkir + makan),
    total_bersih = (goride_pendapatan + gofood_pendapatan + gosend_pendapatan + bonus + tips) - 
                   (bensin + parkir + makan)
  WHERE id = p_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `name`, `email`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(2, 'admin', '$2y$10$W3tGaFkHqIEwUNks1nTbxuV3F4/0tyf0p4t/JTYzEt.Ls5w/waBOm', 'Super Administrator', 'admin@ppdb.com', 'super_admin', 'active', '2026-01-13 01:32:06', '2025-12-29 16:14:08', '2026-01-12 18:32:06'),
(3, 'staff', '$2y$10$8ED0Hep0YzwOAU3mMmmoJOmTtWOCMZdQXDbyL3zZX7qg2UDwNRqlW', 'Staff PPDB', 'staff@ppdb.com', '', 'active', NULL, '2025-12-29 16:14:08', '2025-12-29 16:14:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `landing`
--

CREATE TABLE `landing` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `playstore_link` varchar(255) NOT NULL,
  `apkpure_link` varchar(500) DEFAULT 'https://apkpure.com/mercusuar-tabungan-pintar/com.mercusuar.tabungan',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `logo` varchar(255) DEFAULT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `feature_1` varchar(255) DEFAULT NULL,
  `feature_2` varchar(255) DEFAULT NULL,
  `feature_3` varchar(255) DEFAULT NULL,
  `hero_badge` varchar(150) DEFAULT NULL,
  `stat_user` varchar(50) DEFAULT NULL,
  `stat_rating` varchar(50) DEFAULT NULL,
  `stat_support` varchar(50) DEFAULT NULL,
  `step_1` varchar(255) DEFAULT NULL,
  `step_2` varchar(255) DEFAULT NULL,
  `step_3` varchar(255) DEFAULT NULL,
  `testi_1_name` varchar(100) DEFAULT NULL,
  `testi_1_text` mediumtext DEFAULT NULL,
  `testi_2_name` varchar(100) DEFAULT NULL,
  `testi_2_text` mediumtext DEFAULT NULL,
  `testi_3_name` varchar(100) DEFAULT NULL,
  `testi_3_text` mediumtext DEFAULT NULL,
  `faq_1_q` varchar(255) DEFAULT NULL,
  `faq_1_a` mediumtext DEFAULT NULL,
  `faq_2_q` varchar(255) DEFAULT NULL,
  `faq_2_a` mediumtext DEFAULT NULL,
  `faq_3_q` varchar(255) DEFAULT NULL,
  `faq_3_a` mediumtext DEFAULT NULL,
  `faq_4_q` varchar(255) DEFAULT NULL,
  `faq_4_a` mediumtext DEFAULT NULL,
  `cta_title` varchar(255) DEFAULT NULL,
  `cta_desc` mediumtext DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `landing`
--

INSERT INTO `landing` (`id`, `title`, `description`, `playstore_link`, `apkpure_link`, `created_at`, `logo`, `cta_text`, `feature_1`, `feature_2`, `feature_3`, `hero_badge`, `stat_user`, `stat_rating`, `stat_support`, `step_1`, `step_2`, `step_3`, `testi_1_name`, `testi_1_text`, `testi_2_name`, `testi_2_text`, `testi_3_name`, `testi_3_text`, `faq_1_q`, `faq_1_a`, `faq_2_q`, `faq_2_a`, `faq_3_q`, `faq_3_a`, `faq_4_q`, `faq_4_a`, `cta_title`, `cta_desc`, `is_active`) VALUES
(1, 'Campustool : BTI dan Game', 'Aplikasi multifungsi untuk menghitung berat tubuh ideal, mengatur keuangan driver, dan bermain game sederhana', 'https://play.google.com/store/apps/details?id=com.mercusuar.ramadhanali', 'https://apkpure.com/p/com.mercusuar.ramadhanali', '2025-12-26 04:33:25', 'logo_1768241824.png', 'Download Sekarang', 'Kalkulator Tubuh Ideal (BTI)', 'Jump Game Sederhana', 'Budgeting Fee Driver Gojek', '🎮 Edu & Fun App 2026', '50K+', '4.7', 'Online', 'Install Aplikasi', 'Pilih Fitur yang Dibutuhkan', 'Gunakan & Nikmati', 'Ahmad Fauzi', 'Kalkulator BTI-nya sangat membantu untuk cek berat badan ideal.', 'Dewi Lestari', 'Jump Game-nya seru dan bisa jadi hiburan ringan.', 'Rizky Pramana', 'Fitur budgeting driver sangat membantu mengatur penghasilan harian.', 'Apakah Campustool gratis?', 'Ya, semua fitur utama dapat digunakan secara gratis.', 'Apakah aplikasi ini cocok untuk mahasiswa?', 'Sangat cocok untuk mahasiswa dan pengguna umum.', 'Apakah data disimpan dengan aman?', 'Data pengguna disimpan secara lokal dan aman.', 'Apakah membutuhkan koneksi internet?', 'Beberapa fitur dapat digunakan tanpa koneksi internet.', 'Download Campustool Sekarang', 'Satu aplikasi dengan banyak fungsi edukatif dan hiburan', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_log`
--

CREATE TABLE `login_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendapatan_ojol`
--

CREATE TABLE `pendapatan_ojol` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_kerja` date NOT NULL,
  `goride_jumlah` int(11) DEFAULT 0,
  `goride_pendapatan` decimal(10,2) DEFAULT 0.00,
  `gofood_jumlah` int(11) DEFAULT 0,
  `gofood_pendapatan` decimal(10,2) DEFAULT 0.00,
  `gosend_jumlah` int(11) DEFAULT 0,
  `gosend_pendapatan` decimal(10,2) DEFAULT 0.00,
  `bonus` decimal(10,2) DEFAULT 0.00,
  `tips` decimal(10,2) DEFAULT 0.00,
  `bensin` decimal(10,2) DEFAULT 0.00,
  `parkir` decimal(10,2) DEFAULT 0.00,
  `makan` decimal(10,2) DEFAULT 0.00,
  `total_pemasukan` decimal(10,2) DEFAULT 0.00,
  `total_pengeluaran` decimal(10,2) DEFAULT 0.00,
  `total_bersih` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pendapatan_ojol`
--

INSERT INTO `pendapatan_ojol` (`id`, `user_id`, `tanggal_kerja`, `goride_jumlah`, `goride_pendapatan`, `gofood_jumlah`, `gofood_pendapatan`, `gosend_jumlah`, `gosend_pendapatan`, `bonus`, `tips`, `bensin`, `parkir`, `makan`, `total_pemasukan`, `total_pengeluaran`, `total_bersih`, `created_at`, `updated_at`) VALUES
(1, 2, '2026-01-06', 10, 100000.00, 5, 50000.00, 2, 30000.00, 0.00, 10000.00, 25000.00, 4000.00, 20000.00, 190000.00, 49000.00, 141000.00, '2026-01-06 11:37:36', '2026-01-06 13:32:58'),
(2, 6, '2026-01-06', 5, 50000.00, 2, 20000.00, 1, 15000.00, 0.00, 5000.00, 20000.00, 2000.00, 15000.00, 90000.00, 37000.00, 53000.00, '2026-01-06 13:30:33', '2026-01-06 13:30:33'),
(3, 1, '2026-01-06', 3, 30000.00, 5, 44000.00, 3, 45000.00, 0.00, 5000.00, 15000.00, 4000.00, 20000.00, 124000.00, 39000.00, 85000.00, '2026-01-06 13:54:10', '2026-01-06 13:54:10'),
(4, 8, '2026-01-10', 20, 194000.00, 0, 0.00, 0, 0.00, 0.00, 0.00, 20000.00, 2000.00, 20000.00, 194000.00, 42000.00, 152000.00, '2026-01-10 15:05:22', '2026-01-10 15:05:22');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `Id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(100) NOT NULL,
  `username` varchar(60) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`Id`, `name`, `email`, `password`, `username`, `created_at`) VALUES
(1, 'ali yusup', 'aliyusup@gmail.com', '$2y$10$6SJdALNbjBv5DSDNLiegPuiAeXq3IJ.OfFmLS/pucRTy.mIEyZSbm', 'ram123', '2025-12-29 23:19:41'),
(2, 'rama', 'ramadhan@gmail.com', '$2y$10$dHKSDxUBUTzcDDHxvFsHzeoyTDyyO5VOJz2x4X67Tca00CdXAKXhK', 'rama30', '2025-12-29 23:19:41'),
(3, 'jule', 'juleha@gmail.com', '$2y$10$3KF9.jFhEqRXYx4L1N76TOGOHIZ3YI8t4BIFX.CAS24tuxv4/ybnu', 'ucup', '2025-12-29 23:19:41'),
(4, 'kiki r', 'kikir@gmail.com', '$2y$10$GoVlFE0Olm7APbbpQ6Lneu6kD', 'kiki', '2025-12-29 23:19:41'),
(6, 'rudi', 'rudinur@gmail.com', '$2y$10$4uIDqI/zKj7YpmD74Eyw..NokY3LjNd6HGbDN2IFctbX26u6CvHze', 'rudi', '2026-01-06 19:02:51'),
(7, 'ardan', 'arandan@gmail.com', '$2y$10$d7KX1lhJxYR/R7SRww2D9uVY.4zrywTPYs58kscTS8VGXTQWiLsYi', 'arsat', '2026-01-09 17:43:19'),
(8, 'rifki', 'rifkibur@gmail.com', '$2y$10$v64s5YDCleIQWFMWGDt4AOU5saigVj1UOiF5LumNSM7qd4bkKY5Aa', 'rifki', '2026-01-09 17:54:43'),
(9, 'handika', 'hanka@gmail.com', '$2y$10$7zS6BKYu8zDUPZ.x3YrYPejdzSB/xTK05TwKgWuzlIaL0zEzpbwsa', 'han12', '2026-01-10 21:19:39'),
(10, 'satria', 'satria12@gmail.com', '$2y$10$9XjMlbLpwNbP1hDbsXpUuuWiLq7/CFrmC..7tAZzzEZWBH0DMnR4u', 'satria', '2026-01-10 21:43:07');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `v_pendapatan_bulanan`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `v_pendapatan_bulanan` (
`user_id` int(11)
,`tahun` int(4)
,`bulan` int(2)
,`total_hari_kerja` bigint(21)
,`total_order` decimal(34,0)
,`total_pemasukan_bulanan` decimal(32,2)
,`total_pengeluaran_bulanan` decimal(32,2)
,`total_bersih_bulanan` decimal(32,2)
,`rata_rata_harian` decimal(14,6)
);

-- --------------------------------------------------------

--
-- Struktur untuk view `v_pendapatan_bulanan`
--
DROP TABLE IF EXISTS `v_pendapatan_bulanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_pendapatan_bulanan`  AS SELECT `pendapatan_ojol`.`user_id` AS `user_id`, year(`pendapatan_ojol`.`tanggal_kerja`) AS `tahun`, month(`pendapatan_ojol`.`tanggal_kerja`) AS `bulan`, count(0) AS `total_hari_kerja`, sum(`pendapatan_ojol`.`goride_jumlah` + `pendapatan_ojol`.`gofood_jumlah` + `pendapatan_ojol`.`gosend_jumlah`) AS `total_order`, sum(`pendapatan_ojol`.`total_pemasukan`) AS `total_pemasukan_bulanan`, sum(`pendapatan_ojol`.`total_pengeluaran`) AS `total_pengeluaran_bulanan`, sum(`pendapatan_ojol`.`total_bersih`) AS `total_bersih_bulanan`, avg(`pendapatan_ojol`.`total_bersih`) AS `rata_rata_harian` FROM `pendapatan_ojol` GROUP BY `pendapatan_ojol`.`user_id`, year(`pendapatan_ojol`.`tanggal_kerja`), month(`pendapatan_ojol`.`tanggal_kerja`) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `landing`
--
ALTER TABLE `landing`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_login_time` (`login_time`);

--
-- Indeks untuk tabel `pendapatan_ojol`
--
ALTER TABLE `pendapatan_ojol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`tanggal_kerja`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tanggal_kerja` (`tanggal_kerja`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `landing`
--
ALTER TABLE `landing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `login_log`
--
ALTER TABLE `login_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pendapatan_ojol`
--
ALTER TABLE `pendapatan_ojol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `login_log`
--
ALTER TABLE `login_log`
  ADD CONSTRAINT `login_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pendapatan_ojol`
--
ALTER TABLE `pendapatan_ojol`
  ADD CONSTRAINT `fk_user_pendapatan` FOREIGN KEY (`user_id`) REFERENCES `user` (`Id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
