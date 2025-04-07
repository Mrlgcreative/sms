-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 04 avr. 2025 à 22:14
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `college1`
--

-- --------------------------------------------------------

--
-- Structure de la table `achats_fournitures`
--

CREATE TABLE `achats_fournitures` (
  `id` int(11) NOT NULL,
  `date_achat` date NOT NULL,
  `fournisseur` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `quantite` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `facture_ref` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text NOT NULL,
  `created_at` datetime NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `active_sessions`
--

INSERT INTO `active_sessions` (`id`, `user_id`, `session_id`, `ip_address`, `user_agent`, `created_at`, `last_activity`) VALUES
(6, 21, '9f9l5omdvq8q7ng84ujcb7o8ag', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-02 18:57:23', '2025-04-02 18:57:23'),
(7, 20, 'nvnap7qnn5g2lh3ecbf95r1rf0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 19:04:07', '2025-04-02 19:04:07'),
(9, 20, 'gqqequ1p091mjkls4of8j57pec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 10:15:26', '2025-04-03 10:15:26'),
(10, 21, '0mg18usq7882c89c3pi46bkdk1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-03 12:31:03', '2025-04-03 12:31:03'),
(13, 33, 'c6gfl065lonkbtvn4adcq1voec', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 15:37:11', '2025-04-03 15:37:11'),
(14, 33, '1nlpc558kacd3gbgjm9keeh8aq', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 15:43:57', '2025-04-03 15:43:57'),
(15, 33, '3pab8p1p6h4hg49la8ppg7aaq1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-03 15:55:45', '2025-04-03 15:55:45'),
(17, 33, '6rhgbdrjfct9rkqd63mje2oif0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 16:02:08', '2025-04-03 16:02:08'),
(21, 21, 'at65irpimtp1gkrdpnnqnds2je', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-03 19:50:09', '2025-04-03 19:50:09'),
(22, 21, 'dmbgknvn67ailf75jr94iuu6n0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 07:45:25', '2025-04-04 07:45:25'),
(23, 20, 'haben5c3ufvnq5pmuq02uil183', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 08:14:11', '2025-04-04 08:14:11'),
(24, 20, 'fbbk3hjmd5lr66iip8486qjqmv', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 11:37:09', '2025-04-04 11:37:09'),
(25, 20, 'ccncogavqhsifki8p0cke075hr', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 15:37:35', '2025-04-04 15:37:35'),
(27, 21, 'ob7dph37la4ascc94e18i4bj0i', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 18:50:42', '2025-04-04 18:50:42'),
(30, 20, 'ujbkkgkmb404is3p0k5un2dfdk', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-04 19:49:27', '2025-04-04 19:49:27'),
(32, 22, 'dihld8m3us08fm0qop5ld5dlju', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-04 20:48:26', '2025-04-04 20:48:26');

-- --------------------------------------------------------

--
-- Structure de la table `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `blocked_ips`
--

CREATE TABLE `blocked_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` text NOT NULL,
  `block_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `blocked_ips`
--

INSERT INTO `blocked_ips` (`id`, `ip_address`, `reason`, `block_date`, `expiry_date`) VALUES
(1, '::1', 'Trop de tentatives de connexion échouées', '2025-04-03 14:56:48', '2025-04-03 15:26:48');

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `section` enum('maternelle','primaire','secondaire') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `section`) VALUES
(1, '1er', NULL),
(2, '2ieme', NULL),
(3, '3eme', 'maternelle'),
(4, '4eme', 'primaire');

-- --------------------------------------------------------

--
-- Structure de la table `comptable`
--

CREATE TABLE `comptable` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `professeur_id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL,
  `section` enum('maternelle','primaire','secondaire') NOT NULL,
  `option_` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `directeur`
--

CREATE TABLE `directeur` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `section` enum('primaire','secondaire','maternel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `directeur`
--

INSERT INTO `directeur` (`id`, `nom`, `prenom`, `contact`, `email`, `adresse`, `section`) VALUES
(1, 'moi', 'lui', '0979099031', 'moillui@gmail.com', 'morse', ''),
(2, 'Twende-mbele', 'Gloire Lumingu', '+(243) 9933-18385', 'gloirelumingu10@gmail.com', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'primaire');

-- --------------------------------------------------------

--
-- Structure de la table `directrice`
--

CREATE TABLE `directrice` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `section` enum('primaire','secondaire','maternel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `directrice`
--

INSERT INTO `directrice` (`id`, `nom`, `prenom`, `contact`, `email`, `adresse`, `section`) VALUES
(1, 'Mujinga', 'Ciella', '0979099031', 'ciella@gmail.com', 'Rva', 'maternel'),
(2, 'board', 'Mr sky', '+(243) 9933-18385', 'sky@mail.com', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', '');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `post_nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `sexe` enum('M','F') NOT NULL,
  `lieu_naissance` varchar(150) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `section` enum('maternelle','primaire','secondaire') NOT NULL,
  `classe` enum('1er','2eme','3eme','4eme','5eme','6eme','7eme','8eme') NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `nom_pere` varchar(100) DEFAULT NULL,
  `nom_mere` varchar(100) DEFAULT NULL,
  `contact_pere` varchar(15) DEFAULT NULL,
  `contact_mere` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `option` enum('EB','scientifique','commerciale','mecanique auto','mecanique generale','literature','electronique','electricite','pedagogie genrale') DEFAULT NULL,
  `session_scolaire_id` int(11) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `nom`, `post_nom`, `prenom`, `date_naissance`, `sexe`, `lieu_naissance`, `adresse`, `section`, `classe`, `option_id`, `nom_pere`, `nom_mere`, `contact_pere`, `contact_mere`, `created_at`, `updated_at`, `option`, `session_scolaire_id`, `statut`) VALUES
(45, 'Manga Ochiwa', 'Lumingu', 'Manga', '2025-03-30', 'M', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'primaire', '1er', 1, 'Gloire Lumingu', 'Twende-mbele', '09709987', '09887434', '2025-03-30 13:22:09', '2025-03-30 13:22:09', NULL, NULL, 'actif'),
(47, 'Mr sky board', 'Lui', 'moi', '2022-02-02', '', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'secondaire', '7eme', 8, NULL, NULL, NULL, NULL, '2025-04-01 06:50:14', '2025-04-01 06:50:14', NULL, NULL, 'actif'),
(48, 'Twende-mbele', 'lui', 'moi', '2025-04-01', '', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'primaire', '6eme', NULL, NULL, NULL, NULL, NULL, '2025-04-01 06:52:39', '2025-04-01 06:52:39', NULL, NULL, 'actif'),
(49, 'fabrice ', 'Kapend', 'Manga', '2025-04-01', '', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'maternelle', '3eme', NULL, NULL, NULL, NULL, NULL, '2025-04-01 06:54:48', '2025-04-01 06:54:48', NULL, NULL, 'actif'),
(51, 'sky board', 'lui', 'Gloire', '2025-04-01', 'M', 'kolwezi', '71105', 'primaire', '2eme', NULL, 'Gloire', 'Lumingu', '09709987', '09887434', '2025-04-01 09:18:18', '2025-04-01 09:18:18', NULL, NULL, 'actif'),
(52, 'do', 're', 'mi', '2025-04-01', 'M', 'kolwezi', 'Lupweshi', 'secondaire', '', 4, 'sky', 'board', '09709987', '09887434', '2025-04-01 09:46:34', '2025-04-01 09:46:34', NULL, NULL, 'actif'),
(53, 'Gloire Lumingu', 'lui', 'Mr sky', '2025-04-01', 'M', 'kolwezi', '71105', 'maternelle', '2eme', NULL, 'Mr sky', 'board', '09709987', '09887434', '2025-04-01 09:51:48', '2025-04-01 09:51:48', NULL, NULL, 'actif'),
(54, 'moi lui', 'Lumingu', 'Sky', '2025-04-01', 'M', 'Likasi', '71105', 'maternelle', '2eme', NULL, 'Exou', 'KAPEND', '+243 97 90 99 0', '+243 89 07 91 9', '2025-04-01 13:24:29', '2025-04-01 13:24:29', NULL, NULL, 'actif'),
(55, 'sky board', 'Lumingu', 'Manga', '2025-04-01', 'M', 'kolwezi', '71105', 'secondaire', '2eme', 3, 'moi', 'lui', '09709987', '09887434', '2025-04-01 14:45:32', '2025-04-01 14:45:32', NULL, NULL, 'actif'),
(56, 'sky board', 'Kapend', 'moi', '2025-04-01', 'M', 'kolwezi', '71105', 'maternelle', '2eme', NULL, 'Mr sky', 'board', '09709987', '09887434', '2025-04-01 14:50:49', '2025-04-01 14:50:49', NULL, NULL, 'actif'),
(57, 'kasong', 'godefroid', 'Manga', '2025-04-02', 'M', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'maternelle', '2eme', NULL, 'sky', 'board', '09709987', '09887434', '2025-04-02 06:51:48', '2025-04-02 06:51:48', NULL, NULL, 'actif'),
(58, 'kas', 'tshsola', 'malika', '2025-04-02', 'M', 'kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'secondaire', '8eme', 1, 'moi', 'lui', '09709987', '09887434', '2025-04-02 08:50:05', '2025-04-02 08:50:05', NULL, NULL, 'actif'),
(59, 'mainel', 'mudib', 'mandib', '2025-04-02', 'M', 'Fungurume', 'Kansolondo', 'maternelle', '2eme', NULL, 'sky', 'board', '09709987', '09887434', '2025-04-02 13:41:07', '2025-04-02 13:41:07', NULL, NULL, 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `eleves_archives`
--

CREATE TABLE `eleves_archives` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `post_nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `nom_pere` varchar(100) DEFAULT NULL,
  `nom_mere` varchar(100) DEFAULT NULL,
  `contact_pere` varchar(20) DEFAULT NULL,
  `contact_mere` varchar(20) DEFAULT NULL,
  `date_suppression` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `employes`
--

CREATE TABLE `employes` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `poste` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `adresse` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `employes`
--

INSERT INTO `employes` (`id`, `nom`, `prenom`, `email`, `contact`, `poste`, `created_at`, `adresse`) VALUES
(2, 'board', 'sky', 'gloirelumingu1@gmail.com', '+(001) 8376-38290', 'femme de menage', '2025-02-27 11:43:39', ''),
(3, 'board', 'Mr sky', 'sky@mail.com', '+(243) 9787-866765', 'gardien', '2025-02-27 12:17:20', NULL),
(9, 'Ochiwa', 'Manga', 'manga02@gmail.com', '+(243) 9098-99031', 'vendeuse', '2025-03-22 18:43:37', 'Kolwezi.manika, Moïse Tshombe, mbembe,48');

-- --------------------------------------------------------

--
-- Structure de la table `evenements_scolaires`
--

CREATE TABLE `evenements_scolaires` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `couleur` varchar(50) DEFAULT '#3c8dbc',
  `statut` enum('planifie','en_cours','termine','annule') DEFAULT 'planifie',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenements_scolaires`
--

INSERT INTO `evenements_scolaires` (`id`, `titre`, `description`, `date_debut`, `date_fin`, `lieu`, `responsable`, `couleur`, `statut`, `date_creation`, `date_modification`, `user_id`) VALUES
(1, 'Maths', 'jkj', '2025-04-04 12:56:00', '2025-04-18 19:59:00', 'kj', NULL, '', 'planifie', '2025-04-04 10:56:48', '2025-04-04 10:58:36', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `failed_logins`
--

CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `frais`
--

CREATE TABLE `frais` (
  `id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL,
  `section` enum('primaire','secondaire','maternelle') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `frais`
--

INSERT INTO `frais` (`id`, `montant`, `description`, `section`, `created_at`) VALUES
(10, 65.00, 'Frais mensuel_maternelle', 'maternelle', '2025-04-02 17:27:50');

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `date_action` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `historique`
--

INSERT INTO `historique` (`id`, `user_id`, `action`, `date_action`) VALUES
(2, 20, 'Suppression', '2025-03-30 10:28:01'),
(3, 20, 'Suppression', '2025-03-30 10:48:04'),
(4, 20, 'Suppression', '2025-03-30 10:48:10'),
(5, 20, 'Suppression', '2025-03-30 12:17:32'),
(6, 20, 'Suppression', '2025-03-30 12:17:34'),
(7, 20, 'Suppression', '2025-03-30 12:17:37'),
(8, 20, 'Suppression', '2025-03-30 12:17:39'),
(9, 20, 'Suppression', '2025-03-30 12:27:07'),
(10, 20, 'Suppression', '2025-04-01 08:56:22'),
(11, 20, 'Suppression', '2025-04-01 08:56:26'),
(12, 20, 'Suppression', '2025-04-02 16:20:50'),
(13, 20, 'Suppression', '2025-04-02 16:20:57'),
(14, 20, 'Suppression', '2025-04-02 17:25:55'),
(15, 20, 'Suppression', '2025-04-02 17:26:05'),
(16, 20, 'Suppression', '2025-04-02 17:26:09');

-- --------------------------------------------------------

--
-- Structure de la table `horaires`
--

CREATE TABLE `horaires` (
  `id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL,
  `cour_id` int(11) NOT NULL,
  `jour_de_semaine` varchar(255) NOT NULL,
  `debut_de_cours` time DEFAULT NULL,
  `fin_cours` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `level` varchar(20) NOT NULL DEFAULT 'INFO',
  `username` varchar(100) NOT NULL,
  `action` text NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `context` text DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `level`, `username`, `action`, `message`, `ip_address`, `context`, `date`) VALUES
(1, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-02 15:06:56'),
(2, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-02 15:19:31'),
(3, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-02 15:24:22'),
(4, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-02 15:26:54'),
(5, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-02 15:26:55'),
(6, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-02 15:31:04'),
(7, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-02 15:36:02'),
(8, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-02 15:36:02'),
(9, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-02 15:37:21'),
(10, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-02 15:37:32'),
(11, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-02 15:37:32'),
(12, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-02 17:40:02'),
(13, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-02 17:40:14'),
(14, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-02 17:40:14'),
(15, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-02 18:22:23'),
(16, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-02 18:57:23'),
(17, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-02 18:57:23'),
(18, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-02 19:03:55'),
(19, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-02 19:04:07'),
(20, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-02 19:04:07'),
(21, 21, 'INFO', 'Daniel', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-02 20:28:11'),
(22, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-02 20:28:36'),
(23, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-02 20:28:36'),
(24, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-02 20:40:36'),
(25, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-03 10:15:18'),
(26, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-03 10:15:27'),
(27, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-03 10:15:27'),
(28, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-03 12:31:03'),
(29, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-03 12:31:03'),
(30, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-03 14:29:51'),
(31, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-03 14:29:57'),
(32, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-03 14:29:57'),
(33, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-03 14:43:10'),
(34, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 14:47:14'),
(35, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 14:47:55'),
(36, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 14:48:44'),
(37, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 14:48:47'),
(38, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedi\",\"ip\":\"::1\"}', '2025-04-03 14:56:27'),
(39, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Compte verrouillé après trop de tentatives', '::1', ' {\"username\":\"Plamedi\",\"ip\":\"::1\"}', '2025-04-03 14:56:48'),
(40, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:50'),
(41, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:52'),
(42, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:53'),
(43, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:54'),
(44, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:56'),
(45, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 14:56:57'),
(46, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 15:04:57'),
(47, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 15:05:03'),
(48, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 15:05:17'),
(49, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-03 15:05:19'),
(50, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"Plamedie\",\"role\":\"directrice\"}', '2025-04-03 15:31:13'),
(51, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-03 15:31:18'),
(52, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 15:31:18'),
(53, 33, 'INFO', 'Plamedie', 'Déconnexion', '', NULL, NULL, '2025-04-03 15:37:08'),
(54, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-03 15:37:11'),
(55, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 15:37:11'),
(56, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-03 15:43:57'),
(57, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 15:43:57'),
(58, 33, 'INFO', 'Plamedie', 'Changement de mot de passe', '', NULL, NULL, '2025-04-03 15:47:36'),
(59, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Mot de passe changé avec succès', '::1', ' {\"user_id\":33}', '2025-04-03 15:47:36'),
(60, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-03 15:55:45'),
(61, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 15:55:45'),
(62, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-03 15:59:38'),
(63, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-03 15:59:38'),
(64, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-03 16:01:59'),
(65, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-03 16:02:08'),
(66, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-03 16:02:08'),
(67, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-03 16:02:08'),
(68, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-03 16:05:53'),
(69, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-03 16:05:53'),
(70, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-03 16:05:53'),
(71, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-03 16:06:00'),
(72, 0, 'INFO', 'Exou', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-03 16:06:10'),
(73, 22, 'INFO', 'Exou', 'Connexion réussie', '', NULL, NULL, '2025-04-03 16:06:10'),
(74, 0, 'INFO', 'Exou', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Exou\",\"ip\":\"::1\"}', '2025-04-03 16:06:10'),
(75, 22, 'INFO', 'Exou', 'Déconnexion', '', NULL, NULL, '2025-04-03 16:12:26'),
(76, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"Fab\",\"role\":\"directrice\"}', '2025-04-03 16:13:42'),
(77, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Fab\",\"ip\":\"::1\"}', '2025-04-03 16:14:59'),
(78, 0, 'INFO', 'Fab', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-03 16:15:27'),
(79, 34, 'INFO', 'Fab', 'Connexion réussie', '', NULL, NULL, '2025-04-03 16:15:27'),
(80, 0, 'INFO', 'Fab', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Fab\",\"ip\":\"::1\"}', '2025-04-03 16:15:27'),
(81, 34, 'INFO', 'Fab', 'Déconnexion', '', NULL, NULL, '2025-04-03 16:18:59'),
(82, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-03 19:50:09'),
(83, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-03 19:50:09'),
(84, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-03 19:50:09'),
(85, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 07:45:25'),
(86, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-04 07:45:25'),
(87, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-04 07:45:25'),
(88, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 08:13:58'),
(89, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-04 08:14:11'),
(90, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-04 08:14:11'),
(91, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 08:14:12'),
(92, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-04 11:36:59'),
(93, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-04 11:37:09'),
(94, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-04 11:37:09'),
(95, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 11:37:09'),
(96, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-04 15:37:35'),
(97, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-04 15:37:35'),
(98, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 15:37:35'),
(99, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-04 18:44:09'),
(100, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-04 18:46:50'),
(101, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-04 18:46:50'),
(102, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 18:46:50'),
(103, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-04 18:50:32'),
(104, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 18:50:42'),
(105, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-04 18:50:42'),
(106, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-04 18:50:42'),
(107, 21, 'INFO', 'Daniel', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-04 19:41:51'),
(108, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 19:42:01'),
(109, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-04 19:42:01'),
(110, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-04 19:42:01'),
(111, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-04 19:42:26'),
(112, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 19:42:32'),
(113, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-04 19:42:32'),
(114, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-04 19:42:32'),
(115, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-04 19:49:17'),
(116, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-04 19:49:27'),
(117, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-04 19:49:27'),
(118, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-04 19:49:27'),
(119, 0, 'INFO', 'Exou', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 20:20:01'),
(120, 22, 'INFO', 'Exou', 'Connexion réussie', '', NULL, NULL, '2025-04-04 20:20:01'),
(121, 0, 'INFO', 'Exou', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Exou\",\"ip\":\"::1\"}', '2025-04-04 20:20:01'),
(122, 22, 'INFO', 'Exou', 'Déconnexion', '', NULL, NULL, '2025-04-04 20:48:23'),
(123, 0, 'INFO', 'Exou', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-04 20:48:26'),
(124, 22, 'INFO', 'Exou', 'Connexion réussie', '', NULL, NULL, '2025-04-04 20:48:26'),
(125, 0, 'INFO', 'Exou', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Exou\",\"ip\":\"::1\"}', '2025-04-04 20:48:26');

-- --------------------------------------------------------

--
-- Structure de la table `mois`
--

CREATE TABLE `mois` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `mois`
--

INSERT INTO `mois` (`id`, `nom`) VALUES
(1, 'janvier'),
(2, 'fevrier'),
(3, 'mars'),
(4, 'avril'),
(5, 'mai'),
(6, 'juin'),
(7, 'septembre'),
(8, 'octobre'),
(9, 'novembre'),
(10, 'decembre');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `nom`, `created_at`) VALUES
(1, 'scientifique', '2025-02-28 20:16:51'),
(2, 'commerciale', '2025-02-28 20:16:51'),
(3, 'pedagogie genrale', '2025-02-28 20:17:55'),
(4, 'electronique', '2025-02-28 20:17:55'),
(5, 'electricite', '2025-02-28 20:18:40'),
(6, 'mecanique auto', '2025-02-28 20:18:40'),
(7, 'mecanique generale', '2025-02-28 20:19:14'),
(8, 'EB', '2025-03-21 10:30:42'),
(9, 'literaire', '2025-03-21 10:30:42');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `professeur_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiements_frais`
--

CREATE TABLE `paiements_frais` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `moi_id` int(11) NOT NULL,
  `classe` varchar(10) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `section` enum('maternelle','primaire','secondaire') DEFAULT NULL,
  `frais_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements_frais`
--

INSERT INTO `paiements_frais` (`id`, `eleve_id`, `amount_paid`, `payment_date`, `created_at`, `moi_id`, `classe`, `option_id`, `section`, `frais_id`) VALUES
(13, 45, 300.00, '2025-03-31', '2025-03-30 22:00:00', 1, '1er', 0, 'primaire', 6),
(14, 46, 300.00, '2025-03-31', '2025-03-30 22:00:00', 1, '1er', 0, 'secondaire', 6),
(16, 45, 300.00, '2025-04-01', '2025-05-09 22:00:00', 2, '1er', 0, 'primaire', 6),
(17, 46, 300.00, '2025-04-01', '2025-03-31 22:00:00', 2, '1er', 0, 'secondaire', 6),
(18, 46, 200.00, '2025-04-01', '2025-03-31 22:00:00', 1, '1er', NULL, 'secondaire', 5),
(19, 46, 200.00, '2025-04-01', '2025-03-31 22:00:00', 1, '1er', NULL, 'secondaire', 5),
(20, 46, 200.00, '2025-04-01', '2025-03-31 22:00:00', 1, '1er', NULL, 'secondaire', 5),
(21, 46, 200.00, '2025-04-01', '2025-03-31 22:00:00', 1, '1er', NULL, 'secondaire', 5),
(22, 46, 50.00, '2025-04-01', '2025-03-31 22:00:00', 4, '1er', NULL, 'secondaire', 9),
(23, 45, 300.00, '2025-04-01', '2025-03-31 22:00:00', 5, '1er', NULL, 'primaire', 6),
(24, 48, 300.00, '2025-04-01', '2025-03-31 22:00:00', 5, '6eme', NULL, 'primaire', 6),
(25, 48, 300.00, '2025-04-01', '2025-03-31 22:00:00', 5, '6eme', NULL, 'primaire', 6),
(26, 54, 200.00, '1111-05-05', '0000-00-00 00:00:00', 1, '2eme', NULL, '', 5),
(27, 50, 300.00, '1111-11-11', '0000-00-00 00:00:00', 3, '1er', NULL, '', 6),
(28, 50, 300.00, '1111-11-11', '0000-00-00 00:00:00', 6, '1er', NULL, '', 6),
(36, 49, 300.00, '2025-04-02', '2025-04-01 22:00:00', 3, '3eme', NULL, 'maternelle', 6),
(37, 49, 50.00, '2025-04-02', '2025-04-01 22:00:00', 6, '3eme', NULL, 'maternelle', 9),
(38, 47, 300.00, '0000-00-00', '0000-00-00 00:00:00', 1, '7eme', NULL, 'secondaire', 6),
(39, 45, 65.00, '0000-00-00', '0000-00-00 00:00:00', 3, '1er', NULL, 'primaire', 10),
(40, 47, 65.00, '0000-00-00', '0000-00-00 00:00:00', 2, '7eme', NULL, 'secondaire', 10);

-- --------------------------------------------------------

--
-- Structure de la table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prefet`
--

CREATE TABLE `prefet` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `section` enum('primaire','secondaire','maternel') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `prefet`
--

INSERT INTO `prefet` (`id`, `nom`, `prenom`, `contact`, `email`, `adresse`, `section`) VALUES
(1, 'sky', 'board', '0979099031', 'skyboard@mail.com', 'Kamanyola', 'secondaire'),
(2, 'board', 'Mr sky', '+(243) 9933-18385', 'sky@mail.com', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'secondaire'),
(3, 'board', 'Mr sky', '+(243) 9933-18385', 'sky@mail.com', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'secondaire');

-- --------------------------------------------------------

--
-- Structure de la table `presences`
--

CREATE TABLE `presences` (
  `id` int(11) NOT NULL,
  `professeur_id` int(11) NOT NULL,
  `heure_arrivee` time NOT NULL,
  `heure_depart` time NOT NULL,
  `date_presence` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `professeurs`
--

CREATE TABLE `professeurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text NOT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `cours_id` int(11) DEFAULT NULL,
  `section` enum('primaire','secondaire','maternel') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `recu`
--

CREATE TABLE `recu` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) NOT NULL,
  `paiement_id` int(11) NOT NULL,
  `nom_etablissement` varchar(100) NOT NULL,
  `numero_recu` varchar(50) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `details` text DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `security_logs`
--

INSERT INTO `security_logs` (`id`, `page`, `ip_address`, `details`, `date`) VALUES
(1, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:07:06'),
(2, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:07:19'),
(3, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:11:57'),
(4, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:12:19'),
(5, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:13:28'),
(6, 'login', '::1', 'Invalid CSRF token', '2025-04-02 15:14:24'),
(7, 'login', '::1', 'Too many failed login attempts', '2025-04-03 14:56:48');

-- --------------------------------------------------------

--
-- Structure de la table `sessions_scolaires`
--

CREATE TABLE `sessions_scolaires` (
  `id` int(11) NOT NULL,
  `annee_debut` int(11) NOT NULL,
  `annee_fin` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL,
  `est_active` tinyint(1) DEFAULT 0,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sessions_scolaires`
--

INSERT INTO `sessions_scolaires` (`id`, `annee_debut`, `annee_fin`, `libelle`, `est_active`, `date_creation`) VALUES
(1, 2023, 2024, 'Année scolaire 2023-2024', 1, '2025-04-01 07:50:20'),
(2, 2024, 2025, 'Année scolaire 2024-2025', 0, '2025-04-01 07:50:20'),
(3, 2025, 2026, 'Année scolaire 2025-2026', 0, '2025-04-01 07:50:20');

-- --------------------------------------------------------

--
-- Structure de la table `stock_items`
--

CREATE TABLE `stock_items` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `categorie` varchar(100) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 0,
  `seuil_alerte` int(11) DEFAULT 10,
  `description` text DEFAULT NULL,
  `emplacement` varchar(100) DEFAULT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `stock_items`
--

INSERT INTO `stock_items` (`id`, `nom`, `categorie`, `quantite`, `seuil_alerte`, `description`, `emplacement`, `date_ajout`, `date_modification`, `user_id`) VALUES
(1, 'Lumingu', 'Fourniture', 26, 10, 'frais', NULL, '2025-04-03 22:00:00', '2025-04-04 09:57:52', NULL),
(2, 'Mr sky board', 'Fourniture', 7, 10, '9', NULL, '2025-04-03 22:00:00', '2025-04-04 10:36:37', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stock_mouvements`
--

CREATE TABLE `stock_mouvements` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `type_mouvement` enum('entree','sortie') NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_mouvement` timestamp NOT NULL DEFAULT current_timestamp(),
  `motif` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `action_type` enum('add','edit','delete','login','logout','payment') DEFAULT NULL,
  `action_description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `username`, `action_type`, `action_description`, `ip_address`, `created_at`) VALUES
(1, 21, 'Daniel', 'delete', 'Suppression du paiement #29 pour l\'élève Mr sky board', '::1', '2025-04-01 22:34:23'),
(2, 21, 'Daniel', 'delete', 'Suppression du paiement #15 pour l\'élève Manga Ochiwa', '::1', '2025-04-01 22:34:46'),
(3, 21, 'Daniel', 'delete', 'Suppression du paiement #35 pour l\'élève fabrice ', '::1', '2025-04-01 22:37:48'),
(4, 21, 'Daniel', 'delete', 'Suppression du paiement #34 pour l\'élève fabrice ', '::1', '2025-04-01 22:37:56'),
(5, 21, 'Daniel', 'delete', 'Suppression du paiement #33 pour l\'élève Mr sky board', '::1', '2025-04-01 22:38:03'),
(6, 21, 'Daniel', 'delete', 'Suppression du paiement #32 pour l\'élève gloire', '::1', '2025-04-01 22:38:11'),
(7, 21, 'Daniel', 'delete', 'Suppression du paiement #31 pour l\'élève sky board', '::1', '2025-04-01 22:40:03'),
(8, 21, 'Daniel', 'delete', 'Suppression du paiement #30 pour l\'élève Mr sky board', '::1', '2025-04-01 22:40:25'),
(9, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: kasong Manga', '::1', '2025-04-02 06:51:48'),
(10, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: kas malika', '::1', '2025-04-02 08:50:05'),
(11, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: mainel mandib', '::1', '2025-04-02 13:41:07'),
(12, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:18:28'),
(13, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:22:41'),
(14, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:22:42'),
(15, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:22:54'),
(16, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:24:11'),
(17, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:26:11'),
(18, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:27:27'),
(19, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:29:08'),
(20, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:32:59'),
(21, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:33:36'),
(22, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:34:57'),
(23, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:35:00'),
(24, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:00'),
(25, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:00'),
(26, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:01'),
(27, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:02'),
(28, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:02'),
(29, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:38:10'),
(30, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:39:40'),
(31, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 14:39:49'),
(32, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 46', '::1', '2025-04-02 14:39:56'),
(33, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:00:21'),
(34, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:03:05'),
(35, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:06:54'),
(36, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:07:02'),
(37, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:07:35'),
(38, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:15:10'),
(39, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:18:26'),
(40, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 46', '::1', '2025-04-02 15:18:43'),
(41, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 46', '::1', '2025-04-02 15:18:55'),
(42, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 46', '::1', '2025-04-02 15:19:26'),
(43, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:19:36'),
(44, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 15:20:23'),
(45, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 47', '::1', '2025-04-02 15:20:45'),
(46, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 47', '::1', '2025-04-02 15:21:03'),
(47, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 47', '::1', '2025-04-02 15:25:32'),
(48, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 47', '::1', '2025-04-02 15:25:58'),
(49, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 45', '::1', '2025-04-02 17:08:51'),
(50, NULL, 'Ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-03 10:36:19'),
(51, NULL, 'Ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-03 10:39:48'),
(52, NULL, 'Ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-03 10:56:14'),
(53, NULL, 'Ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-03 11:32:43'),
(54, 21, 'Daniel', '', 'Consultation du profil de l\'élève ID: 47', '::1', '2025-04-04 05:48:01'),
(55, NULL, 'Ciella', NULL, 'Ajout d\'un achat de fourniture: mmm', '::1', '2025-04-04 07:25:24'),
(56, NULL, 'Ciella', NULL, 'Suppression d\'un achat de fourniture: mmm', '::1', '2025-04-04 07:35:20'),
(57, NULL, 'Ciella', NULL, 'Ajout d\'un achat de fourniture: mmmmm', '::1', '2025-04-04 07:35:51'),
(58, NULL, 'Ciella', NULL, 'Ajout d\'un article au stock: Lumingu', '::1', '2025-04-04 09:57:52'),
(59, NULL, 'Ciella', NULL, 'Ajout d\'un article au stock: Mr sky board', '::1', '2025-04-04 10:36:37'),
(60, NULL, 'Ciella', NULL, 'Ajout d\'un événement scolaire: Maths', '::1', '2025-04-04 10:56:48'),
(61, NULL, 'Ciella', NULL, 'Mise à jour d\'un événement scolaire via AJAX: Maths', '::1', '2025-04-04 10:58:36'),
(62, NULL, 'Ciella', NULL, 'Mise à jour d\'un événement scolaire via AJAX: Maths', '::1', '2025-04-04 10:58:38'),
(63, NULL, 'Ciella', NULL, 'Suppression d\'un achat de fourniture: mmmmm', '::1', '2025-04-04 11:01:54'),
(64, NULL, 'Ciella', NULL, 'Ajout d\'un achat de fourniture: maternelle', '::1', '2025-04-04 13:57:34'),
(65, NULL, 'Ciella', NULL, 'Suppression d\'un achat de fourniture: maternelle', '::1', '2025-04-04 14:07:22'),
(66, 21, 'Daniel', '', 'Exportation de la liste des paiements au format Excel', '::1', '2025-04-04 17:06:47');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','director','directrice','prefet','comptable') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `lock_expiry` datetime DEFAULT NULL,
  `password_change_date` datetime DEFAULT NULL,
  `password_expiry_days` int(11) NOT NULL DEFAULT 90
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `email`, `image`, `session_id`, `telephone`, `adresse`, `locked`, `lock_expiry`, `password_change_date`, `password_expiry_days`) VALUES
(19, 'Gloire', '$2y$10$QHAuehKGxjO9jLi0/PJKuORiQ2b0D5KYNBUTVd1sOyw076hC/SMeu', 'comptable', '2025-03-19 08:45:01', 'gloirelumingu10@gmail.com', NULL, 'frfouhlo5luld5mo9a8fbrjkiv', NULL, NULL, 0, NULL, NULL, 90),
(20, 'Ciella', '$2y$10$uJLRIw7C4DRSuD2eh5lVhuDufMtiUOSdWII2vrKctvpQnoNGKeuqW', 'admin', '2025-03-19 08:59:55', 'ciellamujinga@mail.com', 'uploads/avatars/avatar_20_1743764467.jpg', NULL, '+243 89 079 1919', 'AV. Mbembe, Q.KAMANYOLA No. 48', 0, NULL, NULL, 90),
(21, 'Daniel', '$2y$10$WuERiUQT6GPfGzueKeTdBuswctFUtJ1Uf8UVk8n4BPFTMF03QgIEq', 'comptable', '2025-03-19 09:39:16', 'dantunku@mail.com', 'uploads/profile_photos/user_21_1743545962.jpg', 'frfouhlo5luld5mo9a8fbrjkiv', NULL, NULL, 0, NULL, NULL, 90),
(22, 'Exou', '$2y$10$pJW2td5lYJpnzmcHMq6p2OdqFm9jtrhseMCXfT233nQftp7hSLhmG', 'comptable', '2025-03-19 10:54:28', 'exoukapenda@mail.com', NULL, 'frfouhlo5luld5mo9a8fbrjkiv', NULL, NULL, 0, NULL, NULL, 90),
(24, 'piter', '$2y$10$FHhGeCK4MsvJiQVU0qCGpOpoR1Hz0iPoPSUnzt67jvZgKG.h1Yyfu', 'admin', '2025-03-27 12:08:45', 'piterpedro@gmail.com', NULL, NULL, NULL, NULL, 0, NULL, NULL, 90),
(33, 'Plamedie', '$2y$10$P.gqe6pTUb6HAE2GzqJBf.v.ua7zvXoS/ruOqt3cyucNxWV7MFMdG', 'directrice', '2025-04-03 13:31:13', 'plamediemashat@gmail.com', 'dist/img/users/user_1743687073_1073.jpg', NULL, '0979411767', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 0, NULL, '2025-04-03 15:47:36', 90),
(34, 'Fab', '$2y$10$5zrd3efdYMeJ2AivNuN37OogS.2dK50Ya8QRyinghqakbMqI8Hqpu', 'directrice', '2025-04-03 14:13:41', 'fabkap006@gmail.com', 'dist/img/users/user_1743689621_7907.jpg', NULL, '0979411767', 'AV. Mbembe, Q.KAMANYOLA No. 48', 0, NULL, '0000-00-00 00:00:00', 90);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achats_fournitures`
--
ALTER TABLE `achats_fournitures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `last_activity` (`last_activity`);

--
-- Index pour la table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Index pour la table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comptable`
--
ALTER TABLE `comptable`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professeur_id` (`professeur_id`),
  ADD KEY `classe_id` (`classe_id`);

--
-- Index pour la table `directeur`
--
ALTER TABLE `directeur`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `directrice`
--
ALTER TABLE `directrice`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `fk_eleve_session_scolaire` (`session_scolaire_id`);

--
-- Index pour la table `eleves_archives`
--
ALTER TABLE `eleves_archives`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `employes`
--
ALTER TABLE `employes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `evenements_scolaires`
--
ALTER TABLE `evenements_scolaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `attempt_date` (`attempt_date`);

--
-- Index pour la table `frais`
--
ALTER TABLE `frais`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `horaires`
--
ALTER TABLE `horaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classe_id` (`classe_id`),
  ADD KEY `cour_id` (`cour_id`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `mois`
--
ALTER TABLE `mois`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professeur_id` (`professeur_id`);

--
-- Index pour la table `paiements_frais`
--
ALTER TABLE `paiements_frais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleve_id` (`eleve_id`),
  ADD KEY `classe_id` (`classe`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `moi_id` (`moi_id`),
  ADD KEY `fk_frais` (`frais_id`);

--
-- Index pour la table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `prefet`
--
ALTER TABLE `prefet`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `presences`
--
ALTER TABLE `presences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `professeur_id` (`professeur_id`);

--
-- Index pour la table `professeurs`
--
ALTER TABLE `professeurs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_classe_id` (`classe_id`),
  ADD KEY `fk_cours_id` (`cours_id`);

--
-- Index pour la table `recu`
--
ALTER TABLE `recu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleve_id` (`eleve_id`),
  ADD KEY `paiement_id` (`paiement_id`);

--
-- Index pour la table `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `date` (`date`);

--
-- Index pour la table `sessions_scolaires`
--
ALTER TABLE `sessions_scolaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `stock_mouvements`
--
ALTER TABLE `stock_mouvements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `achats_fournitures`
--
ALTER TABLE `achats_fournitures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `comptable`
--
ALTER TABLE `comptable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `directeur`
--
ALTER TABLE `directeur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `directrice`
--
ALTER TABLE `directrice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `eleves_archives`
--
ALTER TABLE `eleves_archives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `employes`
--
ALTER TABLE `employes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `evenements_scolaires`
--
ALTER TABLE `evenements_scolaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `frais`
--
ALTER TABLE `frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `horaires`
--
ALTER TABLE `horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT pour la table `mois`
--
ALTER TABLE `mois`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiements_frais`
--
ALTER TABLE `paiements_frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `prefet`
--
ALTER TABLE `prefet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `presences`
--
ALTER TABLE `presences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `professeurs`
--
ALTER TABLE `professeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `recu`
--
ALTER TABLE `recu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `sessions_scolaires`
--
ALTER TABLE `sessions_scolaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `stock_items`
--
ALTER TABLE `stock_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `stock_mouvements`
--
ALTER TABLE `stock_mouvements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achats_fournitures`
--
ALTER TABLE `achats_fournitures`
  ADD CONSTRAINT `achats_fournitures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`professeur_id`) REFERENCES `professeurs` (`id`),
  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `fk_eleve_session_scolaire` FOREIGN KEY (`session_scolaire_id`) REFERENCES `sessions_scolaires` (`id`);

--
-- Contraintes pour la table `evenements_scolaires`
--
ALTER TABLE `evenements_scolaires`
  ADD CONSTRAINT `evenements_scolaires_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `stock_items`
--
ALTER TABLE `stock_items`
  ADD CONSTRAINT `stock_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `stock_mouvements`
--
ALTER TABLE `stock_mouvements`
  ADD CONSTRAINT `stock_mouvements_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `stock_items` (`id`),
  ADD CONSTRAINT `stock_mouvements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
