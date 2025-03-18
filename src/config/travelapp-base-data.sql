-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Jun 2023 pada 10.43
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
-- Database: `travelapp`
--

--
-- Dumping data untuk tabel `daerah_operasional`
--

INSERT INTO `m_daerah_operasional` (`id`, `nama_kota`, `provinsi`) VALUES
(2, 'Padang', 1),
(3, 'Pekanbaru', 2),
(4, 'Pelalawan', 2),
(5, 'Dumai', 2),
(6, 'Sorek', 2),
(7, 'Kerinci', 2),
(8, 'Pariaman', 1),
(9, 'Padang Panjang', 1),
(10, 'Bukit Tinggi', 1),
(11, 'Payakumbuh', 1);

--
-- Dumping data untuk tabel `jadwal_keberangkatan`
--

INSERT INTO `m_jadwal_keberangkatan` (`id`, `jam`, `alias`) VALUES
(1, '08:00', 'PAGI'),
(2, '16:00', 'SORE'),
(3, '20:00', 'MALAM');

--
-- Dumping data untuk tabel `mobil`
--

INSERT INTO m_mobil (`id`, `merk`, `jumlah_kursi`, `plat_nomor`, `gambar_id`) VALUES
(1, 'Avanza', 8, 'BA 1234 QQ', NULL),
(2, 'Mobil B', 8, 'BA 9892 QO', NULL),
(3, 'Mobil C', 8, 'BA 9922 QE', NULL);

--
-- Dumping data untuk tabel `tiket`
--

INSERT INTO `m_tarif` (`id`, `kota_asal`, `kota_tujuan`, `tipe_penumpang`, `tarif`) VALUES
(1, 6, 2, 3, 200000.00),
(2, 6, 8, 3, 180000.00),
(3, 6, 9, 3, 170000.00),
(4, 7, 2, 3, 200000.00),
(5, 7, 8, 3, 190000.00),
(6, 7, 9, 3, 180000.00),
(7, 6, 2, 2, 250000.00),
(8, 6, 8, 2, 220000.00),
(9, 6, 9, 1, 200000.00),
(10, 6, 2, 1, 230000.00),
(11, 4, 2, 1, 250000.00);

--
-- Dumping data untuk tabel `tipe_penumpang`
--

INSERT INTO `m_kategori_pelanggan` (`id`, `kategori`) VALUES
(1, 'Mahasiswa Umum'),
(2, 'Umum'),
(3, 'IMAPPEL');

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `password`, `level`, `kontak`, `photo`) VALUES
(1, 'Administrator', 'admin', '$2y$10$oKkCaUIHawIqdx33sxwE1O9vHlhXcbfsRVQ/m7uoOL9gnjNWe1cay', 'ADMIN', NULL, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
