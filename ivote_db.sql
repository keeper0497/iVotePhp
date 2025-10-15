-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 15, 2025 at 05:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ivote_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `main_org_candidates`
--

CREATE TABLE `main_org_candidates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `college` varchar(50) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `partylist` varchar(100) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `temporary_address` text DEFAULT NULL,
  `residency_years` int(11) NOT NULL DEFAULT 0,
  `residency_semesters` int(11) NOT NULL DEFAULT 0,
  `semester_year` varchar(100) DEFAULT NULL,
  `certificate_of_candidacy` varchar(255) DEFAULT NULL,
  `comelec_form_1` varchar(255) DEFAULT NULL,
  `recommendation_letter` varchar(255) DEFAULT NULL,
  `prospectus` varchar(255) DEFAULT NULL,
  `clearance` varchar(255) DEFAULT NULL,
  `coe` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `filing_date` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_org_candidates`
--

INSERT INTO `main_org_candidates` (`id`, `user_id`, `organization`, `profile_pic`, `last_name`, `first_name`, `middle_name`, `nickname`, `age`, `gender`, `dob`, `college`, `year`, `program`, `phone`, `email`, `position`, `partylist`, `permanent_address`, `temporary_address`, `residency_years`, `residency_semesters`, `semester_year`, `certificate_of_candidacy`, `comelec_form_1`, `recommendation_letter`, `prospectus`, `clearance`, `coe`, `created_at`, `status`, `filing_date`, `comment`) VALUES
(5, 27, 'USC', 'uploads/1760287813_68ebdc45d7765_ModelLifecycle.jpeg', 'Smith', 'Jhon', 'Pogi', 'Pogi', 32, 'Male', '1992-12-12', 'CICT', 3, 'Bachelor of Science in Information Systems', '09517232406', 'user01@yopmail.com', 'President', 'Party Partylist', 'werw', 'qwe', 3, 2, '2nd/2025', 'uploads/1760287813_68ebdc45d8715_ModelLifecycle.jpeg', 'uploads/1760287813_68ebdc45d7b1e_ModelLifecycle.jpeg', 'uploads/1760287813_68ebdc45d7d8f_ModelLifecycle.jpeg', 'uploads/1760287813_68ebdc45d805f_JR OB slip.pdf', 'uploads/1760287813_68ebdc45d8274_JR OB slip.pdf', 'uploads/1760287813_68ebdc45d8437_JR OB slip.pdf', '2025-10-12 16:50:13', 'Accepted', '2025-10-13 00:50:13', '');

-- --------------------------------------------------------

--
-- Table structure for table `sub_org_candidates`
--

CREATE TABLE `sub_org_candidates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `block_address` text DEFAULT NULL,
  `position_sub` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `filing_date` datetime NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_org_candidates`
--

INSERT INTO `sub_org_candidates` (`id`, `user_id`, `organization`, `last_name`, `first_name`, `middle_name`, `year`, `block_address`, `position_sub`, `created_at`, `status`, `filing_date`, `comment`) VALUES
(2, 27, 'ACCESS', 'Doe', 'Patrick', 'test', 4, 'efsdasdsad', 'President', '2025-10-13 00:54:36', 'Accepted', '2025-10-13 08:54:36', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('voter','commissioner','admin') NOT NULL,
  `college` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `student_id`, `password`, `role`, `college`) VALUES
(2, 'admin01@yopmail.com', '2022-00001', 'admin123', 'admin', ''),
(18, 'user02@yopmail.com', '2022-0003', '12-08-2003', 'voter', 'CICT'),
(27, 'user01@yopmail.com', '2022-0002', '12-08-2003', 'voter', 'CICT'),
(43, 'commissioner01@yopmail.com', '2022-0004', '12-08-2003', 'commissioner', 'CICT');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `organization_type` varchar(50) NOT NULL,
  `voted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `user_id`, `candidate_id`, `position`, `organization_type`, `voted_at`) VALUES
(5, 27, 5, 'President', 'Main', '2025-10-14 13:49:09'),
(6, 27, 2, 'ACCESS', 'Sub', '2025-10-14 13:49:09');

-- --------------------------------------------------------

--
-- Table structure for table `voting_schedule`
--

CREATE TABLE `voting_schedule` (
  `id` int(11) NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'closed',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voting_schedule`
--

INSERT INTO `voting_schedule` (`id`, `status`, `start_date`, `end_date`, `description`, `created_at`, `updated_at`) VALUES
(1, 'closed', '2025-10-10 03:45:00', '2025-10-15 14:45:00', 'Voting closed by admin', '2025-09-23 01:43:24', '2025-10-14 14:53:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_org_candidates`
--
ALTER TABLE `main_org_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sub_org_candidates`
--
ALTER TABLE `sub_org_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_position_org` (`user_id`,`position`,`organization_type`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_candidate_id` (`candidate_id`);

--
-- Indexes for table `voting_schedule`
--
ALTER TABLE `voting_schedule`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_org_candidates`
--
ALTER TABLE `main_org_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sub_org_candidates`
--
ALTER TABLE `sub_org_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `voting_schedule`
--
ALTER TABLE `voting_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `main_org_candidates`
--
ALTER TABLE `main_org_candidates`
  ADD CONSTRAINT `main_org_candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `sub_org_candidates`
--
ALTER TABLE `sub_org_candidates`
  ADD CONSTRAINT `sub_org_candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
