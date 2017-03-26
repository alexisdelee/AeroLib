-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 25 Mars 2017 à 00:18
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `aerodrome`
--

-- --------------------------------------------------------

--
-- Structure de la table `acoustic`
--

CREATE TABLE IF NOT EXISTS `acoustic` (
  `idAcoustic` int(11) NOT NULL AUTO_INCREMENT,
  `groupAcoustic` varchar(2) DEFAULT NULL,
  `coefficientDay` double DEFAULT NULL,
  `coefficientNight` double DEFAULT NULL,
  PRIMARY KEY (`idAcoustic`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `acoustic`
--

INSERT INTO `acoustic` (`idAcoustic`, `groupAcoustic`, `coefficientDay`, `coefficientNight`) VALUES
(1, '1', 1.3, 4),
(2, '2', 1.2, 1.8),
(3, '3', 1.15, 1.725),
(4, '4', 1, 1.5),
(5, '5a', 0.85, 1.275),
(6, '5b', 0.7, 1.05);

-- --------------------------------------------------------

--
-- Structure de la table `administrative`
--

CREATE TABLE IF NOT EXISTS `administrative` (
  `idAdministrative` int(11) NOT NULL AUTO_INCREMENT,
  `costAdministrative` double DEFAULT NULL,
  `tvaAdministrative` double DEFAULT NULL,
  PRIMARY KEY (`idAdministrative`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `administrative`
--

INSERT INTO `administrative` (`idAdministrative`, `costAdministrative`, `tvaAdministrative`) VALUES
(1, 2.6, 15.6);

-- --------------------------------------------------------

--
-- Structure de la table `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `idArea` int(11) NOT NULL AUTO_INCREMENT,
  `typeArea` varchar(100) DEFAULT NULL,
  `idOutdoorParking` int(11) DEFAULT NULL,
  `idIndoorParking` int(11) DEFAULT NULL,
  PRIMARY KEY (`idArea`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `idCategory` int(11) NOT NULL AUTO_INCREMENT,
  `typeCategory` int(11) DEFAULT NULL,
  `timetable` varchar(100) DEFAULT NULL,
  `costCategory` double DEFAULT NULL,
  `tvaCategory` double DEFAULT NULL,
  PRIMARY KEY (`idCategory`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `category`
--

INSERT INTO `category` (`idCategory`, `typeCategory`, `timetable`, `costCategory`, `tvaCategory`) VALUES
(9, 1, 'Tarif mensuel aéronefs basés', 150, 30),
(10, 2, 'Tarif mensuel aéronefs basés', 116.67, 23.33),
(11, 3, 'Tarif mensuel aéronefs basés', 70.83, 14.17),
(12, 1, 'Tarif journalier aéronefs basés', 5.5, 1.1),
(13, 2, 'Tarif journalier aéronefs basés', 4.33, 0.87),
(14, 3, 'Tarif journalier aéronefs basés', 2.63, 0.52),
(15, 1, 'Tarifs journalier aéronefs non-basés', 9.38, 1.87),
(16, 2, 'Tarifs journalier aéronefs non-basés', 7.29, 1.46),
(17, 3, 'Tarifs journalier aéronefs non-basés', 4.42, 0.88);

-- --------------------------------------------------------

--
-- Structure de la table `cleaning`
--

CREATE TABLE IF NOT EXISTS `cleaning` (
  `idCleaning` int(11) NOT NULL AUTO_INCREMENT,
  `costCleaning` double DEFAULT NULL,
  `tvaCleaning` double DEFAULT NULL,
  PRIMARY KEY (`idCleaning`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `indoorparking`
--

CREATE TABLE IF NOT EXISTS `indoorparking` (
  `idIndoorParking` int(11) NOT NULL AUTO_INCREMENT,
  `maxSurface` double DEFAULT NULL,
  `maxMass` double DEFAULT NULL,
  `idCategory` int(11) DEFAULT NULL,
  PRIMARY KEY (`idIndoorParking`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `indoorparking`
--

INSERT INTO `indoorparking` (`idIndoorParking`, `maxSurface`, `maxMass`, `idCategory`) VALUES
(1, 0.5, 60, 2),
(2, 0.5, 100, 2),
(3, 0.5, 1.8446744073709552e19, 3),
(4, 1, 60, 2),
(5, 1, 100, 3),
(6, 1, 1.8446744073709552e19, 3),
(7, 0, 60, 3),
(8, 1.8446744073709552e19, 100, 2),
(9, 1.8446744073709552e19, 1.8446744073709552e19, 1);

-- --------------------------------------------------------

--
-- Structure de la table `landing`
--

CREATE TABLE IF NOT EXISTS `landing` (
  `idLanding` int(11) NOT NULL AUTO_INCREMENT,
  `timetable` varchar(100) DEFAULT NULL,
  `costLanding` double DEFAULT NULL,
  `tvaLanding` double DEFAULT NULL,
  `idModel` int(11) DEFAULT NULL,
  PRIMARY KEY (`idLanding`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `landing`
--

INSERT INTO `landing` (`idLanding`, `timetable`, `costLanding`, `tvaLanding`, `idModel`) VALUES
(1, 'Week-end/JF (non basé)', 31.17, 6.23, 1),
(2, 'Semaine (non-basé)', 34.5, 6.9, 1),
(3, 'Avion basé (mensuel)', 113, 22.6, 1),
(4, 'Avion basé (unité)', 15.25, 3.05, 1),
(5, 'Week-end/JF (non basé)', 41.17, 8.23, 2),
(6, 'Semaine (non-basé)', 37.17, 7.43, 2),
(7, 'Avion basé (mensuel)', 120, 24, 2),
(8, 'Avion basé (unité)', 18, 3.6, 2);

-- --------------------------------------------------------

--
-- Structure de la table `model`
--

CREATE TABLE IF NOT EXISTS `model` (
  `idModel` int(11) NOT NULL AUTO_INCREMENT,
  `typeModel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idModel`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `model`
--

INSERT INTO `model` (`idModel`, `typeModel`) VALUES
(1, 'Mono-turbine Bi-turbine'),
(2, 'Réacteur mono/multi');

-- --------------------------------------------------------

--
-- Structure de la table `outdoorparking`
--

CREATE TABLE IF NOT EXISTS `outdoorparking` (
  `idOutdoorParking` int(11) NOT NULL AUTO_INCREMENT,
  `costOutdoorParking` double DEFAULT NULL,
  `tvaOutdoorParking` double DEFAULT NULL,
  PRIMARY KEY (`idOutdoorParking`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `outdoorparking`
--

INSERT INTO `outdoorparking` (`idOutdoorParking`, `costOutdoorParking`, `tvaOutdoorParking`) VALUES
(1, 2.3, 0.46);

-- --------------------------------------------------------

--
-- Structure de la table `plane`
--

CREATE TABLE IF NOT EXISTS `plane` (
  `idPlane` int(11) NOT NULL AUTO_INCREMENT,
  `surface` double DEFAULT NULL,
  `mass` double DEFAULT NULL,
  `idReservoir` int(11) DEFAULT NULL,
  `idModel` int(11) DEFAULT NULL,
  `idAcoustic` int(11) DEFAULT NULL,
  `idArea` int(11) DEFAULT NULL,
  `idCleaning` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPlane`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `receipt`
--

CREATE TABLE IF NOT EXISTS `receipt` (
  `idReceipt` int(11) NOT NULL AUTO_INCREMENT,
  `prestation` varchar(100) DEFAULT NULL,
  `dateOfDay` bigint(20) DEFAULT NULL,
  `confirmation` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `idPlane` int(11) DEFAULT NULL,
  `idAdministrative` int(11) DEFAULT NULL,
  `costReceipt` double DEFAULT NULL,
  `tvaReceipt` double DEFAULT NULL,
  PRIMARY KEY (`idReceipt`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `remittance`
--

CREATE TABLE IF NOT EXISTS `remittance` (
  `idRemittance` int(11) NOT NULL AUTO_INCREMENT,
  `percent` double DEFAULT NULL,
  `idAcoustic` int(11) DEFAULT NULL,
  PRIMARY KEY (`idRemittance`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `remittance`
--

INSERT INTO `remittance` (`idRemittance`, `percent`, `idAcoustic`) VALUES
(1, 50, 1),
(2, 50, 2);

-- --------------------------------------------------------

--
-- Structure de la table `reservoir`
--

CREATE TABLE IF NOT EXISTS `reservoir` (
  `idReservoir` int(11) NOT NULL AUTO_INCREMENT,
  `product` varchar(100) DEFAULT NULL,
  `costReservoir` double DEFAULT NULL,
  `tvaReservoir` double DEFAULT NULL,
  PRIMARY KEY (`idReservoir`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `reservoir`
--

INSERT INTO `reservoir` (`idReservoir`, `product`, `costReservoir`, `tvaReservoir`) VALUES
(1, 'JETA1 Sans TIC', 1.01, 0.2),
(2, 'JETA1 Plus    TIC', 1.36, 0.27),
(3, 'AVGAS 100LL Sans TIC', 1.5, 0.3),
(4, 'AVGAS 100LL TIC', 1.92, 0.38);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `age` double DEFAULT NULL,
  `accesstoken` varchar(32) DEFAULT NULL,
  `credit` double DEFAULT '0',
  `statut` int(11) DEFAULT '0',
  `landed` int(11) DEFAULT '0',
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`idUser`, `name`, `password`, `email`, `age`, `accesstoken`, `credit`, `statut`, `landed`) VALUES
(1, 'Alexis Delée', '$2y$10$HBnVkTtNOupS4H1X5dwl6.E2.3P90nvTDjeOTVC.i31B/ujtcNiPe', 'alexisdelee@protonmail.com', 19, '3e4e7287459be20bd0241a877f1cafc7', 10, 1, 0),
(6, 'root', '$2y$10$ubHO4SyAoW8r0HxQ6TkDru0JKC0atQNUz1WDEHvkC79QK6EfYWx8e', 'root@debian.fr', 20, 'd91920006e1a24836172513155cc1e58', 0, 2, 0);

-- --------------------------------------------------------

--
-- Structure de la table `weather`
--

CREATE TABLE IF NOT EXISTS `weather` (
  `idWeather` int(11) NOT NULL AUTO_INCREMENT,
  `date` bigint(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `temp` double DEFAULT NULL,
  `pressure` int(11) DEFAULT NULL,
  `humidity` int(11) DEFAULT NULL,
  `temp_min` double DEFAULT NULL,
  `temp_max` double DEFAULT NULL,
  `visibility` int(11) DEFAULT NULL,
  `speed` double DEFAULT NULL,
  `sunrise` bigint(20) DEFAULT NULL,
  `sunset` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idWeather`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `weather`
--

INSERT INTO `weather` (`idWeather`, `date`, `description`, `temp`, `pressure`, `humidity`, `temp_min`, `temp_max`, `visibility`, `speed`, `sunrise`, `sunset`) VALUES
(1, 1489757094, 'neigeux', 19, 10000, 59, 7, 20, 10000, 13, 1000, 10000);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
