-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 07. Sep 2020 um 19:46
-- Server-Version: 10.4.14-MariaDB
-- PHP-Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `chat`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `text` varchar(140) NOT NULL,
  `sendAt` datetime NOT NULL,
  `color` varchar(7) NOT NULL,
  `roomID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `messages`
--

INSERT INTO `messages` (`id`, `userID`, `text`, `sendAt`, `color`, `roomID`) VALUES
(1, 6, 'Registrieren geht nun auch per mail', '2020-08-28 12:19:00', '#006400', 1),
(2, 7, 'Und ich brauch auch kein Mercury daf&uuml;r', '2020-08-28 12:21:00', '#000000', 1),
(3, 1, 'Einfach gut sowas', '2020-08-29 12:14:00', '#f000d0', 2),
(4, 1, 'Nachricht f&uuml;r raum 2', '2020-08-29 14:14:00', '#ffffff', 2),
(5, 1, 'Test', '2020-08-31 16:26:00', '#ffffff', 1),
(6, 1, 'Inaktiv', '2020-08-31 16:26:00', '#ff0000', 1),
(7, 1, 'Zweite Nachricht', '2020-08-31 16:59:00', '#000000', 2),
(8, 1, 'Oder auch die vierte', '2020-08-31 16:59:00', '#59a50d', 2),
(9, 6, 'Geheimer Channel', '2020-08-31 18:11:00', '#006400', 3),
(10, 8, 'test', '2020-09-01 16:04:00', '#28fbc6', 1),
(11, 6, 'Spast', '2020-09-01 16:05:00', '#006400', 1),
(12, 6, '10', '2020-09-01 17:42:00', '#006400', 1),
(13, 6, 'abc', '2020-09-01 17:43:00', '#006400', 1),
(14, 6, 'Test', '2020-09-01 17:56:00', '#006400', 1),
(15, 6, 'Nachricht mit neuer SessionID', '2020-09-02 14:50:00', '#006400', 1),
(16, 6, 'Test', '2020-09-02 14:53:00', '#006400', 1),
(20, 9, 'Bildtest', '2020-09-04 12:12:00', '#006400', 1),
(21, 1, 'Scrolling', '2020-09-04 13:26:00', '#59a50d', 1),
(22, 1, 'Schwarz', '2020-09-04 13:27:00', '#000000', 1),
(23, 6, 'Test', '2020-09-04 17:44:00', '#006400', 1),
(24, 5, 'Hi', '2020-09-05 11:22:00', '#ffffff', 1),
(25, 5, 'Test', '2020-09-05 11:23:00', '#ffffff', 2),
(26, 2, 'Kein Bild', '2020-09-06 13:55:00', '#006400', 1),
(27, 2, 'Mit Bild ', '2020-09-06 13:56:00', '#000000', 1),
(28, 2, '                                                                                   ', '2020-09-06 13:56:00', '#000000', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `desc` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `users` int(11) NOT NULL DEFAULT 0,
  `autoDelete` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `desc`, `password`, `users`, `autoDelete`) VALUES
(1, 'Hauptraum', 'Allgemeine Unterhaltungen', NULL, 1, 0),
(2, 'Coding', 'Programmierertalk', NULL, 0, 0),
(3, 'Verwaltung', 'Nur für Administratoren', 'admin', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `picture` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) DEFAULT 0,
  `challenge` varchar(100) NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(7) NOT NULL DEFAULT '#006400',
  `inRoom` int(11) NOT NULL DEFAULT 1,
  `lastActive` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `mail`, `password`, `gender`, `age`, `picture`, `active`, `challenge`, `confirmed`, `color`, `inRoom`, `lastActive`) VALUES
(1, 'Simon', 'Simon Stauss', 'stauss.simon.01@gmail.com', '$2y$10$PE.OkWTUfvrLbwQD1bxuLuVpDuykw8df5I9ue6qKi35vC1etcCmvi', 'm', 19, 1, 0, '9572e6a5eb83e281c77e86d393b48cd9', 1, '#000000', 0, 1599390128),
(2, 'Anon1', 'Anonymus', 'anon@ymus.com', '$2y$10$JRTSS07BV9XImjO6ebb1sOIOrEzJ2IYNRsddx.GF.UdUqA09Nymr.', NULL, NULL, 1, 1, '27fc28b5bb14c15dd26b5e2198c452fe', 1, '#000000', 1, 1599393399),
(3, 'abcdef', 'Simon Stauss', 'abc@def.com', '$2y$10$gMXTLlA1uDzPMej7NNFtDe.KGl5NQOIZmHP.ojML1PHRDhYO2fg3G', 'w', 11, 0, 0, '155e06f44535d52b86149b3e50b8c677', 1, '#006400', 0, 1599231912),
(4, 'Admin', 'Administrator', 'admin@verwaltung.de', '$2y$10$rziFqmRKi/j9n3x/GM1ba.6W3NsLTOCn6W4pDhnE4a/wS7BtA4Xs2', NULL, NULL, 0, 0, '1e85c0a7d476e59776fbd67b4df2b4be', 1, '#006400', 0, 1599231912),
(5, 'David', 'David Stauss', 'd.stauss98@gmail.com', '$2y$10$CgPf9CA7sE9DvtmLRVvsXeBmHxYB59eTpwUuWKDD5ZgMj5Jgehnpm', NULL, NULL, 0, 0, '1538138d7b25ee0737d2ce4c8980984b', 1, '#ffffff', 0, 1599297895),
(6, 'Testnutzer', 'Simon Stauss', 'stausssi01@gmail.com', '$2y$10$5oZDvYC90zORQy2E9rUpiO0TVWIFgDeQ5YV1WCJxqgJxg1Lkpdn9u', 'm', 19, 0, 0, 'f21e043f2bb804264dea66e76313df4c', 1, '#006400', 0, 1599236421),
(7, 'Crotex', 'Simon Stauss', 'crotexrl1231@gmail.com', '$2y$10$UcU.UqJt6x/KBc3RlVv1f.VWUbBSabS3m5x0mQeuJNbNVH/y6NQg2', 'd', NULL, 0, 0, '2602a0596f1bf42bd6649cd9f946f4bb', 1, '#000000', 0, 1599231912),
(8, 'TestAcc', 'Tom Schneider', 'tom.schneider0603@gmail.com', '$2y$10$AcnsHzNPppNOMALIA31W/ecnq78yZg5iZCxQOWyFy4QgBah8tq9JO', 'm', 15, 0, 0, '9426c32bf2f8524ceaf1bae191285d55', 1, '#28fbc6', 0, 1599231912),
(9, 'Bildnutzer', 'Bild Bildermann', 'crotexrl@gmail.com', '$2y$10$KRqA71W.dE2lT0fpljlxYOXLhGSU4LsdavcMPoUBwW6Y0HD.WMKvu', 'm', 99, 1, 0, '63aef12566d5daf312925f1d6aa5b730', 1, '#006400', 0, 1599231912);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT für Tabelle `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
