-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2023 at 08:42 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `workflow_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_table`
--

CREATE TABLE `document_table` (
  `file_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `document_title` varchar(150) NOT NULL,
  `document_description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_table`
--

CREATE TABLE `employee_table` (
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_phone` varchar(15) NOT NULL,
  `employee_email` varchar(255) NOT NULL,
  `FLA` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_table`
--

INSERT INTO `employee_table` (`employee_id`, `employee_name`, `employee_phone`, `employee_email`, `FLA`) VALUES
(123123, 'FLA1', '1234567899', 'fla1@gmail.com', '700162'),
(600123, 'Priyanshu Pal Datta', '1234567890', 'priyanshu@gmail.com', '123123'),
(700159, 'Ratnadeep ', '8984994899', 'ratnadeep@gmail.com', '948983'),
(700162, 'Nitish Rajbongshi', '6001020913', 'nitishrajbongshi@gmail.com', '500123');

-- --------------------------------------------------------

--
-- Table structure for table `group_table`
--

CREATE TABLE `group_table` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `group_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_table`
--

INSERT INTO `group_table` (`group_id`, `group_name`, `group_description`) VALUES
(123111, 'HRD', 'HR department to handle and manage the employee.'),
(123123, 'Finance', 'Group to handle all the financial works.');

-- --------------------------------------------------------

--
-- Table structure for table `handling_request_group`
--

CREATE TABLE `handling_request_group` (
  `trace_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `handled_by` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `remarks` varchar(255) NOT NULL DEFAULT 'Pending...',
  `trace_order` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `handling_request_group`
--

INSERT INTO `handling_request_group` (`trace_id`, `instance_id`, `group_id`, `handled_by`, `status`, `remarks`, `trace_order`, `created_at`, `updated_at`) VALUES
(78, 94, 123123, 120023, 1, 'Remarks', 3, '2023-05-22 11:40:16', '2023-05-22 08:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `handling_request_person`
--

CREATE TABLE `handling_request_person` (
  `trace_id` int(11) NOT NULL,
  `instance_id` int(11) NOT NULL,
  `step_handleby` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `remarks` varchar(255) NOT NULL DEFAULT 'Pending...',
  `trace_order` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `handling_request_person`
--

INSERT INTO `handling_request_person` (`trace_id`, `instance_id`, `step_handleby`, `status`, `remarks`, `trace_order`, `created_at`, `updated_at`) VALUES
(144, 94, 500123, 1, 'Remarks', 1, '2023-05-22 10:54:28', '2023-05-22 08:09:23'),
(145, 94, 120023, 1, 'Remarks', 2, '2023-05-22 11:39:23', '2023-05-22 08:10:15'),
(146, 94, 123321, 1, 'Remarks', 4, '2023-05-22 11:45:35', '2023-05-22 08:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `status_code`
--

CREATE TABLE `status_code` (
  `status_code` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `status_description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_code`
--

INSERT INTO `status_code` (`status_code`, `status_name`, `status_description`, `created_at`, `updated_at`) VALUES
(-2, 'Rollback', 'Reject and send back to previous step', '2023-05-11 17:46:07', '2023-05-11 17:46:07'),
(-1, 'Reject', 'Reject the task', '2023-05-04 12:37:21', '2023-05-04 12:37:21'),
(0, 'Pending', 'Pending, Not seen yet', '2023-05-04 12:36:52', '2023-05-04 12:36:52'),
(1, 'Accept', 'Accept and move forward', '2023-05-04 12:36:52', '2023-05-04 12:36:52'),
(2, 'Redirect', 'Redirect back to a particular step', '2023-05-04 12:38:22', '2023-05-04 12:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `workflow`
--

CREATE TABLE `workflow` (
  `workflow_id` int(11) NOT NULL,
  `workflow_name` varchar(100) NOT NULL,
  `workflow_description` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow`
--

INSERT INTO `workflow` (`workflow_id`, `workflow_name`, `workflow_description`, `created_at`, `updated_at`) VALUES
(103, 'Intern LogSheet', 'Workflow to maintain the intern Log Sheet', '2023-04-27 11:51:19', '2023-05-22 12:00:34'),
(104, 'Parking Space', 'Workflow for approve parking space', '2023-05-03 11:45:55', '2023-05-22 12:00:34'),
(106, 'Telephone bill', 'Workflow to approve telephone bill', '2023-05-03 11:46:16', '2023-05-22 12:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_instance`
--

CREATE TABLE `workflow_instance` (
  `instance_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `instance_name` varchar(255) NOT NULL,
  `instance_description` varchar(255) NOT NULL,
  `instance_status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_instance`
--

INSERT INTO `workflow_instance` (`instance_id`, `employee_id`, `workflow_id`, `instance_name`, `instance_description`, `instance_status`, `created_at`, `updated_at`) VALUES
(94, 700162, 103, 'Intern logsheet', 'Intern logsheet approval request', 4, '2023-05-22 10:54:28', '2023-05-22 08:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_step`
--

CREATE TABLE `workflow_step` (
  `step_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `step_name` varchar(150) NOT NULL,
  `step_description` varchar(255) NOT NULL,
  `step_order` int(11) NOT NULL,
  `step_type` varchar(50) NOT NULL,
  `step_handleby` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workflow_step`
--

INSERT INTO `workflow_step` (`step_id`, `workflow_id`, `step_name`, `step_description`, `step_order`, `step_type`, `step_handleby`, `created_at`, `updated_at`) VALUES
(206, 103, 'FLA approval', 'Approved by FLA ', 1, 'person', 'FLA', '2023-05-22 11:59:50', '2023-05-22 11:59:50'),
(207, 103, 'HR approval', 'Approved by HR in the role', 2, 'custom', '120023', '2023-05-22 11:59:50', '2023-05-22 11:59:50'),
(208, 103, 'Finance', 'Group of finance officer will approve', 3, 'group', 'Finance', '2023-05-22 11:59:50', '2023-05-22 11:59:50'),
(214, 106, 'HRD', 'HRD will approve', 1, 'group', 'HRD', '2023-05-22 11:59:50', '2023-05-22 11:59:50'),
(215, 106, 'Finance', 'Finance will approve', 2, 'custom', '230023', '2023-05-22 11:59:50', '2023-05-22 11:59:50'),
(217, 103, 'Admin', 'Final approval by Admin', 4, 'custom', '123321', '2023-05-22 11:59:50', '2023-05-22 11:59:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_table`
--
ALTER TABLE `document_table`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `instance_id` (`instance_id`);

--
-- Indexes for table `employee_table`
--
ALTER TABLE `employee_table`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_phone` (`employee_phone`),
  ADD UNIQUE KEY `employee_email` (`employee_email`);

--
-- Indexes for table `group_table`
--
ALTER TABLE `group_table`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `handling_request_group`
--
ALTER TABLE `handling_request_group`
  ADD PRIMARY KEY (`trace_id`),
  ADD KEY `status` (`status`),
  ADD KEY `instance_id` (`instance_id`),
  ADD KEY `fk_instance_group` (`group_id`);

--
-- Indexes for table `handling_request_person`
--
ALTER TABLE `handling_request_person`
  ADD PRIMARY KEY (`trace_id`),
  ADD KEY `status` (`status`),
  ADD KEY `instance_id` (`instance_id`);

--
-- Indexes for table `status_code`
--
ALTER TABLE `status_code`
  ADD PRIMARY KEY (`status_code`),
  ADD UNIQUE KEY `status_description` (`status_description`);

--
-- Indexes for table `workflow`
--
ALTER TABLE `workflow`
  ADD PRIMARY KEY (`workflow_id`),
  ADD UNIQUE KEY `workflow_name` (`workflow_name`);

--
-- Indexes for table `workflow_instance`
--
ALTER TABLE `workflow_instance`
  ADD PRIMARY KEY (`instance_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `workflow_id` (`workflow_id`);

--
-- Indexes for table `workflow_step`
--
ALTER TABLE `workflow_step`
  ADD PRIMARY KEY (`step_id`),
  ADD KEY `workflow_id` (`workflow_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_table`
--
ALTER TABLE `document_table`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `handling_request_group`
--
ALTER TABLE `handling_request_group`
  MODIFY `trace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `handling_request_person`
--
ALTER TABLE `handling_request_person`
  MODIFY `trace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `workflow`
--
ALTER TABLE `workflow`
  MODIFY `workflow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `workflow_instance`
--
ALTER TABLE `workflow_instance`
  MODIFY `instance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `workflow_step`
--
ALTER TABLE `workflow_step`
  MODIFY `step_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_table`
--
ALTER TABLE `document_table`
  ADD CONSTRAINT `document_table_ibfk_1` FOREIGN KEY (`instance_id`) REFERENCES `workflow_instance` (`instance_id`);

--
-- Constraints for table `handling_request_group`
--
ALTER TABLE `handling_request_group`
  ADD CONSTRAINT `fk_instance_group` FOREIGN KEY (`group_id`) REFERENCES `group_table` (`group_id`),
  ADD CONSTRAINT `handling_request_group_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status_code` (`status_code`),
  ADD CONSTRAINT `handling_request_group_ibfk_2` FOREIGN KEY (`instance_id`) REFERENCES `workflow_instance` (`instance_id`);

--
-- Constraints for table `handling_request_person`
--
ALTER TABLE `handling_request_person`
  ADD CONSTRAINT `handling_request_person_ibfk_1` FOREIGN KEY (`status`) REFERENCES `status_code` (`status_code`),
  ADD CONSTRAINT `handling_request_person_ibfk_2` FOREIGN KEY (`instance_id`) REFERENCES `workflow_instance` (`instance_id`);

--
-- Constraints for table `workflow_instance`
--
ALTER TABLE `workflow_instance`
  ADD CONSTRAINT `workflow_instance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee_table` (`employee_id`),
  ADD CONSTRAINT `workflow_instance_ibfk_2` FOREIGN KEY (`workflow_id`) REFERENCES `workflow` (`workflow_id`);

--
-- Constraints for table `workflow_step`
--
ALTER TABLE `workflow_step`
  ADD CONSTRAINT `workflow_step_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflow` (`workflow_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
