-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 09:10 AM
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
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `instructor` varchar(255) NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `video_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `instructor`, `duration`, `price`, `video_url`) VALUES
(1, 'HTML Course for Beginners', 'learn HTML  for from scratch.', 'John Doe', '2.0', 0.00, 'videos/php_intro.mp4'),
(2, 'Front End Development-CSS', 'Introduction to Front End Development-CSS', 'Jane Smith', '4.55', 10.00, 'videos/js_basics.mp4'),
(3, 'Introduction to JavaScript', 'Learn JavaScript', 'John Doe', '2.5', 0.00, 'videos/php_intro.mp4'),
(4, 'Python Programming', 'Introduction to Python programming.', 'Jane Smith', '3.5', 10.00, 'videos/js_basics.mp4'),
(5, 'SQL for Data Science', 'LearnSQL from scratch.', 'John Doe', '2.0', 0.00, 'videos/php_intro.mp4'),
(6, 'ChatGPT for Beginners', 'learn how to use ChatGPT ', 'Jane Smith', '3.5', 10.00, 'videos/js_basics.mp4'),
(7, 'AWS for Beginners', 'Learn AWS from scratch', 'John Doe', '2.0', 0.00, 'videos/php_intro.mp4'),
(8, 'Microsoft Azure Essentials', 'learn Microsoft Azure Essentials', 'Jane Smith', '3.5', 10.00, 'videos/js_basics.mp4'),
(9, 'Introduction to MS Excel', 'Learn how  to use  MS Excel', 'John Doe', '2.0', 0.00, 'videos/php_intro.mp4'),
(10, 'Statistics For Data Science', 'learn Statistics For Data Science', 'Jane Smith', '3.5', 10.00, 'videos/js_basics.mp4'),
(11, 'Java Programming', 'Learn Java Programming', 'John Doe', '2.0', 10.00, 'videos/php_intro.mp4'),
(12, 'C for Beginners', 'learn C Programming language', 'Jane Smith', '3.5', 10.00, 'videos/js_basics.mp4');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) DEFAULT 'completed',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_user_enrollment` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000001;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `fk_user_enrollment` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
