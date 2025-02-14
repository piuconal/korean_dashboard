-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2025 at 05:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `korean_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `crew_history`
--

CREATE TABLE `crew_history` (
  `id` int(11) NOT NULL,
  `crew_id` int(11) NOT NULL,
  `column_name` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `updated_by` varchar(50) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crew_members`
--

CREATE TABLE `crew_members` (
  `id` int(11) NOT NULL,
  `ship_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `passport_number` varchar(50) NOT NULL,
  `entry_date` date DEFAULT NULL,
  `type` enum('신규','근변','재입국') NOT NULL,
  `start_date` date DEFAULT NULL,
  `disembark_date` date DEFAULT NULL,
  `ship_fee` int(11) DEFAULT 0,
  `moving_fee` int(11) DEFAULT 0,
  `outstanding_amount` int(11) DEFAULT 0,
  `refund_amount` int(11) DEFAULT 0,
  `request_status` tinyint(1) DEFAULT 0,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ships`
--

CREATE TABLE `ships` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$zWRXN1Lpdsx/RKIHabz5HeCu9z6aNh9NNb08NLHsEoLNldI.ZjOTW', '2025-02-10 11:32:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crew_history`
--
ALTER TABLE `crew_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crew_id` (`crew_id`);

--
-- Indexes for table `crew_members`
--
ALTER TABLE `crew_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `passport_number` (`passport_number`),
  ADD KEY `ship_id` (`ship_id`);

--
-- Indexes for table `ships`
--
ALTER TABLE `ships`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crew_history`
--
ALTER TABLE `crew_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `crew_members`
--
ALTER TABLE `crew_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `ships`
--
ALTER TABLE `ships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crew_history`
--
ALTER TABLE `crew_history`
  ADD CONSTRAINT `crew_history_ibfk_1` FOREIGN KEY (`crew_id`) REFERENCES `crew_members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crew_members`
--
ALTER TABLE `crew_members`
  ADD CONSTRAINT `crew_members_ibfk_1` FOREIGN KEY (`ship_id`) REFERENCES `ships` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
