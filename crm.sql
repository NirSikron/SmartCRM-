-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: ינואר 04, 2025 בזמן 08:06 PM
-- גרסת שרת: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm`
--

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `affiliations`
--

CREATE TABLE `affiliations` (
  `number_call` int(10) NOT NULL,
  `worker_id` int(9) NOT NULL,
  `date_call` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `calls`
--

CREATE TABLE `calls` (
  `number_call` int(10) NOT NULL,
  `worker_id` int(10) NOT NULL,
  `department_id` int(3) NOT NULL,
  `Content_call` text NOT NULL,
  `PICUTRE` mediumblob NOT NULL,
  `IS_SOS` int(3) NOT NULL,
  `STATUS` varchar(20) NOT NULL,
  `DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- הוצאת מידע עבור טבלה `calls`
--

INSERT INTO `calls` (`number_call`, `worker_id`, `department_id`, `Content_call`, `PICUTRE`, `IS_SOS`, `STATUS`, `DATE`) VALUES
(8, 314683517, 10, 'FSDGF', 0x75706c6f6164732f313733353338393830395f74657874757265645f77686974655f6261636b67726f756e642e6a7067, 0, '', '2024-12-28 14:34:00'),
(9, 3232, 323, '3242', 0x75706c6f6164732f313733353338393935315f63616c6c732d50494355545245202832292e6a7067, 0, '', '2024-12-28 14:45:00'),
(10, 3232, 323, '3242', 0x75706c6f6164732f313733353339303130355f63616c6c732d50494355545245202832292e6a7067, 0, '', '2024-12-28 14:45:00'),
(11, 3232, 323, '3242', 0x75706c6f6164732f313733353339303231395f63616c6c732d50494355545245202832292e6a7067, 0, '', '2024-12-28 14:45:00');

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `department`
--

CREATE TABLE `department` (
  `Department_id` int(3) NOT NULL,
  `Department_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `login`
--

CREATE TABLE `login` (
  `worker_id` int(9) NOT NULL,
  `password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- הוצאת מידע עבור טבלה `login`
--

INSERT INTO `login` (`worker_id`, `password`) VALUES
(314683517, 'S314683517'),
(319132015, 'M319132015');

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `open_call`
--

CREATE TABLE `open_call` (
  `is_same` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `reading comments`
--

CREATE TABLE `reading comments` (
  `number_call` int(10) NOT NULL,
  `Comment` text NOT NULL,
  `Date` datetime NOT NULL,
  `worker_id` int(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `workers`
--

CREATE TABLE `workers` (
  `worker_id` int(9) NOT NULL,
  `Department_id` int(3) NOT NULL,
  `isManager` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='טבלת עובדים';

--
-- הוצאת מידע עבור טבלה `workers`
--

INSERT INTO `workers` (`worker_id`, `Department_id`, `isManager`) VALUES
(314683517, 10, 0),
(319132015, 20, 1);

--
-- Indexes for dumped tables
--

--
-- אינדקסים לטבלה `affiliations`
--
ALTER TABLE `affiliations`
  ADD PRIMARY KEY (`number_call`);

--
-- אינדקסים לטבלה `calls`
--
ALTER TABLE `calls`
  ADD PRIMARY KEY (`number_call`);

--
-- אינדקסים לטבלה `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`worker_id`);

--
-- אינדקסים לטבלה `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`worker_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `affiliations`
--
ALTER TABLE `affiliations`
  MODIFY `number_call` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calls`
--
ALTER TABLE `calls`
  MODIFY `number_call` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `worker_id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=987654322;

--
-- AUTO_INCREMENT for table `workers`
--
ALTER TABLE `workers`
  MODIFY `worker_id` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319132016;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
