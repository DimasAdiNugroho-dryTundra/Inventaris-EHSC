-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2025 at 01:23 PM
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
-- Database: `inventaris_ehsc_rev6`
--

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` int(11) NOT NULL,
  `kode_departemen` varchar(20) NOT NULL,
  `nama_departemen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `kode_departemen`, `nama_departemen`) VALUES
(1, 'EHSC', ' Environment, Health, Safety, and Conservation');

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id_inventaris` int(11) NOT NULL,
  `kode_inventaris` varchar(255) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `id_penerimaan` int(11) DEFAULT NULL,
  `id_departemen` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `jumlah_awal` int(11) NOT NULL,
  `jumlah_akhir` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `sumber_inventaris` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id_inventaris`, `kode_inventaris`, `nama_barang`, `merk`, `id_penerimaan`, `id_departemen`, `id_ruangan`, `id_kategori`, `tanggal_perolehan`, `jumlah_awal`, `jumlah_akhir`, `satuan`, `sumber_inventaris`) VALUES
(1, 'EHSC/RK/FUR/2024/001', 'Meja Karyawan', 'Olympic', 1, 1, 1, 1, '2024-01-02', 5, 4, 'buah', 'Permintaan'),
(2, 'EHSC/RK/TI/2024/001', 'Printer', 'Canon Pixma Pro 1000', 2, 1, 1, 2, '2024-02-02', 1, 1, 'unit', 'Permintaan'),
(3, 'EHSC/RA/FUR/2024/002', 'Lemari Dokumen', 'Tanpa Merk - Buatan tukang', 3, 1, 3, 1, '2024-02-03', 2, 2, 'unit', 'Pengadaan Kantor'),
(4, 'EHSC/RK/TI/2024/002', 'Printer', 'Epson L3210', 4, 1, 1, 2, '2024-03-02', 1, 0, 'unit', 'Permintaan'),
(5, 'EHSC/RK/LAIN/2024/001', 'AC', 'Sharp', 5, 1, 1, 10, '2024-04-05', 2, 1, 'unit', 'Permintaan'),
(6, 'EHSC/RK/KOM/2024/001', 'Monitor', 'Toshiba VA2249SDVI', 6, 1, 1, 6, '2024-04-30', 6, 3, 'unit', 'Permintaan'),
(7, 'EHSC/RK/LAIN/2024/002', 'Jam dinding', 'Seiko', 7, 1, 1, 10, '2024-04-30', 1, 0, 'buah', 'Pengadaan Kantor'),
(8, 'EHSC/RM/ATK/2024/001', 'Papan tulis', 'Golden House', 8, 1, 2, 4, '2024-05-01', 1, 1, 'buah', 'Permintaan'),
(9, 'EHSC/RM/TI/2024/003', 'Layar Proyektor', 'Le Taec', 9, 1, 2, 2, '2024-06-06', 1, 1, 'unit', 'Permintaan'),
(10, 'EHSC/RP/TOOL/2024/001', 'Meteran 3 meter', 'Krisbow', 10, 1, 4, 3, '2024-06-19', 1, 0, 'buah', 'Pengadaan Kantor'),
(11, 'EHSC/RK/FUR/2024/003', 'Meja Dispenser', 'Tanpa Merk - Buatan tukan', 11, 1, 1, 1, '2024-07-01', 1, 1, 'buah', 'Permintaan'),
(12, 'EHSC/RM/FUR/2024/004', 'Kursi Lipat', 'Chitose', 12, 1, 2, 1, '2024-08-08', 11, 10, 'buah', 'Permintaan'),
(13, 'EHSC/RS/KOM/2024/002', 'CPU Server', 'Lenovo Thinksystem St50 V', 13, 1, 12, 6, '2024-09-10', 1, 1, 'unit', 'Permintaan'),
(14, 'EHSC/RP/VISUAL/2024/001', 'Binokuler', 'Nikon Action EX 10x50 CF', 14, 1, 4, 8, '2024-09-24', 1, 0, 'unit', 'Permintaan'),
(15, 'EHSC/RP/TEST/2024/001', 'PH meter digital', 'Tranalab', 15, 1, 4, 5, '2024-10-23', 1, 1, 'unit', 'Pengadaan Kantor'),
(16, 'EHSC/RK/LAIN/2024/003', 'Dispenser', 'Miyako', 16, 1, 1, 10, '2024-10-22', 1, 1, 'buah', 'Permintaan'),
(17, 'EHSC/RM/TI/2024/004', 'Proyektor', 'Epson EB-972', 17, 1, 2, 2, '2024-11-13', 1, 1, 'unit', 'Permintaan'),
(18, 'EHSC/RK/TI/2024/005', 'Scanner', 'Plustek SmartOffice PS396', 18, 1, 1, 2, '2024-12-16', 1, 1, 'unit', 'Permintaan'),
(19, 'EHSC/RP/DOK/2024/001', 'Kamera digital', 'NIKON COOLPIX B500', 19, 1, 4, 9, '2024-12-31', 1, 1, 'unit', 'Permintaan'),
(21, 'EHSC/FUR/RA/2024/001', 'Meja Karyawan', 'Olympic', NULL, 1, 3, 1, '2024-01-02', 1, 1, 'buah', 'Pindah Barang');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `kode_kategori` varchar(10) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `kode_kategori`, `nama_kategori`) VALUES
(1, 'FUR', 'Furnitur'),
(2, 'TI', 'Perangkat Elektronik'),
(3, 'TOOL', 'Peralatan Tukang'),
(4, 'ATK', 'Alat Tulis Kantor'),
(5, 'TEST', 'Alat Uji'),
(6, 'KOM', 'Komputer'),
(7, 'AUDIO', 'Peralatan Audio'),
(8, 'VISUAL', 'Peralatan Visual'),
(9, 'DOK', 'Dokumentasi'),
(10, 'LAIN', 'Lain-lain');

-- --------------------------------------------------------

--
-- Table structure for table `kehilangan_barang`
--

CREATE TABLE `kehilangan_barang` (
  `id_kehilangan_barang` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_kehilangan` date NOT NULL,
  `cawu` varchar(255) NOT NULL,
  `jumlah_kehilangan` int(11) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kehilangan_barang`
--

INSERT INTO `kehilangan_barang` (`id_kehilangan_barang`, `id_inventaris`, `tanggal_kehilangan`, `cawu`, `jumlah_kehilangan`, `keterangan`) VALUES
(1, 10, '2024-08-31', 'Caturwulan 2', 1, 'Hilang saat dilakukan kontrol barang.');

-- --------------------------------------------------------

--
-- Table structure for table `kerusakan_barang`
--

CREATE TABLE `kerusakan_barang` (
  `id_kerusakan_barang` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_kerusakan` date NOT NULL,
  `cawu` varchar(255) NOT NULL,
  `jumlah_kerusakan` int(11) NOT NULL,
  `foto_kerusakan` varchar(255) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kerusakan_barang`
--

INSERT INTO `kerusakan_barang` (`id_kerusakan_barang`, `id_inventaris`, `tanggal_kerusakan`, `cawu`, `jumlah_kerusakan`, `foto_kerusakan`, `keterangan`) VALUES
(2, 6, '2024-08-31', 'Caturwulan 2', 1, '1735025715_monitor_lg_22_inch_layar_pecah_1678188167_116ac6de.jpg', 'Layar monitor pecah sehingga tidak dapat digunakan lagi.'),
(3, 4, '2024-12-31', 'Caturwulan 3', 1, '1735025788_head-printer.jpg', 'Tinta warna tidak mau keluar.');

-- --------------------------------------------------------

--
-- Table structure for table `kontrol_barang_cawu_dua`
--

CREATE TABLE `kontrol_barang_cawu_dua` (
  `id_kontrol_barang_cawu_dua` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_kontrol` date NOT NULL,
  `tahun_kontrol` int(11) NOT NULL,
  `jumlah_baik` int(11) NOT NULL,
  `jumlah_rusak` int(11) NOT NULL,
  `jumlah_pindah` int(11) NOT NULL,
  `jumlah_hilang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrol_barang_cawu_dua`
--

INSERT INTO `kontrol_barang_cawu_dua` (`id_kontrol_barang_cawu_dua`, `id_user`, `id_inventaris`, `tanggal_kontrol`, `tahun_kontrol`, `jumlah_baik`, `jumlah_rusak`, `jumlah_pindah`, `jumlah_hilang`) VALUES
(1, 4, 1, '2024-08-31', 2024, 4, 0, 1, 0),
(2, 4, 2, '2024-08-31', 2024, 1, 0, 0, 0),
(3, 4, 3, '2024-08-31', 2024, 2, 0, 0, 0),
(4, 4, 4, '2024-08-31', 2024, 1, 0, 0, 0),
(5, 4, 5, '2024-08-31', 2024, 2, 0, 0, 0),
(6, 4, 6, '2024-08-31', 2024, 5, 1, 0, 0),
(7, 4, 7, '2024-08-31', 2024, 1, 0, 0, 0),
(8, 4, 8, '2024-08-31', 2024, 1, 0, 0, 0),
(9, 4, 9, '2024-08-31', 2024, 1, 0, 0, 0),
(10, 4, 10, '2024-08-31', 2024, 0, 0, 0, 1),
(11, 4, 11, '2024-08-31', 2024, 1, 0, 0, 0),
(12, 4, 12, '2024-08-31', 2024, 11, 0, 0, 0);

--
-- Triggers `kontrol_barang_cawu_dua`
--
DELIMITER $$
CREATE TRIGGER `after_delete_kontrol_barang_cawu_dua` AFTER DELETE ON `kontrol_barang_cawu_dua` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir + OLD.jumlah_rusak + OLD.jumlah_pindah + OLD.jumlah_hilang
    WHERE id_inventaris = OLD.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_kontrol_barang_cawu_dua` AFTER INSERT ON `kontrol_barang_cawu_dua` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir - NEW.jumlah_rusak - NEW.jumlah_pindah - NEW.jumlah_hilang
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_kontrol_barang_cawu_dua` AFTER UPDATE ON `kontrol_barang_cawu_dua` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir 
        - (NEW.jumlah_rusak - OLD.jumlah_rusak) 
        - (NEW.jumlah_pindah - OLD.jumlah_pindah) 
        - (NEW.jumlah_hilang - OLD.jumlah_hilang)
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kontrol_barang_cawu_satu`
--

CREATE TABLE `kontrol_barang_cawu_satu` (
  `id_kontrol_barang_cawu_satu` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_kontrol` date NOT NULL,
  `tahun_kontrol` int(11) NOT NULL,
  `jumlah_baik` int(11) NOT NULL,
  `jumlah_rusak` int(11) NOT NULL,
  `jumlah_pindah` int(11) NOT NULL,
  `jumlah_hilang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrol_barang_cawu_satu`
--

INSERT INTO `kontrol_barang_cawu_satu` (`id_kontrol_barang_cawu_satu`, `id_user`, `id_inventaris`, `tanggal_kontrol`, `tahun_kontrol`, `jumlah_baik`, `jumlah_rusak`, `jumlah_pindah`, `jumlah_hilang`) VALUES
(1, 4, 1, '2024-04-30', 2024, 5, 0, 0, 0),
(2, 4, 2, '2024-04-30', 2024, 1, 0, 0, 0),
(3, 4, 3, '2024-04-30', 2024, 2, 0, 0, 0),
(4, 4, 4, '2024-04-30', 2024, 1, 0, 0, 0),
(5, 4, 5, '2024-04-30', 2024, 2, 0, 0, 0),
(6, 4, 6, '2024-04-30', 2024, 6, 0, 0, 0),
(7, 4, 7, '2024-04-30', 2024, 1, 0, 0, 0);

--
-- Triggers `kontrol_barang_cawu_satu`
--
DELIMITER $$
CREATE TRIGGER `after_delete_kontrol_barang_cawu_satu` AFTER DELETE ON `kontrol_barang_cawu_satu` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir + OLD.jumlah_rusak + OLD.jumlah_pindah + OLD.jumlah_hilang
    WHERE id_inventaris = OLD.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_kontrol_barang_cawu_satu` AFTER INSERT ON `kontrol_barang_cawu_satu` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir - NEW.jumlah_rusak - NEW.jumlah_pindah - NEW.jumlah_hilang
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_kontrol_barang_cawu_satu` AFTER UPDATE ON `kontrol_barang_cawu_satu` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir 
        - (NEW.jumlah_rusak - OLD.jumlah_rusak) 
        - (NEW.jumlah_pindah - OLD.jumlah_pindah) 
        - (NEW.jumlah_hilang - OLD.jumlah_hilang)
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kontrol_barang_cawu_tiga`
--

CREATE TABLE `kontrol_barang_cawu_tiga` (
  `id_kontrol_barang_cawu_tiga` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `tanggal_kontrol` date NOT NULL,
  `tahun_kontrol` int(11) NOT NULL,
  `jumlah_baik` int(11) NOT NULL,
  `jumlah_rusak` int(11) NOT NULL,
  `jumlah_pindah` int(11) NOT NULL,
  `jumlah_hilang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrol_barang_cawu_tiga`
--

INSERT INTO `kontrol_barang_cawu_tiga` (`id_kontrol_barang_cawu_tiga`, `id_user`, `id_inventaris`, `tanggal_kontrol`, `tahun_kontrol`, `jumlah_baik`, `jumlah_rusak`, `jumlah_pindah`, `jumlah_hilang`) VALUES
(1, 4, 1, '2024-12-31', 2024, 4, 0, 0, 0),
(2, 4, 2, '2024-12-31', 2024, 1, 0, 0, 0),
(3, 4, 3, '2024-12-31', 2024, 2, 0, 0, 0),
(4, 4, 4, '2024-12-31', 2024, 0, 1, 0, 0),
(5, 4, 5, '2024-12-31', 2024, 1, 1, 0, 0),
(6, 4, 6, '2024-12-31', 2024, 3, 2, 0, 0),
(7, 4, 7, '2024-12-31', 2024, 0, 1, 0, 0),
(8, 4, 8, '2024-12-31', 2024, 1, 0, 0, 0),
(9, 4, 9, '2024-12-31', 2024, 1, 0, 0, 0),
(10, 4, 11, '2024-12-31', 2024, 1, 0, 0, 0),
(11, 4, 12, '2024-12-31', 2024, 10, 0, 1, 0),
(12, 4, 13, '2024-12-31', 2024, 1, 0, 0, 0),
(13, 4, 14, '2024-12-31', 2024, 0, 0, 0, 1),
(14, 4, 15, '2024-12-31', 2024, 1, 0, 0, 0),
(15, 4, 16, '2024-12-31', 2024, 1, 0, 0, 0),
(16, 4, 17, '2024-12-31', 2024, 1, 0, 0, 0),
(17, 4, 18, '2024-12-31', 2024, 1, 0, 0, 0),
(18, 4, 19, '2024-12-31', 2024, 1, 0, 0, 0);

--
-- Triggers `kontrol_barang_cawu_tiga`
--
DELIMITER $$
CREATE TRIGGER `after_delete_kontrol_barang_cawu_tiga` AFTER DELETE ON `kontrol_barang_cawu_tiga` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir + OLD.jumlah_rusak + OLD.jumlah_pindah + OLD.jumlah_hilang
    WHERE id_inventaris = OLD.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_kontrol_barang_cawu_tiga` AFTER INSERT ON `kontrol_barang_cawu_tiga` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir - NEW.jumlah_rusak - NEW.jumlah_pindah - NEW.jumlah_hilang
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_kontrol_barang_cawu_tiga` AFTER UPDATE ON `kontrol_barang_cawu_tiga` FOR EACH ROW BEGIN
    UPDATE inventaris
    SET jumlah_akhir = jumlah_akhir 
        - (NEW.jumlah_rusak - OLD.jumlah_rusak) 
        - (NEW.jumlah_pindah - OLD.jumlah_pindah) 
        - (NEW.jumlah_hilang - OLD.jumlah_hilang)
    WHERE id_inventaris = NEW.id_inventaris;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan_barang`
--

CREATE TABLE `penerimaan_barang` (
  `id_penerimaan` int(11) NOT NULL,
  `id_permintaan` int(11) DEFAULT NULL,
  `id_departemen` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `merk` varchar(255) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `sumber_penerimaan` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penerimaan_barang`
--

INSERT INTO `penerimaan_barang` (`id_penerimaan`, `id_permintaan`, `id_departemen`, `nama_barang`, `merk`, `tanggal_terima`, `jumlah`, `satuan`, `sumber_penerimaan`) VALUES
(1, 1, 1, 'Meja Karyawan', 'Olympic', '2024-01-02', 5, 'buah', 'Permintaan'),
(2, 3, 1, 'Printer', 'Canon Pixma Pro 1000', '2024-02-02', 1, 'unit', 'Permintaan'),
(3, NULL, 1, 'Lemari Dokumen', 'Tanpa Merk - Buatan tukang', '2024-02-03', 2, 'unit', 'Pengadaan Kantor'),
(4, 2, 1, 'Printer', 'Epson L3210', '2024-03-02', 1, 'unit', 'Permintaan'),
(5, 4, 1, 'AC', 'Sharp', '2024-04-05', 2, 'unit', 'Permintaan'),
(6, 5, 1, 'Monitor', 'Toshiba VA2249SDVI', '2024-04-30', 6, 'unit', 'Permintaan'),
(7, NULL, 1, 'Jam dinding', 'Seiko', '2024-04-30', 1, 'buah', 'Pengadaan Kantor'),
(8, 6, 1, 'Papan tulis', 'Golden House', '2024-05-01', 1, 'buah', 'Permintaan'),
(9, 7, 1, 'Layar Proyektor', 'Le Taec', '2024-06-06', 1, 'unit', 'Permintaan'),
(10, NULL, 1, 'Meteran 3 meter', 'Krisbow', '2024-06-19', 1, 'buah', 'Pengadaan Kantor'),
(11, 8, 1, 'Meja Dispenser', 'Tanpa Merk - Buatan tukang', '2024-07-01', 1, 'buah', 'Permintaan'),
(12, 9, 1, 'Kursi Lipat', 'Chitose', '2024-08-08', 11, 'buah', 'Permintaan'),
(13, 10, 1, 'CPU Server', 'Lenovo Thinksystem St50 V', '2024-09-10', 1, 'unit', 'Permintaan'),
(14, 11, 1, 'Binokuler', 'Nikon Action EX 10x50 CF', '2024-09-24', 1, 'unit', 'Permintaan'),
(15, NULL, 1, 'PH meter digital', 'Tranalab', '2024-10-23', 1, 'unit', 'Pengadaan Kantor'),
(16, 12, 1, 'Dispenser', 'Miyako', '2024-10-22', 1, 'buah', 'Permintaan'),
(17, 13, 1, 'Proyektor', 'Epson EB-972', '2024-11-13', 1, 'unit', 'Permintaan'),
(18, 14, 1, 'Scanner', 'Plustek SmartOffice PS396', '2024-12-16', 1, 'unit', 'Permintaan'),
(19, 15, 1, 'Kamera digital', 'NIKON COOLPIX B500', '2024-12-31', 1, 'unit', 'Permintaan');

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_barang`
--

CREATE TABLE `permintaan_barang` (
  `id_permintaan` int(11) NOT NULL,
  `id_departemen` int(11) NOT NULL,
  `nama_barang` varchar(25) NOT NULL,
  `merk` varchar(25) NOT NULL,
  `tanggal_permintaan` date NOT NULL,
  `spesifikasi` text NOT NULL,
  `jumlah_kebutuhan` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `status` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permintaan_barang`
--

INSERT INTO `permintaan_barang` (`id_permintaan`, `id_departemen`, `nama_barang`, `merk`, `tanggal_permintaan`, `spesifikasi`, `jumlah_kebutuhan`, `satuan`, `status`) VALUES
(1, 1, 'Meja Karyawan', 'Olympic', '2024-01-01', '200 x 80 cm', 5, 'buah', '1'),
(2, 1, 'Printer', 'Epson L3210', '2024-02-01', 'Printer cetak warna dan hitam dengan scanner', 1, 'unit', '1'),
(3, 1, 'Printer', 'Canon Pixma Pro 1000', '2024-03-01', 'Printer cetak warna dan hitam ukuran kertas A2', 1, 'unit', '1'),
(4, 1, 'AC', 'Sharp', '2024-04-01', '1/2 PK', 2, 'unit', '1'),
(5, 1, 'Monitor', 'Toshiba VA2249SDVI', '2024-04-30', 'Tipe Panel IPS Ukuran Layar 21.5 inch Resolusi 1920 x 1080 Tipe Monitor LED', 6, 'unit', '1'),
(6, 1, 'Papan tulis', 'Golden House', '2024-05-01', '120 x 240 cm', 1, 'buah', '1'),
(7, 1, 'Layar Proyektor', 'Le Taec', '2024-06-01', '70 x 70 cm', 1, 'unit', '1'),
(8, 1, 'Meja Dispenser', 'Tanpa Merk - Buatan tukan', '2024-07-01', '50 x 50 x 80 cm, Bahan kayu', 1, 'buah', '1'),
(9, 1, 'Kursi Lipat', 'Chitose', '2024-08-01', 'Warna hitam', 11, 'buah', '1'),
(10, 1, 'CPU Server', 'Lenovo Thinksystem St50 V', '2024-08-31', 'Processor : Xeon E-2324G 4C 65W 3.1GHz - Memory : 1x8GB, UDIMM - Max Memory : up to 128GB, 3200MHz TruDDR4 ECC, memory - Raid Controller : Intel VROC 6.x SW, RAID - Bays : ThinkSystem ST50 V2 3.5\", Drive Bay 3 Cage Kit - Fan : ThinkSystem ST50 V2, System Rear Fan Kit - Drive : 4XB7A77445 - ST50 V2 3.5\" 1TB 7.2K 6Gb NHS, HDD - DVD-RW : Yes', 1, 'unit', '1'),
(11, 1, 'Binokuler', 'Nikon Action EX 10x50 CF', '2024-09-01', 'WATERPROOF AND FOG-FREE WITH NITROGEN GAS FILLING', 1, 'unit', '1'),
(12, 1, 'Dispenser', 'Miyako', '2024-10-01', 'Muat galon sampai 19 liter, air panas dan dingin', 1, 'buah', '1'),
(13, 1, 'Proyektor', 'Epson EB-972', '2024-11-01', 'Ukuran layar 30\" sampai 300\" [0.83 sampai 8.54 m]', 1, 'unit', '1'),
(14, 1, 'Scanner', 'Plustek SmartOffice PS396', '2024-12-01', 'Kecepatan scan 30 lembar/menit. Kapasitas kertas 100 lembar. Bisa scan ukuran kertas dari Kartu nama, A5, Quarto, Letter, A4, Folio/F4, hingga Legal', 1, 'unit', '1'),
(15, 1, 'Kamera digital', 'NIKON COOLPIX B500', '2024-12-16', '16MP 1/2.3\" BSI CMOS Sensor', 1, 'unit', '1');

-- --------------------------------------------------------

--
-- Table structure for table `perpindahan_barang`
--

CREATE TABLE `perpindahan_barang` (
  `id_perpindahan_barang` int(11) NOT NULL,
  `id_inventaris` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `kode_inventaris_baru` varchar(25) NOT NULL,
  `tanggal_perpindahan` date NOT NULL,
  `cawu` varchar(255) NOT NULL,
  `jumlah_perpindahan` int(11) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perpindahan_barang`
--

INSERT INTO `perpindahan_barang` (`id_perpindahan_barang`, `id_inventaris`, `id_ruangan`, `kode_inventaris_baru`, `tanggal_perpindahan`, `cawu`, `jumlah_perpindahan`, `keterangan`) VALUES
(3, 1, 3, 'EHSC/FUR/RA/2024/001', '2024-08-31', 'Caturwulan 2', 1, 'Ruang Karyawan Sudah Penuh.');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int(11) NOT NULL,
  `kode_ruangan` varchar(10) NOT NULL,
  `nama_ruangan` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id_ruangan`, `kode_ruangan`, `nama_ruangan`) VALUES
(1, 'RK', 'Ruang Karyawan'),
(2, 'RM', 'Ruang Meeting'),
(3, 'RA', 'Ruang Arsip'),
(4, 'RP', 'Ruang Penyimpanan'),
(5, 'RH', 'Ruang Kerja HRD'),
(6, 'RI', 'Ruang Kerja IT'),
(7, 'RF', 'Ruang Kerja Finance'),
(8, 'RMK', 'Ruang Kerja Marketing'),
(9, 'RPR', 'Ruang Kerja Produksi'),
(10, 'RQ', 'Ruang Kerja QA'),
(11, 'RR', 'Ruang Kerja R&D'),
(12, 'RS', 'Ruang Server');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `hak_akses` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `email`, `jabatan`, `foto`, `hak_akses`) VALUES
(1, 'Agus Rohyadin', 'Staff', '8f7f93630c366dc55aec88eb8e9640d0', 'agus.rohyadin@buanakaryabhakti.com', 'staff', '1735021739_1.png', 1),
(2, 'Enik Suhyati', 'Admin1', 'e3afed0047b08059d0fada10f400c1e5', 'enik.suhyati@buanakaryabhakti.com', 'administrasi', '1735021750_2.png', 1),
(3, 'Firmansyah', 'Admin2', '21232f297a57a5a743894a0e4a801fc3', 'firmansyah@buanakaryabhakti.com', 'administrasi', '1735021841_1.png', 1),
(4, 'Enik Sihwati', 'petugas1', 'afb91ef692fd08c445e8cb1bab2ccf9c', 'enik.suhyati@buanakaryabhakti.com', 'petugas kontrol', '1735021856_2.png', 1),
(5, 'Firmansyah', 'petugas2', 'afb91ef692fd08c445e8cb1bab2ccf9c', 'firmansyah@buanakaryabhakti.com', 'petugas kontrol', '1735289466_Untitled design (5).png', 1),
(7, 'dimas', 'dimas123', '51947e3cf64ee746b6f2c73d174d525a', 'dimas@gmail.com', 'operator', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id_inventaris`),
  ADD KEY `id_penerimaan` (`id_penerimaan`),
  ADD KEY `id_departemen` (`id_departemen`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `kehilangan_barang`
--
ALTER TABLE `kehilangan_barang`
  ADD PRIMARY KEY (`id_kehilangan_barang`),
  ADD KEY `id_inventaris` (`id_inventaris`);

--
-- Indexes for table `kerusakan_barang`
--
ALTER TABLE `kerusakan_barang`
  ADD PRIMARY KEY (`id_kerusakan_barang`),
  ADD KEY `id_inventaris` (`id_inventaris`);

--
-- Indexes for table `kontrol_barang_cawu_dua`
--
ALTER TABLE `kontrol_barang_cawu_dua`
  ADD PRIMARY KEY (`id_kontrol_barang_cawu_dua`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_inventaris` (`id_inventaris`);

--
-- Indexes for table `kontrol_barang_cawu_satu`
--
ALTER TABLE `kontrol_barang_cawu_satu`
  ADD PRIMARY KEY (`id_kontrol_barang_cawu_satu`),
  ADD KEY `id_invetaris` (`id_inventaris`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `kontrol_barang_cawu_tiga`
--
ALTER TABLE `kontrol_barang_cawu_tiga`
  ADD PRIMARY KEY (`id_kontrol_barang_cawu_tiga`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_inventaris` (`id_inventaris`);

--
-- Indexes for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  ADD PRIMARY KEY (`id_penerimaan`),
  ADD KEY `penerimaan-permintaan` (`id_permintaan`),
  ADD KEY `id_departemen` (`id_departemen`);

--
-- Indexes for table `permintaan_barang`
--
ALTER TABLE `permintaan_barang`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `departemen-permintaan` (`id_departemen`);

--
-- Indexes for table `perpindahan_barang`
--
ALTER TABLE `perpindahan_barang`
  ADD PRIMARY KEY (`id_perpindahan_barang`),
  ADD KEY `id_inventaris` (`id_inventaris`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id_departemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id_inventaris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kehilangan_barang`
--
ALTER TABLE `kehilangan_barang`
  MODIFY `id_kehilangan_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kerusakan_barang`
--
ALTER TABLE `kerusakan_barang`
  MODIFY `id_kerusakan_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kontrol_barang_cawu_dua`
--
ALTER TABLE `kontrol_barang_cawu_dua`
  MODIFY `id_kontrol_barang_cawu_dua` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kontrol_barang_cawu_satu`
--
ALTER TABLE `kontrol_barang_cawu_satu`
  MODIFY `id_kontrol_barang_cawu_satu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `kontrol_barang_cawu_tiga`
--
ALTER TABLE `kontrol_barang_cawu_tiga`
  MODIFY `id_kontrol_barang_cawu_tiga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  MODIFY `id_penerimaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `permintaan_barang`
--
ALTER TABLE `permintaan_barang`
  MODIFY `id_permintaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `perpindahan_barang`
--
ALTER TABLE `perpindahan_barang`
  MODIFY `id_perpindahan_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id_ruangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD CONSTRAINT `inventaris_ibfk_1` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_barang` (`id_penerimaan`),
  ADD CONSTRAINT `inventaris_ibfk_2` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`),
  ADD CONSTRAINT `inventaris_ibfk_3` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`),
  ADD CONSTRAINT `inventaris_ibfk_4` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`);

--
-- Constraints for table `kehilangan_barang`
--
ALTER TABLE `kehilangan_barang`
  ADD CONSTRAINT `kehilangan_barang_ibfk_1` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`);

--
-- Constraints for table `kerusakan_barang`
--
ALTER TABLE `kerusakan_barang`
  ADD CONSTRAINT `kerusakan_barang_ibfk_1` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`);

--
-- Constraints for table `kontrol_barang_cawu_dua`
--
ALTER TABLE `kontrol_barang_cawu_dua`
  ADD CONSTRAINT `kontrol_barang_cawu_dua_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `kontrol_barang_cawu_dua_ibfk_2` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`);

--
-- Constraints for table `kontrol_barang_cawu_satu`
--
ALTER TABLE `kontrol_barang_cawu_satu`
  ADD CONSTRAINT `kontrol_barang_cawu_satu_ibfk_1` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`),
  ADD CONSTRAINT `kontrol_barang_cawu_satu_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `kontrol_barang_cawu_tiga`
--
ALTER TABLE `kontrol_barang_cawu_tiga`
  ADD CONSTRAINT `kontrol_barang_cawu_tiga_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `kontrol_barang_cawu_tiga_ibfk_2` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`);

--
-- Constraints for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  ADD CONSTRAINT `penerimaan-permintaan` FOREIGN KEY (`id_permintaan`) REFERENCES `permintaan_barang` (`id_permintaan`),
  ADD CONSTRAINT `penerimaan_barang_ibfk_1` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`);

--
-- Constraints for table `permintaan_barang`
--
ALTER TABLE `permintaan_barang`
  ADD CONSTRAINT `departemen-permintaan` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`);

--
-- Constraints for table `perpindahan_barang`
--
ALTER TABLE `perpindahan_barang`
  ADD CONSTRAINT `perpindahan_barang_ibfk_1` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
