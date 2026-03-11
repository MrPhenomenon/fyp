-- FYP Database Schema
-- Created: March 11, 2026
-- This schema is generated from the Yii2 models

-- ============================================================
-- Table: blocks
-- ============================================================
CREATE TABLE IF NOT EXISTS `blocks` (
  `block_id` INT NOT NULL AUTO_INCREMENT,
  `block_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: floors
-- ============================================================
CREATE TABLE IF NOT EXISTS `floors` (
  `floor_id` INT NOT NULL AUTO_INCREMENT,
  `block_id` INT NOT NULL,
  `floor_number` INT NOT NULL,
  PRIMARY KEY (`floor_id`),
  CONSTRAINT `fk_floors_block_id` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: departments
-- ============================================================
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` INT NOT NULL AUTO_INCREMENT,
  `department_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: department_floors
-- ============================================================
CREATE TABLE IF NOT EXISTS `department_floors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `department_id` INT NOT NULL,
  `floor_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_department_floors_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_department_floors_floor_id` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: rooms
-- ============================================================
CREATE TABLE IF NOT EXISTS `rooms` (
  `room_id` INT NOT NULL AUTO_INCREMENT,
  `department_floor` INT NOT NULL,
  `room_number` INT NOT NULL,
  PRIMARY KEY (`room_id`),
  CONSTRAINT `fk_rooms_department_floor` FOREIGN KEY (`department_floor`) REFERENCES `department_floors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('teacher', 'clerk', 'admin') NOT NULL,
  `department_id` INT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `fk_users_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: schedule
-- ============================================================
CREATE TABLE IF NOT EXISTS `schedule` (
  `schedule_id` INT NOT NULL AUTO_INCREMENT,
  `teacher_id` INT NOT NULL,
  `room_id` INT NOT NULL,
  `day_of_week` ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday') NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  PRIMARY KEY (`schedule_id`),
  CONSTRAINT `fk_schedule_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_schedule_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: attendance
-- ============================================================
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` INT NOT NULL AUTO_INCREMENT,
  `teacher_id` INT NOT NULL,
  `marked_by` VARCHAR(50) NOT NULL,
  `room_id` INT NOT NULL,
  `subject` VARCHAR(50) NULL,
  `status` ENUM('Yes', 'No', 'Students Not Present') NOT NULL,
  `schedule_id` INT NOT NULL,
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendance_id`),
  CONSTRAINT `fk_attendance_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attendance_room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_attendance_schedule_id` FOREIGN KEY (`schedule_id`) REFERENCES `schedule` (`schedule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Indexes for better query performance
-- ============================================================
CREATE INDEX `idx_floors_block_id` ON `floors` (`block_id`);
CREATE INDEX `idx_department_floors_department_id` ON `department_floors` (`department_id`);
CREATE INDEX `idx_department_floors_floor_id` ON `department_floors` (`floor_id`);
CREATE INDEX `idx_rooms_department_floor` ON `rooms` (`department_floor`);
CREATE INDEX `idx_users_department_id` ON `users` (`department_id`);
CREATE INDEX `idx_schedule_teacher_id` ON `schedule` (`teacher_id`);
CREATE INDEX `idx_schedule_room_id` ON `schedule` (`room_id`);
CREATE INDEX `idx_attendance_teacher_id` ON `attendance` (`teacher_id`);
CREATE INDEX `idx_attendance_room_id` ON `attendance` (`room_id`);
CREATE INDEX `idx_attendance_schedule_id` ON `attendance` (`schedule_id`);
CREATE INDEX `idx_attendance_timestamp` ON `attendance` (`timestamp`);
