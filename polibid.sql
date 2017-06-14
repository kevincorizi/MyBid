-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 12, 2017 alle 09:34
-- Versione del server: 10.1.21-MariaDB
-- Versione PHP: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `polibid`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `auction`
--

CREATE TABLE `auction` (
  `id` int(11) NOT NULL COMMENT 'Auction unique ID',
  `name` varchar(128) NOT NULL COMMENT 'Auction product name',
  `bid` decimal(10,2) NOT NULL DEFAULT '1.00' COMMENT 'Current greatest offer',
  `bidder` varchar(128) DEFAULT NULL COMMENT 'Current auction winner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Auction details';

--
-- Dump dei dati per la tabella `auction`
--

INSERT INTO `auction` (`id`, `name`, `bid`, `bidder`) VALUES
(1, 'PoliProduct', '1.00', null);

-- --------------------------------------------------------

--
-- Struttura della tabella `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL COMMENT 'Notification unique ID',
  `user` varchar(128) NOT NULL COMMENT 'Username',
  `auction` int(11) NOT NULL COMMENT 'Auction ID',
  `type` varchar(128) NOT NULL COMMENT 'Type (exceeded or highest)',
  `message` varchar(256) NOT NULL COMMENT 'Message of the notification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Notifications details';

--
-- Struttura della tabella `offer`
--

CREATE TABLE `offer` (
  `user` varchar(128) NOT NULL COMMENT 'Bidder name',
  `auction` int(11) NOT NULL COMMENT 'Auction ID',
  `value` decimal(10,2) DEFAULT NULL COMMENT 'Current offer of user for auction',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Moment of offer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Offer details';

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `email` varchar(128) NOT NULL COMMENT 'User unique name',
  `password` varchar(256) NOT NULL COMMENT 'User password'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User credentials';

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `auction`
--
ALTER TABLE `auction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auction_highest_bidder` (`bidder`);

--
-- Indici per le tabelle `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_auction` (`auction`);

--
-- Indici per le tabelle `offer`
--
ALTER TABLE `offer`
  ADD PRIMARY KEY (`user`,`auction`),
  ADD KEY `offer_auction` (`auction`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `auction`
--
ALTER TABLE `auction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Auction unique ID', AUTO_INCREMENT=2;
--

--
-- AUTO_INCREMENT per la tabella `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Notification unique ID', AUTO_INCREMENT=2;
--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `auction`
--
ALTER TABLE `auction`
  ADD CONSTRAINT `auction_highest_bidder` FOREIGN KEY (`bidder`) REFERENCES `users` (`email`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notification_auction` FOREIGN KEY (`auction`) REFERENCES `auction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `notification_user` FOREIGN KEY (`user`) REFERENCES `users` (`email`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `offer`
--
ALTER TABLE `offer`
  ADD CONSTRAINT `offer_auction` FOREIGN KEY (`auction`) REFERENCES `auction` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `offer_user` FOREIGN KEY (`user`) REFERENCES `users` (`email`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
