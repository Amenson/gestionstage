-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 29 jan. 2026 à 20:23
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `formatec_stages`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `idCandidature` int NOT NULL AUTO_INCREMENT,
  `fkEtudiant` int DEFAULT NULL,
  `fkStage` int DEFAULT NULL,
  `dateCandidature` date DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'En attente',
  PRIMARY KEY (`idCandidature`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`idCandidature`, `fkEtudiant`, `fkStage`, `dateCandidature`, `statut`) VALUES
(1, NULL, 3, '2026-01-20', 'En attente'),
(2, NULL, 4, '2026-01-20', 'En attente'),
(3, NULL, 4, '2026-01-20', 'En attente'),
(4, NULL, 4, '2026-01-20', 'En attente'),
(5, NULL, 3, '2026-01-20', 'En attente'),
(6, 2, 2, '2026-01-20', 'En attente'),
(7, 2, 4, '2026-01-20', 'En attente');

-- --------------------------------------------------------

--
-- Structure de la table `competence`
--

DROP TABLE IF EXISTS `competence`;
CREATE TABLE IF NOT EXISTS `competence` (
  `codeCompet` int NOT NULL AUTO_INCREMENT,
  `typeCompet` varchar(50) DEFAULT NULL,
  `libCompet` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`codeCompet`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `competence`
--

INSERT INTO `competence` (`codeCompet`, `typeCompet`, `libCompet`) VALUES
(1, NULL, 'PHP'),
(2, NULL, 'MySQL'),
(3, NULL, 'Java'),
(4, NULL, 'Réseaux'),
(5, NULL, 'Cybersécurité'),
(6, NULL, 'HTML/CSS'),
(7, NULL, 'JavaScript'),
(8, 'java,photoshop', 'développement de logiciel'),
(9, 'technique', 'php, maintenance'),
(10, 'technique', 'php, maintenance'),
(11, 'technique', 'php, maintenance'),
(12, 'technique', 'php, maintenance'),
(13, 'technique', 'php, maintenance'),
(14, 'technique', 'php, maintenance'),
(15, 'technique', 'php, maintenance'),
(16, 'technique', 'php, maintenance'),
(17, 'technique', 'php, maintenance'),
(18, 'technique', 'php, maintenance'),
(19, 'technique', 'php, maintenance'),
(20, 'technique', 'php, maintenance'),
(21, 'technique', 'php, maintenance'),
(22, 'technique', 'php, maintenance'),
(23, 'technique', 'php, maintenance'),
(24, 'technique', 'php, maintenance'),
(25, 'technique', 'php, maintenance'),
(26, 'technique', 'php, maintenance'),
(27, 'technique', 'php, maintenance'),
(28, 'technique', 'php, maintenance'),
(29, 'Reseau', 'maintenance , montage');

-- --------------------------------------------------------

--
-- Structure de la table `domaineactivite`
--

DROP TABLE IF EXISTS `domaineactivite`;
CREATE TABLE IF NOT EXISTS `domaineactivite` (
  `codeDomaineAct` int NOT NULL AUTO_INCREMENT,
  `libDomaineAct` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codeDomaineAct`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `domaineactivite`
--

INSERT INTO `domaineactivite` (`codeDomaineAct`, `libDomaineAct`) VALUES
(1, 'Informatique'),
(2, 'Systeme réseau'),
(3, 'Développement Web'),
(4, 'Réseaux'),
(5, 'Sécurité Informatique'),
(6, 'Intelligence Artificielle'),
(7, 'Base de données'),
(8, 'Maintenance Informatique');

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `numSiret` int NOT NULL AUTO_INCREMENT,
  `nomEntreprise` varchar(100) DEFAULT NULL,
  `numVoieEntreprise` int DEFAULT NULL,
  `voieEntreprise` varchar(100) DEFAULT NULL,
  `cpEntreprise` varchar(10) DEFAULT NULL,
  `villeEntreprise` varchar(50) DEFAULT NULL,
  `telEntreprise` varchar(20) DEFAULT NULL,
  `mailEntreprise` varchar(100) DEFAULT NULL,
  `fkTypeEntreprise` int DEFAULT NULL,
  `fkDomaineAct` int DEFAULT NULL,
  PRIMARY KEY (`numSiret`),
  KEY `fkTypeEntreprise` (`fkTypeEntreprise`),
  KEY `fkDomaineAct` (`fkDomaineAct`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`numSiret`, `nomEntreprise`, `numVoieEntreprise`, `voieEntreprise`, `cpEntreprise`, `villeEntreprise`, `telEntreprise`, `mailEntreprise`, `fkTypeEntreprise`, `fkDomaineAct`) VALUES
(1, 'Telecom TG', 1, 'AVENUE 2', '3141', 'LOME', '93814645', 'yas@gmail.fr', 1, 2),
(2, 'TechNova', 12, 'Rue de la République', '75001', 'Paris', '0145789654', 'contact@technova.fr', 1, 2),
(3, 'BatiPlus', 45, 'Avenue Jean Jaurès', '69007', 'Lyon', '0478123456', 'info@batiplus.fr', 2, 3),
(4, 'GreenFood', 8, 'Chemin des Oliviers', '34000', 'Montpellier', '0467128899', 'contact@greenfood.fr', 3, 1),
(5, 'WOODO', 8, 'rue national', '314', 'LOME', '93814640', 'WOODO@gmail.fr', 6, 1),
(6, 'ciam', 3, 'AVENUE 2', '3141', 'LOME', '93814645', 'WOODO@gmail.fr', 14, 8);

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

DROP TABLE IF EXISTS `etudiant`;
CREATE TABLE IF NOT EXISTS `etudiant` (
  `codeEtud` int NOT NULL AUTO_INCREMENT,
  `nomEtud` varchar(50) DEFAULT NULL,
  `prenomEtud` varchar(50) DEFAULT NULL,
  `sexeEtud` varchar(10) DEFAULT NULL,
  `dateNaissEtud` date DEFAULT NULL,
  `photoEtud` varchar(255) DEFAULT NULL,
  `voieEtud` varchar(50) DEFAULT NULL,
  `cpEtud` varchar(10) DEFAULT NULL,
  `villeEtud` varchar(50) DEFAULT NULL,
  `telEtud` varchar(20) DEFAULT NULL,
  `mailEtud` varchar(100) DEFAULT NULL,
  `statutEtud` varchar(20) DEFAULT NULL,
  `fkPays` int DEFAULT NULL,
  `passwordEtud` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`codeEtud`),
  KEY `fkPays` (`fkPays`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`codeEtud`, `nomEtud`, `prenomEtud`, `sexeEtud`, `dateNaissEtud`, `photoEtud`, `voieEtud`, `cpEtud`, `villeEtud`, `telEtud`, `mailEtud`, `statutEtud`, `fkPays`, `passwordEtud`) VALUES
(1, 'Amen', 'Amenson', 'M', '2025-12-29', NULL, 'leo2000', '314', 'LOME', '93814645', 'AMEN@gmail.com', 'LP DA2', NULL, '1234'),
(2, 'Kossi', 'Mensah', 'M', '2001-03-15', NULL, 'Rue 12', '00228', 'Lomé', '90000000', 'kossi@gmail.com', 'L3', 1, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'KPATCHA', 'jolie', 'Féminin ', '2006-02-20', '', 'Cacaveli', '123', 'Atakpamé', '99757811', 'jolie@gmail.com', 'Actif', 2, '1234');

-- --------------------------------------------------------

--
-- Structure de la table `exiger`
--

DROP TABLE IF EXISTS `exiger`;
CREATE TABLE IF NOT EXISTS `exiger` (
  `numOffre` int NOT NULL,
  `codeCompet` int NOT NULL,
  `degreMaitrise` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`numOffre`,`codeCompet`),
  KEY `codeCompet` (`codeCompet`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `exiger`
--

INSERT INTO `exiger` (`numOffre`, `codeCompet`, `degreMaitrise`) VALUES
(1, 1, 'Débutant'),
(1, 2, 'Intermédiaire'),
(1, 3, 'Avancé'),
(2, 4, 'Intermédiaire'),
(2, 5, 'Débutant'),
(3, 1, 'Avancé'),
(3, 6, 'Intermédiaire');

-- --------------------------------------------------------

--
-- Structure de la table `notestage`
--

DROP TABLE IF EXISTS `notestage`;
CREATE TABLE IF NOT EXISTS `notestage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `numOffre` int DEFAULT NULL,
  `numCritere` varchar(50) DEFAULT NULL,
  `noteStage` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `numOffre` (`numOffre`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notestage`
--

INSERT INTO `notestage` (`id`, `numOffre`, `numCritere`, `noteStage`) VALUES
(1, NULL, '', 0),
(2, 3, 'Soutenance', 17),
(3, 1, 'rapport', 10),
(4, 3, 'Rapport', 12),
(5, 4, 'Rapport', 12);

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

DROP TABLE IF EXISTS `pays`;
CREATE TABLE IF NOT EXISTS `pays` (
  `codePays` int NOT NULL AUTO_INCREMENT,
  `libPays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codePays`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `pays`
--

INSERT INTO `pays` (`codePays`, `libPays`) VALUES
(1, 'Togo'),
(2, 'Bénin'),
(3, 'Ghana'),
(4, 'Côte d\'Ivoire'),
(5, 'Burkina Faso'),
(6, 'France'),
(7, 'TOGO'),
(8, 'TOGO');

-- --------------------------------------------------------

--
-- Structure de la table `professeur`
--

DROP TABLE IF EXISTS `professeur`;
CREATE TABLE IF NOT EXISTS `professeur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12347 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `professeur`
--

INSERT INTO `professeur` (`id`, `login`, `password`, `grade`) VALUES
(1, 'admin', '1234', 'Responsable'),
(2, 'amenson', 'amenson93@', 'Admin'),
(12345, NULL, NULL, NULL),
(12346, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stage`
--

DROP TABLE IF EXISTS `stage`;
CREATE TABLE IF NOT EXISTS `stage` (
  `numOffre` int NOT NULL AUTO_INCREMENT,
  `libStage` varchar(100) DEFAULT NULL,
  `dateParution` date DEFAULT NULL,
  `periodeStage` varchar(50) DEFAULT NULL,
  `moisStage` varchar(20) DEFAULT NULL,
  `descStage` text,
  `fonctionsStage` text,
  `remunerationStage` varchar(50) DEFAULT NULL,
  `mailContact` varchar(100) DEFAULT NULL,
  `fkTypeStage` int DEFAULT NULL,
  `fkEntreprise` int DEFAULT NULL,
  `fkEtudiant` int DEFAULT NULL,
  PRIMARY KEY (`numOffre`),
  KEY `fkTypeStage` (`fkTypeStage`),
  KEY `fkEntreprise` (`fkEntreprise`),
  KEY `fkEtudiant` (`fkEtudiant`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `stage`
--

INSERT INTO `stage` (`numOffre`, `libStage`, `dateParution`, `periodeStage`, `moisStage`, `descStage`, `fonctionsStage`, `remunerationStage`, `mailContact`, `fkTypeStage`, `fkEntreprise`, `fkEtudiant`) VALUES
(1, 'Stage Développeur Web', '2026-01-05', 'Stage de 3 mois', 'Mars à Mai', 'Participation au développement d’applications web.', 'Développement front-end, tests, maintenance.', '60000 CFA/mois', 'recrutement@technova.fr', 1, 1, 1),
(2, 'Stage Assistant Marketing', '2026-01-10', 'Stage de 2 mois', 'Avril à Mai', 'Aide à la mise en place de campagnes marketing.', 'Gestion réseaux sociaux, analyse des performances.', '50000 CFA/mois', 'contact@batiplus.fr', 2, 2, NULL),
(3, 'Stage Maintenance Informatique', '2026-01-12', 'Stage de 4 mois', 'Février à Juin', 'Support et maintenance du parc informatique.', 'Assistance utilisateurs, dépannage matériel.', 'Gratification légale', 'stage@greenfood.fr', 3, 3, 12),
(4, 'stage de design', '2026-01-12', '2021', '2', 'designeur pour le pefectionnement', 'être apte pour constructure des graphique', '20000CFA', 'amenson@gmail.com', 1, 3, 2),
(5, 'Stage programmation web', '2026-01-20', '10-01-2026', '3', 'esprit compétitif', 'programmeurs', '200000', 'jolie@gmail.com', 5, 1, NULL),
(10, 'Info-Web', '2026-01-21', '10-01-2026', '3', 'Stage de perfectionnement pour le graphisme', '', '200000', 'jolie@gmail.com', 5, 5, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `typeentreprise`
--

DROP TABLE IF EXISTS `typeentreprise`;
CREATE TABLE IF NOT EXISTS `typeentreprise` (
  `codeTypeEntr` int NOT NULL AUTO_INCREMENT,
  `libEntr` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codeTypeEntr`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `typeentreprise`
--

INSERT INTO `typeentreprise` (`codeTypeEntr`, `libEntr`) VALUES
(1, 'Yas togo'),
(2, 'Yas togo'),
(3, 'Tmoney'),
(4, 'Formatec'),
(5, 'Formatec'),
(6, 'stage info'),
(7, 'Entreprise informatique'),
(8, 'Banque'),
(9, 'Télécommunication'),
(10, 'Industrie'),
(11, 'ONG'),
(12, 'Administration publique'),
(13, 'École / Université'),
(14, 'stage info');

-- --------------------------------------------------------

--
-- Structure de la table `typestage`
--

DROP TABLE IF EXISTS `typestage`;
CREATE TABLE IF NOT EXISTS `typestage` (
  `codeTypeStage` int NOT NULL AUTO_INCREMENT,
  `libTypeStage` varchar(50) DEFAULT NULL,
  `dureeStage` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`codeTypeStage`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `typestage`
--

INSERT INTO `typestage` (`codeTypeStage`, `libTypeStage`, `dureeStage`) VALUES
(1, 'Stage d\'initiation', NULL),
(2, 'Stage de perfectionnement', NULL),
(3, 'Stage professionnel', NULL),
(4, 'Stage de fin d\'études', NULL),
(5, 'Stage de recherche', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
