-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 02. Jan 2025 um 16:43
-- Server-Version: 8.0.40-0ubuntu0.20.04.1
-- PHP-Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `ch280176_lotto_new`
--
CREATE DATABASE IF NOT EXISTS `ch280176_lotto_new` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE `ch280176_lotto_new`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Card`
--

CREATE TABLE `Card` (
  `ID` int NOT NULL,
  `lotto_id` int NOT NULL,
  `card_nr` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `birthyear` int NOT NULL,
  `location` varchar(1000) NOT NULL,
  `seller` varchar(200) DEFAULT NULL,
  `number_1` int NOT NULL,
  `number_2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Lotto`
--

CREATE TABLE `Lotto` (
  `ID` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Number`
--

CREATE TABLE `Number` (
  `ID` int NOT NULL,
  `series_id` int NOT NULL,
  `number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Price`
--

CREATE TABLE `Price` (
  `ID` int NOT NULL,
  `series_id` int NOT NULL,
  `sequence` int NOT NULL,
  `sponsor` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `winner_name` varchar(1000) DEFAULT NULL,
  `winner_birthyear` int DEFAULT NULL,
  `winner_location` varchar(1000) DEFAULT NULL,
  `winner_seller` varchar(1000) DEFAULT NULL,
  `winner_card_number` int DEFAULT NULL,
  `winner_number_1` int DEFAULT NULL,
  `winner_number_2` int DEFAULT NULL,
  `winner_number` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Series`
--

CREATE TABLE `Series` (
  `ID` int NOT NULL,
  `lotto_id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `mode` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
  `ID` int NOT NULL,
  `username` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Card`
--
ALTER TABLE `Card`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `card_lotto` (`lotto_id`);

--
-- Indizes für die Tabelle `Lotto`
--
ALTER TABLE `Lotto`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `Number`
--
ALTER TABLE `Number`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `number_series_1` (`series_id`);

--
-- Indizes für die Tabelle `Price`
--
ALTER TABLE `Price`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Price_ibfk_1` (`series_id`);

--
-- Indizes für die Tabelle `Series`
--
ALTER TABLE `Series`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `lotto` (`lotto_id`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Card`
--
ALTER TABLE `Card`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Lotto`
--
ALTER TABLE `Lotto`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Number`
--
ALTER TABLE `Number`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Price`
--
ALTER TABLE `Price`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Series`
--
ALTER TABLE `Series`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Card`
--
ALTER TABLE `Card`
  ADD CONSTRAINT `card_lotto` FOREIGN KEY (`lotto_id`) REFERENCES `Lotto` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `Number`
--
ALTER TABLE `Number`
  ADD CONSTRAINT `number_series_1` FOREIGN KEY (`series_id`) REFERENCES `Series` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `Price`
--
ALTER TABLE `Price`
  ADD CONSTRAINT `Price_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `Series` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints der Tabelle `Series`
--
ALTER TABLE `Series`
  ADD CONSTRAINT `lotto` FOREIGN KEY (`lotto_id`) REFERENCES `Lotto` (`ID`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
