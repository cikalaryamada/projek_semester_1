-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 10:24 AM
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
  `Nama_Kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`ID_Kategori`, `Nama_Kategori`) VALUES
(1, 'Makanan'),
(2, 'Minuman');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_Pelanggan` int(11) NOT NULL,
  `Nama_Pelanggan` varchar(100) DEFAULT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_Telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_Pelanggan`, `Nama_Pelanggan`, `Alamat`, `No_Telp`) VALUES
(1, 'Cikal', NULL, '082132384305'),
(2, 'Dinda', NULL, '081311246468'),
(3, 'Arif', NULL, '081528281611'),
(4, 'Dimas', NULL, '085733629903'),
(5, 'Novia', NULL, '082140243675');

-- --------------------------------------------------------

--
-- Table structure for table `penjual`
--

CREATE TABLE `penjual` (
  `ID_Penjual` int(11) NOT NULL,
  `Nama_Karyawan` varchar(100) DEFAULT NULL,
  `Alamat` varchar(100) DEFAULT NULL,
  `No_Telp` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjual`
--

INSERT INTO `penjual` (`ID_Penjual`, `Nama_Karyawan`, `Alamat`, `No_Telp`, `Email`) VALUES
(1, 'Akbar', 'Nganjuk', '087764196445', NULL),
(2, 'Krisna', 'Nganjuk', '087887945416', NULL),
(3, 'Ikhsan', 'Nganjuk', '085807251657', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `ID_Produk` int(11) NOT NULL,
  `Nama_Produk` varchar(100) DEFAULT NULL,
  `Harga` decimal(10,2) DEFAULT NULL,
  `Stok` int(11) DEFAULT NULL,
  `ID_Kategori` int(11) DEFAULT NULL,
  `ID_Supplier` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`ID_Produk`, `Nama_Produk`, `Harga`, `Stok`, `ID_Kategori`, `ID_Supplier`) VALUES
(1, 'Kopi Susu', 10000.00, 100, 2, 1),
(2, 'Cappuccino', 10000.00, 100, 2, 1),
(3, 'Mochachino', 10000.00, 100, 2, 1),
(4, 'Kopi Karamel', 10000.00, 100, 2, 1),
(5, 'Kopi Hazelnut', 10000.00, 100, 2, 1),
(6, 'Kopi Aren', 10000.00, 100, 2, 1),
(7, 'Kopi Vanila', 10000.00, 100, 2, 1),
(8, 'Americano', 10000.00, 100, 2, 1),
(9, 'Kopi Tubruk', 6000.00, 100, 2, 1),
(10, 'Cookies n Cream', 10000.00, 100, 2, 1),
(11, 'Choco Almond', 10000.00, 100, 2, 1),
(12, 'Milky Chocolate', 10000.00, 100, 2, 1),
(13, 'Melon Squash', 8000.00, 100, 2, 1),
(14, 'Lime Squash', 8000.00, 100, 2, 1),
(15, 'Red Velvet', 10000.00, 100, 2, 1),
(16, 'Matcha', 10000.00, 100, 2, 1),
(17, 'Taro', 8000.00, 100, 2, 1),
(18, 'Lychee Tea', 7000.00, 100, 2, 1),
(19, 'Lemon Tea', 7000.00, 100, 2, 1),
(20, 'Jasmine Tea', 7000.00, 100, 2, 1),
(21, 'Mix Platter (Kentang, Sosis, Nugget)', 12000.00, 50, 1, 2),
(22, 'Pisang Nugget', 10000.00, 50, 1, 2),
(23, 'Kentang Goreng', 10000.00, 50, 1, 2),
(24, 'Sosis Goreng', 10000.00, 50, 1, 2),
(25, 'Nugget Goreng', 10000.00, 50, 1, 2),
(26, 'Cireng', 10000.00, 50, 1, 2),
(27, 'Pentol Goreng', 10000.00, 50, 1, 2),
(28, 'Roti Panggang (Coklat/Keju)', 6000.00, 50, 1, 2),
(29, 'Ayam Penyet Sak Segone', 15000.00, 50, 1, 2),
(30, 'Lele Penyet Sak Segone', 15000.00, 50, 1, 2),
(31, 'Tempe Penyet Sak Segone', 12000.00, 50, 1, 2),
(32, 'Telur Penyet Sak Segone', 12000.00, 50, 1, 2),
(33, 'Pentol Penyet Sak Segone', 12000.00, 50, 1, 2),
(34, 'Ayam Geprek Sak Segone', 12000.00, 50, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `ID_Supplier` int(11) NOT NULL,
  `Nama_Supplier` varchar(100) DEFAULT NULL,
  `Alamat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`ID_Supplier`, `Nama_Supplier`, `Alamat`) VALUES
(1, 'PT Minuman', 'Nganjuk'),
(2, 'PT Makanan', 'Nganjuk');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_penjualan`
--

CREATE TABLE `transaksi_penjualan` (
  `ID_Transaksi_Penjualan` int(11) NOT NULL,
  `ID_Penjual` int(11) DEFAULT NULL,
  `ID_Pelanggan` int(11) DEFAULT NULL,
  `ID_Produk` int(11) DEFAULT NULL,
  `Tanggal_Transaksi` date DEFAULT NULL,
  `Metode_Pembayaran` varchar(50) DEFAULT NULL,
  `Jumlah_Barang` int(11) DEFAULT NULL,
  `Total_Harga` decimal(12,2) DEFAULT NULL,
  `Nomor_Meja` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_penjualan`
--

INSERT INTO `transaksi_penjualan` (`ID_Transaksi_Penjualan`, `ID_Penjual`, `ID_Pelanggan`, `ID_Produk`, `Tanggal_Transaksi`, `Metode_Pembayaran`, `Jumlah_Barang`, `Total_Harga`, `Nomor_Meja`) VALUES
(1, 1, 1, 1, '2025-10-06', 'Cash', 2, 20000.00, NULL),
(2, 2, 2, 22, '2025-10-06', 'QRIS', 1, 10000.00, NULL),
(3, 3, 3, 15, '2025-10-06', 'Cash', 3, 30000.00, NULL),
(4, 2, 4, 19, '2025-10-06', 'Cash', 1, 7000.00, NULL),
(5, 1, 5, 27, '2025-10-06', 'Transfer', 2, 20000.00, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`ID_Kategori`);

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
  ADD CONSTRAINT `transaksi_penjualan_ibfk_2` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`),
  ADD CONSTRAINT `transaksi_penjualan_ibfk_3` FOREIGN KEY (`ID_Produk`) REFERENCES `produk` (`ID_Produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
