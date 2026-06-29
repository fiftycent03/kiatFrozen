-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 11 Des 2025 pada 03.22
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kiat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cumi', 'cumi', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09'),
(2, 'Dory', 'dory', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09'),
(3, 'Fillet Ikan', 'fillet ikan', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09'),
(4, 'Kepiting', 'kepiting', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09'),
(5, 'Scallop', 'scallop', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09'),
(6, 'Udang', 'udang', 1, '2025-12-02 07:47:09', '2025-12-02 07:47:09');

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
(4, '2025_09_04_070641_create_shipping_zones_table', 2),
(5, '2025_09_04_070651_create_delivery_slots_table', 3),
(7, '2025_09_04_071828_add_price_per_kg_and_stock_to_products', 4),
(8, '2025_09_06_105134_drop_delivery_slots_and_fk_from_orders', 4),
(9, '2025_11_26_021338_create_orders_table', 5),
(10, '2025_11_26_021430_create_order_items_table', 6),
(11, '2025_11_26_035329_add_role_id_to_users_table', 7),
(12, '2025_11_30_114621_add_category_to_products_table', 8),
(13, '2025_11_30_123400_replace_stock_grams_with_min_pembelian', 9),
(14, '2025_12_02_144055_create_categories_table', 10),
(15, '2025_12_02_144914_add_category_id_to_products_table', 11),
(16, '2025_12_02_160455_drop_category_column_from_products_table', 12),
(17, '2025_12_05_045708_update_orders_table_structure', 13),
(18, '2025_12_05_062319_add_user_id_to_orders_table', 14),
(19, '2025_12_05_233052_add_payment_proof_to_orders_table', 15);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(40) NOT NULL,
  `payment_status` enum('pending','paid','expired','cancelled') NOT NULL DEFAULT 'pending',
  `fulfillment_status` enum('pending','processing','shipped','delivered','failed') NOT NULL DEFAULT 'pending',
  `shipping_tracking_number` varchar(255) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_service` varchar(255) NOT NULL DEFAULT 'standard',
  `total` decimal(12,2) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `shipping_date` date NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `customer_address` text NOT NULL,
  `province` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `code`, `payment_status`, `fulfillment_status`, `shipping_tracking_number`, `subtotal`, `shipping_fee`, `shipping_service`, `total`, `payment_proof`, `shipping_date`, `customer_name`, `customer_phone`, `customer_address`, `province`, `notes`, `payment_channel`, `created_at`, `paid_at`, `updated_at`) VALUES
(21, 36, 'ORD-FH9L8Y', 'paid', 'shipped', NULL, 80000.00, 35000.00, 'express', 115000.00, 'payments/jvtGytdmm8qScHczWAxrUQdryf4kP3m9qWHNdKbF.jpg', '2025-12-12', 'Galih Rahwangga', '085964160759', 'Jln Selolima No. 90, Surabaya, Jawa Timur', 'Jawa Timur', 'Cepat ya.', 'transfer', '2025-12-10 07:38:06', '2025-12-10 07:59:02', '2025-12-10 08:02:39'),
(23, 36, 'ORD-JCVDJF', 'pending', 'shipped', NULL, 135000.00, 20000.00, 'regular', 155000.00, NULL, '2025-12-15', 'Galih Rahwangga', '085976589078', 'Jln Raharjo No. 80, Sleman, Jawa Tengah', 'Jawa Tengah', 'cepat ya', 'cod', '2025-12-10 08:05:06', NULL, '2025-12-10 08:17:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `grams` int(10) UNSIGNED NOT NULL,
  `price_per_kg_snapshot` decimal(12,2) NOT NULL,
  `name_snapshot` varchar(200) NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `grams`, `price_per_kg_snapshot`, `name_snapshot`, `qty`, `subtotal`, `created_at`, `updated_at`) VALUES
(23, 21, 40, 1000, 80000.00, 'Cumi-cumi Flower', 1, 80000.00, '2025-12-10 07:38:06', '2025-12-10 07:38:06'),
(25, 23, 25, 1000, 135000.00, 'Daging Kepiting Rajungan Back Fin', 1, 135000.00, '2025-12-10 08:05:06', '2025-12-10 08:05:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `slug` varchar(190) NOT NULL,
  `sku_root` varchar(100) DEFAULT NULL,
  `price_per_kg` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_pembelian` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `satuan` varchar(255) NOT NULL DEFAULT 'kg',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `slug`, `sku_root`, `price_per_kg`, `min_pembelian`, `satuan`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(22, 'Fillet Ikan Patin Beku', 3, 'fillet-ikan-patin-beku', 'FIL-IKAP', 45000.00, 1, 'kg', 'Ikan patin fillet dengan kualitas dan kesegaran yang teruji', 1, '2025-11-30 13:28:57', '2025-11-30 13:41:13'),
(23, 'Scallop US (size 10/20)', 5, 'scallop-us-size-1020', 'SC-IMP', 98500.00, 1, 'pcs', 'Scallop yang di import langsung dari negara Amerika Serikat yang tidak diragukan lagi kualitasnya', 1, '2025-11-30 13:43:43', '2025-12-02 14:13:03'),
(24, 'Calamary Ring', 1, 'calamary-ring', 'CR-IMP', 70000.00, 1, 'kg', 'Cocok digunakan untuk dimakan goreng tepung dan kesukaan anak anak sebagai camilan', 1, '2025-11-30 13:45:42', '2025-11-30 13:45:42'),
(25, 'Daging Kepiting Rajungan Back Fin', 4, 'daging-kepiting-rajungan-back-fin', 'DA-KEPRA', 135000.00, 1, 'kg', 'Daging kepiting', 1, '2025-11-30 13:47:17', '2025-11-30 13:47:17'),
(26, 'Cumi Tube (Big Size)', 1, 'cumi-tube-big-size', 'CU-TU', 65000.00, 1, 'kg', 'Cumi Tube enak', 1, '2025-11-30 13:50:17', '2025-11-30 13:50:17'),
(27, 'Udang Kupas Vaname (Size 61/70)', 6, 'udang-kupas-vaname-size-6170', 'UD-KV', 80000.00, 1, 'kg', 'Udang Kupas enak', 1, '2025-11-30 13:52:25', '2025-11-30 13:52:25'),
(28, 'Ikan Tenggiri Steak', 3, 'ikan-tenggiri-steak', 'IK-TS', 70000.00, 1, 'kg', 'Steak Tenggiri enak', 1, '2025-11-30 13:55:33', '2025-11-30 13:55:33'),
(29, 'Fillet Salmon Skin', 3, 'fillet-salmon-skin', 'Fil-SS', 250000.00, 1, 'kg', 'Kulit salmon kualitas tinggi', 1, '2025-11-30 13:57:17', '2025-11-30 13:57:17'),
(30, 'Kepiting Soka Kering (Small Size)', 4, 'kepiting-soka-kering-small-size', 'KEP-SK', 155000.00, 1, 'kg', 'Kepiting soka enak', 1, '2025-11-30 13:59:11', '2025-11-30 13:59:11'),
(31, 'Capit Kepiting (Size Medium)', 4, 'capit-kepiting-size-medium', 'KEP-CA', 130000.00, 1, 'kg', 'Capit Kepiting dengan rasa yang manis', 1, '2025-12-01 00:13:36', '2025-12-01 00:13:36'),
(32, 'Fillet Kakap Putih (Skin On)', 3, 'fillet-kakap-putih-skin-on', 'FIL-KAPU', 70000.00, 10, 'kg', 'Fillet kakap putih enak dengan kulit kakap sekaligus', 1, '2025-12-01 00:16:29', '2025-12-01 00:16:29'),
(33, 'Salmon Fillet dan Beku', 3, 'salmon-fillet-dan-beku', 'SAL-FIBE', 350000.00, 1, 'pcs', 'Salmon tersedia dalam bentuk fillet dan beku', 1, '2025-12-01 00:18:46', '2025-12-01 00:18:46'),
(34, 'Ikan Beku Shisamo', 3, 'ikan-beku-shisamo', 'IK-SH', 55000.00, 5, 'pcs', 'Ikan Beku Shisamo', 1, '2025-12-01 00:20:31', '2025-12-01 00:20:31'),
(35, 'Fillet Ikan Dasar (Skinless)', 3, 'fillet-ikan-dasar-skinless', 'FIL-ID', 65000.00, 10, 'kg', 'Fillet ikan dasar tanpa kulit', 1, '2025-12-01 00:22:05', '2025-12-01 00:26:25'),
(36, 'Fillet Ikan Tuna Beku', 3, 'fillet-ikan-tuna-beku', 'FIL-IT', 110000.00, 5, 'kg', 'Fillet Tuna segar', 1, '2025-12-01 00:23:37', '2025-12-01 00:23:37'),
(37, 'Fillet Ikan Beku (No Glassing)', 3, 'fillet-ikan-beku-no-glassing', 'FILL-IB', 35000.00, 1, 'kg', 'Fillet ikan beku no glassing enak', 1, '2025-12-01 00:25:57', '2025-12-01 00:25:57'),
(38, 'Fillet Kakap Merah (Skin On)', 3, 'fillet-kakap-merah-skin-on', 'FIL-KM', 75000.00, 1, 'kg', 'Fillet kakap merah dengan kulit kakap', 1, '2025-12-01 00:28:05', '2025-12-01 00:28:05'),
(39, 'Fillet Skin Ikan Tuna Beku', 3, 'fillet-skin-ikan-tuna-beku', 'FIL-SIT', 90000.00, 10, 'kg', 'Fillet skin ikan tuna enak', 1, '2025-12-01 00:29:38', '2025-12-01 00:29:38'),
(40, 'Cumi-cumi Flower', 1, 'cumi-cumi-flower', 'CF-IMP', 80000.00, 10, 'kg', 'Cumi cumi flower yang di import', 1, '2025-12-01 00:30:58', '2025-12-02 13:11:56'),
(41, 'Ikan Dory', 2, 'ikan-dory', 'IK-DOR', 30000.00, 1, 'kg', 'KMDIWED', 1, '2025-12-03 00:29:31', '2025-12-03 00:29:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `path`, `is_primary`, `created_at`, `updated_at`) VALUES
(25, 22, 'products/VRNZ5GvRvjzSOmG3OE9NYA8WF7AOJEFhn3nDqhdD.jpg', 1, NULL, NULL),
(26, 23, 'products/pGK0fPDzvY9ZYs0ZGKhtrYBZ6iVHtlxXPEgxlNV2.jpg', 1, NULL, NULL),
(27, 24, 'products/51snkMEEjSIJskvmD1MNliVOvu2YYThgCRs1xVz7.jpg', 1, NULL, NULL),
(28, 25, 'products/JUH6cxsuyecn2Y5pmD3bkgSyMsggXtWAz0iPbJQo.jpg', 1, NULL, NULL),
(29, 26, 'products/yWTMMmiVv0X3thsyhgk0kddHbWdCUuPRGFKVKL6t.jpg', 1, NULL, NULL),
(30, 27, 'products/bJM9J5TZ1hezubNEizqJ4LFvg9szOfx8kD0TGtzK.jpg', 1, NULL, NULL),
(31, 28, 'products/Oy08A9FqFzfgMwVFoy0XH6pSY1eXqsrxNWs99EDD.jpg', 1, NULL, NULL),
(32, 29, 'products/kdqo38XJUVNk880aEeloDytg3paupnaaekZIn9sQ.jpg', 1, NULL, NULL),
(33, 30, 'products/Ek41WeuopEnwwvmZyn4Cu4tkKH1dRF6oUNldSVwh.jpg', 1, NULL, NULL),
(34, 31, 'products/lfWL8b7TYLekWp8LSXrMrHeJdYu49G0TkyLIXPsz.jpg', 1, NULL, NULL),
(35, 32, 'products/B9sDtn3CMbJQkmLjy4co2dfXRgrm6eVZOBhCimND.jpg', 1, NULL, NULL),
(36, 33, 'products/lbd5gq3O7TXK7tmkBysYdB0342DTWVs3wugBWOx9.jpg', 1, NULL, NULL),
(37, 34, 'products/XQq9JTFUqTiI7eSSGziXJJOze2HJM7F8uVZDM4Pf.jpg', 1, NULL, NULL),
(38, 35, 'products/9267sVfRdTwWKBbLWOPTw6uJXzyy6MfANmmyUXNH.jpg', 1, NULL, NULL),
(39, 36, 'products/7lzUJ8b0qMFQXvWhKEJDvhgtjGScM65oPGH4nt5l.jpg', 1, NULL, NULL),
(40, 37, 'products/bPzGMUXMhzhVK0cApsJTpyizw5OZekntnCmAPsuR.jpg', 1, NULL, NULL),
(41, 38, 'products/FKzN7evRKpVWCzWh4gLQ1ZsqfpVJC7XqL5RvrITp.jpg', 1, NULL, NULL),
(42, 39, 'products/9wpPYwHDKayHWHqUPGKy4p5yIvAtIrBQ0IOoGJdB.jpg', 1, NULL, NULL),
(43, 40, 'products/VZi3trHgqEcQCfsngELhzytW8DiYowNHNwTslAcI.jpg', 1, NULL, NULL),
(44, 41, 'products/XkRQ8DVNQdkzsl0obOTzzi0T8qJLaCrBE8WCg9Vn.jpg', 1, NULL, NULL),
(45, 41, 'products/6NLk6U6I8uxMhvOOXVfIcRRTquyXFNGgWUNt8ZCs.jpg', 0, NULL, NULL);

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
('BmES9Fp8b5tKQXYQuq2oLrA54mWeaJX3KJsJQc3z', 35, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Safari/605.1.15', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicnZtZkVaME53bm96Q0VSalBDSWZnMVpPeW5EVEJsSDZneWZYRG90eCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM1O30=', 1765418268);

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `created_at`, `updated_at`, `role`) VALUES
(35, 'Super Admin', 'admin@example.com', '2025-12-10 06:38:20', '$2y$12$kkL2UbI4FMzuaKZARwqm4OA4zXxTGjiPjQGolOHQ4rTPDhRChFDcO', '2025-12-10 06:38:20', '2025-12-10 06:38:20', 'admin'),
(36, 'Galih Rahwangga', 'galih@gmail.com', NULL, '$2y$12$H9CGssECcb7l93gsSSmItutXdVZ.w4T1xDCiLVItPDEk86eilVocS', '2025-12-10 00:29:13', '2025-12-10 00:29:13', 'user'),
(37, 'Verrel Juvenio', 'verrel@gmail.com', NULL, '$2y$12$2ht01SSilxmxgLbY7V0EMOR8lDDnp1qMAJAcoGIFtSeo7ZA4SFhk2', '2025-12-10 00:29:35', '2025-12-10 00:29:35', 'user'),
(38, 'Irvan Adi', 'irvan@gmail.com', NULL, '$2y$12$b.1nTYtJ1q1w35PrhEDBCefB32rWPmRi1kTp4UwfQ0XiZkg4TOOVG', '2025-12-10 00:29:56', '2025-12-10 00:29:56', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_code` (`code`),
  ADD KEY `idx_orders_status` (`payment_status`,`fulfillment_status`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `idx_products_active` (`is_active`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_images_product` (`product_id`);

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
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
