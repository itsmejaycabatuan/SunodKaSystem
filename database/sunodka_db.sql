-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 07:09 PM
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
(76, '12345', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 1', 6, 'Serving', '2025-10-31 06:45:57'),
(77, '11111', 'Tatay Valen', 'BSIT 2A', 'Tuition Balance', 'Accounting', 'Semester: 1st Semester, Remarks: ', 6, 'Skipped', '2025-10-31 06:46:21'),
(78, '11111', 'Jason Williams', 'BSIT 2A', 'Transcript of Records', 'Registrar', 'Copies: 1, Purpose: employment, Notes: ', 7, 'Serving', '2025-10-31 07:31:39'),
(79, '11111', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 2', 8, 'Pending', '2025-11-11 16:21:56'),
(80, '11111', 'Allen Jay Cabatuan', 'Graduate', 'Certificate of Enrollment', 'Registrar', 'Term: current, Copies: 2', 9, 'Pending', '2025-11-11 16:22:23');

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
(6, 'admin', 'admin123', 'admin');

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
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
