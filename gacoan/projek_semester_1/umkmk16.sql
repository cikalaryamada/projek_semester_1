-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 07:07 AM
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
(7, 7, 'Novia Kalcer', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-17 08:12:18');

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
(44, 49, 5, 98, 97, 'PENJUALAN', '2025-11-17 09:23:53');

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
(7, 'Novia Kalcer');

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
(1, 'Ayam Penyet Sak Segone', 16000.00, 98, NULL, 1, 1),
(2, 'Tempe Penyet Sak Segone', 12000.00, 98, NULL, 1, 1),
(3, 'Telur Penyet Sak Segone', 12000.00, 82, NULL, 1, 1),
(4, 'Pentol Penyet Sak Segone', 12000.00, 98, NULL, 1, 1),
(5, 'Ayam Geprek Sak Segone', 12000.00, 96, '6917f7960b11f_ayam_geprek_sak_segone.png', 1, 1),
(6, 'Chicken Ricebowl (Blackpepper / Spicy Mayo)', 15000.00, 98, NULL, 1, 1),
(7, 'Kopi Susu', 12000.00, 98, '6916f20d3ee1c_kopi_susu.png', 2, 2),
(8, 'Cappuccino', 12000.00, 98, '6916f265a2c40_cappuccino.png', 2, 2),
(9, 'Mochachino', 14000.00, 98, '6916f2a46cd4c_mochachino.png', 2, 2),
(10, 'Kopi Karamel', 14000.00, 98, '6916f32472c10_kopi_karamel.png', 2, 2),
(11, 'Kopi Hazelnut', 14000.00, 98, '6917f38934387_kopi_hazelnut.png', 2, 2),
(12, 'Kopi Aren', 14000.00, 98, '6917f336c59f6_kopi_aren.png', 2, 2),
(13, 'Kopi Vanilla', 14000.00, 98, '6917f4a5d19bc_kopi_vanilla.png', 2, 2),
(14, 'Kopi Pandan', 14000.00, 98, '6917f3f7cbf75_kopi_pandan.png', 2, 2),
(15, 'Americano', 10000.00, 98, '6917f5b8b9b86_americano.png', 2, 2),
(16, 'Kopi Tubruk', 6000.00, 98, '6917f428b647a_kopi_tubruk.png', 2, 2),
(17, 'Choco Almond', 14000.00, 98, '6917f192c5c08_choco_almond.png', 2, 2),
(18, 'Milky Chocolate', 13000.00, 98, '6917e81873681_milky_chocolate.png', 2, 2),
(19, 'Melon Squash', 10000.00, 98, '6916f68a6ee47_melon_squash.png', 2, 2),
(20, 'Lime Squash', 10000.00, 98, '6916f50f9ea26_lime_squash.png', 2, 2),
(21, 'Mango Squash', 10000.00, 98, '6916f638714ff_mango_squash.png', 2, 2),
(22, 'Grape Squash', 10000.00, 98, '6916f4caf000a_grape_squash.png', 2, 2),
(23, 'Red Velvet', 12000.00, 98, '6916f5d566753_red_velvet.png', 2, 2),
(24, 'Matcha', 12000.00, 98, '6916f0c14cc2c_matcha.png', 2, 2),
(25, 'Taro', 12000.00, 98, '6916f5ff29724_taro.png', 2, 2),
(26, 'Lychee Tea', 8000.00, 98, '6916f5a396fa3_lychee_tea.png', 2, 2),
(27, 'Lemon Tea', 8000.00, 98, '6916f52fd365d_lemon_tea.png', 2, 2),
(28, 'Jasmine Tea', 5000.00, 98, '6917f12e93233_jasmine_tea.png', 2, 2),
(29, 'Mix Platter (Kentang, Sosis, Nugget)', 12000.00, 98, NULL, 3, 3),
(30, 'Otak Otak', 10000.00, 98, NULL, 3, 3),
(31, 'Kentang Goreng', 12000.00, 98, NULL, 3, 3),
(32, 'Sosis Goreng', 10000.00, 98, NULL, 3, 3),
(33, 'Nugget Goreng', 12000.00, 98, NULL, 3, 3),
(34, 'Cireng', 10000.00, 98, NULL, 3, 3),
(35, 'Pentol Goreng', 12000.00, 98, NULL, 3, 3),
(36, 'Risol', 12000.00, 98, NULL, 3, 3),
(37, 'Roti Panggang (Coklat / Keju)', 8000.00, 98, NULL, 3, 3),
(38, 'Pisang Goreng', 8000.00, 98, NULL, 3, 3);

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
  `Nomor_Meja` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_penjualan`
--

INSERT INTO `transaksi_penjualan` (`ID_Transaksi_Penjualan`, `ID_Penjual`, `ID_Pelanggan`, `ID_Produk`, `Tanggal_Transaksi`, `Metode_Pembayaran`, `Jumlah_Barang`, `Total_Harga`, `Nomor_Meja`) VALUES
(1, 1, 1, 1, '2025-10-06', 'Cash', 2, 32000.00, 'A1'),
(2, 2, 2, 7, '2025-10-06', 'QRIS', 1, 12000.00, 'A2'),
(3, 3, 3, 29, '2025-10-06', 'Cash', 3, 36000.00, 'B1'),
(4, 2, 4, 25, '2025-10-06', 'Cash', 1, 12000.00, 'B2'),
(5, 1, 5, 33, '2025-10-06', 'Transfer', 2, 24000.00, 'C1'),
(6, 1, 6, 3, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(7, 1, 1, 3, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(8, 1, 1, 5, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(9, 1, 1, 1, '2025-11-15', 'Cash', 1, 16000.00, 'VIP 1'),
(10, 1, 1, 6, '2025-11-15', 'Cash', 1, 15000.00, 'VIP 1'),
(11, 1, 1, 2, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(12, 1, 1, 4, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(13, 1, 1, 15, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(14, 1, 1, 8, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(15, 1, 1, 17, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(16, 1, 1, 12, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(17, 1, 1, 28, '2025-11-15', 'Cash', 1, 5000.00, 'VIP 1'),
(18, 1, 1, 22, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(19, 1, 1, 11, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(20, 1, 1, 10, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(21, 1, 1, 14, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(22, 1, 1, 13, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(23, 1, 1, 16, '2025-11-15', 'Cash', 1, 6000.00, 'VIP 1'),
(24, 1, 1, 7, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(25, 1, 1, 27, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1'),
(26, 1, 1, 20, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(27, 1, 1, 26, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1'),
(28, 1, 1, 19, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(29, 1, 1, 24, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(30, 1, 1, 21, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(31, 1, 1, 18, '2025-11-15', 'Cash', 1, 13000.00, 'VIP 1'),
(32, 1, 1, 9, '2025-11-15', 'Cash', 1, 14000.00, 'VIP 1'),
(33, 1, 1, 23, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(34, 1, 1, 25, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(35, 1, 1, 34, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(36, 1, 1, 31, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(37, 1, 1, 29, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(38, 1, 1, 35, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(39, 1, 1, 30, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(40, 1, 1, 33, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(41, 1, 1, 38, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1'),
(42, 1, 1, 36, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1'),
(43, 1, 1, 37, '2025-11-15', 'Cash', 1, 8000.00, 'VIP 1'),
(44, 1, 1, 32, '2025-11-15', 'Cash', 1, 10000.00, 'VIP 1'),
(45, 1, 1, 3, '2025-11-16', 'Cash', 1, 12000.00, 'VIP 1'),
(46, 1, 2, 3, '2025-11-16', 'Cash', 2, 24000.00, 'VIP 1'),
(47, 1, 7, 3, '2025-11-17', 'Cash', 2, 24000.00, 'VIP 1'),
(48, 1, 2, 3, '2025-11-17', 'Cash', 2, 24000.00, 'd'),
(49, 1, 2, 5, '2025-11-17', 'Cash', 1, 12000.00, 'd');

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
  `Nama_Pelanggan` varchar(100) NOT NULL,
  `ID_Pelanggan` int(11) DEFAULT NULL,
  `ID_Produk` int(11) DEFAULT NULL,
  `Rating` int(11) NOT NULL CHECK (`Rating` between 1 and 5),
  `Judul_Ulasan` varchar(200) NOT NULL,
  `Isi_Ulasan` text NOT NULL,
  `Rekomendasi` enum('yes','no') NOT NULL,
  `Tanggal_Ulasan` datetime DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ulasan`
--

INSERT INTO `ulasan` (`ID_Ulasan`, `Nama_Pelanggan`, `ID_Pelanggan`, `ID_Produk`, `Rating`, `Judul_Ulasan`, `Isi_Ulasan`, `Rekomendasi`, `Tanggal_Ulasan`, `Status`) VALUES
(1, 'Dinda', NULL, NULL, 5, 'mntap', 'asadsfghgfdsAsdfksghszarjkra', 'yes', '2025-11-16 21:27:54', 'approved'),
(2, 'Cikal', NULL, NULL, 5, 'mntap', 'atsydjfglhgfjfsatjykdwafgh', 'yes', '2025-11-16 22:35:43', 'approved'),
(3, 'Cikal', NULL, NULL, 5, 'mntap', 'alkfhowuiegfuiqwh89gtyq2-89phgnpuieGFQ', 'yes', '2025-11-17 08:01:19', 'approved'),
(4, 'test', NULL, NULL, 5, 'ya', '1234567890', 'yes', '2025-11-17 09:01:43', 'approved'),
(5, 'Cikal', NULL, NULL, 5, 'mntap', 'lhhwdfwujhfljkBIUDGQFYUAGHJFAOFYAPIG', 'yes', '2025-11-17 09:08:35', 'approved'),
(6, 'Cikal', NULL, NULL, 5, 'mntap', 'afdsdhgffjhgoihljkl;jhjhfgfxfdses', 'yes', '2025-11-17 12:09:55', 'approved');

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
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`),
  ADD KEY `ID_Produk` (`ID_Produk`),
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
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `log_transaksi`
--
ALTER TABLE `log_transaksi`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `ID_Pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penjual`
--
ALTER TABLE `penjual`
  MODIFY `ID_Penjual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `ID_Produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `ID_Supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  MODIFY `ID_Transaksi_Penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `ulasan`
--
ALTER TABLE `ulasan`
  MODIFY `ID_Ulasan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

--
-- Constraints for table `ulasan`
--
ALTER TABLE `ulasan`
  ADD CONSTRAINT `ulasan_ibfk_1` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`) ON DELETE SET NULL,
  ADD CONSTRAINT `ulasan_ibfk_2` FOREIGN KEY (`ID_Produk`) REFERENCES `produk` (`ID_Produk`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
