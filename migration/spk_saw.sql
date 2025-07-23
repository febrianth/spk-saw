-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 20, 2025 at 06:19 PM
-- Server version: 8.0.42-0ubuntu0.22.04.1
-- PHP Version: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spk_saw`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatives`
--

CREATE TABLE `alternatives` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
);

-- --------------------------------------------------------

--
-- Table structure for table `criterias`
--

CREATE TABLE `criterias` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(10) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `attribute` enum('benefit','cost') NOT NULL DEFAULT 'benefit',
  `input_type` ENUM('number', 'option') NOT NULL DEFAULT 'number'
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--
-- Dumping data for table `criterias`
--

INSERT INTO `criterias` (`id`, `name`, `code`, `weight`, `attribute`, `input_type`, `created_at`, `updated_at`) VALUES
(1, 'Harga Pakan (Rp)', 'C1', '0.30', 'cost', 'number', '2025-07-18 15:05:06', '2025-07-20 10:00:49'),
(2, 'Kualitas Pakan', 'C2', '0.20', 'benefit', 'option', '2025-07-18 15:05:06', '2025-07-20 09:22:44'),
(3, 'Ketepatan Pengiriman (Hari)', 'C3', '0.20', 'benefit', 'number','2025-07-18 15:05:06', '2025-07-20 10:01:08'),
(4, 'Jarak ke Lokasi (Km)', 'C4', '0.15', 'cost', 'number','2025-07-18 15:05:06', '2025-07-20 10:58:00'),
(5, 'Ketersediaan Pakan', 'C5', '0.15', 'benefit', 'option','2025-07-18 15:05:06', '2025-07-20 09:58:33');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int NOT NULL,
  `alternative_id` int NOT NULL,
  `criteria_id` int NOT NULL,
  `score_value` decimal(13,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `sub_criterias`
--

CREATE TABLE `sub_criterias` (
  `id` int NOT NULL,
  `criteria_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--
-- Dumping data for table `sub_criterias`
--

INSERT INTO `sub_criterias` (`id`, `criteria_id`, `name`, `value`, `created_at`, `updated_at`) VALUES
(1, 2, 'Sangat Halus', '9.00', '2025-07-18 15:05:06', '2025-07-18 15:05:06'),
(2, 2, 'Halus', '7.00', '2025-07-18 15:05:06', '2025-07-18 15:05:06'),
(3, 2, 'Sedang', '5.00', '2025-07-18 15:05:06', '2025-07-18 15:05:06'),
(4, 2, 'Kasar', '3.00', '2025-07-18 15:05:06', '2025-07-18 15:05:06'),
(5, 2, 'Sangat Kasar', '1.00', '2025-07-18 15:05:06', '2025-07-18 15:05:06'),
(6, 5, 'Sangat Tersedia', '9.00', '2025-07-18 15:05:07', '2025-07-18 15:05:07'),
(7, 5, 'Tersedia', '7.00', '2025-07-18 15:05:07', '2025-07-18 15:05:07'),
(8, 5, 'Kadang Tersedia', '5.00', '2025-07-18 15:05:07', '2025-07-18 15:05:07'),
(9, 5, 'Jarang Tersedia', '3.00', '2025-07-18 15:05:07', '2025-07-18 15:05:07'),
(10, 5, 'Tidak Pernah Tersedia', '1.00', '2025-07-18 15:05:07', '2025-07-18 15:05:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` enum('admin','operator') DEFAULT 'operator',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin SPK', 'admin@example.com', 'admin123', 'admin', '2025-07-18 15:05:06', '2025-07-18 15:05:06', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criterias`
--
ALTER TABLE `criterias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alternative_criteria_unique` (`alternative_id`,`criteria_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `sub_criterias`
--
ALTER TABLE `sub_criterias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `criterias`
--
ALTER TABLE `criterias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sub_criterias`
--
ALTER TABLE `sub_criterias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores` ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`criteria_id`) REFERENCES `criterias` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_criterias`
--
ALTER TABLE `sub_criterias`
  ADD CONSTRAINT `sub_criterias_ibfk_1` FOREIGN KEY (`criteria_id`) REFERENCES `criterias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
