-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 02:38 AM
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
-- Database: `payroll_cs`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `empNo` int(11) NOT NULL,
  `empName` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL,
  `tinNo` varchar(20) DEFAULT NULL,
  `sssNo` varchar(20) DEFAULT NULL,
  `philHealthNo` varchar(20) DEFAULT NULL,
  `pagIbigNo` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Employed','Resigned') DEFAULT 'Employed',
  `password` varchar(255) NOT NULL,
  `bankqr` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `empNo`, `empName`, `designation`, `email`, `salary`, `tinNo`, `sssNo`, `philHealthNo`, `pagIbigNo`, `image`, `created_at`, `status`, `password`, `bankqr`) VALUES
(1, 202515, 'EROJO, JUVERCINET FLORES', 'supervisor', 'vers.cscpas@gmail.com', 18000.00, '67804133700000', '3515472272', '022523231530', '121347881496', '3.jpg', '2026-04-22 04:23:50', 'Employed', '$2y$10$rdJFhRkk7TfFqjfNry5OWuJVU.kHBiM0gsPnE8BqNFo0hHC/SU4LS', 'e4314126-98e7-4b7b-969e-31191f0e8e43.jpeg'),
(2, 202304, 'LEDAMA, JOHN ERIC CASAS', 'Supervisor', 'ericledama.cscpas@gmail.com', 30000.00, '33347338700000', '3458395856', '020266997826', '121168644317', 'eric.png', '2026-04-22 04:47:14', 'Employed', '$2y$10$ZQDbRSzPN21vimtVRPGCU.68Y/llPGF/X.dcRGkC99GMS3b1yVPwC', 'sireric.jpg'),
(3, 202409, 'BASAS, MHAE LEEN TEOFISTO', 'Accounting Staff', 'mlbasas.cscpas@gmail.com', 20000.00, '66534202200000', '3538227048', '082501431733', '121363831079', 'mhae.png', '2026-04-22 04:51:51', 'Employed', '$2y$10$CZ73WQJnlYnzvSd8u4.pxuwFC4Zi2hDZyxcYWcLY.lDUCv2vN4zNy', ''),
(4, 202408, 'BAUTISTA, JUNBOY CASTROVERDE', 'Liaison Officer', 'unobautista.cscpas@gmail.com', 18000.00, '33715778300000', '3429766427', '010256997105', '121131813762', 'jumboy.png', '2026-04-22 04:55:05', 'Employed', '$2y$10$EVMs8j3q2tVSGjnBjlGOce9ehMuxWjpJZdHrKvLGRdav.UTZIglhm', ''),
(5, 202513, 'BARTOLOME, KIM ASHLEY ', 'Accounting Staff', 'kim.cscpas@gmail.com', 16000.00, '00000000000000', '0000000000', '000000000000', '000000000000', 'kim.png', '2026-04-22 04:55:52', 'Employed', '$2y$10$3NPqDp.k5s7n6BTyJ2dM6O5oTvC9/Q4XVwk5XJWCXUTmtMObIv4ii', ''),
(6, 202514, 'ISLA, CYRIL ALIYAH MYR ENDONELA', 'Accounting Staff', 'camisla.cscpas@gmail.com', 18000.00, '62145612100000', '3538554685', '082505586162', '121374248689', '17.png', '2026-04-22 04:56:45', 'Employed', '$2y$10$Hgum4aXAo7fdCt3s5w5CIujHkDHcoVa5vl9yw707BtNuPMAIj9pWe', ''),
(7, 202305, 'BUCIO, CAMILA KEYT CALAMBA', 'Accounting Staff', 'ckbucio.cscpas@gmail.com', 21000.00, '64526717200000', '3525787122', '012511410450', '121321442582', 'keyt.png', '2026-04-22 05:13:46', 'Employed', '$2y$10$vP6S51jr1PDQB36rcLPqxOBiIXKYX3gfzkragtKPiVIT4Sg6a/bQS', '2611d075-24dd-42c5-b739-59bb573e520c.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `reset_count` int(11) NOT NULL DEFAULT 0,
  `last_reset_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `emp_id`, `reset_count`, `last_reset_at`, `created_at`) VALUES
(4, 1, 1, '2026-04-29 09:48:17', '2026-04-29 01:48:17'),
(5, 7, 1, '2026-04-29 16:21:36', '2026-04-29 08:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `cut_off_start` date DEFAULT NULL,
  `cut_off_end` date DEFAULT NULL,
  `payroll_date` date DEFAULT NULL,
  `work_days` int(11) DEFAULT NULL,
  `basic_pay` decimal(10,2) DEFAULT NULL,
  `regular_holiday` decimal(10,2) DEFAULT NULL,
  `overtime_pay` decimal(10,2) DEFAULT NULL,
  `restday_ot` decimal(10,2) DEFAULT NULL,
  `special_ot` decimal(10,2) DEFAULT NULL,
  `allowance` decimal(10,2) DEFAULT NULL,
  `adjustment` decimal(10,2) DEFAULT NULL,
  `thirteenth_month` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT NULL,
  `sss` decimal(10,2) DEFAULT NULL,
  `pagibig` decimal(10,2) DEFAULT NULL,
  `philhealth` decimal(10,2) DEFAULT NULL,
  `late` decimal(10,2) DEFAULT NULL,
  `absent` decimal(10,2) DEFAULT NULL,
  `absent_deduction` double DEFAULT 0,
  `total_deductions` decimal(10,2) DEFAULT NULL,
  `gross_pay` decimal(10,2) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reg_holiday` int(11) NOT NULL,
  `ot` int(11) NOT NULL,
  `rd_ot` int(11) NOT NULL,
  `spec_ot` int(11) NOT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `employee_id`, `cut_off_start`, `cut_off_end`, `payroll_date`, `work_days`, `basic_pay`, `regular_holiday`, `overtime_pay`, `restday_ot`, `special_ot`, `allowance`, `adjustment`, `thirteenth_month`, `tax`, `sss`, `pagibig`, `philhealth`, `late`, `absent`, `absent_deduction`, `total_deductions`, `gross_pay`, `net_pay`, `created_at`, `reg_holiday`, `ot`, `rd_ot`, `spec_ot`, `proof_of_payment`) VALUES
(5, 2, '2026-03-16', '2026-03-31', '2026-03-31', 12, 14000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1950.00, 0.00, 0.00, 700.00, 100.00, 350.00, 0.00, 0.00, 0, 1150.00, 15950.00, 14800.00, '2026-04-23 22:37:23', 0, 0, 0, 0, NULL),
(6, 7, '2026-03-16', '2026-03-31', '2026-03-31', 12, 10500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525.00, 100.00, 262.50, 0.00, 0.00, 0, 887.50, 10500.00, 9612.50, '2026-04-23 22:53:26', 0, 0, 0, 0, 'proof_6_1776986178.jpg'),
(7, 4, '2026-03-16', '2026-03-31', '2026-03-31', 12, 9000.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 10000.00, 9225.00, '2026-04-23 22:56:31', 0, 0, 0, 0, 'proof_7_1776986804.jpg'),
(8, 3, '2026-03-16', '2026-03-31', '2026-03-31', 12, 10000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 100.00, 250.00, 0.00, 0.00, 0, 850.00, 10000.00, 9150.00, '2026-04-23 22:57:34', 0, 0, 0, 0, 'proof_8_1776986826.jpg'),
(9, 6, '2026-03-16', '2026-03-31', '2026-03-31', 12, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 9000.00, 8225.00, '2026-04-23 23:03:44', 0, 0, 0, 0, 'proof_9_1776986857.jpg'),
(10, 1, '2026-03-16', '2026-03-31', '2026-03-31', 12, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 9000.00, 8225.00, '2026-04-23 23:04:55', 0, 0, 0, 0, 'proof_10_1776986879.jpg'),
(19, 5, '2026-03-16', '2026-03-31', '2026-03-31', 12, 8000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.00, 735.63, 735.63, 8000.00, 7264.37, '2026-04-24 02:35:01', 0, 0, 0, 0, 'proof_19_1776998155.jpg'),
(20, 7, '2026-04-01', '2026-04-15', '2026-04-15', 11, 10500.00, 965.52, 1206.90, 0.00, 0.00, 5400.00, 0.00, 0.00, 0.00, 525.00, 100.00, 262.50, 0.00, 0.00, 0, 887.50, 18072.42, 17184.92, '2026-04-24 02:50:23', 0, 0, 0, 0, 'proof_20_1777000049.jpg'),
(21, 4, '2026-04-01', '2026-04-15', '2026-04-15', 11, 9000.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 10000.00, 9225.00, '2026-04-24 02:51:31', 0, 0, 0, 0, 'proof_21_1777000075.jpg'),
(22, 3, '2026-04-01', '2026-04-15', '2026-04-15', 11, 10000.00, 919.54, 431.03, 2390.80, 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 100.00, 250.00, 0.00, 0.00, 0, 850.00, 13741.38, 12891.38, '2026-04-24 02:55:25', 0, 0, 0, 0, 'proof_22_1777000092.jpg'),
(23, 6, '2026-04-01', '2026-04-15', '2026-04-15', 11, 9000.00, 827.59, 0.00, 1075.87, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 10903.46, 10128.46, '2026-04-24 02:59:45', 0, 0, 0, 0, 'proof_23_1777000117.jpg'),
(24, 1, '2026-04-01', '2026-04-15', '2026-04-15', 11, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 9000.00, 8225.00, '2026-04-24 03:00:30', 0, 0, 0, 0, 'proof_24_1777000130.jpg'),
(25, 2, '2026-04-01', '2026-04-15', '2026-04-15', 11, 15000.00, 1379.31, 1724.14, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 750.00, 100.00, 375.00, 0.00, 0.00, 0, 1225.00, 18103.45, 16878.45, '2026-04-24 03:02:20', 0, 0, 0, 0, NULL),
(26, 5, '2026-04-01', '2026-04-15', '2026-04-15', 11, 8000.00, 735.63, 0.00, 956.32, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.50, 367.815, 367.82, 9691.95, 9324.13, '2026-04-24 03:11:03', 0, 0, 0, 0, 'proof_26_1777000286.jpg'),
(33, 2, '2026-04-16', '2026-04-30', '2026-04-30', 11, 15000.00, 0.00, 862.07, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 750.00, 100.00, 375.00, 0.00, 0.00, 0, 1225.00, 15862.07, 14637.07, '2026-04-29 07:50:10', 0, 0, 0, 0, NULL),
(34, 7, '2026-04-16', '2026-04-30', '2026-04-30', 11, 10500.00, 0.00, 905.18, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525.00, 100.00, 262.50, 0.00, 0.00, 0, 887.50, 11405.18, 10517.68, '2026-04-29 07:51:17', 0, 0, 0, 0, 'proof_34_1777450102.jpg'),
(35, 4, '2026-04-16', '2026-04-30', '2026-04-30', 11, 9000.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 10000.00, 9225.00, '2026-04-29 07:52:12', 0, 0, 0, 0, 'proof_35_1777450186.jpg'),
(36, 3, '2026-04-16', '2026-04-30', '2026-04-30', 11, 10000.00, 0.00, 862.07, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 100.00, 250.00, 0.00, 0.00, 0, 850.00, 10862.07, 10012.07, '2026-04-29 07:52:57', 0, 0, 0, 0, 'proof_36_1777450206.jpg'),
(37, 5, '2026-04-16', '2026-04-30', '2026-04-30', 11, 8000.00, 0.00, 459.77, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.50, 367.815, 367.82, 8459.77, 8091.95, '2026-04-29 07:58:39', 0, 0, 0, 0, 'proof_37_1777450227.jpg'),
(38, 6, '2026-04-16', '2026-04-30', '2026-04-30', 11, 9000.00, 0.00, 517.24, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 9517.24, 8742.24, '2026-04-29 08:04:13', 0, 0, 0, 0, 'proof_38_1777450286.jpg'),
(40, 1, '2026-04-16', '2026-04-30', '2026-04-30', 11, 9000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 450.00, 100.00, 225.00, 0.00, 0.00, 0, 775.00, 9000.00, 8225.00, '2026-04-29 08:07:21', 0, 0, 0, 0, 'proof_40_1777450298.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `empNo` (`empNo`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_emp_id` (`emp_id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_emp` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
