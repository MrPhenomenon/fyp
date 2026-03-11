-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260107.f7f22adaa8
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Mar 11, 2026 at 08:18 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fyp`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `marked_by` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_id` int NOT NULL,
  `subject` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Yes','No','Students Not Present') COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_id` int NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `teacher_id`, `marked_by`, `room_id`, `subject`, `status`, `schedule_id`, `timestamp`) VALUES
(1, 1, 'Usman', 3, 'Math', 'No', 3, '2026-03-11 23:59:04'),
(2, 1, 'Usman', 3, 'CS', 'No', 4, '2026-03-12 00:24:08');

-- --------------------------------------------------------

--
-- Table structure for table `blocks`
--

CREATE TABLE `blocks` (
  `block_id` int NOT NULL,
  `block_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blocks`
--

INSERT INTO `blocks` (`block_id`, `block_name`) VALUES
(1, 'Block A'),
(2, 'Block B');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int NOT NULL,
  `department_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'Mathematics'),
(2, 'Physics'),
(3, 'Computer Science'),
(4, 'Information Technology'),
(5, 'English'),
(6, 'Chemistry'),
(7, 'Biology'),
(8, 'Statistics'),
(9, 'Economics');

-- --------------------------------------------------------

--
-- Table structure for table `department_floors`
--

CREATE TABLE `department_floors` (
  `id` int NOT NULL,
  `department_id` int NOT NULL,
  `floor_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department_floors`
--

INSERT INTO `department_floors` (`id`, `department_id`, `floor_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 3),
(4, 4, 3),
(5, 5, 2),
(6, 6, 4),
(7, 7, 4),
(8, 8, 2),
(9, 9, 3);

-- --------------------------------------------------------

--
-- Table structure for table `floors`
--

CREATE TABLE `floors` (
  `floor_id` int NOT NULL,
  `block_id` int NOT NULL,
  `floor_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `floors`
--

INSERT INTO `floors` (`floor_id`, `block_id`, `floor_number`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int NOT NULL,
  `department_floor` int NOT NULL,
  `room_number` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `department_floor`, `room_number`) VALUES
(1, 1, '101'),
(2, 2, '102'),
(3, 5, 'Lab 2'),
(4, 8, '104'),
(5, 3, '201'),
(6, 4, '202'),
(7, 6, '203'),
(8, 7, '204'),
(9, 9, '205');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `schedule_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `room_id` int NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`schedule_id`, `teacher_id`, `room_id`, `day_of_week`, `subject`, `start_time`, `end_time`) VALUES
(1, 1, 1, 'Monday', NULL, '08:00:00', '09:00:00'),
(2, 1, 1, 'Tuesday', NULL, '08:00:00', '09:00:00'),
(3, 1, 3, 'Wednesday', 'Math', '21:00:00', '23:59:00'),
(4, 1, 3, 'Thursday', 'CS', '00:01:00', '01:00:00'),
(5, 1, 1, 'Friday', NULL, '08:00:00', '09:00:00'),
(6, 2, 2, 'Monday', NULL, '09:00:00', '10:00:00'),
(7, 2, 2, 'Tuesday', NULL, '09:00:00', '10:00:00'),
(8, 2, 2, 'Wednesday', NULL, '09:00:00', '10:00:00'),
(9, 2, 2, 'Thursday', NULL, '09:00:00', '10:00:00'),
(10, 2, 2, 'Friday', NULL, '09:00:00', '10:00:00'),
(11, 3, 5, 'Monday', NULL, '10:00:00', '11:00:00'),
(12, 3, 5, 'Tuesday', NULL, '10:00:00', '11:00:00'),
(13, 3, 5, 'Wednesday', NULL, '10:00:00', '11:00:00'),
(14, 3, 5, 'Thursday', NULL, '10:00:00', '11:00:00'),
(15, 3, 5, 'Friday', NULL, '10:00:00', '11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('teacher','clerk','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `department_id`) VALUES
(1, 'Kashif', 'kashif@uni.edu', 'demo', 'teacher', 1),
(2, 'Sara', 'sara@uni.edu', 'demo', 'teacher', 2),
(3, 'Ahmed', 'ahmed@uni.edu', 'demo', 'teacher', 3),
(4, 'Fatima', 'fatima@uni.edu', 'demo', 'teacher', 4),
(5, 'Bilal', 'bilal@uni.edu', 'demo', 'teacher', 5),
(6, 'Hina', 'hina@uni.edu', 'demo', 'teacher', 6),
(7, 'Omar', 'omar@uni.edu', 'demo', 'teacher', 7),
(8, 'Ali', 'ali@uni.edu', 'demo', 'teacher', 8),
(9, 'Zain', 'zain@uni.edu', 'demo', 'teacher', 9),
(10, 'Usman', 'usman@uni.edu', 'demo', 'clerk', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `idx_attendance_teacher_id` (`teacher_id`),
  ADD KEY `idx_attendance_room_id` (`room_id`),
  ADD KEY `idx_attendance_schedule_id` (`schedule_id`),
  ADD KEY `idx_attendance_timestamp` (`timestamp`);

--
-- Indexes for table `blocks`
--
ALTER TABLE `blocks`
  ADD PRIMARY KEY (`block_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `department_floors`
--
ALTER TABLE `department_floors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_department_floors_department_id` (`department_id`),
  ADD KEY `idx_department_floors_floor_id` (`floor_id`);

--
-- Indexes for table `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`floor_id`),
  ADD KEY `idx_floors_block_id` (`block_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `idx_rooms_department_floor` (`department_floor`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `idx_schedule_teacher_id` (`teacher_id`),
  ADD KEY `idx_schedule_room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blocks`
--
ALTER TABLE `blocks`
  MODIFY `block_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `department_floors`
--
ALTER TABLE `department_floors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `floors`
--
ALTER TABLE `floors`
  MODIFY `floor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `schedule_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attendance_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attendance_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `department_floors`
--
ALTER TABLE `department_floors`
  ADD CONSTRAINT `fk_department_floors_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_department_floors_floor_id` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `floors`
--
ALTER TABLE `floors`
  ADD CONSTRAINT `fk_floors_block_id` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_department_floor` FOREIGN KEY (`department_floor`) REFERENCES `department_floors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `fk_schedule_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_schedule_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
