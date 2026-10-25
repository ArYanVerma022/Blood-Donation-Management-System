-- ========================================================
-- JeevanRakt – Blood Donor Management System
-- MySQL Database Setup & Seed Script
-- Developed By: Aryan Verma (Student ID: 241306064)
-- Technology: PHP + MySQL + HTML5 + Bootstrap
-- ========================================================

CREATE DATABASE IF NOT EXISTS `blood_donor_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blood_donor_management`;

-- --------------------------------------------------------
-- Table: admins
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password for 'admin' is 'admin123' (hashed using password_hash)
INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$w8.3f6/9fR9sN1mN5Qz.3.X4mY7sK0m2zX9qR6Y.2M5Q6Y.2M5Q6Y');

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Rahul Sharma', 'rahul.sharma@example.com', '$2y$10$w8.3f6/9fR9sN1mN5Qz.3.X4mY7sK0m2zX9qR6Y.2M5Q6Y.2M5Q6Y'),
(2, 'Priya Verma', 'priya.verma@example.com', '$2y$10$w8.3f6/9fR9sN1mN5Qz.3.X4mY7sK0m2zX9qR6Y.2M5Q6Y.2M5Q6Y');

-- --------------------------------------------------------
-- Table: donors
-- --------------------------------------------------------
DROP TABLE IF EXISTS `donors`;
CREATE TABLE `donors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `age` INT NOT NULL,
  `blood_group` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `phone` VARCHAR(15) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `city` VARCHAR(50) NOT NULL,
  `address` TEXT NOT NULL,
  `last_donation_date` DATE DEFAULT NULL,
  `status` ENUM('Available', 'Temporarily Unavailable', 'Unavailable') DEFAULT 'Available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `donors` (`id`, `user_id`, `name`, `gender`, `age`, `blood_group`, `phone`, `email`, `city`, `address`, `last_donation_date`, `status`) VALUES
(1, 1, 'Rahul Sharma', 'Male', 26, 'O+', '9876543210', 'rahul.sharma@example.com', 'Delhi', 'Connaught Place, Sector 4', '2026-05-10', 'Available'),
(2, 2, 'Priya Verma', 'Female', 24, 'A+', '9876543211', 'priya.verma@example.com', 'Mumbai', 'Andheri West, Link Road', '2026-03-15', 'Available'),
(3, NULL, 'Amit Kumar', 'Male', 31, 'B+', '9876543212', 'amit.kumar@example.com', 'Delhi', 'Karol Bagh, Street 12', '2026-01-20', 'Available'),
(4, NULL, 'Neha Singh', 'Female', 28, 'AB-', '9876543213', 'neha.singh@example.com', 'Bangalore', 'Koramangala, 5th Block', NULL, 'Temporarily Unavailable'),
(5, NULL, 'Vikas Gupta', 'Male', 35, 'O-', '9876543214', 'vikas.gupta@example.com', 'Delhi', 'Rohini Sector 7', '2026-04-05', 'Available');

-- --------------------------------------------------------
-- Table: donor_screening
-- --------------------------------------------------------
DROP TABLE IF EXISTS `donor_screening`;
CREATE TABLE `donor_screening` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT NOT NULL,
  `unwell` ENUM('yes', 'no') DEFAULT 'no',
  `fever` ENUM('yes', 'no') DEFAULT 'no',
  `cold_flu` ENUM('yes', 'no') DEFAULT 'no',
  `infection` ENUM('yes', 'no') DEFAULT 'no',
  `antibiotics` ENUM('yes', 'no') DEFAULT 'no',
  `hiv_history` ENUM('yes', 'no') DEFAULT 'no',
  `hepatitis_history` ENUM('yes', 'no') DEFAULT 'no',
  `bleeding_disorder` ENUM('yes', 'no') DEFAULT 'no',
  `heart_condition` ENUM('yes', 'no') DEFAULT 'no',
  `diabetes` ENUM('yes', 'no') DEFAULT 'no',
  `medical_condition` ENUM('yes', 'no') DEFAULT 'no',
  `medication` ENUM('yes', 'no') DEFAULT 'no',
  `blood_thinner` ENUM('yes', 'no') DEFAULT 'no',
  `hiv_meds` ENUM('yes', 'no') DEFAULT 'no',
  `smoking` ENUM('yes', 'no') DEFAULT 'no',
  `tobacco` ENUM('yes', 'no') DEFAULT 'no',
  `alcohol` ENUM('yes', 'no') DEFAULT 'no',
  `recent_surgery` ENUM('yes', 'no') DEFAULT 'no',
  `dental_procedure` ENUM('yes', 'no') DEFAULT 'no',
  `tattoo` ENUM('yes', 'no') DEFAULT 'no',
  `piercing` ENUM('yes', 'no') DEFAULT 'no',
  `blood_transfusion` ENUM('yes', 'no') DEFAULT 'no',
  `travel_history` ENUM('yes', 'no') DEFAULT 'no',
  `infection_exposure` ENUM('yes', 'no') DEFAULT 'no',
  `screening_result` ENUM('Proceed to Screening', 'Medical Review Required', 'Temporarily Deferred') NOT NULL,
  `deferral_reason` TEXT DEFAULT NULL,
  `screening_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `reviewed_by` VARCHAR(100) DEFAULT 'System Preliminary Screening',
  FOREIGN KEY (`donor_id`) REFERENCES `donors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `donor_screening` (`id`, `donor_id`, `screening_result`, `deferral_reason`) VALUES
(1, 1, 'Proceed to Screening', 'All preliminary screening criteria met satisfactorily.'),
(2, 2, 'Proceed to Screening', 'All preliminary screening criteria met satisfactorily.'),
(3, 4, 'Temporarily Deferred', 'Donor reported currently feeling unwell and having a fever.');

-- --------------------------------------------------------
-- Table: donations
-- --------------------------------------------------------
DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT NOT NULL,
  `donation_date` DATE NOT NULL,
  `blood_group` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1, -- in units
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`donor_id`) REFERENCES `donors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `donations` (`id`, `donor_id`, `donation_date`, `blood_group`, `quantity`) VALUES
(1, 1, '2026-05-10', 'O+', 1),
(2, 2, '2026-03-15', 'A+', 1),
(3, 3, '2026-01-20', 'B+', 1);

-- --------------------------------------------------------
-- Table: blood_stock
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blood_stock`;
CREATE TABLE `blood_stock` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `blood_group` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL UNIQUE,
  `quantity` INT NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blood_stock` (`blood_group`, `quantity`) VALUES
('A+', 12),
('A-', 4),
('B+', 15),
('B-', 3),
('AB+', 8),
('AB-', 2),
('O+', 18),
('O-', 4);

-- --------------------------------------------------------
-- Table: blood_requests
-- --------------------------------------------------------
DROP TABLE IF EXISTS `blood_requests`;
CREATE TABLE `blood_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `requester_name` VARCHAR(100) NOT NULL,
  `blood_group` ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `hospital` VARCHAR(150) NOT NULL,
  `city` VARCHAR(50) NOT NULL,
  `phone` VARCHAR(15) NOT NULL,
  `reason` TEXT NOT NULL,
  `request_date` DATE NOT NULL,
  `status` ENUM('Pending', 'Approved', 'Rejected', 'Completed') DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `blood_requests` (`id`, `requester_name`, `blood_group`, `quantity`, `hospital`, `city`, `phone`, `reason`, `request_date`, `status`) VALUES
(1, 'Suresh Patel', 'O+', 2, 'AIIMS Hospital', 'Delhi', '9811223344', 'Emergency surgery required for accident patient.', '2026-08-14', 'Pending'),
(2, 'Sunita Devi', 'B+', 1, 'Fortis Healthcare', 'Mumbai', '9822334455', 'Low hemoglobin count treatment.', '2026-08-12', 'Approved'),
(3, 'Manish Sharma', 'AB-', 1, 'Max Super Speciality', 'Delhi', '9833445566', 'Urgent blood requirement for critical care.', '2026-08-10', 'Pending');

-- ========================================================
-- End of SQL Setup Script
-- JeevanRakt System by Aryan Verma (241306064)
-- ========================================================
