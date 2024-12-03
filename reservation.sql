-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2024 at 11:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reservation`
--
CREATE DATABASE IF NOT EXISTS `reservation` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `reservation`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`log_id`, `admin_id`, `action`, `description`, `timestamp`) VALUES
(1, 1, 'Add', 'Added new boat: Name = waterway 3, Capacity = 70, Status = Available', '2024-10-04 11:03:51'),
(2, 1, 'Delete', 'Deleted boat: ID = 4, Name = waterway 3', '2024-10-04 11:03:55'),
(3, 1, 'Delete', 'Deleted boat: ID = 4, Name = waterway 3', '2024-10-04 11:03:59'),
(4, 1, 'Add Route', 'Added Route: Desert', '2024-10-04 11:13:23'),
(5, 1, 'Delete Route', 'Deleted Route ID: 3', '2024-10-04 11:13:38'),
(6, 1, 'Edit Route', 'Edited Route ID: 1', '2024-10-04 11:13:58'),
(7, 1, 'Added stop ID: 13 for route ID: 2', NULL, '2024-10-04 11:17:19'),
(8, 1, 'Add Stop', 'Added stop ID: 14 for route ID: 2 with location \'Coast F\', stop order 6, and arrival time \'04:21\'.', '2024-10-04 11:21:35'),
(9, 1, 'Delete Stop', 'Deleted stop ID: 14 for route ID: 2.', '2024-10-04 11:22:31'),
(10, 1, 'Delete Stop', 'Deleted stop ID: 13 for route ID: 2.', '2024-10-04 11:22:57'),
(11, 1, 'Edit Stop', 'Edited stop ID: 12 for route ID: 2 with location \'Coast D\', stop order 4, and arrival time \'02:00:00\'.', '2024-10-04 11:28:49'),
(12, 1, 'Edit Stop', 'Edited stop ID: 12 for route ID: 2 with location \'Coast D\', stop order 4, and arrival time \'00:02:02\'.', '2024-10-04 11:30:45'),
(13, 1, 'Add Stop Price', 'Added price for start stop ID: 6 and end stop ID: 8', '2024-10-04 12:14:41'),
(14, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:15:18'),
(15, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:16:17'),
(16, 1, 'Edit Stop Price', 'Updated price for ID: 10', '2024-10-04 12:16:47'),
(17, 1, 'Edit Stop Price', 'Updated price for ID: 10', '2024-10-04 12:17:11'),
(18, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:17:19'),
(19, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:19:45'),
(20, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:19:49'),
(21, 1, 'Edit Stop Price', 'Updated price for ID: 4', '2024-10-04 12:20:04'),
(22, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:22:47'),
(23, 1, 'Edit Stop Price', 'Updated price for ID: 10', '2024-10-04 12:23:17'),
(24, 1, 'Edit Stop Price', 'Updated price for ID: 10', '2024-10-04 12:25:06'),
(25, 1, 'Add Stop Price', 'Added price for start stop ID: 7 and end stop ID: 8', '2024-10-04 12:25:21'),
(26, 1, 'Remove Stop Price', 'Removed price for ID: 9', '2024-10-04 12:25:40'),
(27, 1, 'Add Schedule', 'Added schedule for boat ID: 3, route ID: 2, departure time: 2024-10-04T05:55.', '2024-10-04 09:00:51'),
(28, 1, 'Edit Schedule', 'Edited schedule ID: 22. New Departure: 2024-10-04T12:00, New Arrival: 2024-10-04T22:51, New Status: Scheduled.', '2024-10-04 09:03:15'),
(29, 1, 'Edit Schedule', 'Edited schedule ID: 1. New Departure: 2024-10-03T10:00, New Arrival: 2024-10-03T13:31, New Status: Scheduled.', '2024-10-04 09:06:11'),
(30, 1, 'Edit Schedule', 'Edited schedule ID: 1. New Departure: 2024-10-03T10:00, New Arrival: 2024-10-03T13:31, New Status: Unscheduled.', '2024-10-04 09:06:19'),
(31, 1, 'Edit Stop Price', 'Updated price for ID: 1', '2024-10-04 12:40:10'),
(32, 1, 'Add', 'Added new boat: Name = waterway 4, Capacity = 40, Status = Available', '2024-10-04 12:48:49'),
(33, 1, 'Added seats to max capacity', 'Added 40 seats for Boat ID: 5', '2024-10-04 12:48:59'),
(34, 1, 'Updated a seat', 'Updated Seat ID: 283 to Seat Number: 1, Type: vip', '2024-10-04 12:49:19'),
(35, 1, 'Deleted a seat', 'Deleted Seat Number: 27 from Boat ID: 5', '2024-10-04 12:49:37'),
(36, 1, 'Deleted a seat', 'Deleted Seat Number: 8 from Boat ID: 5', '2024-10-04 12:50:12'),
(37, 1, 'Updated a seat', 'Updated Seat ID: 297 to Seat Number: 99, Type: Regular', '2024-10-04 12:50:30'),
(38, 1, 'Cancelled booking ID: 7. Refund Amount: ₹33.75', NULL, '2024-10-04 12:56:32'),
(39, 1, 'Cancellation', 'Cancelled booking ID: 5. Refund Amount: ₹18.75', '2024-10-04 12:59:03'),
(40, 1, 'Add Boat', 'Added new boat: Name = waterway 5, Capacity = 25, Status = Available', '2024-10-04 13:02:16'),
(41, 1, 'Added seats to max capacity', 'Added 25 seats for Boat ID: 6', '2024-10-04 13:02:23'),
(42, 1, 'Add Schedule', 'Added schedule for boat ID', '2024-10-04 09:33:09'),
(43, 1, 'Add Schedule', 'Added schedule for boat ID: 6, route ID: 2, departure time: 2024-10-05T16:09.', '2024-10-04 09:36:19'),
(44, 1, 'Update', 'Updated schedule ID: 25. New Departure Time: 2024-10-04T13:02, New Arrival Time: 2024-10-04T20:03, New Status: Scheduled.', '2024-10-04 13:08:04'),
(45, 1, 'Insert', 'Added schedule for Boat ID: 6, Route ID: 1, Departure Time: 2024-10-19T13:08.', '2024-10-04 13:09:00'),
(46, 1, 'Update', 'Updated schedule ID: 1. New Departure Time: 2024-10-04T10:00, New Arrival Time: 2024-10-03T13:31, New Status: Unscheduled.', '2024-10-05 12:28:53'),
(47, 1, 'Update', 'Updated schedule ID: 1. New Departure Time: 2024-10-04T10:00, New Arrival Time: 2024-10-05T13:31, New Status: Unscheduled.', '2024-10-05 12:29:18'),
(48, 1, 'Update', 'Updated schedule ID: 1. New Departure Time: 2024-10-05T10:00, New Arrival Time: 2024-10-05T13:31, New Status: Unscheduled.', '2024-10-05 12:29:36'),
(49, 1, 'Update', 'Updated schedule ID: 1. New Departure Time: 2024-10-05T11:00, New Arrival Time: 2024-10-05T13:31, New Status: Unscheduled.', '2024-10-05 12:29:54'),
(50, 1, 'Updated a seat', 'Updated Seat ID: 75 to Seat Number: 28, Type: Vip', '2024-10-06 20:05:39'),
(51, 1, 'Cancellation', 'Cancelled booking ID: 1. Refund Amount: ₹21.00', '2024-10-06 20:06:06'),
(52, 1, 'Update', 'Updated schedule ID: 2. New Departure Time: 2024-10-10T18:00, New Arrival Time: 2024-10-10T23:00, New Status: Scheduled.', '2024-10-10 10:42:38'),
(53, 1, 'Update', 'Updated schedule ID: 18. New Departure Time: 2024-10-10T10:00, New Arrival Time: 2024-10-10T14:38, New Status: Scheduled.', '2024-10-10 10:43:04'),
(54, 1, 'Add Stop Price', 'Added price for start stop ID: 2 and end stop ID: 7', '2024-10-10 12:04:20'),
(55, 1, 'Add Stop', 'Added stop ID: 15 for route ID: 1 with location \'place i\', stop order 9, and arrival time \'03:48\'.', '2024-10-10 13:48:40'),
(56, 1, 'Add Stop Price', 'Added price for start stop ID: 1 and end stop ID: 15', '2024-10-10 13:49:05'),
(57, 1, 'Insert', 'Added schedule for Boat ID: 2, Route ID: 1, Departure Time: 2024-10-10T10:25.', '2024-10-10 13:49:51'),
(58, 1, 'Insert', 'Added schedule for Boat ID: 6, Route ID: 1, Departure Time: 2024-10-10T10:25.', '2024-10-10 13:50:48'),
(59, 1, 'Update', 'Updated schedule ID: 2. New Departure Time: 2024-10-10T21:00, New Arrival Time: 2024-10-10T00:00, New Status: Scheduled.', '2024-10-10 14:12:17'),
(60, 1, 'Update', 'Updated schedule ID: 6. New Departure Time: 2024-10-25T09:45, New Arrival Time: 2024-10-25T18:46, New Status: Scheduled.', '2024-10-25 11:44:55');

-- --------------------------------------------------------

--
-- Table structure for table `boats`
--

DROP TABLE IF EXISTS `boats`;
CREATE TABLE `boats` (
  `boat_id` int(11) NOT NULL,
  `boat_name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boats`
--

INSERT INTO `boats` (`boat_id`, `boat_name`, `capacity`, `status`) VALUES
(1, 'Waterway 1', 60, 'Available'),
(2, 'waterway 2', 100, 'Available'),
(3, 'waterway 3', 70, 'Available'),
(5, 'waterway 4', 40, 'Available'),
(6, 'waterway 5', 25, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `cancellations`
--

DROP TABLE IF EXISTS `cancellations`;
CREATE TABLE `cancellations` (
  `cancellation_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `cancellation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cancellations`
--

INSERT INTO `cancellations` (`cancellation_id`, `booking_id`, `cancellation_date`, `reason`) VALUES
(1, 16, '2024-09-19 11:56:13', NULL),
(2, 16, '2024-09-19 11:57:02', NULL),
(3, 47, '2024-09-19 12:01:14', NULL),
(4, 48, '2024-09-19 12:01:25', NULL),
(5, 48, '2024-09-19 12:01:27', NULL),
(6, 20, '2024-09-19 12:13:29', NULL),
(7, 47, '2024-09-19 12:14:36', NULL),
(8, 48, '2024-09-19 12:14:40', NULL),
(9, 49, '2024-09-19 12:14:42', NULL),
(19, 103, '2024-10-03 20:56:02', NULL),
(20, 104, '2024-10-03 20:58:40', NULL),
(21, 106, '2024-10-03 21:06:05', NULL),
(22, 107, '2024-10-03 21:22:02', NULL),
(23, 108, '2024-10-03 21:22:16', NULL),
(24, 109, '2024-10-03 21:24:37', NULL),
(25, 110, '2024-10-03 21:26:56', NULL),
(26, 111, '2024-10-03 21:35:58', NULL),
(27, 112, '2024-10-03 21:44:42', NULL),
(28, 113, '2024-10-03 21:48:21', NULL),
(29, 114, '2024-10-03 21:56:05', NULL),
(30, 1, '2024-10-03 22:56:22', NULL),
(31, 6, '2024-10-03 23:10:42', NULL),
(32, 7, '2024-10-04 12:56:27', NULL),
(33, 5, '2024-10-04 12:58:58', NULL),
(34, 1, '2024-10-06 20:06:01', NULL),
(35, 64, '2024-10-25 13:36:11', NULL),
(36, 64, '2024-10-25 13:45:20', NULL),
(37, 65, '2024-10-25 13:45:56', NULL),
(38, 63, '2024-10-25 13:46:07', NULL),
(39, 33, '2024-10-25 13:48:20', NULL),
(40, 67, '2024-10-25 14:17:02', NULL),
(41, 68, '2024-10-25 14:30:22', NULL),
(42, 69, '2024-10-25 14:30:22', NULL),
(43, 68, '2024-10-25 14:31:13', NULL),
(44, 69, '2024-10-25 14:31:13', NULL),
(45, 70, '2024-10-25 14:31:34', NULL),
(46, 71, '2024-10-25 14:31:34', NULL),
(49, 73, '2024-10-25 14:37:31', NULL),
(50, 76, '2024-10-25 14:43:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `amount`, `payment_method`, `payment_status`, `transaction_id`) VALUES
(1, 75.00, 'Credit Card', 'Paid', NULL),
(2, 50.00, 'Credit Card', 'Paid', NULL),
(3, 45.00, 'Credit Card', 'Paid', NULL),
(4, 45.00, 'Credit Card', 'Paid', NULL),
(5, 24.00, 'Credit Card', 'Paid', NULL),
(6, 24.00, 'Credit Card', 'Paid', NULL),
(7, 56.00, 'Credit Card', 'Paid', NULL),
(8, 56.00, 'Credit Card', 'Paid', NULL),
(9, 12.00, 'Credit Card', 'Paid', NULL),
(10, 12.00, 'Credit Card', 'Paid', NULL),
(11, 25.00, 'Credit Card', 'Paid', NULL),
(12, 25.00, 'Credit Card', 'Paid', NULL),
(13, 25.00, 'Credit Card', 'Paid', NULL),
(14, 75.00, 'Credit Card', 'Paid', NULL),
(15, 24.00, 'Credit Card', 'Paid', NULL),
(16, 12.00, 'Credit Card', 'Paid', NULL),
(17, 24.00, 'Credit Card', 'Paid', NULL),
(18, 36.00, 'Credit Card', 'Paid', NULL),
(19, 36.00, 'Credit Card', 'Paid', NULL),
(20, 24.00, 'Credit Card', 'Paid', NULL),
(21, 24.00, 'Credit Card', 'Paid', NULL),
(22, 24.00, 'Credit Card', 'Paid', NULL),
(23, 12.00, 'Credit Card', 'Paid', NULL),
(24, 90.00, 'Credit Card', 'Paid', NULL),
(25, 36.00, 'Credit Card', 'Paid', NULL),
(26, 56.00, 'Credit Card', 'Paid', NULL),
(27, 12.00, 'Credit Card', 'Paid', NULL),
(28, 12.00, 'Credit Card', 'Paid', NULL),
(29, 12.00, 'Credit Card', 'Paid', NULL),
(30, 12.00, 'Credit Card', 'Paid', NULL),
(31, 12.00, 'Credit Card', 'Paid', NULL),
(32, 12.00, 'Credit Card', 'Paid', NULL),
(33, 12.00, 'Credit Card', 'Paid', NULL),
(34, 12.00, 'Credit Card', 'Paid', NULL),
(35, 12.00, 'Credit Card', 'Paid', NULL),
(36, 12.00, 'Credit Card', 'Paid', NULL),
(37, 12.00, 'Credit Card', 'Paid', NULL),
(38, 12.00, 'Credit Card', 'Paid', NULL),
(39, 12.00, 'Credit Card', 'Paid', NULL),
(40, 12.00, 'Credit Card', 'Paid', NULL),
(41, 12.00, 'Credit Card', 'Paid', NULL),
(42, 12.00, 'Credit Card', 'Paid', NULL),
(43, 28.00, 'Credit Card', 'Paid', NULL),
(44, 21.00, 'Credit Card', 'Paid', NULL),
(45, 12.00, 'Credit Card', 'Paid', NULL),
(46, 56.00, 'Credit Card', 'Paid', NULL),
(47, 24.00, 'Credit Card', 'Paid', NULL),
(48, 12.00, 'Credit Card', 'Paid', NULL),
(49, 12.00, 'Credit Card', 'Paid', NULL),
(50, 12.00, 'Credit Card', 'Paid', NULL),
(51, 12.00, 'Credit Card', 'Paid', NULL),
(52, 25.00, 'Credit Card', 'Paid', NULL),
(53, 50.00, 'Credit Card', 'Paid', NULL),
(54, 25.00, 'Credit Card', 'Paid', NULL),
(55, 25.00, 'Credit Card', 'Paid', NULL),
(56, 175.00, 'Credit Card', 'Paid', NULL),
(57, 125.00, 'Credit Card', 'Paid', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL,
  `route_name` varchar(255) NOT NULL,
  `start_location` varchar(255) NOT NULL,
  `end_location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`route_id`, `route_name`, `start_location`, `end_location`) VALUES
(1, 'Lakeside', 'Place A', 'Place H'),
(2, 'Coastal', 'Coast A', 'Coast D');

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

DROP TABLE IF EXISTS `route_stops`;
CREATE TABLE `route_stops` (
  `stop_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `stop_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_stops`
--

INSERT INTO `route_stops` (`stop_id`, `route_id`, `location`, `stop_order`) VALUES
(1, 1, 'Place A', 1),
(2, 1, 'Place B', 2),
(3, 1, 'Place C', 3),
(4, 1, 'Place D', 4),
(5, 1, 'Place E', 5),
(6, 1, 'Place F', 6),
(7, 1, 'Place G', 7),
(8, 1, 'Place H', 8),
(9, 2, 'Coast A', 1),
(10, 2, 'Coast B', 2),
(11, 2, 'Coast C', 3),
(12, 2, 'Coast D', 4),
(15, 1, 'place i', 9);

-- --------------------------------------------------------

--
-- Table structure for table `route_stop_times`
--

DROP TABLE IF EXISTS `route_stop_times`;
CREATE TABLE `route_stop_times` (
  `route_id` int(11) NOT NULL,
  `stop_id` int(11) NOT NULL,
  `arrival_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route_stop_times`
--

INSERT INTO `route_stop_times` (`route_id`, `stop_id`, `arrival_time`) VALUES
(1, 1, '00:00:00'),
(1, 2, '00:20:00'),
(1, 3, '00:30:00'),
(1, 4, '00:40:00'),
(1, 5, '01:10:00'),
(1, 6, '01:40:00'),
(1, 7, '01:40:00'),
(1, 8, '02:00:00'),
(1, 15, '03:48:00'),
(2, 9, '00:00:00'),
(2, 10, '01:15:00'),
(2, 11, '01:30:00'),
(2, 12, '00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `boat_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `boat_id`, `route_id`, `departure_time`, `arrival_time`, `status`) VALUES
(1, 1, 1, '2024-10-05 11:00:00', '2024-10-05 13:31:00', 'Unscheduled'),
(2, 1, 1, '2024-10-10 21:00:00', '2024-10-10 00:00:00', 'Scheduled'),
(4, 1, 1, '2024-09-11 05:00:00', '2024-09-11 09:00:00', 'Scheduled'),
(6, 1, 2, '2024-10-25 09:45:00', '2024-10-25 18:46:00', 'Scheduled'),
(8, 1, 1, '2024-09-11 10:00:00', '2024-09-11 14:25:00', 'Scheduled'),
(17, 1, 2, '2024-09-01 14:46:00', '2024-09-01 17:49:00', 'Scheduled'),
(18, 2, 2, '2024-10-10 10:00:00', '2024-10-10 14:38:00', 'Scheduled'),
(20, 1, 1, '2024-09-25 14:02:00', '2024-09-25 18:03:00', 'Scheduled'),
(21, 1, 2, '2024-09-25 19:40:00', '2024-09-25 20:43:00', 'Scheduled'),
(22, 3, 2, '2024-10-04 12:00:00', '2024-10-04 22:51:00', 'Scheduled'),
(23, 2, 2, '2024-10-04 05:28:00', '2024-10-04 16:28:00', 'Scheduled'),
(24, 3, 2, '2024-10-04 05:55:00', '2024-10-04 12:30:00', 'Scheduled'),
(25, 6, 2, '2024-10-04 13:02:00', '2024-10-04 20:03:00', 'Scheduled'),
(26, 6, 2, '2024-10-05 16:09:00', '2024-10-05 19:12:00', 'Scheduled'),
(27, 6, 1, '2024-10-19 13:08:00', '2024-10-19 19:13:00', 'Scheduled'),
(28, 2, 1, '2024-10-10 10:25:00', '2024-10-10 16:52:00', 'Scheduled'),
(29, 6, 1, '2024-10-10 10:25:00', '2024-10-10 17:54:00', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

DROP TABLE IF EXISTS `seats`;
CREATE TABLE `seats` (
  `seat_id` int(11) NOT NULL,
  `boat_id` int(11) NOT NULL,
  `seat_number` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'Regular'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `boat_id`, `seat_number`, `type`) VALUES
(1, 1, '1', 'Standard'),
(2, 1, '2', 'Standard'),
(3, 1, '3', 'Standard'),
(4, 1, '4', 'Standard'),
(5, 1, '5', 'Standard'),
(6, 1, '6', 'Standard'),
(7, 1, '7', 'Standard'),
(8, 1, '8', 'Standard'),
(9, 1, '9', 'Standard'),
(10, 1, '10', 'Standard'),
(11, 1, '11', 'Standard'),
(12, 1, '11', 'Standard'),
(13, 1, '13', 'Standard'),
(61, 1, '14', 'Regular'),
(62, 1, '15', 'Regular'),
(63, 1, '16', 'Regular'),
(64, 1, '17', 'Regular'),
(65, 1, '18', 'Regular'),
(66, 1, '19', 'Regular'),
(67, 1, '20', 'Regular'),
(68, 1, '21', 'Regular'),
(69, 1, '22', 'Regular'),
(70, 1, '23', 'Regular'),
(71, 1, '24', 'Regular'),
(72, 1, '25', 'Regular'),
(73, 1, '26', 'Regular'),
(74, 1, '27', 'Regular'),
(75, 1, '28', 'Vip'),
(76, 1, '29', 'Regular'),
(77, 1, '30', 'Regular'),
(78, 1, '31', 'Regular'),
(79, 1, '32', 'Regular'),
(80, 1, '33', 'Regular'),
(81, 1, '34', 'Regular'),
(82, 1, '35', 'Regular'),
(83, 1, '99', 'Regular'),
(84, 1, '37', 'Regular'),
(85, 1, '38', 'Regular'),
(86, 1, '39', 'Regular'),
(87, 1, '40', 'Regular'),
(88, 1, '41', 'Regular'),
(89, 1, '42', 'Regular'),
(90, 1, '43', 'Regular'),
(91, 1, '44', 'Regular'),
(92, 1, '45', 'Regular'),
(93, 1, '46', 'Regular'),
(94, 1, '47', 'Regular'),
(95, 1, '48', 'Regular'),
(96, 1, '49', 'Regular'),
(98, 1, '51', 'Regular'),
(99, 1, '52', 'Regular'),
(100, 1, '53', 'Regular'),
(101, 1, '54', 'Regular'),
(102, 1, '55', 'Regular'),
(103, 1, '56', 'Regular'),
(104, 1, '57', 'Regular'),
(105, 1, '58', 'Regular'),
(106, 1, '59', 'Regular'),
(107, 1, '60', 'Regular'),
(108, 2, '1', 'Regular'),
(109, 2, '2', 'Regular'),
(110, 2, '3', 'Regular'),
(111, 2, '4', 'Regular'),
(112, 2, '5', 'Regular'),
(113, 2, '6', 'Regular'),
(114, 2, '7', 'Regular'),
(115, 2, '8', 'Regular'),
(116, 2, '9', 'Regular'),
(117, 2, '10', 'Regular'),
(118, 2, '11', 'Regular'),
(119, 2, '12', 'Regular'),
(120, 2, '13', 'Regular'),
(121, 2, '14', 'Regular'),
(122, 2, '15', 'Regular'),
(123, 2, '16', 'Regular'),
(124, 2, '17', 'Regular'),
(125, 2, '18', 'Regular'),
(126, 2, '19', 'Regular'),
(127, 2, '20', 'Regular'),
(128, 2, '21', 'Regular'),
(129, 2, '22', 'Regular'),
(130, 2, '23', 'Regular'),
(131, 2, '24', 'Regular'),
(132, 2, '25', 'Regular'),
(133, 2, '26', 'Regular'),
(134, 2, '27', 'Regular'),
(135, 2, '28', 'Regular'),
(136, 2, '29', 'Regular'),
(137, 2, '30', 'Regular'),
(138, 2, '31', 'Regular'),
(139, 2, '32', 'Regular'),
(140, 2, '33', 'Regular'),
(141, 2, '34', 'Regular'),
(142, 2, '35', 'Regular'),
(143, 2, '36', 'Regular'),
(144, 2, '37', 'Regular'),
(145, 2, '38', 'Regular'),
(146, 2, '39', 'Regular'),
(147, 2, '40', 'Regular'),
(148, 2, '41', 'Regular'),
(149, 2, '42', 'Regular'),
(150, 2, '43', 'Regular'),
(151, 2, '44', 'Regular'),
(152, 2, '45', 'Regular'),
(153, 2, '46', 'Regular'),
(154, 2, '47', 'Regular'),
(155, 2, '48', 'Regular'),
(156, 2, '49', 'Regular'),
(157, 2, '50', 'Regular'),
(158, 2, '51', 'Regular'),
(159, 2, '52', 'Regular'),
(160, 2, '53', 'Regular'),
(161, 2, '54', 'Regular'),
(162, 2, '55', 'Regular'),
(163, 2, '56', 'Regular'),
(164, 2, '57', 'Regular'),
(165, 2, '58', 'Regular'),
(166, 2, '59', 'Regular'),
(167, 2, '60', 'Regular'),
(168, 2, '61', 'Regular'),
(169, 2, '62', 'Regular'),
(170, 2, '63', 'Regular'),
(171, 2, '64', 'Regular'),
(172, 2, '65', 'Regular'),
(173, 2, '66', 'Regular'),
(174, 2, '67', 'Regular'),
(175, 2, '68', 'Regular'),
(176, 2, '69', 'Regular'),
(177, 2, '70', 'Regular'),
(178, 2, '71', 'Regular'),
(179, 2, '72', 'Regular'),
(180, 2, '73', 'Regular'),
(181, 2, '74', 'Regular'),
(182, 2, '75', 'Regular'),
(183, 2, '76', 'Regular'),
(184, 2, '77', 'Regular'),
(185, 2, '78', 'Regular'),
(186, 2, '79', 'Regular'),
(187, 2, '80', 'Regular'),
(188, 2, '81', 'Regular'),
(189, 2, '82', 'Regular'),
(190, 2, '83', 'Regular'),
(191, 2, '84', 'Regular'),
(192, 2, '85', 'Regular'),
(193, 2, '86', 'Regular'),
(194, 2, '87', 'Regular'),
(195, 2, '88', 'Regular'),
(196, 2, '89', 'Regular'),
(197, 2, '90', 'Regular'),
(198, 2, '91', 'Regular'),
(199, 2, '92', 'Regular'),
(200, 2, '93', 'Regular'),
(201, 2, '94', 'Regular'),
(202, 2, '95', 'Regular'),
(204, 2, '97', 'Regular'),
(205, 2, '98', 'Regular'),
(209, 1, '60', 'Regular'),
(210, 2, '98', 'Regular'),
(211, 2, '99', 'Regular'),
(212, 2, '100', 'Regular'),
(213, 3, '1', 'Regular'),
(214, 3, '2', 'Regular'),
(215, 3, '3', 'Regular'),
(216, 3, '4', 'Regular'),
(217, 3, '5', 'Regular'),
(218, 3, '6', 'Regular'),
(219, 3, '7', 'Regular'),
(220, 3, '8', 'Regular'),
(221, 3, '9', 'Regular'),
(222, 3, '10', 'Regular'),
(223, 3, '11', 'Regular'),
(224, 3, '12', 'Regular'),
(225, 3, '13', 'Regular'),
(226, 3, '14', 'Regular'),
(227, 3, '15', 'Regular'),
(228, 3, '16', 'Regular'),
(229, 3, '17', 'Regular'),
(230, 3, '18', 'Regular'),
(231, 3, '19', 'Regular'),
(232, 3, '20', 'Regular'),
(233, 3, '21', 'Regular'),
(234, 3, '22', 'Regular'),
(235, 3, '23', 'Regular'),
(236, 3, '24', 'Regular'),
(237, 3, '25', 'Regular'),
(238, 3, '26', 'Regular'),
(239, 3, '27', 'Regular'),
(240, 3, '28', 'Regular'),
(241, 3, '29', 'Regular'),
(242, 3, '30', 'Regular'),
(243, 3, '31', 'Regular'),
(244, 3, '32', 'Regular'),
(245, 3, '33', 'Regular'),
(246, 3, '34', 'Regular'),
(247, 3, '35', 'Regular'),
(248, 3, '36', 'Regular'),
(249, 3, '37', 'Regular'),
(250, 3, '38', 'Regular'),
(251, 3, '39', 'Regular'),
(252, 3, '40', 'Regular'),
(253, 3, '41', 'Regular'),
(254, 3, '42', 'Regular'),
(255, 3, '43', 'Regular'),
(256, 3, '44', 'Regular'),
(257, 3, '45', 'Regular'),
(258, 3, '46', 'Regular'),
(259, 3, '47', 'Regular'),
(260, 3, '48', 'Regular'),
(261, 3, '49', 'Regular'),
(262, 3, '50', 'Regular'),
(263, 3, '51', 'Regular'),
(264, 3, '52', 'Regular'),
(265, 3, '53', 'Regular'),
(266, 3, '54', 'Regular'),
(267, 3, '55', 'Regular'),
(268, 3, '56', 'Regular'),
(269, 3, '57', 'Regular'),
(270, 3, '58', 'Regular'),
(271, 3, '59', 'Regular'),
(272, 3, '60', 'Regular'),
(273, 3, '61', 'Regular'),
(274, 3, '62', 'Regular'),
(275, 3, '63', 'Regular'),
(276, 3, '64', 'Regular'),
(277, 3, '65', 'Regular'),
(278, 3, '66', 'Regular'),
(279, 3, '67', 'Regular'),
(280, 3, '68', 'Regular'),
(281, 3, '69', 'Regular'),
(282, 3, '70', 'Regular'),
(283, 5, '1', 'vip'),
(284, 5, '2', 'Regular'),
(285, 5, '3', 'Regular'),
(286, 5, '4', 'Regular'),
(287, 5, '5', 'Regular'),
(288, 5, '6', 'Regular'),
(289, 5, '7', 'Regular'),
(291, 5, '9', 'Regular'),
(292, 5, '10', 'Regular'),
(293, 5, '11', 'Regular'),
(294, 5, '12', 'Regular'),
(295, 5, '13', 'Regular'),
(296, 5, '14', 'Regular'),
(297, 5, '99', 'Regular'),
(298, 5, '16', 'Regular'),
(299, 5, '17', 'Regular'),
(300, 5, '18', 'Regular'),
(301, 5, '19', 'Regular'),
(302, 5, '20', 'Regular'),
(303, 5, '21', 'Regular'),
(304, 5, '22', 'Regular'),
(305, 5, '23', 'Regular'),
(306, 5, '24', 'Regular'),
(307, 5, '25', 'Regular'),
(308, 5, '26', 'Regular'),
(310, 5, '28', 'Regular'),
(311, 5, '29', 'Regular'),
(312, 5, '30', 'Regular'),
(313, 5, '31', 'Regular'),
(314, 5, '32', 'Regular'),
(315, 5, '33', 'Regular'),
(316, 5, '34', 'Regular'),
(317, 5, '35', 'Regular'),
(318, 5, '36', 'Regular'),
(319, 5, '37', 'Regular'),
(320, 5, '38', 'Regular'),
(321, 5, '39', 'Regular'),
(322, 5, '40', 'Regular'),
(323, 6, '1', 'Regular'),
(324, 6, '2', 'Regular'),
(325, 6, '3', 'Regular'),
(326, 6, '4', 'Regular'),
(327, 6, '5', 'Regular'),
(328, 6, '6', 'Regular'),
(329, 6, '7', 'Regular'),
(330, 6, '8', 'Regular'),
(331, 6, '9', 'Regular'),
(332, 6, '10', 'Regular'),
(333, 6, '11', 'Regular'),
(334, 6, '12', 'Regular'),
(335, 6, '13', 'Regular'),
(336, 6, '14', 'Regular'),
(337, 6, '15', 'Regular'),
(338, 6, '16', 'Regular'),
(339, 6, '17', 'Regular'),
(340, 6, '18', 'Regular'),
(341, 6, '19', 'Regular'),
(342, 6, '20', 'Regular'),
(343, 6, '21', 'Regular'),
(344, 6, '22', 'Regular'),
(345, 6, '23', 'Regular'),
(346, 6, '24', 'Regular'),
(347, 6, '25', 'Regular');

-- --------------------------------------------------------

--
-- Table structure for table `seat_bookings`
--

DROP TABLE IF EXISTS `seat_bookings`;
CREATE TABLE `seat_bookings` (
  `booking_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `start_stop_id` int(11) NOT NULL,
  `end_stop_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_id` int(11) DEFAULT NULL,
  `boat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat_bookings`
--

INSERT INTO `seat_bookings` (`booking_id`, `schedule_id`, `user_id`, `seat_id`, `start_stop_id`, `end_stop_id`, `booking_date`, `payment_status`, `payment_id`, `boat_id`) VALUES
(1, 1, 2, 1, 1, 3, '2024-10-05 12:55:46', 'Cancelled', 7, 1),
(2, 1, 2, 2, 1, 3, '2024-10-05 12:55:46', 'Paid', 7, 1),
(3, 1, 2, 1, 1, 3, '2024-10-05 14:45:48', 'Paid', 8, 1),
(4, 1, 2, 2, 1, 3, '2024-10-05 14:45:48', 'Paid', 8, 1),
(5, 1, 2, 72, 1, 2, '2024-10-05 15:57:48', 'Paid', 9, 1),
(6, 1, 2, 7, 1, 2, '2024-10-05 15:58:44', 'Paid', 10, 1),
(7, 26, 2, 323, 9, 10, '2024-10-06 10:38:09', 'Paid', 11, 6),
(8, 26, 2, 324, 9, 10, '2024-10-06 10:42:39', 'Paid', 12, 6),
(9, 26, 2, 325, 9, 10, '2024-10-06 10:59:04', 'Paid', 13, 6),
(10, 26, 2, 328, 9, 10, '2024-10-06 11:00:43', 'Paid', 14, 6),
(11, 26, 2, 329, 9, 10, '2024-10-06 11:00:43', 'Paid', 14, 6),
(12, 26, 2, 330, 9, 10, '2024-10-06 11:00:43', 'Paid', 14, 6),
(13, 1, 2, 5, 1, 2, '2024-10-06 14:33:38', 'Paid', 15, 1),
(14, 1, 2, 6, 1, 2, '2024-10-06 14:33:38', 'Paid', 15, 1),
(15, 1, 2, 91, 1, 2, '2024-10-06 14:38:42', 'Paid', 16, 1),
(16, 1, 2, 3, 1, 2, '2024-10-06 14:45:51', 'Paid', 17, 1),
(17, 1, 2, 12, 1, 2, '2024-10-06 14:45:51', 'Paid', 17, 1),
(18, 1, 2, 4, 1, 2, '2024-10-06 16:08:43', 'Paid', 18, 1),
(19, 1, 2, 13, 1, 2, '2024-10-06 16:08:43', 'Paid', 18, 1),
(20, 1, 2, 61, 1, 2, '2024-10-06 16:08:43', 'Paid', 18, 1),
(21, 1, 2, 78, 1, 2, '2024-10-06 17:08:55', 'Paid', 19, 1),
(22, 1, 2, 79, 1, 2, '2024-10-06 17:08:55', 'Paid', 19, 1),
(23, 1, 2, 80, 1, 2, '2024-10-06 17:08:55', 'Paid', 19, 1),
(24, 1, 2, 105, 1, 2, '2024-10-06 17:23:18', 'Paid', 20, 1),
(25, 1, 2, 107, 1, 2, '2024-10-06 17:23:18', 'Paid', 20, 1),
(26, 1, 2, 8, 1, 2, '2024-10-06 19:53:11', 'Paid', 21, 1),
(27, 1, 2, 9, 1, 2, '2024-10-06 19:53:11', 'Paid', 21, 1),
(28, 1, 2, 10, 1, 2, '2024-10-06 20:02:27', 'Paid', 22, 1),
(29, 1, 2, 11, 1, 2, '2024-10-06 20:02:27', 'Paid', 22, 1),
(30, 2, 2, 1, 1, 2, '2024-10-10 11:08:19', 'Paid', 23, 1),
(31, 2, 8, 67, 2, 7, '2024-10-10 12:08:14', 'Paid', 24, 1),
(32, 2, 8, 106, 2, 7, '2024-10-10 12:08:14', 'Paid', 24, 1),
(33, 2, 2, 2, 1, 2, '2024-10-10 13:45:17', 'Cancelled', 25, 1),
(34, 2, 2, 3, 1, 2, '2024-10-10 13:45:17', 'Paid', 25, 1),
(35, 2, 2, 11, 1, 2, '2024-10-10 13:45:17', 'Paid', 25, 1),
(36, 2, 2, 101, 1, 15, '2024-10-10 13:53:10', 'Paid', 26, 1),
(37, 2, 2, 10, 1, 2, '2024-10-10 13:54:02', 'Paid', 27, 1),
(38, 1, 2, 65, 1, 2, '2024-10-19 09:33:23', 'Paid', 28, 1),
(39, 1, 2, 62, 1, 2, '2024-10-19 09:44:13', 'Paid', 29, 1),
(40, 1, 2, 62, 1, 2, '2024-10-19 09:53:42', 'Paid', 30, 1),
(41, 1, 2, 62, 1, 2, '2024-10-19 09:53:59', 'Paid', 31, 1),
(42, 1, 2, 62, 1, 2, '2024-10-19 09:54:21', 'Paid', 32, 1),
(43, 1, 2, 67, 1, 2, '2024-10-19 10:05:50', 'Paid', 33, 1),
(44, 1, 2, 63, 1, 2, '2024-10-19 11:07:09', 'Paid', 34, 1),
(45, 1, 2, 63, 1, 2, '2024-10-19 11:07:09', 'Paid', 35, 1),
(46, 1, 2, 68, 1, 2, '2024-10-19 11:16:16', 'Paid', 36, 1),
(47, 1, 6, 64, 1, 2, '2024-10-19 11:17:10', 'Paid', 37, 1),
(48, 1, 6, 69, 1, 2, '2024-10-19 11:24:45', 'Paid', 38, 1),
(49, 1, 2, 70, 1, 2, '2024-10-19 11:27:09', 'Paid', 39, 1),
(50, 1, 6, 71, 1, 2, '2024-10-19 11:30:07', 'Paid', 40, 1),
(51, 1, 6, 66, 1, 2, '2024-10-19 11:30:50', 'Paid', 41, 1),
(52, 1, 6, 75, 1, 2, '2024-10-19 11:31:53', 'Paid', 42, 1),
(53, 1, 6, 76, 1, 3, '2024-10-19 11:35:57', 'Paid', 43, 1),
(54, 1, 6, 73, 2, 3, '2024-10-19 11:36:47', 'Paid', 44, 1),
(55, 1, 2, 73, 1, 2, '2024-10-19 11:36:48', 'Paid', 45, 1),
(56, 1, 6, 85, 1, 3, '2024-10-19 11:39:46', 'Paid', 46, 1),
(57, 1, 6, 86, 1, 3, '2024-10-19 11:39:46', 'Paid', 46, 1),
(58, 1, 2, 84, 1, 2, '2024-10-19 11:39:47', 'Paid', 47, 1),
(59, 1, 6, 81, 1, 2, '2024-10-19 11:44:01', 'Paid', 48, 1),
(60, 1, 2, 87, 1, 2, '2024-10-19 11:59:59', 'Paid', 49, 1),
(61, 1, 2, 77, 1, 2, '2024-10-19 12:00:21', 'Paid', 50, 1),
(62, 1, 2, 83, 1, 2, '2024-10-19 12:02:36', 'Paid', 51, 1),
(63, 6, 2, 1, 9, 10, '2024-10-25 11:46:01', 'Cancelled', 52, 1),
(64, 6, 2, 2, 9, 10, '2024-10-25 11:51:39', 'Cancelled', 53, 1),
(65, 6, 2, 3, 9, 10, '2024-10-25 11:51:39', 'Cancelled', 53, 1),
(66, 6, 6, 4, 9, 10, '2024-10-25 12:49:41', 'Paid', 54, 1),
(67, 6, 2, 12, 9, 10, '2024-10-25 13:53:34', 'Cancelled', 55, 1),
(68, 6, 2, 93, 9, 10, '2024-10-25 14:19:40', 'Cancelled', 56, 1),
(69, 6, 2, 94, 9, 10, '2024-10-25 14:19:40', 'Cancelled', 56, 1),
(70, 6, 2, 95, 9, 10, '2024-10-25 14:19:40', 'Cancelled', 56, 1),
(71, 6, 2, 96, 9, 10, '2024-10-25 14:19:40', 'Cancelled', 56, 1),
(73, 6, 2, 104, 9, 10, '2024-10-25 14:19:40', 'Cancelled', 56, 1),
(76, 6, 2, 100, 9, 10, '2024-10-25 14:43:08', 'Cancelled', 57, 1),
(77, 6, 2, 101, 9, 10, '2024-10-25 14:43:08', 'Paid', 57, 1),
(78, 6, 2, 102, 9, 10, '2024-10-25 14:43:08', 'Paid', 57, 1),
(79, 6, 2, 107, 9, 10, '2024-10-25 14:43:08', 'Paid', 57, 1),
(80, 6, 2, 209, 9, 10, '2024-10-25 14:43:08', 'Paid', 57, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stop_pricing`
--

DROP TABLE IF EXISTS `stop_pricing`;
CREATE TABLE `stop_pricing` (
  `id` int(11) NOT NULL,
  `start_stop_id` int(11) NOT NULL,
  `end_stop_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stop_pricing`
--

INSERT INTO `stop_pricing` (`id`, `start_stop_id`, `end_stop_id`, `price`) VALUES
(1, 1, 2, 12.00),
(3, 1, 3, 28.00),
(4, 1, 4, 31.00),
(6, 2, 3, 21.00),
(10, 2, 4, 35.00),
(11, 9, 12, 45.00),
(12, 9, 10, 25.00),
(13, 9, 12, 70.00),
(14, 9, 12, 70.00),
(15, 11, 12, 35.00),
(16, 10, 12, 30.00),
(17, 5, 7, 29.00),
(18, 5, 7, 15.00),
(19, 3, 6, 45.00),
(20, 4, 6, 23.00),
(21, 4, 7, 12.00),
(22, 6, 7, 56.00),
(23, 6, 8, 35.00),
(24, 7, 8, 35.00),
(25, 2, 7, 45.00),
(26, 1, 15, 56.00);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `booking_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `booking_id`, `amount`, `user_id`) VALUES
(5, '10,11,12', 75.00, 2),
(6, '16,17', 24.00, 2),
(7, '18,19,20', 36.00, 2),
(8, '21,22,23', 36.00, 2),
(9, '24,25', 24.00, 2),
(10, '26,27', 24.00, 2),
(11, '28,29', 24.00, 2),
(12, '30', 12.00, 2),
(13, '31,32', 90.00, 8),
(14, '34,35', 36.00, 2),
(15, '36', 56.00, 2),
(16, '37', 12.00, 2),
(17, '40', 12.00, 2),
(18, '41', 12.00, 2),
(19, '42', 12.00, 2),
(20, '43', 12.00, 2),
(21, '45', 12.00, 2),
(22, '44', 12.00, 2),
(23, '49', 12.00, 2),
(24, '55', 12.00, 2),
(25, '56,57', 56.00, 6),
(26, '58', 24.00, 2),
(27, '62', 12.00, 2),
(30, '66', 25.00, 6),
(32, '68,69,70,71,73', -50.00, 2),
(33, '77,78,79,80', 100.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone_number`, `address`, `role`) VALUES
(1, 'Deepak Joshy', 'deepakjoshy1771@gmail.com', '$2y$10$9deXTnuOUSZbkgBKkLbf7./iVCzz311599bba9TuF6Z.D.0t0E.nG', '9497384572', 'Puthanthara House', 'Admin'),
(2, 'Deepak Joshy', 'deepakjoshy17@gmail.com', '$2y$10$fs3Qis572ysVRkYgBobL4e5i9J4mFlQwQTskfI.m2qWwrD/DMGisq', '9497384572', 'Puthanthara House', 'User'),
(5, 'Deeptha', 'appujoshysindhu17@gmail.com', '$2y$10$OfKRqOp6LFKNTQpCUcA.ruq.SomdaeHQUUrypC5f3HFWJSwcPFPwe', '1234567890', 'AW', 'User'),
(6, 'Alan', 'appujoshy8@gmail.com', '$2y$10$6IULRbZaqYs87qK0k6bGZO52/UlcTurNxcBgutBvdSaJEoqis.oh2', '9961617599', 'AWQA', 'User'),
(7, 'midhun', 'midhunpsunil142@gmail.com', '$2y$10$kbJxlQG1MfYo2yGF2UAEG.Zls5oR7MgkQjiFdOUYToWQEoL44qp8W', '8590169721', 'kuppethazham ,Thiruvaniyoor', 'User'),
(8, 'Ben Yohannan', 'benyohannanillickal@gmail.com', '$2y$10$mXpiKfenSW6umillKz3aXu7h0KEFtPVCH64Vmohs7PQgXEmv56h9u', '7558088553', 'illickal house', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `boats`
--
ALTER TABLE `boats`
  ADD PRIMARY KEY (`boat_id`);

--
-- Indexes for table `cancellations`
--
ALTER TABLE `cancellations`
  ADD PRIMARY KEY (`cancellation_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`stop_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `route_stop_times`
--
ALTER TABLE `route_stop_times`
  ADD PRIMARY KEY (`route_id`,`stop_id`),
  ADD KEY `stop_id` (`stop_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `boat_id` (`boat_id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`),
  ADD KEY `boat_id` (`boat_id`);

--
-- Indexes for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seat_id` (`seat_id`),
  ADD KEY `start_stop_id` (`start_stop_id`),
  ADD KEY `end_stop_id` (`end_stop_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `boat_id` (`boat_id`);

--
-- Indexes for table `stop_pricing`
--
ALTER TABLE `stop_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `start_stop_id` (`start_stop_id`),
  ADD KEY `end_stop_id` (`end_stop_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `boats`
--
ALTER TABLE `boats`
  MODIFY `boat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cancellations`
--
ALTER TABLE `cancellations`
  MODIFY `cancellation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `stop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `stop_pricing`
--
ALTER TABLE `stop_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `cancellations`
--
ALTER TABLE `cancellations`
  ADD CONSTRAINT `cancellations_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `seat_bookings` (`booking_id`);

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `route_stops_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);

--
-- Constraints for table `route_stop_times`
--
ALTER TABLE `route_stop_times`
  ADD CONSTRAINT `route_stop_times_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`),
  ADD CONSTRAINT `route_stop_times_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `route_stops` (`stop_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`boat_id`) REFERENCES `boats` (`boat_id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`);

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`boat_id`) REFERENCES `boats` (`boat_id`);

--
-- Constraints for table `seat_bookings`
--
ALTER TABLE `seat_bookings`
  ADD CONSTRAINT `seat_bookings_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_4` FOREIGN KEY (`start_stop_id`) REFERENCES `route_stops` (`stop_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_5` FOREIGN KEY (`end_stop_id`) REFERENCES `route_stops` (`stop_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_6` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`),
  ADD CONSTRAINT `seat_bookings_ibfk_7` FOREIGN KEY (`boat_id`) REFERENCES `boats` (`boat_id`);

--
-- Constraints for table `stop_pricing`
--
ALTER TABLE `stop_pricing`
  ADD CONSTRAINT `stop_pricing_ibfk_1` FOREIGN KEY (`start_stop_id`) REFERENCES `route_stops` (`stop_id`),
  ADD CONSTRAINT `stop_pricing_ibfk_2` FOREIGN KEY (`end_stop_id`) REFERENCES `route_stops` (`stop_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
