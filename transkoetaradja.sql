-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Nov 2026 pada 05.57
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transkoetaradja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buses`
--

CREATE TABLE `buses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nomor_plat` varchar(255) NOT NULL,
  `nomor_lambung` varchar(255) DEFAULT NULL,
  `merk_bus` varchar(255) DEFAULT NULL,
  `tipe_bus` varchar(255) DEFAULT NULL,
  `tahun_pengadaan` year(4) DEFAULT NULL,
  `status_bus` enum('aktif','nonaktif','service') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `buses`
--

INSERT INTO `buses` (`id`, `nomor_plat`, `nomor_lambung`, `merk_bus`, `tipe_bus`, `tahun_pengadaan`, `status_bus`, `created_at`, `updated_at`) VALUES
(3, 'BL 7647 AA', 'TR01', 'HINO', NULL, '2015', 'aktif', '2026-11-19 06:59:02', '2026-11-19 06:59:02'),
(4, 'BL 7654 AA', 'TR02', 'HINO', NULL, '2021', 'aktif', '2026-11-22 21:55:56', '2026-11-22 21:55:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `inventories`
--

CREATE TABLE `inventories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bus_nomor_plat_ref` varchar(255) DEFAULT NULL,
  `kode_alat` varchar(255) DEFAULT NULL,
  `nama_alat` varchar(255) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `merk` varchar(255) DEFAULT NULL,
  `seri` varchar(255) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `tanggal_pembelian` date DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `harga_beli` decimal(15,2) DEFAULT NULL,
  `status_barang` varchar(255) DEFAULT NULL,
  `kondisi_awal` text DEFAULT NULL,
  `lokasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `inventories`
--

INSERT INTO `inventories` (`id`, `bus_nomor_plat_ref`, `kode_alat`, `nama_alat`, `kategori`, `merk`, `seri`, `supplier`, `tanggal_pembelian`, `tanggal_masuk`, `harga_beli`, `status_barang`, `kondisi_awal`, `lokasi`, `created_at`, `updated_at`) VALUES
(5, NULL, '012ADI', 'Oli Mesin 15W40', 'Oli', 'Ipone', '12HSD', 'daffa', '2026-04-15', NULL, 150000.00, 'terpasang', 'baik', NULL, '2026-06-16 07:11:56', '2026-11-12 03:24:53'),
(6, NULL, '012ADY', 'Oli Mesin 15W45', 'Oli', 'Ipone', ', kjbjhb', 'daffa', '2026-05-25', NULL, 1500000.00, 'rusak', 'baik', NULL, '2026-07-24 17:02:03', '2026-10-10 08:13:34'),
(7, NULL, '56D32L', 'OLI MESIN', 'Oli', 'GS ASTRA', 'DOT', 'PT Astra International Tbk', '2026-05-24', NULL, 510000.00, 'terpasang', 'baik', NULL, '2026-10-10 08:13:15', '2026-10-10 08:13:34'),
(8, NULL, '012ADI', 'aki', 'aki', 'aki', '12HSD', 'daffa', NULL, NULL, 1500.00, 'terpasang', 'baik', NULL, '2026-11-12 03:24:12', '2026-11-12 03:24:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bus_plat` varchar(255) NOT NULL,
  `komponen_lama_kode` varchar(255) NOT NULL,
  `komponen_lama_nama` varchar(255) DEFAULT NULL,
  `komponen_baru_kode` varchar(255) NOT NULL,
  `komponen_baru_nama` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `tanggal_ganti` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `km_saat_ganti` bigint(20) UNSIGNED DEFAULT NULL,
  `user_petugas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `bus_plat`, `komponen_lama_kode`, `komponen_lama_nama`, `komponen_baru_kode`, `komponen_baru_nama`, `catatan`, `tanggal_ganti`, `created_at`, `updated_at`, `km_saat_ganti`, `user_petugas`) VALUES
(1, 'BL 2131 JJ', '012ADI', 'Oli Mesin 15W40', '012ADY', 'Oli Mesin 15W45', 'Penggantian komponen melalui halaman monitoring.', '2026-07-24 17:02:40', '2026-07-24 17:02:40', '2026-07-24 17:02:40', NULL, NULL),
(2, 'BL 2131 JJ', '012ADY', 'Oli Mesin 15W45', '56D32L', 'OLI MESIN', 'Penggantian komponen melalui halaman monitoring.', '2026-10-10 08:13:34', '2026-10-10 08:13:34', '2026-10-10 08:13:34', 358000, 'Admin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_20_193228_create_buses_table', 1),
(5, '2026_04_20_193612_create_inventories_table', 1),
(6, '2026_04_20_193846_create_monitoring_assets_table', 1),
(7, '2026_04_20_194015_create_odometer_logs_table', 1),
(8, '2026_04_28_021436_add_inventory_columns_to_inventories_table', 1),
(9, '2026_06_16_102115_create_maintenance_logs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitoring_assets`
--

CREATE TABLE `monitoring_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_kode_alat_ref` varchar(255) NOT NULL,
  `bus_nomor_plat_ref` varchar(255) NOT NULL,
  `tanggal_acuan` date DEFAULT NULL,
  `km_acuan` int(11) DEFAULT NULL,
  `batas_waktu_bulan` int(11) DEFAULT NULL,
  `batas_km` int(11) DEFAULT NULL,
  `jenis_monitoring` enum('waktu','km','hybrid') NOT NULL DEFAULT 'hybrid',
  `warning_hari` int(11) DEFAULT NULL,
  `warning_km` int(11) DEFAULT NULL,
  `status_monitoring` enum('aman','peringatan','wajib_ganti') NOT NULL DEFAULT 'aman',
  `tanggal_expired` date DEFAULT NULL,
  `km_expired` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `monitoring_assets`
--

INSERT INTO `monitoring_assets` (`id`, `inventory_kode_alat_ref`, `bus_nomor_plat_ref`, `tanggal_acuan`, `km_acuan`, `batas_waktu_bulan`, `batas_km`, `jenis_monitoring`, `warning_hari`, `warning_km`, `status_monitoring`, `tanggal_expired`, `km_expired`, `catatan`, `last_checked_at`, `created_at`, `updated_at`) VALUES
(3, '56D32L', 'BL 2131 JJ', '2026-10-10', 358000, NULL, 368000, 'km', NULL, 2000, 'aman', NULL, NULL, NULL, NULL, '2026-06-16 07:13:00', '2026-10-10 08:13:34'),
(4, '012ADI', 'BL 2131 JJ', '2026-11-12', NULL, 24, NULL, 'waktu', 60, NULL, 'aman', NULL, NULL, NULL, NULL, '2026-11-12 03:24:53', '2026-11-12 03:24:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `odometer_logs`
--

CREATE TABLE `odometer_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bus_nomor_plat_ref` varchar(255) NOT NULL,
  `tanggal_update` date NOT NULL,
  `kilometer` int(11) NOT NULL,
  `user_nama` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `odometer_logs`
--

INSERT INTO `odometer_logs` (`id`, `bus_nomor_plat_ref`, `tanggal_update`, `kilometer`, `user_nama`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 'BL 2131 JJ', '2026-06-16', 725000, 'daffa', NULL, '2026-06-16 04:04:30', '2026-11-15 08:06:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('PMzww1gBJLdcNMySEewSiaMPgyZZzSotZgdROmaI', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJmQ1I4dmtiTzNaUkVlZzVGZGxJeFM4WUd4MjE2dGNyWndNekpyTXlvIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL21vbml0b3JpbmcudGVzdFwvYnVzZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1795096742),
('XfDCg0nxjL9pH6QwqJP62KMKmMJHPjCeTpHvbj5o', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJndmtmanE4WVZMRzVZQUhYOER0alZwSkJDb3BobTUwV0NvVzhIdWd5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL21vbml0b3JpbmcudGVzdFwvYnVzZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1795409756),
('xUPZ521GYnEYNSSLL3SVwtOvWgPtJsVatLe9Lq9i', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIzU2dKdFBVSWRyaW9TVDRlNmc3UjhLMFVVTTdwdGFYdXhXUnY5aTF0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL21vbml0b3JpbmcudGVzdCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1795409616);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `buses_nomor_plat_unique` (`nomor_plat`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `monitoring_assets`
--
ALTER TABLE `monitoring_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `odometer_logs`
--
ALTER TABLE `odometer_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buses`
--
ALTER TABLE `buses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `monitoring_assets`
--
ALTER TABLE `monitoring_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `odometer_logs`
--
ALTER TABLE `odometer_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
