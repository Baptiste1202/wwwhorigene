-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mar. 15 avr. 2025 à 15:58
-- Version du serveur : 8.0.41
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vivacadb`
--
CREATE DATABASE IF NOT EXISTS `vivacadb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `vivacadb`;

-- --------------------------------------------------------

--
-- Structure de la table `collec`
--

CREATE TABLE `collec` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `collec`
--

INSERT INTO `collec` (`id`, `name`, `description`, `comment`) VALUES
(1, 'collection 1', 'description collection 1', 'commentaire collection 1'),
(2, 'collection 2', 'description collection 2', 'commentaire collection 2'),
(3, 'collection 3', 'description collection 3', 'commentaire collection 3'),
(4, 'collection 4', 'description collection 4', 'commentaire collection 4'),
(5, 'collection 5', 'description collection 5', 'commentaire collection 5'),
(6, 'collection 6', NULL, NULL),
(7, 'collection 6', NULL, NULL),
(8, 'collection 6', NULL, NULL),
(9, 'collection 6', NULL, NULL),
(10, 'collection 6', NULL, NULL),
(11, 'collection 6', NULL, NULL),
(12, 'collection 6', NULL, NULL),
(13, 'collection 6', NULL, NULL),
(14, 'collection 6', NULL, NULL),
(15, 'collection 6', NULL, NULL),
(16, 'collection 6', NULL, NULL),
(17, 'collection 6', NULL, NULL),
(18, 'collection 6', NULL, NULL),
(19, 'collection 6', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `depot`
--

CREATE TABLE `depot` (
  `id` int NOT NULL,
  `date_depot` date NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `user` varchar(255) NOT NULL,
  `sample` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250221100045', '2025-02-21 11:01:00', 3133),
('DoctrineMigrations\\Version20250221101445', '2025-02-21 11:14:51', 49),
('DoctrineMigrations\\Version20250221103055', '2025-02-21 11:31:00', 43),
('DoctrineMigrations\\Version20250221103752', '2025-02-21 11:38:02', 41),
('DoctrineMigrations\\Version20250221105157', '2025-02-21 11:52:01', 52),
('DoctrineMigrations\\Version20250221111640', '2025-02-21 12:16:45', 458),
('DoctrineMigrations\\Version20250221114718', '2025-02-21 12:47:23', 5),
('DoctrineMigrations\\Version20250221122142', '2025-02-21 13:21:47', 389),
('DoctrineMigrations\\Version20250221122617', '2025-02-21 13:26:22', 1),
('DoctrineMigrations\\Version20250221124509', '2025-02-21 13:45:15', 1),
('DoctrineMigrations\\Version20250221125041', '2025-02-21 13:50:46', 1),
('DoctrineMigrations\\Version20250221145316', '2025-02-21 15:53:24', 3),
('DoctrineMigrations\\Version20250221145521', '2025-02-21 15:55:28', 3),
('DoctrineMigrations\\Version20250221150222', '2025-02-21 16:02:37', 112),
('DoctrineMigrations\\Version20250228135845', '2025-02-28 14:58:51', 1),
('DoctrineMigrations\\Version20250228135927', '2025-02-28 14:59:33', 278),
('DoctrineMigrations\\Version20250228141204', '2025-02-28 15:12:11', 34),
('DoctrineMigrations\\Version20250228175719', '2025-02-28 18:57:24', 183),
('DoctrineMigrations\\Version20250228175802', '2025-02-28 18:58:07', 1),
('DoctrineMigrations\\Version20250322101756', '2025-03-22 11:18:02', 264),
('DoctrineMigrations\\Version20250328060948', '2025-03-28 07:09:54', 128),
('DoctrineMigrations\\Version20250328062205', '2025-03-28 07:22:09', 198),
('DoctrineMigrations\\Version20250328064935', '2025-03-28 07:49:39', 78),
('DoctrineMigrations\\Version20250328070505', '2025-03-28 08:05:09', 54),
('DoctrineMigrations\\Version20250328074427', '2025-03-28 08:44:33', 3),
('DoctrineMigrations\\Version20250328074556', '2025-03-28 08:46:02', 88),
('DoctrineMigrations\\Version20250411144505', '2025-04-11 16:45:09', 37),
('DoctrineMigrations\\Version20250411145649', '2025-04-11 16:56:53', 45),
('DoctrineMigrations\\Version20250411150137', '2025-04-11 17:01:40', 79),
('DoctrineMigrations\\Version20250414195911', '2025-04-14 21:59:16', 97);

-- --------------------------------------------------------

--
-- Structure de la table `drug_resistance`
--

CREATE TABLE `drug_resistance` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `drug_resistance`
--

INSERT INTO `drug_resistance` (`id`, `name`, `type`, `description`, `comment`) VALUES
(4, 'Peniciline', 'quantitative', NULL, NULL),
(5, 'Antibio', 'quantitative', NULL, NULL),
(6, 'Antibio', 'quantitative', NULL, NULL),
(7, 'Antibio', 'quantitative', NULL, NULL),
(8, 'Antibio', 'quantitative', NULL, NULL),
(9, 'Antibio', 'quantitative', NULL, NULL),
(10, 'Antibio', 'quantitative', NULL, NULL),
(11, 'Antibio', 'quantitative', NULL, NULL),
(12, 'Antibio', 'quantitative', NULL, NULL),
(13, 'Antibio', 'quantitative', NULL, NULL),
(14, 'Antibio', 'quantitative', NULL, NULL),
(15, 'Antibio', 'quantitative', NULL, NULL),
(16, 'Antibio', 'quantitative', NULL, NULL),
(17, 'Antibio', 'quantitative', NULL, NULL),
(18, 'Antibio', 'quantitative', NULL, NULL),
(19, 'Antibio', 'quantitative', NULL, NULL),
(20, 'Antibio', 'quantitative', NULL, NULL),
(21, 'Antibio', 'quantitative', NULL, NULL),
(22, 'Antibio', 'quantitative', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `drug_resistance_on_strain`
--

CREATE TABLE `drug_resistance_on_strain` (
  `id` int NOT NULL,
  `concentration` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `drug_resistance_id` int DEFAULT NULL,
  `strain_id` int DEFAULT NULL,
  `resistant` tinyint(1) DEFAULT NULL,
  `name_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `drug_resistance_on_strain`
--

INSERT INTO `drug_resistance_on_strain` (`id`, `concentration`, `description`, `comment`, `drug_resistance_id`, `strain_id`, `resistant`, `name_file`) VALUES
(9, 10, NULL, NULL, 4, 143, 1, 'capture-d-ecran-de-2024-10-16-10-14-56-67fd6dfb98046585669349.png');

-- --------------------------------------------------------

--
-- Structure de la table `file_sequencing`
--

CREATE TABLE `file_sequencing` (
  `id` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `genotype`
--

CREATE TABLE `genotype` (
  `id` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `genotype`
--

INSERT INTO `genotype` (`id`, `type`, `description`, `comment`) VALUES
(1, 'WT', NULL, NULL),
(2, 'MUTANT', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `method_sequencing`
--

CREATE TABLE `method_sequencing` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `name_file` varchar(255) DEFAULT NULL,
  `strain_id` int DEFAULT NULL,
  `type_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `method_sequencing`
--

INSERT INTO `method_sequencing` (`id`, `name`, `description`, `comment`, `name_file`, `strain_id`, `type_file`) VALUES
(20, 'aa', NULL, NULL, NULL, 67, NULL),
(21, 'aa', NULL, NULL, NULL, 68, NULL),
(22, 'aa', NULL, NULL, NULL, 69, NULL),
(23, 'blue frame', NULL, NULL, '04-mvp-concept-on-the-roadmap-67f92db8aeeb0067637372.png', 105, NULL),
(24, 'a', NULL, NULL, '04-mvp-concept-on-the-roadmap-67f92e034bf24870860507.png', 106, NULL),
(25, 'a', NULL, NULL, '04-mvp-concept-on-the-roadmap-67f92e0a1e2d1739049259.png', 107, NULL),
(26, NULL, NULL, NULL, '50-ans-renault-5-retromobile-02-crop-67f92ed700455120203282.jpg', 109, NULL),
(27, NULL, NULL, NULL, NULL, 109, NULL),
(28, NULL, NULL, NULL, '50-ans-renault-5-retromobile-02-crop-67f92edd83e34851522627.jpg', 110, NULL),
(29, NULL, NULL, NULL, NULL, 110, NULL),
(30, NULL, NULL, NULL, '50-ans-renault-5-retromobile-02-crop-67f92f038c488720586567.jpg', 111, NULL),
(31, NULL, NULL, NULL, NULL, 111, NULL),
(32, NULL, NULL, NULL, NULL, 112, NULL),
(33, NULL, NULL, NULL, NULL, 112, NULL),
(34, NULL, NULL, NULL, NULL, 113, NULL),
(35, NULL, NULL, NULL, NULL, 113, NULL),
(36, NULL, NULL, NULL, NULL, 114, NULL),
(37, NULL, NULL, NULL, NULL, 114, NULL),
(38, NULL, NULL, NULL, NULL, 115, NULL),
(39, NULL, NULL, NULL, NULL, 115, NULL),
(40, NULL, NULL, NULL, NULL, 116, NULL),
(41, NULL, NULL, NULL, NULL, 116, NULL),
(42, NULL, NULL, NULL, NULL, 117, NULL),
(43, NULL, NULL, NULL, NULL, 117, NULL),
(44, NULL, NULL, NULL, NULL, 118, NULL),
(45, NULL, NULL, NULL, NULL, 118, NULL),
(46, NULL, NULL, NULL, NULL, 119, NULL),
(47, NULL, NULL, NULL, NULL, 119, NULL),
(48, NULL, NULL, NULL, NULL, 120, NULL),
(49, NULL, NULL, NULL, NULL, 120, NULL),
(76, NULL, 'zz', 'test', 'capture-d-ecran-de-2024-10-16-10-14-56-67f949c8329f0585052187.png', 139, NULL),
(77, NULL, NULL, NULL, 'capture-d-ecran-du-2025-02-26-17-09-25-67f94b1028fbb163080007.png', 140, NULL),
(78, NULL, NULL, NULL, 'capture-d-ecran-du-2025-01-22-22-50-44-67f94b10293a9879659598.png', 140, NULL),
(79, NULL, NULL, NULL, 'capture-d-ecran-de-2024-09-26-09-46-56-67f94b3246a4a598836373.png', 141, NULL),
(80, NULL, NULL, NULL, NULL, 141, NULL),
(81, NULL, NULL, NULL, NULL, 141, NULL),
(82, NULL, NULL, NULL, 'capture-d-ecran-de-2024-09-26-09-46-56-67f94b4cd392f194763307.png', 142, NULL),
(83, NULL, NULL, NULL, 'capture-d-ecran-de-2024-10-16-10-14-56-67fe029ce377b800158534.png', 144, 'png'),
(84, NULL, NULL, NULL, 'capture-d-ecran-du-2025-01-22-22-51-58-67fe029ce494f265414656.png', 144, 'png');

-- --------------------------------------------------------

--
-- Structure de la table `plasmyd`
--

CREATE TABLE `plasmyd` (
  `id` int NOT NULL,
  `name_plasmyd` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `plasmyd`
--

INSERT INTO `plasmyd` (`id`, `name_plasmyd`, `type`, `description`, `comment`, `slug`) VALUES
(6, 'plasmyd A', 'wt', 'description plasmyd 6', 'commentaire plasmyd 6', 'plasmyd 6 - wt'),
(7, 'plasmyd B', 'wt', 'description 2', 'commentaire 2', 'plasmyd B - wt'),
(8, 'plasmyd C', 'wt', 'description 3', 'commentaire 3', 'plasmyd C - wt'),
(9, 'plasmyd D', 'wt', 'description 4', 'commentaire 4', 'plasmyd D - wt'),
(10, 'plasmyd E', 'wt', 'description 5', 'commentaire 5', 'plasmyd E - wt'),
(11, 'P2704', 'wt', NULL, NULL, 'P2704 - wt'),
(12, 'P2014', 'wt', NULL, NULL, 'P2014 - wt'),
(13, 'P2014', 'wt', NULL, NULL, 'P2014 - wt'),
(14, 'P2014', 'wt', NULL, NULL, 'P2014 - wt'),
(15, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(16, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(17, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(18, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(19, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(20, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(21, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(22, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(23, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic'),
(24, 'P2014', 'synthetic', NULL, NULL, 'P2014 - synthetic');

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE `project` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`id`, `name`, `description`, `comment`) VALUES
(2, 'project 2', 'description projet 2', 'commentaire projet 2'),
(5, 'project 5', 'description projet 5', 'commentaire projet 5'),
(6, 'proejt 6', NULL, NULL),
(7, 'proejt 6', NULL, NULL),
(8, 'proejt 6', NULL, NULL),
(9, 'proejt 6', NULL, NULL),
(10, 'proejt 6', NULL, NULL),
(11, 'proejt 6', NULL, NULL),
(12, 'proejt 6', NULL, NULL),
(13, 'proejt 6', NULL, NULL),
(14, 'proejt 6', NULL, NULL),
(15, 'proejt 6', NULL, NULL),
(16, 'proejt 6', NULL, NULL),
(17, 'proejt 6', NULL, NULL),
(18, 'proejt 6', NULL, NULL),
(19, 'proejt 6', NULL, NULL),
(20, 'proejt 6', NULL, NULL),
(21, 'proejt 6', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `publication`
--

CREATE TABLE `publication` (
  `id` int NOT NULL,
  `article_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `publication`
--

INSERT INTO `publication` (`id`, `article_url`, `title`, `autor`, `year`, `description`, `slug`) VALUES
(2, 'http://test.com', 'titre A', 'auteur A', '2025', 'description publication A', 'titre A - auteur A - 2025'),
(3, 'http://test.com', 'titre B', 'auteur B', '2025', 'description publication B', 'titre B - auteur B - 2025'),
(4, 'http://test.com', 'titre C', 'auteur C', '2025', 'description publication C', 'titre C - auteur C - 2025');

-- --------------------------------------------------------

--
-- Structure de la table `sample`
--

CREATE TABLE `sample` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `under_localisation` varchar(255) DEFAULT NULL,
  `gps` varchar(255) DEFAULT NULL,
  `environment` varchar(255) DEFAULT NULL,
  `other` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `sample`
--

INSERT INTO `sample` (`id`, `name`, `type`, `date`, `country`, `city`, `localisation`, `under_localisation`, `gps`, `environment`, `other`, `description`, `comment`, `user`) VALUES
(1, 'sample 1', NULL, '2025-02-26', 'FRANCE', 'Lyon', 'Université Lyon', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(2, 'sample 2', NULL, '2025-02-26', 'FRANCE', 'Lille', 'CHU Lille', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(3, 'sample 3', NULL, '2025-02-17', 'ITALIE', 'Rome', 'Colisée', NULL, NULL, NULL, NULL, NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Structure de la table `storage`
--

CREATE TABLE `storage` (
  `id` int NOT NULL,
  `room` varchar(255) DEFAULT NULL,
  `rack` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `strain_id` int DEFAULT NULL,
  `fridge` varchar(255) DEFAULT NULL,
  `shelf` varchar(255) DEFAULT NULL,
  `container_type` varchar(255) DEFAULT NULL,
  `container_position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `storage`
--

INSERT INTO `storage` (`id`, `room`, `rack`, `description`, `comment`, `strain_id`, `fridge`, `shelf`, `container_type`, `container_position`) VALUES
(1, 'aa', NULL, NULL, NULL, 64, NULL, NULL, NULL, NULL),
(2, 'a', 'a', NULL, NULL, 64, NULL, NULL, NULL, NULL),
(3, 'aa', NULL, NULL, NULL, 65, NULL, NULL, NULL, NULL),
(4, 'a', 'a', NULL, NULL, 65, NULL, NULL, NULL, NULL),
(5, 'aa', NULL, NULL, NULL, 66, NULL, NULL, NULL, NULL),
(6, 'a', 'a', NULL, NULL, 66, NULL, NULL, NULL, NULL),
(7, 'aa', NULL, NULL, NULL, 67, NULL, NULL, NULL, NULL),
(8, 'a', 'a', NULL, NULL, 67, NULL, NULL, NULL, NULL),
(9, 'aa', NULL, NULL, NULL, 68, NULL, NULL, NULL, NULL),
(10, 'a', 'a', NULL, NULL, 68, NULL, NULL, NULL, NULL),
(11, 'aa', NULL, NULL, NULL, 69, NULL, NULL, NULL, NULL),
(12, 'a', 'a', NULL, NULL, 69, NULL, NULL, NULL, NULL),
(13, 'aaa', NULL, NULL, NULL, 70, NULL, NULL, NULL, NULL),
(14, 'aaa', NULL, NULL, NULL, 71, NULL, NULL, NULL, NULL),
(15, 'bbbb', NULL, NULL, NULL, 72, NULL, NULL, NULL, NULL),
(16, 'aaa', NULL, NULL, NULL, 72, NULL, NULL, NULL, NULL),
(17, 'bbbb', NULL, NULL, NULL, 73, NULL, NULL, NULL, NULL),
(18, 'aaa', NULL, NULL, NULL, 73, NULL, NULL, NULL, NULL),
(19, 'bbbb', NULL, NULL, NULL, 74, NULL, NULL, NULL, NULL),
(20, 'aaa', NULL, NULL, NULL, 74, NULL, NULL, NULL, NULL),
(21, 'bbbb', NULL, NULL, NULL, 75, NULL, NULL, NULL, NULL),
(22, 'aaa', NULL, NULL, NULL, 75, NULL, NULL, NULL, NULL),
(23, 'bbbb', NULL, NULL, NULL, 76, NULL, NULL, NULL, NULL),
(24, 'aaa', NULL, NULL, NULL, 76, NULL, NULL, NULL, NULL),
(25, 'bbbb', NULL, NULL, NULL, 77, NULL, NULL, NULL, NULL),
(26, 'aaa', NULL, NULL, NULL, 77, NULL, NULL, NULL, NULL),
(27, 'zzzz', NULL, NULL, NULL, 78, NULL, NULL, NULL, NULL),
(28, 'zzzz', NULL, NULL, NULL, 79, NULL, NULL, NULL, NULL),
(29, 'zzzz', NULL, NULL, NULL, 80, NULL, NULL, NULL, NULL),
(30, 'aaaa', NULL, NULL, NULL, 81, NULL, NULL, NULL, NULL),
(31, 'zzzz', NULL, NULL, NULL, 81, NULL, NULL, NULL, NULL),
(32, 'aaaaaa', NULL, NULL, NULL, 82, 'bb', NULL, NULL, NULL),
(33, 'aaa', NULL, NULL, NULL, 82, 'b', NULL, NULL, NULL),
(34, 'aaaaaa', NULL, NULL, NULL, 83, 'bb', NULL, NULL, NULL),
(35, 'aaa', NULL, NULL, NULL, 83, 'b', NULL, NULL, NULL),
(36, 'cccccc', NULL, NULL, NULL, 84, 'd', NULL, NULL, NULL),
(37, 'aaaaaa', NULL, NULL, NULL, 84, 'bb', NULL, NULL, NULL),
(38, 'aaa', NULL, NULL, NULL, 84, 'b', NULL, NULL, NULL),
(39, 'cccccc', NULL, NULL, NULL, 85, 'd', NULL, NULL, NULL),
(40, 'aaaaaa', NULL, NULL, NULL, 85, 'bb', NULL, NULL, NULL),
(41, 'aaa', NULL, NULL, NULL, 85, 'b', NULL, NULL, NULL),
(42, 'cccccc', NULL, NULL, NULL, 86, 'd', NULL, NULL, NULL),
(43, 'aaaaaa', NULL, NULL, NULL, 86, 'bb', NULL, NULL, NULL),
(44, 'aaa', NULL, NULL, NULL, 86, 'b', NULL, NULL, NULL),
(45, 'cccccc', NULL, NULL, NULL, 87, 'd', NULL, NULL, NULL),
(46, 'aaaaaa', NULL, NULL, NULL, 87, 'bb', NULL, NULL, NULL),
(47, 'aaa', NULL, NULL, NULL, 87, 'b', NULL, NULL, NULL),
(48, 'cccccc', NULL, NULL, NULL, 88, 'd', NULL, NULL, NULL),
(49, 'aaaaaa', NULL, NULL, NULL, 88, 'bb', NULL, NULL, NULL),
(50, 'aaa', NULL, NULL, NULL, 88, 'b', NULL, NULL, NULL),
(51, 'cccccc', NULL, NULL, NULL, 89, 'd', NULL, NULL, NULL),
(52, 'aaaaaa', NULL, NULL, NULL, 89, 'bb', NULL, NULL, NULL),
(53, 'aaa', NULL, NULL, NULL, 89, 'b', NULL, NULL, NULL),
(54, 'cccccc', NULL, NULL, NULL, 90, 'd', NULL, NULL, NULL),
(55, 'aaaaaa', NULL, NULL, NULL, 90, 'bb', NULL, NULL, NULL),
(56, 'aaa', NULL, NULL, NULL, 90, 'b', NULL, NULL, NULL),
(57, 'cccccc', NULL, NULL, NULL, 91, 'd', NULL, NULL, NULL),
(58, 'aaaaaa', NULL, NULL, NULL, 91, 'bb', NULL, NULL, NULL),
(59, 'aaa', NULL, NULL, NULL, 91, 'b', NULL, NULL, NULL),
(60, 'cccccc', NULL, NULL, NULL, 92, 'd', NULL, NULL, NULL),
(61, 'aaaaaa', NULL, NULL, NULL, 92, 'bb', NULL, NULL, NULL),
(62, 'aaa', NULL, NULL, NULL, 92, 'b', NULL, NULL, NULL),
(63, 'cccccc', NULL, NULL, NULL, 93, 'd', NULL, NULL, NULL),
(64, 'aaaaaa', NULL, NULL, NULL, 93, 'bb', NULL, NULL, NULL),
(65, 'aaa', NULL, NULL, NULL, 93, 'b', NULL, NULL, NULL),
(66, 'cccccc', NULL, NULL, NULL, 94, 'd', NULL, NULL, NULL),
(67, 'aaaaaa', NULL, NULL, NULL, 94, 'bb', NULL, NULL, NULL),
(68, 'aaa', NULL, NULL, NULL, 94, 'b', NULL, NULL, NULL),
(69, 'cccccc', NULL, NULL, NULL, 95, 'd', NULL, NULL, NULL),
(70, 'aaaaaa', NULL, NULL, NULL, 95, 'bb', NULL, NULL, NULL),
(71, 'aaa', NULL, NULL, NULL, 95, 'b', NULL, NULL, NULL),
(72, 'cccccc', NULL, NULL, NULL, 96, 'd', NULL, NULL, NULL),
(73, 'aaaaaa', NULL, NULL, NULL, 96, 'bb', NULL, NULL, NULL),
(74, 'aaa', NULL, NULL, NULL, 96, 'b', NULL, NULL, NULL),
(75, 'cccccc', NULL, NULL, NULL, 97, 'd', NULL, NULL, NULL),
(76, 'aaaaaa', NULL, NULL, NULL, 97, 'bb', NULL, NULL, NULL),
(77, 'aaa', NULL, NULL, NULL, 97, 'b', NULL, NULL, NULL),
(78, 'cccccc', NULL, NULL, NULL, 98, 'd', NULL, NULL, NULL),
(79, 'aaaaaa', NULL, NULL, NULL, 98, 'bb', NULL, NULL, NULL),
(80, 'aaa', NULL, NULL, NULL, 98, 'b', NULL, NULL, NULL),
(81, 'cccccc', NULL, NULL, NULL, 99, 'd', NULL, NULL, NULL),
(82, 'aaaaaa', NULL, NULL, NULL, 99, 'bb', NULL, NULL, NULL),
(83, 'aaa', NULL, NULL, NULL, 99, 'b', NULL, NULL, NULL),
(84, 'cccccc', NULL, NULL, NULL, 100, 'd', NULL, NULL, NULL),
(85, 'aaaaaa', NULL, NULL, NULL, 100, 'bb', NULL, NULL, NULL),
(86, 'aaa', NULL, NULL, NULL, 100, 'b', NULL, NULL, NULL),
(87, 'cccccc', NULL, NULL, NULL, 101, 'd', NULL, NULL, NULL),
(88, 'aaaaaa', NULL, NULL, NULL, 101, 'bb', NULL, NULL, NULL),
(89, 'aaa', NULL, NULL, NULL, 101, 'b', NULL, NULL, NULL),
(90, 'cccccc', NULL, NULL, NULL, 102, 'd', NULL, NULL, NULL),
(91, 'aaaaaa', NULL, NULL, NULL, 102, 'bb', NULL, NULL, NULL),
(92, 'aaa', NULL, NULL, NULL, 102, 'b', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `strain`
--

CREATE TABLE `strain` (
  `id` int NOT NULL,
  `name_strain` varchar(255) NOT NULL,
  `specie` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `parent_strain_id` int DEFAULT NULL,
  `genotype_id` int DEFAULT NULL,
  `depot_id` int DEFAULT NULL,
  `prelevement_id` int DEFAULT NULL,
  `created_by_id` int NOT NULL,
  `description_genotype` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `strain`
--

INSERT INTO `strain` (`id`, `name_strain`, `specie`, `gender`, `comment`, `description`, `created_by_name`, `date`, `parent_strain_id`, `genotype_id`, `depot_id`, `prelevement_id`, `created_by_id`, `description_genotype`) VALUES
(61, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(62, 'aalea', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(63, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(64, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(65, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(66, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(67, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(68, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(69, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(70, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(71, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(72, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(73, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(74, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(75, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(76, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(77, 'aaaaa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(78, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(79, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(80, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(81, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(82, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(83, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(84, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(85, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(86, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(87, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(88, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(89, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(90, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(91, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(92, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(93, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(94, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(95, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(96, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(97, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, NULL, NULL, NULL, 1, NULL),
(98, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, 1, NULL, NULL, 1, 'il est beau'),
(99, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, 1, NULL, NULL, 1, 'il est beau'),
(100, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, 1, NULL, NULL, 1, 'il est beau'),
(101, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, 1, NULL, NULL, 1, 'il est beau'),
(102, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-03-28', NULL, 1, NULL, NULL, 1, 'il est beau'),
(103, 'AA', 'A', 'A', NULL, NULL, 'BERTRAND Baptiste', '2025-04-03', NULL, NULL, NULL, 1, 1, NULL),
(105, 'aa', 'aa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(106, 'aaaa', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(107, 'aaaa', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(109, 'aaaa5', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(110, 'aaaa5', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(111, 'aaaa5', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(112, 'aaaa6', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(113, 'aaaa7', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(114, 'aaaa87', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(115, 'aaaa87', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(116, 'aaaa88', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(117, 'aaaa88', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(118, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(119, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(120, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(130, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, 1, NULL, NULL, 1, NULL),
(131, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, 1, NULL, NULL, 1, NULL),
(132, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, 1, NULL, NULL, 1, NULL),
(133, 'aaaa89', 'aaa', 'aa', 'aha', 'ihi', 'BERTRAND Baptiste', '2025-04-11', NULL, 1, NULL, 2, 1, NULL),
(134, 'aaaa89', 'aaa', 'aa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, 1, NULL, 2, 1, NULL),
(135, 'aaaa89', 'aaa', 'aa', 'aha', 'ihi', 'BERTRAND Baptiste', '2025-04-11', NULL, 2, NULL, NULL, 1, NULL),
(136, 'aaaa89', 'aaa', 'aa', 'aha', 'ihi', 'BERTRAND Baptiste', '2025-04-11', 133, 2, NULL, NULL, 1, NULL),
(137, 'aaa', 'aaaaaa', 'aaaa', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', 61, 2, NULL, NULL, 1, NULL),
(138, 'bbbb', 'bbbbb', 'bb', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(139, 'bbbb', 'bbbbb', 'bb', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(140, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(141, 'a', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(142, 'aa', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-11', NULL, NULL, NULL, NULL, 1, NULL),
(143, 'ccc', 'c', 'c', NULL, NULL, 'BERTRAND Baptiste', '2025-04-14', NULL, NULL, NULL, NULL, 1, NULL),
(144, 'test transfo', 'specie', 'gender', NULL, NULL, 'BERTRAND Baptiste', '2025-04-15', NULL, NULL, NULL, NULL, 1, NULL),
(145, 'test plasmd', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-15', NULL, NULL, NULL, NULL, 1, NULL),
(146, 'tast', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-15', NULL, NULL, NULL, NULL, 1, NULL),
(147, 'tast', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-15', NULL, NULL, NULL, NULL, 1, NULL),
(148, 'teast plasmyd', 'a', 'a', NULL, NULL, 'BERTRAND Baptiste', '2025-04-15', NULL, NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `strain_collec`
--

CREATE TABLE `strain_collec` (
  `strain_id` int NOT NULL,
  `collec_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `strain_plasmyd`
--

CREATE TABLE `strain_plasmyd` (
  `strain_id` int NOT NULL,
  `plasmyd_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `strain_plasmyd`
--

INSERT INTO `strain_plasmyd` (`strain_id`, `plasmyd_id`) VALUES
(145, 6),
(146, 6),
(148, 7),
(148, 8);

-- --------------------------------------------------------

--
-- Structure de la table `strain_project`
--

CREATE TABLE `strain_project` (
  `strain_id` int NOT NULL,
  `project_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `strain_project`
--

INSERT INTO `strain_project` (`strain_id`, `project_id`) VALUES
(138, 5);

-- --------------------------------------------------------

--
-- Structure de la table `strain_publication`
--

CREATE TABLE `strain_publication` (
  `strain_id` int NOT NULL,
  `publication_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transformability`
--

CREATE TABLE `transformability` (
  `id` int NOT NULL,
  `technique` varchar(255) DEFAULT NULL,
  `mesure` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `strain_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `transformability`
--

INSERT INTO `transformability` (`id`, `technique`, `mesure`, `description`, `comment`, `nom`, `strain_id`) VALUES
(13, 'aaa', 'mesure', NULL, NULL, 'capture-d-ecran-du-2025-01-22-22-52-59-67fe029ce244c701406781.png', 144),
(14, 'bbb', 'mesure', NULL, NULL, 'capture-d-ecran-du-2025-01-22-22-54-23-67fe029ce2943626071629.png', 144);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) NOT NULL,
  `firstname` varchar(180) NOT NULL,
  `lastname` varchar(180) NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `firstname`, `lastname`, `roles`, `password`, `is_verified`, `created_at`) VALUES
(1, 'test@test.fr', 'Baptiste', 'BERTRAND', '[\"ROLE_ADMIN\"]', '$2y$13$KcrqG0c.6TXur02GyCfxdet4TKPvES/mMYmSJd7bbj3nF0bblD5LO', 0, '2025-02-21 11:01:51');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `collec`
--
ALTER TABLE `collec`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `depot`
--
ALTER TABLE `depot`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `drug_resistance`
--
ALTER TABLE `drug_resistance`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_623B5112A3654322` (`drug_resistance_id`),
  ADD KEY `IDX_623B511269B9E007` (`strain_id`);

--
-- Index pour la table `file_sequencing`
--
ALTER TABLE `file_sequencing`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `genotype`
--
ALTER TABLE `genotype`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DCBFD3B969B9E007` (`strain_id`);

--
-- Index pour la table `plasmyd`
--
ALTER TABLE `plasmyd`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `sample`
--
ALTER TABLE `sample`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_547A1B3469B9E007` (`strain_id`);

--
-- Index pour la table `strain`
--
ALTER TABLE `strain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A630CD7298F2CFE9` (`parent_strain_id`),
  ADD KEY `IDX_A630CD72E0EC46AF` (`genotype_id`),
  ADD KEY `IDX_A630CD728510D4DE` (`depot_id`),
  ADD KEY `IDX_A630CD72CE389617` (`prelevement_id`),
  ADD KEY `IDX_A630CD72B03A8386` (`created_by_id`);

--
-- Index pour la table `strain_collec`
--
ALTER TABLE `strain_collec`
  ADD PRIMARY KEY (`strain_id`,`collec_id`),
  ADD KEY `IDX_3E5F3C8069B9E007` (`strain_id`),
  ADD KEY `IDX_3E5F3C80584D4E9A` (`collec_id`);

--
-- Index pour la table `strain_plasmyd`
--
ALTER TABLE `strain_plasmyd`
  ADD PRIMARY KEY (`strain_id`,`plasmyd_id`),
  ADD KEY `IDX_EB2D4A3669B9E007` (`strain_id`),
  ADD KEY `IDX_EB2D4A363B91781` (`plasmyd_id`);

--
-- Index pour la table `strain_project`
--
ALTER TABLE `strain_project`
  ADD PRIMARY KEY (`strain_id`,`project_id`),
  ADD KEY `IDX_E35934AE69B9E007` (`strain_id`),
  ADD KEY `IDX_E35934AE166D1F9C` (`project_id`);

--
-- Index pour la table `strain_publication`
--
ALTER TABLE `strain_publication`
  ADD PRIMARY KEY (`strain_id`,`publication_id`),
  ADD KEY `IDX_AC80D2C569B9E007` (`strain_id`),
  ADD KEY `IDX_AC80D2C538B217A7` (`publication_id`);

--
-- Index pour la table `transformability`
--
ALTER TABLE `transformability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5AB058BE69B9E007` (`strain_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `collec`
--
ALTER TABLE `collec`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `depot`
--
ALTER TABLE `depot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `drug_resistance`
--
ALTER TABLE `drug_resistance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `file_sequencing`
--
ALTER TABLE `file_sequencing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `genotype`
--
ALTER TABLE `genotype`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT pour la table `plasmyd`
--
ALTER TABLE `plasmyd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `project`
--
ALTER TABLE `project`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `sample`
--
ALTER TABLE `sample`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `storage`
--
ALTER TABLE `storage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT pour la table `strain`
--
ALTER TABLE `strain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT pour la table `transformability`
--
ALTER TABLE `transformability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  ADD CONSTRAINT `FK_623B511269B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`),
  ADD CONSTRAINT `FK_623B5112A3654322` FOREIGN KEY (`drug_resistance_id`) REFERENCES `drug_resistance` (`id`);

--
-- Contraintes pour la table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  ADD CONSTRAINT `FK_DCBFD3B969B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `storage`
--
ALTER TABLE `storage`
  ADD CONSTRAINT `FK_547A1B3469B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `strain`
--
ALTER TABLE `strain`
  ADD CONSTRAINT `FK_A630CD728510D4DE` FOREIGN KEY (`depot_id`) REFERENCES `depot` (`id`),
  ADD CONSTRAINT `FK_A630CD7298F2CFE9` FOREIGN KEY (`parent_strain_id`) REFERENCES `strain` (`id`),
  ADD CONSTRAINT `FK_A630CD72B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_A630CD72CE389617` FOREIGN KEY (`prelevement_id`) REFERENCES `sample` (`id`),
  ADD CONSTRAINT `FK_A630CD72E0EC46AF` FOREIGN KEY (`genotype_id`) REFERENCES `genotype` (`id`);

--
-- Contraintes pour la table `strain_collec`
--
ALTER TABLE `strain_collec`
  ADD CONSTRAINT `FK_3E5F3C80584D4E9A` FOREIGN KEY (`collec_id`) REFERENCES `collec` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3E5F3C8069B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `strain_plasmyd`
--
ALTER TABLE `strain_plasmyd`
  ADD CONSTRAINT `FK_EB2D4A363B91781` FOREIGN KEY (`plasmyd_id`) REFERENCES `plasmyd` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EB2D4A3669B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `strain_project`
--
ALTER TABLE `strain_project`
  ADD CONSTRAINT `FK_E35934AE166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E35934AE69B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `strain_publication`
--
ALTER TABLE `strain_publication`
  ADD CONSTRAINT `FK_AC80D2C538B217A7` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_AC80D2C569B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Contraintes pour la table `transformability`
--
ALTER TABLE `transformability`
  ADD CONSTRAINT `FK_5AB058BE69B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
