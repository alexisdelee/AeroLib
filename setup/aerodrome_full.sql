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

INSERT INTO `acoustic` (`idAcoustic`, `groupAcoustic`, `coefficientDay`, `coefficientNight`) VALUES
(1, '1', 1.3, 4),
(2, '2', 1.2, 1.8),
(3, '3', 1.15, 1.725),
(4, '4', 1, 1.5),
(5, '5a', 0.85, 1.275),
(6, '5b', 0.7, 1.05);

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

INSERT INTO `activity` (`idActivity`, `title`, `use`, `formation`, `age`, `description`, `cost`, `tva`) VALUES
(1, 'baptème de l''air', 'ecole', 0, 16, NULL, 240, 60),
(2, 'saut en parachute', 'voyage', 0, 16, NULL, 208, 52),
(3, 'location ulm', 'voyage', 0, 16, NULL, 48, 12),
(4, 'sans engagement', 'ecole', 1, 15, 'Sans engagement de votre part, testez une première leçon théorique suivie d''un vol d''environ 30 minutes avec un instructeur en étant vous-même aux commandes\n\n<strong>Ce programme n''est disponible qu''une fois par an</strong>', 104, 26),
(5, 'brevet de base (BB)', 'ecole', 1, 15, 'Minimum de 6h en double commande, 4h en solo et 20 atterrissages\nExamen théorique de 1h (60 questions à choix multiples)\nAptitude physique et mentale reconnue par un médecin agréé\n\n<strong>Le BB permet de voler seul à bord de 30 km autour de l''aérodrome</strong>', 2792, 698),
(6, 'licence pilote d''avion léger (LALP)', 'ecole', 1, 16, 'Minimum de 30h de vol pour 13h en double-commande et 6h en solo comprenant 3h de navigation dont un voyage seul à bord de plus de 145 km\nExamen théorique en 2 épreuves de 120 QCM\nCertificat médical pour LAPL\nElectrogramme si plus de 40 ans\n\n<strong>La LAPL permet d''emmener 3 passagers pour des vols de jour dans toute l''Union Européenne</strong>, après 10h de vol solo. Qualification vol de nuit possible sur notre terrain', 3680, 920),
(7, 'brevet de pilote privé (PPL)', 'ecole', 1, 16, 'Minimum de 45 heures de vol dont 25h en double commande et 10 heures en solo comprenant 5 heures de navigation dont un voyage seul à bord de plus de 270km\nExamen théorique en 2 épreuves de 120 QCM\nCertificat médical de classe 2 - électrocardiogramme obligatoire si plus de 40 ans\n\n<strong>Le PPL permet de piloter des avions de plus de 2 tonnes et d''emmener des passagers partout dans le monde. Permet de viser les qualifications IFR (vol aux instruments), bimoteur, vol de nuit, hydravion, langue anglaise, voltige, vol en montagne, remorquage de planeurs, largage pars</strong>', 6371.2, 1592.8);

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
(1, 25.83, 5.17);

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

INSERT INTO `aeroclub` (`idAeroclub`, `idActivity`, `idPrivatePlane`) VALUES
(24, 3, 3),
(25, 1, 4),
(27, 2, 2),
(79, 5, 1),
(81, 2, 2),
(82, 4, 1),
(83, 6, 4),
(84, 6, 1);

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

INSERT INTO `category` (`idCategory`, `typeCategory`, `timetable`, `base`, `ratio`, `costCategory`, `tvaCategory`) VALUES
(9, 1, 'Tarif mensuel aéronefs basés', 1, 30, 150, 30),
(10, 2, 'Tarif mensuel aéronefs basés', 1, 30, 116.67, 23.33),
(11, 3, 'Tarif mensuel aéronefs basés', 1, 30, 70.83, 14.17),
(12, 1, 'Tarif journalier aéronefs basés', 1, 1, 5.5, 1.1),
(13, 2, 'Tarif journalier aéronefs basés', 1, 1, 4.33, 0.87),
(14, 3, 'Tarif journalier aéronefs basés', 1, 1, 2.63, 0.52),
(15, 1, 'Tarifs journalier aéronefs non-basés', 0, 1, 9.38, 1.87),
(16, 2, 'Tarifs journalier aéronefs non-basés', 0, 1, 7.29, 1.46),
(17, 3, 'Tarifs journalier aéronefs non-basés', 0, 1, 4.42, 0.88);

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

INSERT INTO `cleaning` (`idCleaning`, `costCleaning`, `tvaCleaning`) VALUES
(1, 1.77, 0.35);

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

INSERT INTO `ffa` (`idFFA`, `revue`, `costFFA`, `tvaFFA`) VALUES
(1, 0, 59.2, 14.8),
(2, 1, 91.2, 22.8);

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

INSERT INTO `indoorparking` (`idIndoorParking`, `minMass`, `maxMass`, `minSurface`, `maxSurface`, `idCategory`) VALUES
(1, 0, 0.5, 0, 60, 2),
(2, 0, 0.5, 0, 100, 2),
(3, 0, 0.5, 100, 1.8446744073709552e19, 3),
(4, 0.5, 1, 0, 60, 2),
(5, 0.5, 1, 60, 100, 3),
(6, 0.5, 1, 100, 1.8446744073709552e19, 3),
(7, 1, 1.8446744073709552e19, 0, 60, 3),
(8, 1, 1.8446744073709552e19, 60, 100, 3),
(9, 1, 1.8446744073709552e19, 100, 1.8446744073709552e19, 1);

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

INSERT INTO `landing` (`idLanding`, `timetable`, `base`, `ratio`, `costLanding`, `tvaLanding`, `idModel`) VALUES
(1, 'Week-end/JF', 0, 1, 34.5, 6.9, 1),
(2, 'Semaine', 0, 1, 31.17, 6.23, 1),
(3, 'Mensuel', 1, 30, 113, 22.6, 1),
(4, 'Unité', 1, 1, 15.25, 3.05, 1),
(5, 'Week-end/JF', 0, 1, 41.17, 8.23, 2),
(6, 'Semaine', 0, 1, 37.17, 7.43, 2),
(7, 'Mensuel', 1, 30, 120, 24, 2),
(8, 'Unité', 1, 1, 18, 3.6, 2);

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

INSERT INTO `plane` (`idPlane`, `matricule`, `surface`, `mass`, `base`, `idReservoir`, `idModel`, `idAcoustic`, `idArea`, `idCleaning`, `idUser`) VALUES
(42, '13BB8D58', 20, 400, 0, NULL, 1, 3, NULL, NULL, 16);

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

INSERT INTO `privateplane` (`idPrivatePlane`, `type`, `tarif_solo`, `tarif_instruction`, `use`, `ulm`) VALUES
(1, 'Robin DR 400 120cv F-GDES', 161.6, 161.6, 'ecole', 0),
(2, 'PIPER PA 28 180cv F-GIDI', 195, 195, 'voyage', 0),
(3, 'BEST OFF Nynja 912S', 109.7, 109.7, 'voyage', 1),
(4, 'JMB Aircraft VL3 E-912S', 91, 91, 'ecole', 1);

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

INSERT INTO `receipt` (`idReceipt`, `creation`, `isPaid`, `idAdministrative`, `idUser`, `totalCost`, `totalTva`) VALUES
(61, 1493041108, 1, NULL, 16, 259.62, 75.25),
(63, 0, 0, NULL, 18, 0, 0),
(64, 1493887494, 1, NULL, 16, 144, 36),
(65, 1494430250, 1, NULL, 16, 122.4, 30.6),
(66, 1494686083, 1, NULL, 16, 4455.41, 1386.0100000000002),
(67, 0, 0, NULL, 19, 0, 0),
(68, 0, 0, NULL, 20, 0, 0),
(69, 0, 0, NULL, 16, 0, 0);

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
(2, 'JETA1 Plus TIC', 1.36, 0.27),
(3, 'AVGAS 100LL Sans TIC', 1.5, 0.3),
(4, 'AVGAS 100LL TIC', 1.92, 0.38);

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

INSERT INTO `service` (`idService`, `name`, `description`, `subscription`, `inscription`, `dateStart`, `dateEnd`, `contributions`, `confirmation`, `idReceipt`, `idPlane`, `idAeroclub`, `costService`, `tvaService`) VALUES
(82, 'Alexis Delée', 'atterrissage', 1493041063, 1493200800, 1501020000, 1501279200, 0, 1, 61, 42, NULL, 142.95, 28.59),
(83, 'Alexis Delée', 'stationnement intérieur de 27 jours', 1493041063, 1493200800, 1501020000, 1501279200, 0, 0, 61, 42, NULL, 116.67, 46.66),
(109, 'Alexis Delée', 'location ulm de 180 minutes', 1493887476, 1496253600, 1496250000, 1496253599, 0, 0, 64, NULL, 24, 144, 36),
(110, 'Alexis Delée', 'baptème de l''air de 15 minutes', 1494429255, 1513077120, 1513077120, 1513078020, 0, 0, 65, NULL, 25, 60, 15),
(112, 'Alexis Delée', 'saut en parachute de 18 minutes', 1494430188, 1513083600, 1513083600, 1513084680, 0, 0, 65, NULL, 27, 62.4, 15.6),
(150, 'Bruce Tout Puissant', 'saut en parachute de 89 minutes', 1494683316, 1502796600, 1502796600, 1502801940, 0, 0, 66, NULL, 81, 308.53, 77.13),
(151, 'Cédric Nanteau', 'sans engagement', 1494685970, 1496157420, 1496157420, 1496159220, 0, 0, 66, NULL, 82, 104, 26),
(152, 'Alexis Delée', 'licence pilote d''avion léger (LALP)', 1494686049, 1496157420, 1496157420, 1496175420, 1, 0, 66, NULL, 83, 4042.88, 1282.88);

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

INSERT INTO `signs` (`idSigns`, `costSigns`, `tvaSigns`) VALUES
(1, 13, 2.6);

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

INSERT INTO `user` (`idUser`, `name`, `password`, `email`, `birthday`, `accesstoken`, `credit`, `statut`) VALUES
(16, 'Alexis Delée', '$2y$10$6ZiTi6NUaZBOHvLV3LhhhOvcxHm/IbVHdEjAMruSjxa27uTXPdDJa', 'alexisdelee@protonmail.com', 860191200, '1a94235d48d980ff09a4bbaaa3434cfb', 3490.709999999999, 1),
(18, 'root', '$2y$10$9a2rvJrxWKS6BoUsXAjW3.UENUjHm2VVnO5OI.V7vGZJvDRdxLgVO', 'root@aen.fr', 19782000, '848ac178f0307ebe1e5ed30fe7a02e9f', 0, 2),
(19, 'Service comptabilité', '$2y$10$QPzCu/GSXYOzMTzBAVlB/uvMeOoNdSbQLKOD2yaqMRge0x3Hq5Sgq', 'service.compta@aen.fr', 19782000, '5b3482ee9f5176a8f33e8775495ee2b3', 0, 3),
(20, 'Cedric Nanteau', '$2y$10$WEYkOQ5yDf9P9JFTRPw.4ORebfKsnNenh5nMlQoQ0BlYINKZR.tpW', 'cedric.nanteau@gmail.com', 871164000, 'dd99a00a33a7454262dd98435d6d7d45', 0, 1);

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
