-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2022 at 09:23 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `department_id` int(2) NOT NULL AUTO_INCREMENT,
  `image_file` varchar(35) DEFAULT NULL,
  `department_name` varchar(35) NOT NULL,
  `tel_number` varchar(14) NOT NULL,
  `email` varchar(35) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `image_file`, `department_name`, `tel_number`, `email`, `created`, `updated`) VALUES
(1, NULL, 'Automatic Vehicle Sales', '1(204)1001232', 'automatics@VROAR.com', '2022-08-15 01:52:03', '2022-08-19 04:16:34'),
(2, NULL, 'Car Detailing', '1(204)2001234', 'detailing@VROAR.com', '2022-08-15 01:52:37', '2022-08-15 01:52:37'),
(3, NULL, 'Customer Service', '1(204)3001234', 'customer.service@VROAR.com', '2022-08-15 01:52:41', '2022-08-15 01:52:41'),
(4, NULL, 'Finance Manager', '1(204)4001234', 'finance@VROAR.com', '2022-08-15 01:52:45', '2022-08-15 01:52:45'),
(5, NULL, 'Lot Manager', '1(204)5001234', 'lot.manager@VROAR.com', '2022-08-15 01:52:52', '2022-08-15 01:52:52'),
(6, NULL, 'Parts Technician', '1(204)6001234', 'parts.tech@VROAR.com', '2022-08-15 01:52:57', '2022-08-15 01:52:57'),
(7, NULL, 'Sales Manager', '1(204)7001234', 'sales@VROAR.com', '2022-08-15 01:53:03', '2022-08-15 01:53:03'),
(8, NULL, 'Service Technician', '1(204)8001234', 'service.tech@VROAR.com', '2022-08-15 01:53:07', '2022-08-15 01:53:07'),
(9, NULL, 'Standard Vehicle Sales', '1(204)9001234', 'standards@VROAR.com', '2022-08-15 01:53:10', '2022-08-15 01:53:10');

-- --------------------------------------------------------

--
-- Table structure for table `dept_comments`
--

DROP TABLE IF EXISTS `dept_comments`;
CREATE TABLE IF NOT EXISTS `dept_comments` (
  `comm_id` int(3) NOT NULL AUTO_INCREMENT,
  `department_id` int(2) NOT NULL,
  `user` varchar(25) NOT NULL DEFAULT 'anonymous',
  `comment` longtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comm_id`),
  KEY `department_id` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dept_comments`
--

INSERT INTO `dept_comments` (`comm_id`, `department_id`, `user`, `comment`, `created`) VALUES
(12, 1, 'anonymous', 'test', '2022-08-19 06:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `emp_id` int(3) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  `tel_number` varchar(14) NOT NULL,
  `email` varchar(35) NOT NULL,
  `department_id` int(2) NOT NULL,
  `image_file` varchar(35) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`emp_id`),
  KEY `department_id` (`department_id`),
  KEY `department_id_2` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`emp_id`, `first_name`, `last_name`, `tel_number`, `email`, `department_id`, `image_file`, `created`, `updated`) VALUES
(1, 'Jonah', 'Hopkins', '1(204)1234567', 'jonah.hopkins@VROAR.com', 1, NULL, '2022-08-15 01:59:05', '2022-08-19 03:34:35'),
(2, 'Marley', 'Davidson', '1(204)9871237', 'marley.davidson@VROAR.com', 2, NULL, '2022-08-15 01:57:54', '2022-08-15 02:04:42'),
(3, 'Silo', 'Trynkowski', '1(204)1291232', 'silo.trynkowski@VROAR.com', 3, NULL, '2022-08-15 01:57:54', '2022-08-16 06:07:30'),
(4, 'Samantha', 'Steele', '1(204)1259849', 'samantha.steele@VROAR.com', 3, NULL, '2022-08-15 01:57:54', '2022-08-15 02:05:48'),
(5, 'Wesley', 'Barker', '1(204)8825608', 'wesley.barker@VROAR.com', 4, NULL, '2022-08-15 01:57:54', '2022-08-19 04:18:38'),
(6, 'Harold', 'Bogsworth', '1(204)8881271', 'harold.bogsworth@VROAR.com', 5, NULL, '2022-08-15 01:57:54', '2022-08-17 08:22:20'),
(7, 'Kenna', 'McClinton', '1(204)6638261', 'kenna.mcclinton@VROAR.com', 6, NULL, '2022-08-15 01:57:54', '2022-08-15 02:06:35'),
(8, 'Bianca', 'Penny', '1(204)5529841', 'bianca.samez@VROAR.com', 7, NULL, '2022-08-15 01:57:54', '2022-08-18 04:58:56'),
(9, 'Jordy', 'Gosby', '1(204)7724511', 'jordy.gosby@VROAR.com', 8, NULL, '2022-08-15 01:57:54', '2022-08-15 02:06:44'),
(10, 'Liliana', 'Rodrigo', '1(204)6129008', 'liliana.rodrigo@VROAR.com', 9, NULL, '2022-08-15 01:57:54', '2022-08-15 02:06:19'),
(11, 'Ronnie', 'Ericson', '1(204)8200902', 'ronnie.ericson@VROAR.com', 6, NULL, '2022-08-15 01:57:54', '2022-08-17 01:15:04'),
(12, 'Thomas', 'Pritchard', '1(204)4432399', 'thomas.pritchard@VROAR.com', 8, NULL, '2022-08-15 01:57:54', '2022-08-15 02:06:28');

-- --------------------------------------------------------

--
-- Table structure for table `emp_comments`
--

DROP TABLE IF EXISTS `emp_comments`;
CREATE TABLE IF NOT EXISTS `emp_comments` (
  `comm_id` int(11) NOT NULL AUTO_INCREMENT,
  `emp_id` int(3) NOT NULL,
  `user` varchar(25) NOT NULL DEFAULT 'anonymous',
  `comment` longtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comm_id`),
  KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `emp_comments`
--

INSERT INTO `emp_comments` (`comm_id`, `emp_id`, `user`, `comment`, `created`) VALUES
(18, 5, 'anonymous', 'test', '2022-08-19 06:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `logins`
--

DROP TABLE IF EXISTS `logins`;
CREATE TABLE IF NOT EXISTS `logins` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logins`
--

INSERT INTO `logins` (`id`, `username`, `password`) VALUES
(81, 'as', '$2y$10$O7T0.u0TPLicpco8sHXtg.mBJaPkGAObhpc7jLBKRIdZ5/xBBpyvu');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dept_comments`
--
ALTER TABLE `dept_comments`
  ADD CONSTRAINT `dept_comments_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emp_comments`
--
ALTER TABLE `emp_comments`
  ADD CONSTRAINT `emp_comments_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `employees` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
