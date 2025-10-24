-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2025 at 01:40 PM
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
-- Database: `bhaviclients`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `owner_first_name` varchar(100) NOT NULL,
  `owner_last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 3,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `user_id`, `name`, `owner_first_name`, `owner_last_name`, `email`, `phone`, `role_id`, `created_at`, `updated_at`) VALUES
(4, 8, 'Vr Facts', 'raja', 'ram', 'rajaram@gmail.com', '4564564564', 3, '2025-10-24 11:22:44', '2025-10-24 11:22:44'),
(5, 9, 'Vk Constructions', 'Rajus', 'Abhiram', 'ramaravo@gmail.com', '1478523690', 3, '2025-10-24 11:31:14', '2025-10-24 11:31:14'),
(7, 1, 'Hematology', 'Raju', 'kala', 'ramaraveo@gmail.com', '1234560123', 3, '2025-10-24 11:38:51', '2025-10-24 11:38:51'),
(15, 31, 'Vk Constructions', 'Rajus', 'Abhiram', 'ramarwavo@gmail.com', '9879879871', 3, '2025-10-24 14:58:59', '2025-10-24 14:58:59'),
(16, 32, 'fbsrbse', 'rearega', 'araear', 'ramargrravo@gmail.com', '9879879222', 3, '2025-10-24 14:59:16', '2025-10-24 14:59:16'),
(18, 34, 'raja', 'rerger', 'erqgqerg', 'ragnork@gmail.com', '9879879855', 3, '2025-10-24 15:48:34', '2025-10-24 16:05:30');

-- --------------------------------------------------------

--
-- Table structure for table `client_files`
--

CREATE TABLE `client_files` (
  `id` int(11) UNSIGNED NOT NULL,
  `client_id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `client_files`
--

INSERT INTO `client_files` (`id`, `client_id`, `file_name`, `original_name`, `file_type`, `file_size`, `uploaded_at`) VALUES
(10, 18, 'estimation of cctv.xlsx', NULL, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 13675, '2025-10-24 16:25:04'),
(11, 18, 'shangai times Option -1.xlsx', NULL, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 13885, '2025-10-24 16:29:25'),
(12, 18, 'Office Management System  Bhavi Creations (1).xlsx', NULL, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 70538, '2025-10-24 16:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(3, 'Social Media', 'Posting in social media', '2025-10-22 16:14:12', '2025-10-22 17:41:42'),
(4, 'Website Development', 'websites', '2025-10-22 16:17:35', '2025-10-22 17:41:59'),
(5, 'SEO', 'Search Engin ', '2025-10-22 16:17:43', '2025-10-22 17:42:14'),
(6, 'Telecaller', ' ', '2025-10-22 16:19:24', '2025-10-22 17:42:23'),
(7, 'Marketing ', ' ', '2025-10-22 16:26:44', '2025-10-22 17:42:36');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `department_id`, `role_id`, `first_name`, `last_name`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(8, 0, 4, 0, 'sathish', 'rao', 'sathishraj@gmail.com', '7897897897', '2025-10-24 10:51:10', '2025-10-24 10:51:10'),
(9, 0, 6, 0, 'sairaj', 'raj', 'sai@gmail.com', '9879879879', '2025-10-24 10:55:18', '2025-10-24 10:55:18'),
(10, 0, 5, 0, 'raja', 'appa', 'rajappa@gmail.com', '1231231231', '2025-10-24 11:01:22', '2025-10-24 11:01:22'),
(11, 0, 7, 0, 'eeeeee', 'rrrrrr', 'ramarrrrrao@gmail.com', '7412589630', '2025-10-24 11:49:29', '2025-10-24 11:49:29'),
(12, 0, 6, 0, 'b sertset', 'serthsrt', 'ramarahtro@gmail.com', '7894561230', '2025-10-24 11:51:06', '2025-10-24 11:51:06'),
(13, 20, 6, 2, 'Rama Raover', 'area', 'ramrevgagergaerarao@gmail.com', '9876543210', '2025-10-24 11:53:00', '2025-10-24 11:53:00'),
(14, 21, 5, 2, 'Praveen Kumar', 'rao', 'vkconstrunregrtions@gmail.com', '7894561333', '2025-10-24 11:53:56', '2025-10-24 11:53:56'),
(15, 22, 6, 2, 'rvaergaerg', 'qrgvqrgq', 'ramararqevgeo@gmail.com', '9876543222', '2025-10-24 12:13:20', '2025-10-24 12:13:20'),
(18, 25, 7, 2, 'abbaer', 'aeraer', 'ramarregeao@gmail.com', '9876543200', '2025-10-24 12:24:47', '2025-10-24 12:24:47'),
(19, 26, 3, 2, 'Rama Rao', 'raosdvsdv', 'ramadveraoraj@gmail.com', '1876543211', '2025-10-24 12:25:31', '2025-10-24 13:17:49'),
(20, 27, 6, 2, 'hey e', 'ram', 'admewfin@example.com', '0011447789', '2025-10-24 13:30:06', '2025-10-24 13:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `employee_tasks`
--

CREATE TABLE `employee_tasks` (
  `id` int(11) UNSIGNED NOT NULL,
  `employee_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('Pending','In Progress','Completed','Review') NOT NULL DEFAULT 'Pending',
  `due_date` date DEFAULT NULL,
  `submitted_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'Has full system access and client management rights.', '2025-10-23 16:24:02', '2025-10-23 16:24:02', NULL),
(2, 'Employee', 'Can only manage and submit their assigned work/tasks.', '2025-10-23 16:24:02', '2025-10-23 16:24:02', NULL),
(3, 'Clients', 'This is the clients role', '2025-10-23 17:58:06', '2025-10-23 17:58:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `first_name`, `last_name`, `email`, `username`, `password`, `phone`, `department_id`, `company_name`, `client_id`, `employee_id`, `created_at`, `updated_at`) VALUES
(5, 2, 'sathish', 'rao', 'sathishraj@gmail.com', '7897897897', '$2y$10$MxMr78I.gVkGxo/GkyKy4OsHTyQ01nfOLapW91DgEtHjvNENnbkra', '7897897897', 4, NULL, NULL, 8, '2025-10-24 10:51:10', '2025-10-24 10:51:10'),
(6, 2, 'sairaj', 'raj', 'sai@gmail.com', 'sai@gmail.com', '$2y$10$khoFiieqyKqrEvEaOUQDJeA4vG.AQ2L7sk3vppdhkpWlsufej71N6', '9879879879', 6, NULL, NULL, 9, '2025-10-24 10:55:18', '2025-10-24 10:55:18'),
(7, 2, 'raja', 'appa', 'rajappa@gmail.com', 'rajappa@gmail.com', '$2y$10$n5ZQRXqorKFBN3bGr5/hV.HSKxDy6QvoXkO2bReoDV3ETbrThzvyq', '1231231231', 5, NULL, NULL, 10, '2025-10-24 11:01:22', '2025-10-24 11:01:22'),
(8, 3, 'raja', 'ram', 'rajaram@gmail.com', 'rajaram@gmail.com', '$2y$10$6rk8F9WtBW7vdOkQJCK03elFI6gYvPkt/e4i1bLgsUsn5tClkMSZO', '4564564564', NULL, NULL, NULL, NULL, '2025-10-24 11:22:44', '2025-10-24 11:22:44'),
(9, 3, 'Rajus', 'Abhiram', 'ramaravo@gmail.com', 'ramaravo@gmail.com', '$2y$10$ngilMb9GCxYCDp6ScJKigOVf4vsVtTTJtDCt2Ad750tV4SMTdz0hu', '1478523690', NULL, 'Vk Constructions', NULL, NULL, '2025-10-24 11:31:14', '2025-10-24 11:31:14'),
(13, 3, 'Raju', 'kala', 'ramaraveo@gmail.com', 'ramaraveo@gmail.com', '$2y$10$5EY7gxzqGY6aDdKXAAO/yeDfnVLeq7cPzJc2EJNOh80.bA6R1i1RO', '1234560123', NULL, NULL, NULL, NULL, '2025-10-24 11:38:51', '2025-10-24 11:38:51'),
(18, 2, 'eeeeee', 'rrrrrr', 'ramarrrrrao@gmail.com', 'ramarrrrrao@gmail.com', '$2y$10$/wO42UvH/5uog/8AISUXp.ojLEoc0lj2k/t9EFQ9FJfI0Ktxick2.', '7412589630', 7, NULL, NULL, 11, '2025-10-24 11:49:29', '2025-10-24 11:49:29'),
(19, 2, 'b sertset', 'serthsrt', 'ramarahtro@gmail.com', 'ramarahtro@gmail.com', '$2y$10$0pDugRpubV/lw3fxKiNf0uQYbnpRAT/tXRWx1tPcB6ruN8BIYPbRK', '7894561230', 6, NULL, NULL, 12, '2025-10-24 11:51:06', '2025-10-24 11:51:06'),
(20, 2, 'Rama Raover', 'area', 'ramrevgagergaerarao@gmail.com', 'ramrevgagergaerarao@gmail.com', '$2y$10$1.fraGkAoZ4ulE80MRgm1uZpspnBnxCA/4iY5Z2264rX9goUVQXPC', '9876543210', 6, NULL, NULL, 13, '2025-10-24 11:53:00', '2025-10-24 11:53:00'),
(21, 2, 'Praveen Kumar', 'rao', 'vkconstrunregrtions@gmail.com', 'vkconstrunregrtions@gmail.com', '$2y$10$WXWXjXlnj1LJz0xG5BPCGOnwSbr45YMayvRBdaBsyOmYeL6vdoYg.', '7894561333', 5, NULL, NULL, 14, '2025-10-24 11:53:56', '2025-10-24 11:53:56'),
(22, 2, 'rvaergaerg', 'qrgvqrgq', 'ramararqevgeo@gmail.com', 'ramararqevgeo@gmail.com', '$2y$10$gK4AyaZ/0N4NsVJwRi/dXuBTvP4eiptQj/dDnKThblxwmn19cvVTW', '9876543222', 6, NULL, NULL, 15, '2025-10-24 12:13:20', '2025-10-24 12:13:20'),
(25, 2, 'abbaer', 'aeraer', 'ramarregeao@gmail.com', 'ramarregeao@gmail.com', '$2y$10$Ei8ZmVwC7XgxZcPxG81kuerdeCV2CgofGfNxaGqs7.wWoYdqqPYn6', '9876543200', 7, NULL, NULL, 18, '2025-10-24 12:24:47', '2025-10-24 12:24:47'),
(26, 2, 'Rama Rao', 'raosdvsdv', 'ramadveraoraj@gmail.com', 'ramadveraoraj@gmail.com', '$2y$10$mlo497iw5LwG./DFS3fSruJQF9dDOvKK9lOfz45KHBK1cdqygk2V.', '1876543211', 3, NULL, NULL, 19, '2025-10-24 12:25:31', '2025-10-24 13:17:49'),
(27, 2, 'hey e', 'ram', 'admewfin@example.com', 'admewfin@example.com', '$2y$10$uqG45QG/UXc2t2Zvn91nzuVUhZWP8OVKxrr95MPsB118mbeYGFkHy', '0011447789', 6, NULL, NULL, 20, '2025-10-24 13:30:06', '2025-10-24 13:30:33'),
(31, 3, 'Rajus', 'Abhiram', 'ramarwavo@gmail.com', 'ramarwavo@gmail.com', '$2y$10$/tJ8BQG4NqzMbHl/H3xKVelA/UiSJARno8s1vqeWB0siXD7l.pc1.', '9879879871', NULL, 'Vk Constructions', 15, NULL, '2025-10-24 14:58:59', '2025-10-24 14:58:59'),
(32, 3, 'rearega', 'araear', 'ramargrravo@gmail.com', 'ramargrravo@gmail.com', '$2y$10$rhpRh.//Fef3T/2V3Ol55.0xOgbTZDZWMt5qvWbSz6gyE3ldJl2mG', '9879879223', NULL, 'fbsrbse', 16, NULL, '2025-10-24 14:59:16', '2025-10-24 15:08:40'),
(33, 3, 'Raju', 'Abhiram', 'praveenewlanka@gmail.com', 'praveenewlanka@gmail.com', '$2y$10$h7oZ9k6UKhP.bRb.4zYQ7OAieIrhO1ZTOkkZLBExx2sZ9IXqGI9N.', '9879879556', NULL, 'Vk Constructions', 17, NULL, '2025-10-24 15:48:19', '2025-10-24 16:06:48'),
(34, 3, 'rerger', 'erqgqerg', 'ragnork@gmail.com', 'ragnork@gmail.com', '$2y$10$mhnT/sqNN6WXt7LWGZyK7OS8th6Q9MWd682GYhKOndkL5ezbcjdAq', '9879879855', NULL, 'raja', 18, NULL, '2025-10-24 15:48:34', '2025-10-24 16:05:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_client_user` (`user_id`);

--
-- Indexes for table `client_files`
--
ALTER TABLE `client_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `fk_employees_role_id` (`role_id`);

--
-- Indexes for table `employee_tasks`
--
ALTER TABLE `employee_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `client_files`
--
ALTER TABLE `client_files`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `employee_tasks`
--
ALTER TABLE `employee_tasks`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `fk_client_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `client_files`
--
ALTER TABLE `client_files`
  ADD CONSTRAINT `client_files_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
