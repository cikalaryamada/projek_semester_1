-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 10:44 AM
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
(6, 6, 'Arif cihuy', NULL, NULL, NULL, NULL, 'INSERT', '2025-11-15 16:36:52');

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
(1, 6, 3, 100, 99, 'PENJUALAN', '2025-11-15 16:36:52');

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
(6, 'Arif cihuy');

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
(1, 'Ayam Penyet Sak Segone', 16000.00, 100, NULL, 1, 1),
(2, 'Tempe Penyet Sak Segone', 12000.00, 100, NULL, 1, 1),
(3, 'Telur Penyet Sak Segone', 12000.00, 98, NULL, 1, 1),
(4, 'Pentol Penyet Sak Segone', 12000.00, 100, NULL, 1, 1),
(5, 'Ayam Geprek Sak Segone', 12000.00, 100, '6917f7960b11f_ayam_geprek_sak_segone.png', 1, 1),
(6, 'Chicken Ricebowl (Blackpepper / Spicy Mayo)', 15000.00, 100, NULL, 1, 1),
(7, 'Kopi Susu', 12000.00, 100, '6916f20d3ee1c_kopi_susu.png', 2, 2),
(8, 'Cappuccino', 12000.00, 100, '6916f265a2c40_cappuccino.png', 2, 2),
(9, 'Mochachino', 14000.00, 100, '6916f2a46cd4c_mochachino.png', 2, 2),
(10, 'Kopi Karamel', 14000.00, 100, '6916f32472c10_kopi_karamel.png', 2, 2),
(11, 'Kopi Hazelnut', 14000.00, 100, '6917f38934387_kopi_hazelnut.png', 2, 2),
(12, 'Kopi Aren', 14000.00, 100, '6917f336c59f6_kopi_aren.png', 2, 2),
(13, 'Kopi Vanilla', 14000.00, 100, '6917f4a5d19bc_kopi_vanilla.png', 2, 2),
(14, 'Kopi Pandan', 14000.00, 100, '6917f3f7cbf75_kopi_pandan.png', 2, 2),
(15, 'Americano', 10000.00, 100, '6917f5b8b9b86_americano.png', 2, 2),
(16, 'Kopi Tubruk', 6000.00, 100, '6917f428b647a_kopi_tubruk.png', 2, 2),
(17, 'Choco Almond', 14000.00, 100, '6917f192c5c08_choco_almond.png', 2, 2),
(18, 'Milky Chocolate', 13000.00, 100, '6917e81873681_milky_chocolate.png', 2, 2),
(19, 'Melon Squash', 10000.00, 100, '6916f68a6ee47_melon_squash.png', 2, 2),
(20, 'Lime Squash', 10000.00, 100, '6916f50f9ea26_lime_squash.png', 2, 2),
(21, 'Mango Squash', 10000.00, 100, '6916f638714ff_mango_squash.png', 2, 2),
(22, 'Grape Squash', 10000.00, 100, '6916f4caf000a_grape_squash.png', 2, 2),
(23, 'Red Velvet', 12000.00, 100, '6916f5d566753_red_velvet.png', 2, 2),
(24, 'Matcha', 12000.00, 100, '6916f0c14cc2c_matcha.png', 2, 2),
(25, 'Taro', 12000.00, 100, '6916f5ff29724_taro.png', 2, 2),
(26, 'Lychee Tea', 8000.00, 100, '6916f5a396fa3_lychee_tea.png', 2, 2),
(27, 'Lemon Tea', 8000.00, 100, '6916f52fd365d_lemon_tea.png', 2, 2),
(28, 'Jasmine Tea', 5000.00, 100, '6917f12e93233_jasmine_tea.png', 2, 2),
(29, 'Mix Platter (Kentang, Sosis, Nugget)', 12000.00, 100, NULL, 3, 3),
(30, 'Otak Otak', 10000.00, 100, NULL, 3, 3),
(31, 'Kentang Goreng', 12000.00, 100, NULL, 3, 3),
(32, 'Sosis Goreng', 10000.00, 100, NULL, 3, 3),
(33, 'Nugget Goreng', 12000.00, 100, NULL, 3, 3),
(34, 'Cireng', 10000.00, 100, NULL, 3, 3),
(35, 'Pentol Goreng', 12000.00, 100, NULL, 3, 3),
(36, 'Risol', 12000.00, 100, NULL, 3, 3),
(37, 'Roti Panggang (Coklat / Keju)', 8000.00, 100, NULL, 3, 3),
(38, 'Pisang Goreng', 8000.00, 100, NULL, 3, 3);

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
(6, 1, 6, 3, '2025-11-15', 'Cash', 1, 12000.00, 'VIP 1');

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
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_transaksi`
--
ALTER TABLE `log_transaksi`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `ID_Pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `ID_Transaksi_Penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
