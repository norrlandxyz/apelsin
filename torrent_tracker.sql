-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 13, 2023 at 11:01 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `torrent_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `peers`
--

CREATE TABLE `peers` (
  `id` int(11) NOT NULL,
  `infohash` text NOT NULL,
  `b32_addr` text NOT NULL,
  `port` int(11) NOT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp(),
  `seeding` tinyint(1) DEFAULT 0,
  `leeching` tinyint(1) DEFAULT 0,
  `complete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `peers`
--

INSERT INTO `peers` (`id`, `infohash`, `b32_addr`, `port`, `last_update`, `seeding`, `leeching`, `complete`) VALUES
(60, '%BCK%92%DCk%5B+%01%AF%CB9%B5%3F%7E%C4%27%99w%E3t', '2cioejs2fm32gg2e7vab3qdh432rotrlzna2xa6o3vayyyaqjnvq.b32.i2p', 6881, '2023-05-12 23:44:30', 0, 1, 0),
(70, 'S%945a%B1%3Ez%DD%3E%92m%E6%8D0%8F%F7C%C4%94%D0', '2cioejs2fm32gg2e7vab3qdh432rotrlzna2xa6o3vayyyaqjnvq.b32.i2p', 6881, '2023-05-12 23:43:52', 0, 0, 0),
(71, 'S%945a%B1%3Ez%DD%3E%92m%E6%8D0%8F%F7C%C4%94%D0', 'cvk22yldagvdake5keztaxaropgroz34er3chxfyjha3remjjnvq.b32.i2p', 6881, '2023-05-12 23:44:11', 0, 0, 0),
(72, '%BCK%92%DCk%5B+%01%AF%CB9%B5%3F%7E%C4%27%99w%E3t', 'cvk22yldagvdake5keztaxaropgroz34er3chxfyjha3remjjnvq.b32.i2p', 6881, '2023-05-12 23:43:44', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(11) NOT NULL,
  `desc` text NOT NULL,
  `owner` text NOT NULL,
  `upload_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_update` datetime NOT NULL DEFAULT current_timestamp(),
  `name` tinytext NOT NULL,
  `file_path` tinytext NOT NULL,
  `size` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `infohash` text NOT NULL,
  `tags` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `torrents`
--

INSERT INTO `torrents` (`id`, `desc`, `owner`, `upload_date`, `last_update`, `name`, `file_path`, `size`, `rating`, `infohash`, `tags`) VALUES
(4, 'riktigit cool torrent', 'bbb', '2023-05-10 13:19:09', '2023-05-10 13:19:09', 'AblnaiaðŸ‡¦ðŸ‡±ðŸ‡¦ðŸ‡±ðŸ‡¦ðŸ‡±', '/opt/lampp/htdocs/htdocs-termin2/henrik-tracker/upload/content/Ablnaia%F0%9F%87%A6%F0%9F%87%B1%F0%9F%87%A6%F0%9F%87%B1%F0%9F%87%A6%F0%9F%87%B1645b7dad886e2.torrent', 131729, 0, 'O%5BA%22%FD%AA%E3%CC%1Eu%93%2A%3F%AD%92-%856%F7G', 'other'),
(5, 'henrik :)', 'bbb', '2023-05-10 15:20:31', '2023-05-10 15:20:31', 'henrik.jpg!', '/opt/lampp/htdocs/htdocs-termin2/henrik-tracker/upload/content/henrik.jpg%21645b9a1f5c9fe.torrent', 56273, 0, 'a%AA%BD%BFh%D7%80%9C%21%AA%BD%EF%CF%BE%CA7f%91%D6%1C', 'other'),
(6, 'menu :)', 'gayhomo', '2023-05-12 11:13:16', '2023-05-12 11:13:16', 'movie menu', '/opt/lampp/htdocs/htdocs-termin2/henrik-tracker/upload/content/movie+menu645e032ca6dd3.torrent', 1791274706, 0, '%BCK%92%DCk%5B+%01%AF%CB9%B5%3F%7E%C4%27%99w%E3t', 'movie'),
(9, 'how he di to', 'vincent', '2023-05-12 23:25:09', '2023-05-12 23:25:09', 'tou how', '/opt/lampp/htdocs/htdocs-termin2/henrik-tracker/upload/content/tou+how645eaeb53d82e.torrent', 438830067, 0, 'S%945a%B1%3Ez%DD%3E%92m%E6%8D0%8F%F7C%C4%94%D0', 'other');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `passwd` text NOT NULL,
  `perm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `passwd`, `perm`) VALUES
(1, 'robin', '$2y$10$027CKdgfspSfrAQ2dE5yL.f0tv3AwGCDJhHJFfGKo9EwvTU/nMUfy', 0),
(3, 'test', '$2y$10$/lCPZOcas6qb8qCGU3n5SeHQ.8qtxWyCA2sC/1Z.r6tTvfWAj0W.6', 0),
(5, 'henrik', '$2y$10$E5TgvAMM0K3OePo1S9K31OLxYqP9uBFgBRkN2lCuEaHz5BxcIqy16', 0),
(6, 'robin2', '$2y$10$FJm93JvYNKRimsHMKrLDa.sGSWoCIU/VYlApB.Yawy.2yXW8znjAa', 0),
(7, 'isak', '$2y$10$wfmtqHqOjXfzuAffqgOGVeY3OEboFtPChatfNbZEL3.hNZgegBgGe', 0),
(8, '<h1>bruh</h1>', '$2y$10$J.aF6PFBq/bMzTzTiUjfyundaEwzZ4mUNKssY2vbsvwfGXHiYZIza', 0),
(9, 'RobinÃ¤rgay', '$2y$10$Vwx9QdKbqqT.xS/VAUTPBOvI/zLFnnV9iiPkavTefbXDYUC0bPYhu', 0),
(10, 'bbb', '$2y$10$AB4baosXuH/gtNbJ7TL6jO1wBrdFJgrmJbOwBexgf.XNESEQTbgte', 0),
(11, 'gayhomo', '$2y$10$OYv1ktF4z.M6VSNx7v3T2eW6HSqlIdkadCPm.GMvyM2U2f2adTCfW', 0),
(12, 'joebiden', '$2y$10$b2qzSM/G8rS0LCaMPh5y4OCkChuZY7pZIwY2G02cysyE8TMx9LYFS', 0),
(13, 'vincent', '$2y$10$EgyKAYO0CNhtp818rIr0w.ehXUWD34MAPMkJhSNtQ8v4HeEUJ3nlG', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `peers`
--
ALTER TABLE `peers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `torrents`
--
ALTER TABLE `torrents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `peers`
--
ALTER TABLE `peers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `torrents`
--
ALTER TABLE `torrents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
