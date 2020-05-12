-- phpMyAdmin SQL Dump
-- version OVH
-- https://www.phpmyadmin.net/
--
-- Hôte : lehichcodmfrogga.mysql.db
-- Généré le :  mar. 12 mai 2020 à 08:10
-- Version du serveur :  5.6.46-log
-- Version de PHP :  7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `lehichcodmfrogga`
--

-- --------------------------------------------------------

--
-- Structure de la table `sr_flux_rss`
--

CREATE TABLE `sr_flux_rss` (
  `FRSS_ID` int(11) NOT NULL,
  `FRSS_WEBSITE_NAME` varchar(50) NOT NULL,
  `FRSS_URL_RSS` text NOT NULL,
  `FRSS_LAST_ITEM_DATE` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `sr_lien_scan_user`
--

CREATE TABLE `sr_lien_scan_user` (
  `LSU_ID` int(11) NOT NULL,
  `LSU_SCAN_ID` int(11) NOT NULL,
  `LSU_USER_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sr_mangas`
--

CREATE TABLE `sr_mangas` (
  `MAN_ID` int(11) NOT NULL,
  `MAN_NAME` varchar(100) DEFAULT NULL,
  `MAN_SCAN_HREF` varchar(100) DEFAULT NULL,
  `MAN_COMMENT` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sr_scans`
--

CREATE TABLE `sr_scans` (
  `SCA_ID` int(11) NOT NULL DEFAULT '0',
  `SCA_FK_MAN_ID` int(11) DEFAULT NULL,
  `SCA_NAME` varchar(50) DEFAULT NULL,
  `SCA_URL_RSS` varchar(50) DEFAULT NULL,
  `SCA_LAST_SCAN` int(11) DEFAULT NULL,
  `SCA_RELEASE_DATE` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `sr_flux_rss`
--
ALTER TABLE `sr_flux_rss`
  ADD PRIMARY KEY (`FRSS_ID`);

--
-- Index pour la table `sr_lien_scan_user`
--
ALTER TABLE `sr_lien_scan_user`
  ADD PRIMARY KEY (`LSU_ID`);

--
-- Index pour la table `sr_mangas`
--
ALTER TABLE `sr_mangas`
  ADD PRIMARY KEY (`MAN_ID`),
  ADD UNIQUE KEY `MAN_NAME` (`MAN_NAME`);

--
-- Index pour la table `sr_scans`
--
ALTER TABLE `sr_scans`
  ADD PRIMARY KEY (`SCA_ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `sr_flux_rss`
--
ALTER TABLE `sr_flux_rss`
  MODIFY `FRSS_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sr_lien_scan_user`
--
ALTER TABLE `sr_lien_scan_user`
  MODIFY `LSU_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sr_mangas`
--
ALTER TABLE `sr_mangas`
  MODIFY `MAN_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Structure de la table `sr_users`
--

CREATE TABLE `sr_users` (
  `USR_ID` int(11) NOT NULL,
  `USR_MSG_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `sr_users`
--
ALTER TABLE `sr_users`
  ADD PRIMARY KEY (`USR_ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `sr_users`
--
ALTER TABLE `sr_users`
  MODIFY `USR_ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
