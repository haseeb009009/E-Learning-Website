-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 08:52 AM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`) VALUES
(1, 'admin@example.com', 'admin@example.com'),
(2, 'admin2@gmail.com', 'admin2@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `duration` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `video_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `duration`, `price`, `video_url`) VALUES
(1, 'HTML Course for Beginners', 'A beginner-friendly course that teaches the basics of HTML, the standard language used to create and structure web pages. Perfect for those new to web development.', '14.1', 0.00, 'https://www.youtube.com/watch?v=rklidcZ-aLU&t=35s'),
(2, 'Front End Development-CSS', 'An introductory course on front-end development focusing on CSS, which is used to style and visually enhance HTML elements on web pages. Ideal for beginners looking to design attractive, responsive layouts.', '9.24', 0.00, 'https://www.youtube.com/watch?v=jgfq8OybWZQ&t=85s'),
(3, 'Introduction to JavaScript', 'A beginner-level course to learn JavaScript, the programming language that adds interactivity and dynamic behavior to websites. Great for enhancing your front-end development skills.', '1', 0.00, 'https://www.youtube.com/watch?v=BI1o2H9z9fo&t=60s'),
(4, 'Python Programming', 'A beginner-friendly course that introduces Python programming, covering basic concepts and syntax. Ideal for those starting their coding journey.', '9.8', 1.00, 'https://www.youtube.com/watch?v=R2G7xQymBCs'),
(5, 'SQL for Data Science', 'A beginner-level course to learn SQL from scratch, focusing on how to manage and query data in relational databases. Perfect for aspiring data analysts and developers.', '10.12', 1.00, 'https://www.youtube.com/watch?v=R2fQ5-PMju0'),
(6, 'ChatGPT for Beginners', 'A simple course that teaches how to effectively use ChatGPT for tasks like writing, coding, brainstorming, and learning. Great for beginners exploring AI tools.', '1', 0.00, 'https://www.youtube.com/watch?v=LhkC1X6BfB0'),
(7, 'AWS for Beginners', 'A beginner-friendly course that teaches the fundamentals of Amazon Web Services (AWS), covering cloud computing, storage, and deployment. Ideal for those new to cloud technologies.', '9.2', 1.00, 'https://www.youtube.com/watch?v=k1RI5locZE4'),
(8, 'Microsoft Azure Essentials', 'An introductory course on Microsoft Azure Essentials, covering core cloud concepts, services, and tools. Perfect for beginners starting their cloud computing journey with Azure.', '8', 1.00, 'https://www.youtube.com/watch?v=5abffC-K40c'),
(9, 'Introduction to MS Excel', 'A beginner-level course introducing Microsoft Excel, focusing on spreadsheets, formulas, and basic data analysis. Ideal for boosting productivity and organizing data efficiently.', '1', 0.00, 'https://www.youtube.com/watch?v=OX-iyb-21tk'),
(10, 'Statistics For Data Science', 'A course designed to teach the fundamentals of statistics for data science, including probability, hypothesis testing, and data analysis techniques. Perfect for those looking to build a strong foundation in data-driven decision-making.', '3.5', 1.00, 'https://www.youtube.com/watch?v=Nbnht4fvTDs&t=7384s'),
(11, 'Java Programming', 'A course that covers the basics of Java programming, including syntax, object-oriented concepts, and common libraries. Ideal for beginners interested in building applications and software development.', '18', 0.00, 'https://www.youtube.com/watch?v=32DLasxoOiM'),
(12, 'C for Beginners', 'A beginner-friendly course that teaches the fundamentals of C programming, focusing on syntax, data structures, and algorithms. Perfect for those looking to dive into low-level programming and system development.', '11', 0.00, 'https://www.youtube.com/watch?v=rBoylHlvdwc');

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
  `payment_status` enum('pending','completed') DEFAULT 'pending',
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
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

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
