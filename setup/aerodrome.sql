-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 16 Mars 2017 à 21:39
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `administrative`
--

CREATE TABLE IF NOT EXISTS `administrative` (
  `idAdministrative` int(11) NOT NULL AUTO_INCREMENT,
  `costAdministrative` double DEFAULT NULL,
  `tvaAdministrative` double DEFAULT NULL,
  PRIMARY KEY (`idAdministrative`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `model`
--

CREATE TABLE IF NOT EXISTS `model` (
  `idModel` int(11) NOT NULL AUTO_INCREMENT,
  `typeModel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idModel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `outdoorparking`
--

CREATE TABLE IF NOT EXISTS `outdoorparking` (
  `idOutdoorParking` int(11) NOT NULL AUTO_INCREMENT,
  `costOutdoorParking` double DEFAULT NULL,
  `tvaOutdoorParking` double DEFAULT NULL,
  PRIMARY KEY (`idOutdoorParking`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`idPlane`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `prestation`
--

CREATE TABLE IF NOT EXISTS `prestation` (
  `idPrestation` int(11) NOT NULL AUTO_INCREMENT,
  `prestation` varchar(100) DEFAULT NULL,
  `idCleaning` int(11) DEFAULT NULL,
  `idWeather` int(11) DEFAULT NULL,
  PRIMARY KEY (`idPrestation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `receipt`
--

CREATE TABLE IF NOT EXISTS `receipt` (
  `idReceipt` int(11) NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
