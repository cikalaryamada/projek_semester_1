-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 07:20 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `umkmk16`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `ID_Kategori` int(11) NOT NULL,
  `Nama_Kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`ID_Kategori`, `Nama_Kategori`) VALUES
(1, 'Makanan'),
(2, 'Minuman'),
(3, 'Camilan');

--
-- Triggers `kategori`
--
DELIMITER $$
CREATE TRIGGER `after_insert_kategori` AFTER INSERT ON `kategori` FOR EACH ROW BEGIN
    INSERT INTO produk (Nama_Produk, Harga, Stok, ID_Kategori, ID_Supplier)
    VALUES (CONCAT('Produk Baru - ', NEW.Nama_Kategori), 0, 0, NEW.ID_Kategori, 1);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `log_pelanggan`
--

CREATE TABLE `log_pelanggan` (
  `id_log` int(11) NOT NULL,
  `id_pelanggan` int(11) DEFAULT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `alamat_lama` varchar(100) DEFAULT NULL,
  `alamat_baru` varchar(100) DEFAULT NULL,
  `no_telp_lama` varchar(20) DEFAULT NULL,
  `no_telp_baru` varchar(20) DEFAULT NULL,
  `aksi` varchar(20) DEFAULT NULL,
  `waktu` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_pelanggan`
--

INSERT INTO `log_pelanggan` (`id_log`, `id_pelanggan`, `nama_pelanggan`, `alamat_lama`, `alamat_baru`, `no_telp_lama`, `no_telp_baru`, `aksi`, `waktu`) VALUES
(1, 1, 'Cikal', 'Kertosono', '', '082132384305', '', 'UPDATE', '2025-11-14 15:32:50'),
(2, 2, 'Dinda', 'Nganjuk', '', '081311246468', '', 'UPDATE', '2025-11-14 15:32:50'),
(3, 3, 'Arif', 'Nganjuk', '', '081528281611', '', 'UPDATE', '2025-11-14 15:32:50'),
(4, 4, 'Dimas', 'Nganjuk', '', '085733629903', '', 'UPDATE', '2025-11-14 15:32:50'),
(5, 5, 'Novia', 'Nganjuk', '', '082140243675', '', 'UPDATE', '2025-11-14 15:32:50'),
(6, 6, 'Arif cihuy', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-15 16:36:52'),
(7, 7, 'Novia Kalcer', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-17 08:12:18'),
(8, 8, 'Novia Kalcer ui', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-18 10:12:39'),
(9, 9, 'Dimas dimsum for lifeeee', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-18 10:26:22'),
(10, 10, 'Alan walker', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-18 12:58:27'),
(11, 11, 'aalan lomt', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-18 13:00:39');

-- --------------------------------------------------------

--
-- Table structure for table `log_transaksi`
--

CREATE TABLE `log_transaksi` (
  `id_log` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `stok_sebelum` int(11) DEFAULT NULL,
  `stok_sesudah` int(11) DEFAULT NULL,
  `aksi` varchar(20) DEFAULT NULL,
  `waktu` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_transaksi`
--

INSERT INTO `log_transaksi` (`id_log`, `id_transaksi`, `id_produk`, `stok_sebelum`, `stok_sesudah`, `aksi`, `waktu`) VALUES
(1, 6, 3, 100, 99, 'PENJUALAN', '2025-11-15 16:36:52'),
(2, 7, 3, 98, 97, 'PENJUALAN', '2025-11-15 16:51:33'),
(3, 8, 5, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(4, 9, 1, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(5, 10, 6, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(6, 11, 2, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(7, 12, 4, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(8, 13, 15, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(9, 14, 8, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(10, 15, 17, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(11, 16, 12, 100, 99, 'PENJUALAN', '2025-11-15 16:51:33'),
(12, 17, 28, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(13, 18, 22, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(14, 19, 11, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(15, 20, 10, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(16, 21, 14, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(17, 22, 13, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(18, 23, 16, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(19, 24, 7, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(20, 25, 27, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(21, 26, 20, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(22, 27, 26, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(23, 28, 19, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(24, 29, 24, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(25, 30, 21, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(26, 31, 18, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(27, 32, 9, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(28, 33, 23, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(29, 34, 25, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(30, 35, 34, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(31, 36, 31, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(32, 37, 29, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(33, 38, 35, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(34, 39, 30, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(35, 40, 33, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(36, 41, 38, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(37, 42, 36, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(38, 43, 37, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(39, 44, 32, 100, 99, 'PENJUALAN', '2025-11-15 16:51:34'),
(40, 45, 3, 96, 95, 'PENJUALAN', '2025-11-16 21:01:37'),
(41, 46, 3, 94, 92, 'PENJUALAN', '2025-11-16 21:04:36'),
(42, 47, 3, 90, 88, 'PENJUALAN', '2025-11-17 08:12:18'),
(43, 48, 3, 86, 84, 'PENJUALAN', '2025-11-17 09:23:53'),
(44, 49, 5, 98, 97, 'PENJUALAN', '2025-11-17 09:23:53'),
(45, 6, 3, 82, 83, 'HAPUS_TRANSAKSI', '2025-11-17 18:52:02'),
(46, 10, 6, 98, 99, 'HAPUS_TRANSAKSI', '2025-11-17 18:58:51'),
(47, 41, 38, 98, 99, 'HAPUS_TRANSAKSI', '2025-11-17 21:38:36'),
(48, 50, 37, 98, 97, 'PENJUALAN', '2025-11-18 07:57:57'),
(49, 51, 37, 96, 95, 'PENJUALAN', '2025-11-18 08:02:58'),
(50, 52, 2, 98, 97, 'PENJUALAN', '2025-11-18 08:02:58'),
(51, 53, 2, 96, 95, 'PENJUALAN', '2025-11-18 08:03:33'),
(52, 54, 34, 98, 97, 'PENJUALAN', '2025-11-18 08:30:09'),
(53, 55, 15, 98, 0, 'PENJUALAN', '2025-11-18 08:30:09'),
(54, 56, 8, 98, 97, 'PENJUALAN', '2025-11-18 08:30:34'),
(55, 57, 1, 98, 97, 'PENJUALAN', '2025-11-18 10:10:58'),
(56, 58, 1, 96, 95, 'PENJUALAN', '2025-11-18 10:12:05'),
(57, 59, 4, 98, 97, 'PENJUALAN', '2025-11-18 10:12:05'),
(58, 60, 4, 96, 95, 'PENJUALAN', '2025-11-18 10:12:39'),
(59, 61, 24, 98, 86, 'PENJUALAN', '2025-11-18 10:26:22'),
(60, 62, 25, 98, 96, 'PENJUALAN', '2025-11-18 10:32:30'),
(61, 63, 45, 100, 99, 'PENJUALAN', '2025-11-18 10:32:30'),
(62, 64, 4, 94, 93, 'PENJUALAN', '2025-11-18 12:58:27'),
(63, 65, 28, 98, 96, 'PENJUALAN', '2025-11-18 12:58:27'),
(64, 66, 45, 98, 96, 'PENJUALAN', '2025-11-18 13:00:39'),
(65, 67, 5, 96, 95, 'PENJUALAN', '2025-11-18 13:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_Pelanggan` int(11) NOT NULL,
  `Nama_Pelanggan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_Pelanggan`, `Nama_Pelanggan`) VALUES
(1, 'Cikal'),
(2, 'Dinda'),
(3, 'Arif'),
(4, 'Dimas'),
(5, 'Novia'),
(6, 'Arif cihuy'),
(7, 'Novia Kalcer'),
(8, 'Novia Kalcer ui'),
(9, 'Dimas dimsum for lifeeee'),
(10, 'Alan walker'),
(11, 'aalan lomt');

--
-- Triggers `pelanggan`
--
DELIMITER $$
CREATE TRIGGER `after_insert_pelanggan_simple` AFTER INSERT ON `pelanggan` FOR EACH ROW BEGIN
    INSERT INTO log_pelanggan (id_pelanggan, nama_pelanggan, aksi, waktu)
    VALUES (NEW.ID_Pelanggan, NEW.Nama_Pelanggan, 'INSERT', NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `penjual`
--

CREATE TABLE `penjual` (
  `ID_Penjual` int(11) NOT NULL,
  `Nama_Karyawan` varchar(100) NOT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_Telp` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjual`
--

INSERT INTO `penjual` (`ID_Penjual`, `Nama_Karyawan`, `Alamat`, `No_Telp`, `Email`) VALUES
(1, 'Akbar', 'Nganjuk', '087764196445', 'akbar@gmail.com'),
(2, 'Krisna', 'Nganjuk', '087887945416', 'krisna@gmail.com'),
(3, 'Ikhsan', 'Nganjuk', '085807251657', 'ikhsan@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `ID_Produk` int(11) NOT NULL,
  `Nama_Produk` varchar(100) NOT NULL,
  `Harga` decimal(10,2) NOT NULL,
  `Stok` int(11) DEFAULT 100,
  `Gambar` varchar(255) DEFAULT NULL,
  `ID_Kategori` int(11) NOT NULL,
  `ID_Supplier` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`ID_Produk`, `Nama_Produk`, `Harga`, `Stok`, `Gambar`, `ID_Kategori`, `ID_Supplier`) VALUES
(1, 'Ayam Penyet Sak Segone', 16000.00, 94, '691b13a5cdcd1_ayam_penyet_sak_segone.jpeg', 1, 1),
(2, 'Tempe Penyet Sak Segone', 12000.00, 94, '691b0a0f2dfbd_tempe_penyet_sak_segone.jpeg', 1, 1),
(3, 'Telur Penyet Sak Segone', 12000.00, 83, '691b09eeddf18_telur_penyet_sak_segone.jpeg', 1, 1),
(4, 'Pentol Penyet Sak Segone', 12000.00, 92, '691b13fde15c9_pentol_penyet_sak_segone.jpeg', 1, 1),
(5, 'Ayam Geprek Sak Segone', 12000.00, 94, '6917f7960b11f_ayam_geprek_sak_segone.png', 1, 1),
(7, 'Kopi Susu', 12000.00, 98, '6916f20d3ee1c_kopi_susu.png', 2, 2),
(8, 'Cappuccino', 12000.00, 96, '6916f265a2c40_cappuccino.png', 2, 2),
(9, 'Mochachino', 14000.00, 98, '6916f2a46cd4c_mochachino.png', 2, 2),
(10, 'Kopi Karamel', 14000.00, 98, '6916f32472c10_kopi_karamel.png', 2, 2),
(11, 'Kopi Hazelnut', 14000.00, 98, '6917f38934387_kopi_hazelnut.png', 2, 2),
(12, 'Kopi Aren', 14000.00, 98, '6917f336c59f6_kopi_aren.png', 2, 2),
(13, 'Kopi Vanilla', 14000.00, 98, '6917f4a5d19bc_kopi_vanilla.png', 2, 2),
(14, 'Kopi Pandan', 14000.00, 98, '6917f3f7cbf75_kopi_pandan.png', 2, 2),
(15, 'Americano', 10000.00, -98, '6917f5b8b9b86_americano.png', 2, 2),
(16, 'Kopi Tubruk', 6000.00, 98, '6917f428b647a_kopi_tubruk.png', 2, 2),
(17, 'Choco Almond', 14000.00, 98, '6917f192c5c08_choco_almond.png', 2, 2),
(18, 'Milky Chocolate', 13000.00, 98, '6917e81873681_milky_chocolate.png', 2, 2),
(19, 'Melon Squash', 10000.00, 98, '6916f68a6ee47_melon_squash.png', 2, 2),
(20, 'Lime Squash', 10000.00, 98, '6916f50f9ea26_lime_squash.png', 2, 2),
(21, 'Mango Squash', 10000.00, 98, '6916f638714ff_mango_squash.png', 2, 2),
(22, 'Grape Squash', 10000.00, 98, '6916f4caf000a_grape_squash.png', 2, 2),
(23, 'Red Velvet', 12000.00, 98, '6916f5d566753_red_velvet.png', 2, 2),
(24, 'Matcha', 12000.00, 74, '6916f0c14cc2c_matcha.png', 2, 2),
(25, 'Taro', 12000.00, 94, '6916f5ff29724_taro.png', 2, 2),
(26, 'Lychee Tea', 8000.00, 98, '6916f5a396fa3_lychee_tea.png', 2, 2),
(27, 'Lemon Tea', 8000.00, 98, '6916f52fd365d_lemon_tea.png', 2, 2),
(28, 'Jasmine Tea', 5000.00, 94, '6917f12e93233_jasmine_tea.png', 2, 2),
(29, 'Mix Platter (Kentang, Sosis, Nugget)', 12000.00, 98, '691b0f6866c53_mix_platter_(kentang,_sosis,_nugget).jpeg', 3, 3),
(30, 'Otak Otak', 10000.00, 98, '691b0fec516f2_otak_otak.jpeg', 3, 3),
(31, 'Kentang Goreng', 12000.00, 98, '691b0efb2b2db_kentang_goreng.jpeg', 3, 3),
(32, 'Sosis Goreng', 10000.00, 98, '691b1025e0046_sosis_goreng.jpeg', 3, 3),
(33, 'Nugget Goreng', 12000.00, 98, '691b10135232c_nugget_goreng.jpeg', 3, 3),
(34, 'Cireng', 10000.00, 96, '691bd09cc1933_cireng.jpeg', 3, 3),
(35, 'Pentol Goreng', 12000.00, 98, '691b132ef0cea_pentol_goreng.jpeg', 3, 3),
(36, 'Risol', 12000.00, 98, '691b0f223a63c_risol.jpeg', 3, 3),
(37, 'Roti Panggang (Coklat)', 8000.00, 94, '691b11e98df00_roti_panggang_(coklat).jpeg', 3, 3),
(44, 'Chicken Ricebowl (Blackpepper)', 15000.00, 100, '691bd1a405ed8_chicken_ricebowl_(blackpepper).jpeg', 1, 1),
(45, 'Chicken Ricebowl (Spicy Mayo)', 15000.00, 94, '691bd1d1c4aea_chicken_ricebowl_(spicy_mayo).jpeg', 1, 1),
(46, 'Roti Panggang (Keju)', 8000.00, 100, '691b123223fc5_roti_panggang_(keju).jpeg', 3, 3),
(48, 'ayam alan', 1222.00, 1, '691c0b452fdb6_ayam_alan.jpeg', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `ID_Supplier` int(11) NOT NULL,
  `Nama_Supplier` varchar(100) NOT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_Telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`ID_Supplier`, `Nama_Supplier`, `Alamat`, `No_Telp`) VALUES
(1, 'Pasar Nganjuk', 'Nganjuk', '081234567890'),
(2, 'Rumah Seduh', 'Surabaya', '081234567891'),
(3, 'Bima Sejahtera', 'Nganjuk', '081234567892');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_penjualan`
--

CREATE TABLE `transaksi_penjualan` (
  `ID_Transaksi_Penjualan` int(11) NOT NULL,
  `ID_Penjual` int(11) NOT NULL,
  `ID_Pelanggan` int(11) DEFAULT NULL,
  `ID_Produk` int(11) NOT NULL,
  `Tanggal_Transaksi` date NOT NULL,
  `Metode_Pembayaran` varchar(50) NOT NULL,
  `Jumlah_Barang` int(11) NOT NULL,
  `Total_Harga` decimal(12,2) NOT NULL,
  `Nomor_Meja` varchar(10) DEFAULT NULL,
  `order_status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `transfer_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_penjualan`
--

INSERT INTO `transaksi_penjualan` (`ID_Transaksi_Penjualan`, `ID_Penjual`, `ID_Pelanggan`, `ID_Produk`, `Tanggal_Transaksi`, `Metode_Pembayaran`, `Jumlah_Barang`, `Total_Harga`, `Nomor_Meja`, `order_status`, `transfer_proof`) VALUES
(1, 1, 1, 1, '2025-10-06', 'Cash', 2, 32000.00, 'A1', 'completed', NULL),
(2, 2, 2, 7, '2025-10-06', 'QRIS', 1, 12000.00, 'A2', 'completed', NULL),
(3, 3, 3, 29, '2025-10-06', 'Cash', 3, 36000.00, 'B1', 'completed', NULL),
(4, 2, 4, 25, '2025-10-06', 'Cash', 1, 12000.00, 'B2', 'completed', NULL),
(5, 1, 5, 33, '2025-10-06', 'Transfer', 2, 24000.00, 'C1', 'completed', NULL),
(7, 1, 1, 3, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(8, 1, 1, 5, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(9, 1, 1, 1, '2025-11-15', 'Cash', 1, 16000.00, 'VIP 1', 'completed', NULL),
(11, 1, 1, 2, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(12, 1, 1, 4, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(13, 1, 1, 15, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(14, 1, 1, 8, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(15, 1, 1, 17, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(16, 1, 1, 12, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(17, 1, 1, 28, '2025-11-15', 'Cash', 1, 5000.00, 'VIP 1', 'completed', NULL),
(18, 1, 1, 22, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(19, 1, 1, 11, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(20, 1, 1, 10, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(21, 1, 1, 14, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(22, 1, 1, 13, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(23, 1, 1, 16, '2025-11-15', 'Cash', 1, 6000.00, 'VIP 1', 'completed', NULL),
(24, 1, 1, 7, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(25, 1, 1, 27, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1', 'completed', NULL),
(26, 1, 1, 20, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(27, 1, 1, 26, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1', 'completed', NULL),
(28, 1, 1, 19, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(29, 1, 1, 24, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(30, 1, 1, 21, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(31, 1, 1, 18, '2025-11-15', 'Cash', 1, 13000.00, 'VIP 1', 'completed', NULL),
(32, 1, 1, 9, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1', 'completed', NULL),
(33, 1, 1, 23, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(34, 1, 1, 25, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(35, 1, 1, 34, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(36, 1, 1, 31, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(37, 1, 1, 29, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(38, 1, 1, 35, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(39, 1, 1, 30, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(40, 1, 1, 33, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(42, 1, 1, 36, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(43, 1, 1, 37, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1', 'completed', NULL),
(44, 1, 1, 32, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(45, 1, 1, 3, '2025-11-16', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(46, 1, 2, 3, '2025-11-16', 'Cash', 2, 24000.00, 'VIP 1', 'completed', NULL),
(47, 1, 7, 3, '2025-11-17', 'Cash', 2, 24000.00, 'VIP 1', 'completed', NULL),
(48, 1, 2, 3, '2025-11-17', 'Cash', 2, 24000.00, 'd', 'completed', NULL),
(49, 1, 2, 5, '2025-11-17', 'Cash', 1, 12000.00, 'd', 'completed', NULL),
(50, 1, 7, 37, '2025-11-18', 'Cash', 1, 8000.00, 'VIP 1', 'completed', NULL),
(51, 1, 7, 37, '2025-11-18', 'Cash', 1, 8000.00, 'VIP 1', 'completed', NULL),
(52, 1, 7, 2, '2025-11-18', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(53, 1, 7, 2, '2025-11-18', 'Transfer', 1, 12000.00, 'VIP 1', 'completed', NULL),
(54, 1, 2, 34, '2025-11-18', 'Cash', 1, 10000.00, 'VIP 1', 'completed', NULL),
(55, 1, 2, 15, '2025-11-18', 'Cash', 98, 980000.00, 'VIP 1', 'completed', NULL),
(56, 1, 2, 8, '2025-11-18', 'Transfer', 1, 12000.00, 'VIP 1', 'completed', NULL),
(57, 1, 2, 1, '2025-11-18', 'Cash', 1, 16000.00, 'VIP 1', 'completed', NULL),
(58, 1, 7, 1, '2025-11-18', 'Cash', 1, 16000.00, 'VIP 1', 'completed', NULL),
(59, 1, 7, 4, '2025-11-18', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(60, 1, 8, 4, '2025-11-18', 'Cash', 1, 12000.00, 'VIP 1', 'completed', NULL),
(61, 1, 9, 24, '2025-11-18', 'Cash', 12, 144000.00, 'VIP 1', 'completed', NULL),
(62, 1, 2, 25, '2025-11-18', 'Cash', 2, 24000.00, 'VIP 1', 'completed', NULL),
(63, 1, 2, 45, '2025-11-18', 'Cash', 1, 15000.00, 'VIP 1', 'completed', NULL),
(64, 1, 10, 4, '2025-11-18', 'Cash', 1, 12000.00, 'VIP 1', 'pending', NULL),
(65, 1, 10, 28, '2025-11-18', 'Cash', 2, 10000.00, 'VIP 1', 'pending', NULL),
(66, 1, 11, 45, '2025-11-18', 'Cash', 2, 30000.00, 'qqq', 'pending', NULL),
(67, 1, 7, 5, '2025-11-18', 'Cash', 1, 12000.00, 'VIP 1', 'pending', NULL);

--
-- Triggers `transaksi_penjualan`
--
DELIMITER $$
CREATE TRIGGER `after_insert_transaksi` AFTER INSERT ON `transaksi_penjualan` FOR EACH ROW BEGIN
    DECLARE stok_sekarang INT;
    
    -- Dapatkan stok saat ini
    SELECT Stok INTO stok_sekarang FROM produk WHERE ID_Produk = NEW.ID_Produk;
    
    -- Kurangi stok
    UPDATE produk 
    SET Stok = Stok - NEW.Jumlah_Barang 
    WHERE ID_Produk = NEW.ID_Produk;
    
    -- Log perubahan stok
    INSERT INTO log_transaksi (id_transaksi, id_produk, stok_sebelum, stok_sesudah, aksi, waktu)
    VALUES (NEW.ID_Transaksi_Penjualan, NEW.ID_Produk, stok_sekarang, (stok_sekarang - NEW.Jumlah_Barang), 'PENJUALAN', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_delete_transaksi` BEFORE DELETE ON `transaksi_penjualan` FOR EACH ROW BEGIN
    DECLARE stok_sekarang INT;
    
    -- Dapatkan stok saat ini
    SELECT Stok INTO stok_sekarang FROM produk WHERE ID_Produk = OLD.ID_Produk;
    
    -- Kembalikan stok
    UPDATE produk 
    SET Stok = Stok + OLD.Jumlah_Barang 
    WHERE ID_Produk = OLD.ID_Produk;
    
    -- Log perubahan stok
    INSERT INTO log_transaksi (id_transaksi, id_produk, stok_sebelum, stok_sesudah, aksi, waktu)
    VALUES (OLD.ID_Transaksi_Penjualan, OLD.ID_Produk, stok_sekarang, (stok_sekarang + OLD.Jumlah_Barang), 'HAPUS_TRANSAKSI', NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_transaksi` BEFORE UPDATE ON `transaksi_penjualan` FOR EACH ROW BEGIN
    DECLARE stok_sekarang INT;
    
    -- Jika jumlah barang berubah
    IF OLD.Jumlah_Barang != NEW.Jumlah_Barang THEN
        -- Dapatkan stok saat ini
        SELECT Stok INTO stok_sekarang FROM produk WHERE ID_Produk = OLD.ID_Produk;
        
        -- Kembalikan stok lama dan kurangi dengan stok baru
        UPDATE produk 
        SET Stok = (Stok + OLD.Jumlah_Barang) - NEW.Jumlah_Barang
        WHERE ID_Produk = OLD.ID_Produk;
        
        -- Log perubahan stok
        INSERT INTO log_transaksi (id_transaksi, id_produk, stok_sebelum, stok_sesudah, aksi, waktu)
        VALUES (NEW.ID_Transaksi_Penjualan, NEW.ID_Produk, stok_sekarang, ((stok_sekarang + OLD.Jumlah_Barang) - NEW.Jumlah_Barang), 'UPDATE_TRANSAKSI', NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ulasan`
--

CREATE TABLE `ulasan` (
  `ID_Ulasan` int(11) NOT NULL,
  `Nama_Pelanggan` varchar(100) NOT NULL DEFAULT 'Pelanggan',
  `Rating` int(11) NOT NULL CHECK (`Rating` between 1 and 5),
  `Judul_Ulasan` varchar(200) NOT NULL,
  `Isi_Ulasan` text NOT NULL,
  `Rekomendasi` enum('yes','no') NOT NULL,
  `Foto_Ulasan` varchar(255) DEFAULT NULL,
  `Tanggal_Ulasan` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`ID_Ulasan`, `Nama_Pelanggan`, `Rating`, `Judul_Ulasan`, `Isi_Ulasan`, `Rekomendasi`, `Foto_Ulasan`, `Tanggal_Ulasan`, `Status`) VALUES
(2, 'Cikal', 5, 'mntap', 'atsydjfglhgfjfsatjykdwafgh', 'yes', NULL, '2025-11-16 22:35:43', 'approved'),
(3, 'Cikal', 5, 'mntap', 'alkfhowuiegfuiqwh89gtyq2-89phgnpuieGFQ', 'yes', NULL, '2025-11-17 08:01:19', 'approved'),
(5, 'Cikal', 5, 'mntap', 'lhhwdfwujhfljkBIUDGQFYUAGHJFAOFYAPIG', 'yes', NULL, '2025-11-17 09:08:35', 'approved'),
(6, 'Cikal', 5, 'mntap', 'afdsdhgffjhgoihljkl;jhjhfgfxfdses', 'yes', NULL, '2025-11-17 12:09:55', 'approved'),
(7, 'Cikal', 5, 'mntap', 'w\';djewjklhfioewjvopiewjvpoe', 'yes', NULL, '2025-11-18 08:04:50', 'approved'),
(8, 'Cikal', 5, 'DAN YAPPP', 'WELLLLLLLLLLLLLLLLLLLLLLL', 'yes', NULL, '2025-11-18 08:11:39', 'approved'),
(9, 'Cikal', 5, 'mntap', 'ASDSDGFDHGFKULUYIRUD', 'yes', NULL, '2025-11-18 09:21:44', 'approved'),
(10, 'Cikal', 5, 'aku cikal', 'qWFERWYHTERJTRYSKYTKTUKTUKT', 'yes', NULL, '2025-11-18 09:24:45', 'approved'),
(11, 'Dinda', 5, 'DAN YAPPP CIhuy', 'reyrahetrhergawehgerj', 'yes', NULL, '2025-11-18 09:31:34', 'approved'),
(12, 'Cikal', 5, 'wefpwirhfiuewofuhewf', 'erhertyjersedhdfgesefw', 'yes', NULL, '2025-11-18 09:39:43', 'approved'),
(13, 'Cikal', 5, 'DAN YAPPPPPPPPPPPPPPPP', 'ARIFF KALLCERR ABIXZZZZZZ', 'yes', NULL, '2025-11-18 09:42:13', 'approved'),
(14, 'Dinda', 5, 'AKU KALCERRRRRR', 'NOPIA KALCERRRRRR', 'yes', NULL, '2025-11-18 09:44:39', 'approved'),
(15, 'Cikal', 5, 'wefpwirhfiuewofuhewf', 'wqeastjdzarhdfavasgfsndfmd', 'no', NULL, '2025-11-18 09:48:41', 'approved'),
(16, 'Cikal', 5, 'DAN YAPPP CIhuy', 'esrjdgdsfawr32ryersdhsdgsfg', 'yes', NULL, '2025-11-18 09:58:26', 'pending'),
(17, 'Cikal arya', 5, 'mntap', 'cafe keren dan kalcer', 'yes', NULL, '2025-11-18 10:33:35', 'approved'),
(18, 'Cikal', 4, 'mantapp', 'mantapppppppppppppppp', 'yes', NULL, '2025-11-18 10:35:15', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`ID_Kategori`);

--
-- Indexes for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `log_transaksi`
--
ALTER TABLE `log_transaksi`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_Pelanggan`);

--
-- Indexes for table `penjual`
--
ALTER TABLE `penjual`
  ADD PRIMARY KEY (`ID_Penjual`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`ID_Produk`),
  ADD KEY `ID_Kategori` (`ID_Kategori`),
  ADD KEY `ID_Supplier` (`ID_Supplier`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`ID_Supplier`);

--
-- Indexes for table `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  ADD PRIMARY KEY (`ID_Transaksi_Penjualan`),
  ADD KEY `ID_Penjual` (`ID_Penjual`),
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`),
  ADD KEY `ID_Produk` (`ID_Produk`);

--
-- Indexes for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD PRIMARY KEY (`ID_Ulasan`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_tanggal` (`Tanggal_Ulasan`),
  ADD KEY `idx_rating` (`Rating`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `ID_Kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `log_pelanggan`
--
ALTER TABLE `log_pelanggan`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `log_transaksi`
--
ALTER TABLE `log_transaksi`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `ID_Pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `penjual`
--
ALTER TABLE `penjual`
  MODIFY `ID_Penjual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `ID_Produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `ID_Supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  MODIFY `ID_Transaksi_Penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `ID_Ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`ID_Kategori`) REFERENCES `kategori` (`ID_Kategori`),
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`ID_Supplier`) REFERENCES `supplier` (`ID_Supplier`);

--
-- Constraints for table `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  ADD CONSTRAINT `transaksi_penjualan_ibfk_1` FOREIGN KEY (`ID_Penjual`) REFERENCES `penjual` (`ID_Penjual`),
  ADD CONSTRAINT `transaksi_penjualan_ibfk_2` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_penjualan_ibfk_3` FOREIGN KEY (`ID_Produk`) REFERENCES `produk` (`ID_Produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
