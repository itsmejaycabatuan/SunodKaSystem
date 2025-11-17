-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 05:52 AM
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
-- Database: `sunodka_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(100) NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `request_details` text DEFAULT NULL,
  `queue_number` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queue_id`, `student_id`, `full_name`, `course`, `service_type`, `department`, `request_details`, `queue_number`, `status`, `created_at`) VALUES
(66, 'SCC-01111-000241424', 'Allen Jay Cabatuan', 'Graduate', 'Transcript of Records', 'Registrar', 'Copies: 1, Purpose: employment, Notes: ', 1, 'Done', '2025-10-31 06:06:21'),
(67, 'SCC-01111-000241424', 'Jason Williams', 'BSIT-2a', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 1, Purpose: employment, Notes: ', 2, 'Done', '2025-10-31 06:06:48'),
(68, 'SCC-01111-000241424', 'Tatay Valen', 'bsit-3a', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 1, 'Done', '2025-10-31 06:12:03'),
(69, '12345', 'Test', 'BSIT 2A', 'Financial Clearance', 'Accounting', 'Purpose: Enrollment', 2, 'Done', '2025-10-31 06:14:24'),
(70, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Accounting Clearance', 'Accounting', 'Remarks: ', 3, 'Done', '2025-10-31 06:21:46'),
(71, 'SCC-01111-000241424', 'Jay Cabatuan', 'BSIT-2a', 'Transcript of Records', 'Registrar', 'Copies: 1, Purpose: employment, Notes: ', 3, 'Done', '2025-10-31 06:22:28'),
(72, 'SCC-01111-000241424', 'Allen Jay Cabatuan', 'BSIT-2a', 'Certificate of Grades', 'Registrar', 'Semester: 2nd, SY: 2024, Copies: 1, Purpose: employment, Notes: ', 4, 'Done', '2025-10-31 06:29:35'),
(73, 'SCC-01111-000241424', 'Allen Jay Cabatuan', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 1, Purpose: employment, Notes: ', 5, 'Done', '2025-10-31 06:32:05'),
(74, 'SCC-01111-000241424', 'Jay Cabatuan', 'BSIT 2A', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 4, 'Done', '2025-10-31 06:38:09'),
(75, 'SCC-01111-000241424', 'Jay Cabatuan', 'BSIT 2A', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 5, 'Done', '2025-10-31 06:39:50'),
(76, '12345', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 1', 6, 'Skipped', '2025-10-31 06:45:57'),
(77, '11111', 'Tatay Valen', 'BSIT 2A', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 6, 'Done', '2025-10-31 06:46:21'),
(78, '11111', 'Jason Williams', 'BSIT 2A', 'Transcript of Records', 'Registrar', 'Copies: 1, Purpose: employment, Notes: ', 7, 'Skipped', '2025-10-31 07:31:39'),
(79, '11111', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 2', 8, 'Done', '2025-11-11 16:21:56'),
(80, '11111', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 2', 9, 'Skipped', '2025-11-11 16:22:23'),
(81, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT-2a', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 7, 'Done', '2025-11-11 18:11:46'),
(82, 'SCC-01111-000241424', 'Allen Jay Cabatuan', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 1, Purpose: employment, Notes: ', 10, 'Done', '2025-11-13 06:18:19'),
(83, 'SCC-01111-000241424', 'Jay Cabatuan', 'BSIT 2A', 'Transcript of Records', 'Registrar', 'Copies: 1, Purpose: employment, Notes: ', 11, 'Skipped', '2025-11-13 09:36:25'),
(84, '12345', 'Allen Jay Cabatuan', 'Graduate', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 8, 'Done', '2025-11-15 12:31:34'),
(85, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 2, Purpose: employment, Notes: ', 12, 'Done', '2025-11-15 12:41:03'),
(86, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 2, Purpose: employment, Notes: ', 13, 'Done', '2025-11-15 12:42:00'),
(87, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 2, Purpose: employment, Notes: ', 14, 'Done', '2025-11-15 12:42:44'),
(88, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 2, Purpose: employment, Notes: ', 15, 'Done', '2025-11-15 12:43:10'),
(89, 'SCC-01111-000241424', 'Karl Campoy', 'BSIT 2A', 'Certificate of Grades', 'Registrar', 'Semester: 1st, SY: 2024, Copies: 2, Purpose: employment, Notes: ', 16, 'Done', '2025-11-15 12:44:05'),
(90, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 17, 'Done', '2025-11-15 12:45:53'),
(91, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 18, 'Done', '2025-11-15 12:48:54'),
(92, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 19, 'Skipped', '2025-11-15 12:49:22'),
(93, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 20, 'Pending', '2025-11-15 12:52:44'),
(94, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 21, 'Pending', '2025-11-15 12:53:12'),
(95, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 22, 'Skipped', '2025-11-15 12:55:56'),
(96, '22222', 'Jay Cabatuan', 'Bsit3a', 'Accounting Clearance', 'Registrar', 'Remarks: ', 23, 'Done', '2025-11-15 12:56:44'),
(97, 'SCC-01111-000241424', 'Ryan Sir', 'BSIT 4Th year', 'Financial Clearance', 'Accounting', 'Purpose: Clearaance', 9, 'Done', '2025-11-15 14:11:11'),
(98, 'SCC-01111-000241424', 'Ryan Sir', 'BSIT 4th YEAr', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 10, 'Skipped', '2025-11-15 14:11:45'),
(99, 'SCC-01111-000241424', 'Ryan Sir', 'BSIT 4th YEAr', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 11, 'Pending', '2025-11-15 14:58:35'),
(100, 'SCC-01111-000003497', 'Allen Jay B. CAbatuan', 'BSIT 3A', 'Statement of Account', 'Accounting', 'Remarks: ', 12, 'Pending', '2025-11-15 15:38:37'),
(101, '12345', 'Allen Jay Cabatuan', 'BSIT 2A', 'Accounting Clearance', 'Registrar', 'Remarks: ', 24, 'Done', '2025-11-17 04:50:11');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `full_name`, `course`, `year_level`) VALUES
('11111', 'Angelo Mendoza', 'BSIT', '2nd Year'),
('12345', 'Juan Dela Cruz', 'BSIT', '3rd Year'),
('22222', 'Aljon Paragoso', 'BSIT', '1st Year'),
('SCC-123-123456', 'Jay Cabatuan', 'BSIT', '3rd Year');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `staff_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('registrar','accounting','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`staff_id`, `username`, `password`, `role`) VALUES
(1, 'registrar1', '12345', 'registrar'),
(2, 'accounting1', '12345', 'accounting'),
(4, 'accounting', '$2y$10$YpeqcCL9hqI9n1WahbppdO5knNZUFDf3XSvBDeRNw13XVU99l/j0O', 'accounting'),
(6, 'admin', '12345', 'admin'),
(9, 'accouting2', '$2y$10$aa2kxsLw73gITzP5Sz6KNeLEt.1o8BaL5NXBekLmJOdrSzifCsgGi', 'accounting'),
(10, 'accounting3', '12345', 'accounting');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queue_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
