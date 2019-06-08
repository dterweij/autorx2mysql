-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Gegenereerd op: 09 jun 2019 om 00:33
-- Serverversie: 5.5.62
-- PHP-versie: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autorx`
--
CREATE DATABASE IF NOT EXISTS `autorx` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `autorx`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `first_seen`
--

CREATE TABLE `first_seen` (
  `id` int(11) NOT NULL,
  `last_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `station` varchar(40) NOT NULL,
  `callsign` varchar(40) NOT NULL,
  `time` varchar(40) NOT NULL,
  `alt` float NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  `temp` float NOT NULL,
  `freq` varchar(40) NOT NULL,
  `frame` int(15) NOT NULL,
  `sats` int(4) NOT NULL,
  `batt` float NOT NULL,
  `bt` varchar(40) NOT NULL,
  `speed` float NOT NULL,
  `model` varchar(40) NOT NULL,
  `distance` float NOT NULL,
  `direction` varchar(40) NOT NULL,
  `comment` varchar(40) NOT NULL,
  `evel` float NOT NULL,
  `bear` float NOT NULL,
  `hum` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `sondedata`
--

CREATE TABLE `sondedata` (
  `id` int(11) NOT NULL,
  `last_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `station` varchar(40) NOT NULL,
  `callsign` varchar(40) NOT NULL,
  `time` varchar(40) NOT NULL,
  `alt` float NOT NULL,
  `lat` float NOT NULL,
  `lon` float NOT NULL,
  `temp` float NOT NULL,
  `freq` varchar(40) NOT NULL,
  `frame` int(15) NOT NULL,
  `sats` int(4) NOT NULL,
  `batt` float NOT NULL,
  `bt` varchar(40) NOT NULL,
  `speed` float NOT NULL,
  `model` varchar(40) NOT NULL,
  `distance` float NOT NULL,
  `direction` varchar(40) NOT NULL,
  `comment` varchar(40) NOT NULL,
  `evel` float NOT NULL,
  `bear` float NOT NULL,
  `hum` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `first_seen`
--
ALTER TABLE `first_seen`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `sondedata`
--
ALTER TABLE `sondedata`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `first_seen`
--
ALTER TABLE `first_seen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `sondedata`
--
ALTER TABLE `sondedata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
