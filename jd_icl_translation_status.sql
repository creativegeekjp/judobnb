-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 23, 2016 at 07:43 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `judobnb`
--

-- --------------------------------------------------------

--
-- Table structure for table `jd_icl_translation_status`
--

CREATE TABLE `jd_icl_translation_status` (
  `rid` bigint(20) NOT NULL,
  `translation_id` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `translator_id` bigint(20) NOT NULL,
  `needs_update` tinyint(4) NOT NULL,
  `md5` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation_service` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` int(11) NOT NULL DEFAULT '0',
  `translation_package` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `links_fixed` tinyint(4) NOT NULL DEFAULT '0',
  `_prevstate` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jd_icl_translation_status`
--

INSERT INTO `jd_icl_translation_status` (`rid`, `translation_id`, `status`, `translator_id`, `needs_update`, `md5`, `translation_service`, `batch_id`, `translation_package`, `timestamp`, `links_fixed`, `_prevstate`) VALUES
(1, 940, 9, 0, 0, '', '', 0, '', '2016-05-12 06:47:48', 0, NULL),
(2, 983, 9, 0, 0, '', '', 0, '', '2016-05-13 02:22:08', 0, NULL),
(3, 1046, 9, 0, 0, '', '', 0, '', '2016-05-16 03:13:06', 0, NULL),
(4, 1061, 9, 0, 0, '', '', 0, '', '2016-05-16 03:18:45', 0, NULL),
(6, 1077, 9, 0, 0, '', '', 0, '', '2016-05-16 03:34:28', 0, NULL),
(7, 1078, 9, 0, 0, '', '', 0, '', '2016-05-16 03:35:15', 0, NULL),
(8, 1079, 9, 0, 0, '', '', 0, '', '2016-05-16 03:36:27', 0, NULL),
(9, 1109, 10, 0, 0, '', '', 0, '', '2016-05-16 07:52:20', 0, NULL),
(10, 1158, 9, 0, 0, '', '', 0, '', '2016-05-23 03:17:35', 0, NULL),
(11, 1159, 9, 0, 0, '', '', 0, '', '2016-05-23 03:27:39', 0, NULL),
(12, 1160, 9, 0, 0, '', '', 0, '', '2016-05-23 03:30:14', 0, NULL),
(13, 1161, 9, 0, 0, '', '', 0, '', '2016-05-23 03:32:46', 0, NULL),
(14, 1162, 9, 0, 0, '', '', 0, '', '2016-05-23 03:44:37', 0, NULL),
(15, 1163, 9, 0, 0, '', '', 0, '', '2016-05-23 03:50:31', 0, NULL),
(16, 1184, 9, 0, 0, '', '', 0, '', '2016-05-24 01:38:39', 0, NULL),
(17, 1185, 9, 0, 0, '', '', 0, '', '2016-05-24 01:39:59', 0, NULL),
(18, 1186, 9, 0, 0, '', '', 0, '', '2016-05-24 01:41:39', 0, NULL),
(19, 1187, 9, 0, 0, '', '', 0, '', '2016-05-24 01:42:50', 0, NULL),
(20, 1188, 9, 0, 0, '', '', 0, '', '2016-05-24 01:43:45', 0, NULL),
(21, 1189, 9, 0, 0, '', '', 0, '', '2016-05-24 01:45:18', 0, NULL),
(22, 1190, 9, 0, 0, '', '', 0, '', '2016-05-24 01:46:34', 0, NULL),
(23, 1191, 9, 0, 0, '', '', 0, '', '2016-05-24 01:47:25', 0, NULL),
(24, 1201, 9, 0, 0, '', '', 0, '', '2016-05-24 01:56:14', 0, NULL),
(25, 1202, 9, 0, 0, '', '', 0, '', '2016-05-24 01:57:27', 0, NULL),
(26, 1203, 9, 0, 0, '', '', 0, '', '2016-05-24 01:58:46', 0, NULL),
(27, 1204, 9, 0, 0, '', '', 0, '', '2016-05-24 02:00:20', 0, NULL),
(28, 1205, 9, 0, 0, '', '', 0, '', '2016-05-24 02:02:22', 0, NULL),
(32, 1217, 9, 0, 0, '', '', 0, '', '2016-05-24 07:07:27', 0, NULL),
(33, 1235, 9, 0, 0, '', '', 0, '', '2016-05-26 01:37:37', 0, NULL),
(34, 1236, 9, 0, 0, '', '', 0, '', '2016-05-26 01:47:17', 0, NULL),
(35, 1238, 9, 0, 0, '', '', 0, '', '2016-05-26 01:58:39', 0, NULL),
(36, 1240, 9, 0, 0, '', '', 0, '', '2016-05-26 01:59:36', 0, NULL),
(37, 1241, 9, 0, 0, '', '', 0, '', '2016-05-26 02:00:22', 0, NULL),
(38, 1242, 9, 0, 0, '', '', 0, '', '2016-05-26 02:02:49', 0, NULL),
(39, 1245, 10, 0, 0, '', '', 0, '', '2016-05-26 02:46:56', 0, NULL),
(40, 1379, 9, 0, 0, '', '', 0, '', '2016-05-26 05:32:35', 0, NULL),
(41, 1382, 9, 0, 0, '', '', 0, '', '2016-05-26 05:34:37', 0, NULL),
(42, 1384, 9, 0, 0, '', '', 0, '', '2016-05-26 05:35:41', 0, NULL),
(43, 1386, 9, 0, 0, '', '', 0, '', '2016-05-26 05:37:30', 0, NULL),
(44, 1388, 9, 0, 0, '', '', 0, '', '2016-05-26 05:38:56', 0, NULL),
(45, 1390, 9, 0, 0, '', '', 0, '', '2016-05-26 05:40:05', 0, NULL),
(46, 1392, 9, 0, 0, '', '', 0, '', '2016-05-26 05:41:06', 0, NULL),
(47, 1394, 9, 0, 0, '', '', 0, '', '2016-05-26 05:45:49', 0, NULL),
(48, 1527, 9, 0, 0, '', '', 0, '', '2016-05-27 08:45:43', 0, NULL),
(49, 1528, 9, 0, 0, '', '', 0, '', '2016-05-27 08:54:31', 0, NULL),
(50, 1529, 9, 0, 0, '', '', 0, '', '2016-05-27 08:57:23', 0, NULL),
(51, 1530, 9, 0, 0, '', '', 0, '', '2016-05-27 09:20:54', 0, NULL),
(52, 1533, 9, 0, 0, '', '', 0, '', '2016-05-27 09:30:03', 0, NULL),
(53, 1534, 9, 0, 0, '', '', 0, '', '2016-05-27 09:30:30', 0, NULL),
(54, 1535, 9, 0, 0, '', '', 0, '', '2016-05-27 09:31:40', 0, NULL),
(55, 1536, 9, 0, 0, '', '', 0, '', '2016-05-27 09:31:54', 0, NULL),
(56, 1539, 9, 0, 0, '', '', 0, '', '2016-05-27 09:32:55', 0, NULL),
(57, 1540, 9, 0, 0, '', '', 0, '', '2016-05-27 09:35:08', 0, NULL),
(58, 1541, 9, 0, 0, '', '', 0, '', '2016-05-27 09:35:48', 0, NULL),
(59, 1542, 9, 0, 0, '', '', 0, '', '2016-05-27 09:36:11', 0, NULL),
(60, 1543, 9, 0, 0, '', '', 0, '', '2016-05-27 09:36:30', 0, NULL),
(61, 1544, 9, 0, 0, '', '', 0, '', '2016-05-27 09:36:54', 0, NULL),
(62, 1545, 9, 0, 0, '', '', 0, '', '2016-05-27 09:37:14', 0, NULL),
(63, 1546, 9, 0, 0, '', '', 0, '', '2016-05-27 09:37:31', 0, NULL),
(64, 2007, 9, 0, 0, '', '', 0, '', '2016-06-14 09:49:46', 0, NULL),
(65, 2248, 9, 0, 0, '', '', 0, '', '2016-06-16 07:12:23', 0, NULL),
(66, 2274, 9, 0, 0, '', '', 0, '', '2016-06-21 07:49:07', 0, NULL),
(67, 2275, 9, 0, 0, '', '', 0, '', '2016-06-21 07:51:33', 0, NULL),
(68, 2276, 9, 0, 0, '', '', 0, '', '2016-06-21 08:16:19', 0, NULL),
(69, 2277, 9, 0, 0, '', '', 0, '', '2016-06-21 08:17:54', 0, NULL),
(70, 2278, 9, 0, 0, '', '', 0, '', '2016-06-21 08:21:50', 0, NULL),
(71, 2279, 9, 0, 0, '', '', 0, '', '2016-06-21 08:24:03', 0, NULL),
(72, 2309, 9, 0, 0, '', '', 0, '', '2016-06-23 06:47:41', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jd_icl_translation_status`
--
ALTER TABLE `jd_icl_translation_status`
  ADD PRIMARY KEY (`rid`),
  ADD UNIQUE KEY `translation_id` (`translation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jd_icl_translation_status`
--
ALTER TABLE `jd_icl_translation_status`
  MODIFY `rid` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
