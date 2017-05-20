-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 13 Mai 2017 à 23:25
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

-- --------------------------------------------------------

--
-- Structure de la table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `idActivity` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `use` varchar(30) DEFAULT NULL,
  `formation` tinyint(1) DEFAULT '0',
  `age` double NOT NULL DEFAULT '0',
  `description` text,
  `cost` double NOT NULL DEFAULT '0',
  `tva` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`idActivity`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Contenu de la table `activity`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `aeroclub`
--

CREATE TABLE IF NOT EXISTS `aeroclub` (
  `idAeroclub` int(11) NOT NULL AUTO_INCREMENT,
  `idActivity` int(11) DEFAULT NULL,
  `idPrivatePlane` int(11) DEFAULT NULL,
  PRIMARY KEY (`idAeroclub`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=85 ;

--
-- Contenu de la table `aeroclub`
--

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
  `base` int(11) NOT NULL DEFAULT '0',
  `ratio` int(11) NOT NULL DEFAULT '0',
  `costCategory` double DEFAULT NULL,
  `tvaCategory` double DEFAULT NULL,
  PRIMARY KEY (`idCategory`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Contenu de la table `category`
--

-- --------------------------------------------------------

--
-- Structure de la table `cleaning`
--

CREATE TABLE IF NOT EXISTS `cleaning` (
  `idCleaning` int(11) NOT NULL AUTO_INCREMENT,
  `costCleaning` double DEFAULT NULL,
  `tvaCleaning` double DEFAULT NULL,
  PRIMARY KEY (`idCleaning`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `cleaning`
--

-- --------------------------------------------------------

--
-- Structure de la table `ffa`
--

CREATE TABLE IF NOT EXISTS `ffa` (
  `idFFA` int(11) NOT NULL AUTO_INCREMENT,
  `revue` tinyint(1) NOT NULL DEFAULT '0',
  `costFFA` double NOT NULL DEFAULT '0',
  `tvaFFA` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`idFFA`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ffa`
--

-- --------------------------------------------------------

--
-- Structure de la table `indoorparking`
--

CREATE TABLE IF NOT EXISTS `indoorparking` (
  `idIndoorParking` int(11) NOT NULL AUTO_INCREMENT,
  `minMass` double NOT NULL DEFAULT '0',
  `maxMass` double DEFAULT NULL,
  `minSurface` double NOT NULL DEFAULT '0',
  `maxSurface` double DEFAULT NULL,
  `idCategory` int(11) DEFAULT NULL,
  PRIMARY KEY (`idIndoorParking`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Contenu de la table `indoorparking`
--

-- --------------------------------------------------------

--
-- Structure de la table `landing`
--

CREATE TABLE IF NOT EXISTS `landing` (
  `idLanding` int(11) NOT NULL AUTO_INCREMENT,
  `timetable` varchar(100) DEFAULT NULL,
  `base` int(11) NOT NULL DEFAULT '0',
  `ratio` int(11) NOT NULL DEFAULT '0',
  `costLanding` double DEFAULT NULL,
  `tvaLanding` double DEFAULT NULL,
  `idModel` int(11) DEFAULT NULL,
  PRIMARY KEY (`idLanding`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `landing`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `plane`
--

CREATE TABLE IF NOT EXISTS `plane` (
  `idPlane` int(11) NOT NULL AUTO_INCREMENT,
  `matricule` varchar(8) DEFAULT NULL,
  `surface` double DEFAULT NULL,
  `mass` double DEFAULT NULL,
  `base` int(11) NOT NULL DEFAULT '0',
  `idReservoir` int(11) DEFAULT NULL,
  `idModel` int(11) DEFAULT NULL,
  `idAcoustic` int(11) DEFAULT NULL,
  `idArea` int(11) DEFAULT NULL,
  `idCleaning` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPlane`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Contenu de la table `plane`
--

-- --------------------------------------------------------

--
-- Structure de la table `privateplane`
--

CREATE TABLE IF NOT EXISTS `privateplane` (
  `idPrivatePlane` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `tarif_solo` double NOT NULL DEFAULT '0',
  `tarif_instruction` double NOT NULL DEFAULT '0',
  `use` varchar(50) DEFAULT NULL,
  `ulm` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idPrivatePlane`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `privateplane`
--

-- --------------------------------------------------------

--
-- Structure de la table `receipt`
--

CREATE TABLE IF NOT EXISTS `receipt` (
  `idReceipt` int(11) NOT NULL AUTO_INCREMENT,
  `creation` bigint(20) DEFAULT '0',
  `isPaid` int(11) NOT NULL DEFAULT '0',
  `idAdministrative` int(11) DEFAULT NULL,
  `idUser` int(11) DEFAULT NULL,
  `totalCost` double NOT NULL DEFAULT '0',
  `totalTva` double DEFAULT '0',
  PRIMARY KEY (`idReceipt`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=70 ;

--
-- Contenu de la table `receipt`
--

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

-- --------------------------------------------------------

--
-- Structure de la table `service`
--

CREATE TABLE IF NOT EXISTS `service` (
  `idService` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `subscription` bigint(20) DEFAULT '0',
  `inscription` bigint(20) DEFAULT '0',
  `dateStart` bigint(20) DEFAULT '0',
  `dateEnd` bigint(20) DEFAULT '0',
  `contributions` tinyint(1) DEFAULT '0',
  `confirmation` int(11) NOT NULL DEFAULT '0',
  `idReceipt` int(11) DEFAULT NULL,
  `idPlane` int(11) DEFAULT NULL,
  `idAeroclub` int(11) DEFAULT NULL,
  `costService` double NOT NULL DEFAULT '0',
  `tvaService` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`idService`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=158 ;

--
-- Contenu de la table `service`
--

-- --------------------------------------------------------

--
-- Structure de la table `signs`
--

CREATE TABLE IF NOT EXISTS `signs` (
  `idSigns` int(11) NOT NULL AUTO_INCREMENT,
  `costSigns` double DEFAULT '0',
  `tvaSigns` double DEFAULT '0',
  PRIMARY KEY (`idSigns`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `signs`
--

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `birthday` bigint(20) DEFAULT '0',
  `accesstoken` varchar(32) DEFAULT NULL,
  `credit` double DEFAULT '0',
  `statut` int(11) DEFAULT '0',
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Contenu de la table `user`
--

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
