-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Jul 01, 2025 at 12:28 AM
-- Server version: 8.0.42
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vivacadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `collec`
--

CREATE TABLE `collec` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `collec`
--

INSERT INTO `collec` (`id`, `name`, `description`, `comment`) VALUES
(1, 'collection 1', 'description collection 1', 'commentaire collection 1'),
(2, 'collection 2', 'description collection 2', 'commentaire collection 2'),
(3, 'collection 3', 'description collection 3', 'commentaire collection 3'),
(4, 'collection 4', 'description collection 4', 'commentaire collection 4'),
(5, 'collection 5', 'description collection 5', 'commentaire collection 5');

-- --------------------------------------------------------

--
-- Table structure for table `depot`
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
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctrine_migration_versions`
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
('DoctrineMigrations\\Version20250414195911', '2025-04-14 21:59:16', 97),
('DoctrineMigrations\\Version20250602101448', '2025-06-03 15:53:43', 134),
('DoctrineMigrations\\Version20250602102435', '2025-06-03 16:01:36', 91);

-- --------------------------------------------------------

--
-- Table structure for table `drug_resistance`
--

CREATE TABLE `drug_resistance` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `drug_resistance`
--

INSERT INTO `drug_resistance` (`id`, `name`, `type`, `description`, `comment`) VALUES
(23, 'Pénicillines', 'quantitative', NULL, NULL),
(24, 'Céphalosporines', 'quantitative', 'ex. céfazoline', NULL),
(25, 'Macrolides', 'quantitative', 'ex. érythromycine', NULL),
(26, 'Fluoroquinolones', 'quantitative', 'ex. ciprofloxacine', NULL),
(27, 'Apramycine', 'quantitative', NULL, NULL),
(28, 'Tétracycline', 'quantitative', NULL, NULL),
(29, 'Imipénème', 'quantitative', NULL, NULL),
(30, 'Rifampicine', 'quantitative', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drug_resistance_on_strain`
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
-- Dumping data for table `drug_resistance_on_strain`
--

INSERT INTO `drug_resistance_on_strain` (`id`, `concentration`, `description`, `comment`, `drug_resistance_id`, `strain_id`, `resistant`, `name_file`) VALUES
(12, 30, NULL, NULL, 27, 188, 1, 'screenshot-from-2025-06-28-04-40-52-68602bc71af16146324939.png'),
(13, 10, NULL, NULL, 28, 189, 1, 'screenshot-from-2025-06-28-04-40-56-68602cfa5dda5771807734.png'),
(14, 100, NULL, NULL, 27, 190, 1, NULL),
(15, 100, NULL, NULL, 26, 190, 0, 'screenshot-from-2025-06-28-04-41-07-68602ec383f1a522413453.png'),
(16, 50, NULL, NULL, 30, 191, 0, NULL),
(17, 20, NULL, NULL, 25, 215, 1, 'screenshot-from-2025-06-28-20-12-20-68632b74ea2bb529568061.png');

-- --------------------------------------------------------

--
-- Table structure for table `file_sequencing`
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
-- Table structure for table `genotype`
--

CREATE TABLE `genotype` (
  `id` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `genotype`
--

INSERT INTO `genotype` (`id`, `type`, `description`, `comment`) VALUES
(1, 'WT', NULL, NULL),
(2, 'GMO', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `method_sequencing`
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
-- Dumping data for table `method_sequencing`
--

INSERT INTO `method_sequencing` (`id`, `name`, `description`, `comment`, `name_file`, `strain_id`, `type_file`) VALUES
(91, 'illumina', NULL, 'RAS', 'screenshot-from-2025-06-28-04-40-46-68602accb675d016392663.png', 187, 'png'),
(92, 'illumina', NULL, NULL, 'screenshot-from-2025-06-28-04-41-02-68602bc71ba62928583165.png', 188, 'png'),
(93, 'illumina', NULL, NULL, 'screenshot-from-2025-06-28-04-41-02-68602cfa5e535798994682.png', 189, 'png'),
(94, 'illumina2', NULL, NULL, 'screenshot-from-2025-06-28-04-40-46-68602cfa5e918261988326.png', 189, 'png'),
(95, 'illumina', NULL, NULL, 'screenshot-from-2025-06-28-04-40-52-68602ec384324466872199.png', 190, 'png'),
(96, 'illumina', NULL, NULL, 'screenshot-from-2025-06-28-04-41-02-68632b74eb91a455226991.png', 215, 'png'),
(97, 'illumina2', NULL, NULL, 'screenshot-from-2025-06-28-04-41-07-68632b74ec0ac871972459.png', 215, 'png');

-- --------------------------------------------------------

--
-- Table structure for table `plasmyd`
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
-- Dumping data for table `plasmyd`
--

INSERT INTO `plasmyd` (`id`, `name_plasmyd`, `type`, `description`, `comment`, `slug`) VALUES
(25, 'pUC19', 'wt', 'Origine ColE1, ampicilline, MCS', NULL, 'pUC19 - wt'),
(27, 'pGEX-4T-1', 'wt', 'Ampicilline, GST tag', NULL, 'pGEX-4T-1 - wt'),
(28, 'p2AB5075', 'synthetic', 'tetA + the other natural plasmids', NULL, 'p2AB5075 - synthetic'),
(30, 'pBluescript SK(−)', 'wt', NULL, NULL, 'pBluescript SK(−) - wt'),
(31, 'pJET2.1', 'wt', 'Origine ColE1, ampicilline, MCS', NULL, 'pJET2.1 - wt');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `name`, `description`, `comment`) VALUES
(1, 'project 1', 'description projet 1', 'commentaire projet 1'),
(2, 'project 2', 'description projet 2', 'commentaire projet 2'),
(3, 'project 3', 'description projet 3', 'commentaire projet 3'),
(4, 'project 4', 'description projet 4', 'commentaire projet 4'),
(5, 'project 5', 'description projet 5', 'commentaire projet 5');

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

CREATE TABLE `publication` (
  `id` int NOT NULL,
  `article_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `autor` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`id`, `article_url`, `title`, `autor`, `year`, `description`, `slug`, `doi`) VALUES
(2, 'http://test.com', 'titre A', 'auteur A', '2025', 'description publication A', 'titre A - auteur A - 2025', NULL),
(3, 'http://test.com', 'titre B', 'auteur B', '2025', 'description publication B', 'titre B - auteur B - 2025', NULL),
(4, 'http://test.com', 'titre C', 'auteur C', '2025', 'description publication C', 'titre C - auteur C - 2025', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sample`
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
-- Dumping data for table `sample`
--

INSERT INTO `sample` (`id`, `name`, `type`, `date`, `country`, `city`, `localisation`, `under_localisation`, `gps`, `environment`, `other`, `description`, `comment`, `user`) VALUES
(1, 'sample 1', NULL, '2025-02-26', 'FRANCE', 'Lyon', 'Université Lyon', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(2, 'sample 2', NULL, '2025-02-26', 'FRANCE', 'Lille', 'CHU Lille', NULL, NULL, NULL, NULL, NULL, NULL, '1'),
(3, 'sample 3', NULL, '2025-02-17', 'ITALIE', 'Rome', 'Colisée', NULL, NULL, NULL, NULL, NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `storage`
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
  `container_position` varchar(255) DEFAULT NULL,
  `volume` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `storage`
--

INSERT INTO `storage` (`id`, `room`, `rack`, `description`, `comment`, `strain_id`, `fridge`, `shelf`, `container_type`, `container_position`, `volume`) VALUES
(99, 'room 1', 'rack 1', NULL, NULL, 191, 'cong 1', 'etag 1', 'boite 1', '1-2-3-4', '100yl'),
(100, 'room 2', 'rack 2', NULL, NULL, 191, 'cong 2', 'etag 2', 'boite 2', '1-2-3-4', '100yl'),
(101, 'room 1', 'rack 1', NULL, NULL, 215, 'cong 1', 'etag 1', 'boite 1', '1-2-3-4', '1ml');

-- --------------------------------------------------------

--
-- Table structure for table `strain`
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
-- Dumping data for table `strain`
--

INSERT INTO `strain` (`id`, `name_strain`, `specie`, `gender`, `comment`, `description`, `created_by_name`, `date`, `parent_strain_id`, `genotype_id`, `depot_id`, `prelevement_id`, `created_by_id`, `description_genotype`) VALUES
(163, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(170, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(171, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(172, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(173, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(174, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(175, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(176, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(178, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(179, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(180, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(181, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(182, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(183, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(185, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(186, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, NULL, 1, NULL),
(187, 'AB5075', 'baumannii', 'Acinetobacter', 'Obtained from the collin\'s lab.', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, 1, 1, NULL),
(188, 'AB5075', 'baumannii', 'Acinetobacter', NULL, NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 2, 1, 'comM(FL) pho::ApraR #2'),
(189, 'AB5075', 'baumannii', 'Acinetobacter', 'Insertion of the tetA gene on p2AB5075', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, 3, 1, NULL),
(190, 'DH5α', 'Escherichia coli', 'Escherichia', NULL, NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 3, 1, 'Δ(araD-araB)567, ΔlacZ4787'),
(191, 'M2', 'nosocomialis', 'Acinetobacter', 'Made by co-culturing of M2 rpoB (Box6C4) with 40288 ∆comEC::aac, annotated as AB25', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 2, NULL, 3, 1, 'rpoB* vgrG::Tn2006 #7'),
(200, 'AB5075', 'baumannii', 'Acinetobacter', 'premiere souche', NULL, 'BERTRAND Baptiste', '2025-06-28', NULL, 1, NULL, NULL, 1, NULL),
(215, 'AB5075', 'baumannii', 'Acinetobacter', 'test 0107', NULL, 'BERTRAND Baptiste', '2025-07-01', NULL, 1, NULL, 2, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `strain_collec`
--

CREATE TABLE `strain_collec` (
  `strain_id` int NOT NULL,
  `collec_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `strain_collec`
--

INSERT INTO `strain_collec` (`strain_id`, `collec_id`) VALUES
(188, 3),
(188, 4),
(189, 5),
(191, 4),
(215, 1);

-- --------------------------------------------------------

--
-- Table structure for table `strain_plasmyd`
--

CREATE TABLE `strain_plasmyd` (
  `strain_id` int NOT NULL,
  `plasmyd_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `strain_plasmyd`
--

INSERT INTO `strain_plasmyd` (`strain_id`, `plasmyd_id`) VALUES
(189, 28),
(190, 31),
(215, 25),
(215, 28);

-- --------------------------------------------------------

--
-- Table structure for table `strain_project`
--

CREATE TABLE `strain_project` (
  `strain_id` int NOT NULL,
  `project_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `strain_project`
--

INSERT INTO `strain_project` (`strain_id`, `project_id`) VALUES
(187, 1),
(187, 2),
(188, 2),
(189, 5),
(191, 4),
(215, 1),
(215, 2);

-- --------------------------------------------------------

--
-- Table structure for table `strain_publication`
--

CREATE TABLE `strain_publication` (
  `strain_id` int NOT NULL,
  `publication_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `strain_publication`
--

INSERT INTO `strain_publication` (`strain_id`, `publication_id`) VALUES
(189, 2),
(190, 3),
(191, 3),
(215, 3);

-- --------------------------------------------------------

--
-- Table structure for table `transformability`
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
-- Dumping data for table `transformability`
--

INSERT INTO `transformability` (`id`, `technique`, `mesure`, `description`, `comment`, `nom`, `strain_id`) VALUES
(18, 'technique transfo1', 'experience 1', NULL, NULL, 'screenshot-from-2025-06-28-04-41-07-68602ec356858138111207.png', 190),
(19, 'technique transfo2', 'experience 2', NULL, NULL, '25124816-docking-68602ec38222a178757968.txt', 190),
(20, 'technique transfo3', 'experience3', NULL, NULL, 'screenshot-from-2025-06-28-04-40-46-6860305e59b7e641196712.png', 191);

-- --------------------------------------------------------

--
-- Table structure for table `user`
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `firstname`, `lastname`, `roles`, `password`, `is_verified`, `created_at`) VALUES
(1, 'test@test.fr', 'Baptiste', 'BERTRAND', '[\"ROLE_ADMIN\"]', '$2y$13$KcrqG0c.6TXur02GyCfxdet4TKPvES/mMYmSJd7bbj3nF0bblD5LO', 0, '2025-02-21 11:01:51'),
(2, 'nicolasgaudin13@gmail.com', 'nicolas', 'gaudin', '[]', '$2y$13$NSkc8NeSiCzahAnqq3b3fuc6oS6mnXBJCQ3ZlU1Oa95IvoQndQGHe', 0, '2025-06-03 17:10:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `collec`
--
ALTER TABLE `collec`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `depot`
--
ALTER TABLE `depot`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `drug_resistance`
--
ALTER TABLE `drug_resistance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_623B5112A3654322` (`drug_resistance_id`),
  ADD KEY `IDX_623B511269B9E007` (`strain_id`);

--
-- Indexes for table `file_sequencing`
--
ALTER TABLE `file_sequencing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genotype`
--
ALTER TABLE `genotype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DCBFD3B969B9E007` (`strain_id`);

--
-- Indexes for table `plasmyd`
--
ALTER TABLE `plasmyd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publication`
--
ALTER TABLE `publication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sample`
--
ALTER TABLE `sample`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_547A1B3469B9E007` (`strain_id`);

--
-- Indexes for table `strain`
--
ALTER TABLE `strain`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A630CD7298F2CFE9` (`parent_strain_id`),
  ADD KEY `IDX_A630CD72E0EC46AF` (`genotype_id`),
  ADD KEY `IDX_A630CD728510D4DE` (`depot_id`),
  ADD KEY `IDX_A630CD72CE389617` (`prelevement_id`),
  ADD KEY `IDX_A630CD72B03A8386` (`created_by_id`);

--
-- Indexes for table `strain_collec`
--
ALTER TABLE `strain_collec`
  ADD PRIMARY KEY (`strain_id`,`collec_id`),
  ADD KEY `IDX_3E5F3C8069B9E007` (`strain_id`),
  ADD KEY `IDX_3E5F3C80584D4E9A` (`collec_id`);

--
-- Indexes for table `strain_plasmyd`
--
ALTER TABLE `strain_plasmyd`
  ADD PRIMARY KEY (`strain_id`,`plasmyd_id`),
  ADD KEY `IDX_EB2D4A3669B9E007` (`strain_id`),
  ADD KEY `IDX_EB2D4A363B91781` (`plasmyd_id`);

--
-- Indexes for table `strain_project`
--
ALTER TABLE `strain_project`
  ADD PRIMARY KEY (`strain_id`,`project_id`),
  ADD KEY `IDX_E35934AE69B9E007` (`strain_id`),
  ADD KEY `IDX_E35934AE166D1F9C` (`project_id`);

--
-- Indexes for table `strain_publication`
--
ALTER TABLE `strain_publication`
  ADD PRIMARY KEY (`strain_id`,`publication_id`),
  ADD KEY `IDX_AC80D2C569B9E007` (`strain_id`),
  ADD KEY `IDX_AC80D2C538B217A7` (`publication_id`);

--
-- Indexes for table `transformability`
--
ALTER TABLE `transformability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5AB058BE69B9E007` (`strain_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `collec`
--
ALTER TABLE `collec`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `depot`
--
ALTER TABLE `depot`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drug_resistance`
--
ALTER TABLE `drug_resistance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `file_sequencing`
--
ALTER TABLE `file_sequencing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genotype`
--
ALTER TABLE `genotype`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `plasmyd`
--
ALTER TABLE `plasmyd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `publication`
--
ALTER TABLE `publication`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sample`
--
ALTER TABLE `sample`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `storage`
--
ALTER TABLE `storage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `strain`
--
ALTER TABLE `strain`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `transformability`
--
ALTER TABLE `transformability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drug_resistance_on_strain`
--
ALTER TABLE `drug_resistance_on_strain`
  ADD CONSTRAINT `FK_623B511269B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`),
  ADD CONSTRAINT `FK_623B5112A3654322` FOREIGN KEY (`drug_resistance_id`) REFERENCES `drug_resistance` (`id`);

--
-- Constraints for table `method_sequencing`
--
ALTER TABLE `method_sequencing`
  ADD CONSTRAINT `FK_DCBFD3B969B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `storage`
--
ALTER TABLE `storage`
  ADD CONSTRAINT `FK_547A1B3469B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `strain`
--
ALTER TABLE `strain`
  ADD CONSTRAINT `FK_A630CD728510D4DE` FOREIGN KEY (`depot_id`) REFERENCES `depot` (`id`),
  ADD CONSTRAINT `FK_A630CD7298F2CFE9` FOREIGN KEY (`parent_strain_id`) REFERENCES `strain` (`id`),
  ADD CONSTRAINT `FK_A630CD72B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_A630CD72CE389617` FOREIGN KEY (`prelevement_id`) REFERENCES `sample` (`id`),
  ADD CONSTRAINT `FK_A630CD72E0EC46AF` FOREIGN KEY (`genotype_id`) REFERENCES `genotype` (`id`);

--
-- Constraints for table `strain_collec`
--
ALTER TABLE `strain_collec`
  ADD CONSTRAINT `FK_3E5F3C80584D4E9A` FOREIGN KEY (`collec_id`) REFERENCES `collec` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3E5F3C8069B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `strain_plasmyd`
--
ALTER TABLE `strain_plasmyd`
  ADD CONSTRAINT `FK_EB2D4A363B91781` FOREIGN KEY (`plasmyd_id`) REFERENCES `plasmyd` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_EB2D4A3669B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `strain_project`
--
ALTER TABLE `strain_project`
  ADD CONSTRAINT `FK_E35934AE166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E35934AE69B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `strain_publication`
--
ALTER TABLE `strain_publication`
  ADD CONSTRAINT `FK_AC80D2C538B217A7` FOREIGN KEY (`publication_id`) REFERENCES `publication` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_AC80D2C569B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);

--
-- Constraints for table `transformability`
--
ALTER TABLE `transformability`
  ADD CONSTRAINT `FK_5AB058BE69B9E007` FOREIGN KEY (`strain_id`) REFERENCES `strain` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
