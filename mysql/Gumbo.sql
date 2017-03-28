CREATE DATABASE  IF NOT EXISTS `gumbo`
USE `gumbo`;

--
-- Table structure for table `groep`
--

DROP TABLE IF EXISTS `groep`;
CREATE TABLE `groep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(240) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `email` varchar(240) DEFAULT NULL,
  `omschrijving` text,
  PRIMARY KEY (`id`));

--
-- Table structure for table `lidstatus`
--

DROP TABLE IF EXISTS `lidstatus`;
CREATE TABLE `lidstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`));

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon_id` int(11) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`));

--
-- Table structure for table `persoon`
--

DROP TABLE IF EXISTS `persoon`;
CREATE TABLE `persoon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(240) DEFAULT NULL,
  `tussenvoegsel` varchar(240) DEFAULT NULL,
  `achternaam` varchar(240) DEFAULT NULL,
  `email` varchar(240) DEFAULT NULL,
  `adres` varchar(240) DEFAULT NULL,
  `postcode` varchar(240) DEFAULT NULL,
  `woonplaats` varchar(240) DEFAULT NULL,
  `wachtwoord` varchar(240) DEFAULT NULL,
  `geboortedatum` date DEFAULT NULL,
  `geslacht` varchar(45) DEFAULT NULL,
  `opmerkingen` text,
  `lid_sinds` date DEFAULT NULL,
  `lid_tot` date DEFAULT NULL,
  `gumbode` tinyint(1) DEFAULT '0',
  `post` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`));

--
-- Table structure for table `persoon_evenement`
--

DROP TABLE IF EXISTS `persoon_evenement`;
CREATE TABLE `persoon_evenement` (
  `persoon_id` int(11) NOT NULL,
  `evenement_id` int(11) NOT NULL,
  `betaald` tinyint(1) DEFAULT '0',
  `datum_inschrijven` date DEFAULT NULL,
  `datum_betaald` date DEFAULT NULL,
  PRIMARY KEY (`persoon_id`,`evenement_id`));

--
-- Table structure for table `persoon_groep`
--

DROP TABLE IF EXISTS `persoon_groep`;
CREATE TABLE `persoon_groep` (
  `persoon_id` int(11) NOT NULL,
  `groep_id` int(11) NOT NULL,
  `rol_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`persoon_id`,`groep_id`));

--
-- Table structure for table `persoon_lidstatus`
--

DROP TABLE IF EXISTS `persoon_lidstatus`;
CREATE TABLE `persoon_lidstatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon_id` int(11) NOT NULL,
  `jaar` varchar(20) NOT NULL,
  `lidstatus_id` int(11) NOT NULL,
  PRIMARY KEY (`id`));

--
-- Table structure for table `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`));

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(45) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `persoon_id` int(11) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`));