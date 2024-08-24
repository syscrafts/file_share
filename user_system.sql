-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 24, 2024 at 11:19 AM
-- Server version: 11.4.2-MariaDB
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_time` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `user_id`, `file_name`, `file_path`, `upload_time`) VALUES
(3, 9, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-18 16:35:51'),
(4, 5, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-18 17:44:55'),
(5, 5, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-18 17:45:29'),
(6, 10, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-24 02:32:23'),
(8, 11, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-24 06:55:48'),
(9, 11, 'a.pdf', '/srv/http/file_share/uploads/a.pdf', '2024-08-24 07:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session_id`, `user_id`, `created_at`) VALUES
(21, 'hprrklg7v509omspisr3f6bjkl', 9, '2024-08-18 16:34:50'),
(22, 'bckbddfrnj3m8o430lf4uc9c5g', 10, '2024-08-24 02:31:41'),
(23, 'de5oae08f8akmmtp0aa254tjca', 11, '2024-08-24 06:01:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(3, 'ham', '$2y$10$7Y5xEucz1anU5050EDivAersUoO5bAn065W4EOjtYHUgDyd8BRZGO', '2024-08-18 15:27:11'),
(5, 'sam', '$2y$10$VVzLPWa82f9o6rh.iYw9y.nsV1bb0JMZNnWSwTDlRqZFJHbTYEizu', '2024-08-18 15:58:54'),
(6, 'jimmy', '$2y$10$Mn23PpJHID4gHxTK40BFNOSC16iaHfYQcpOskK/rAGq1rHBVWiTnO', '2024-08-18 16:00:39'),
(8, 'jam', '$2y$10$mzPZHXRyYXRQmKCVME8zAeqcZq.ty1yROeUBDkMUyZ9r.DkCVEH3C', '2024-08-18 16:31:58'),
(9, 'john', '$2y$10$bwiwDqtMmV9eJMGLxUsT5eAPHcxUE7ZG1xe0Di2V3wR2ARYAdghcK', '2024-08-18 16:34:39'),
(10, 'travis', '$2y$10$oBYxGKQiGtJpxhRDt1XVq.fPFc0GI4IGibFL.rL2M6ersFSPx0QLu', '2024-08-24 02:31:27'),
(11, 'sujal', '$2y$10$lVdVxJDnhMNh0YKQUrfy4uvWfrd8FRfLTfdi1fFsj6ZHcZ7X1yuQS', '2024-08-24 06:01:24'),
(12, 'utsab', '$2y$10$BNqrK43B9mFaR7u8cJP2YueLyl9UWsilZufTmmfBcI3hnID6qYzvq', '2024-08-24 07:19:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
