-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 13 avr. 2025 à 16:46
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

--
-- Déchargement des données de la table `achats_fournitures`
--

INSERT INTO `achats_fournitures` (`id`, `date_achat`, `fournisseur`, `description`, `quantite`, `montant`, `facture_ref`, `date_creation`, `date_modification`, `user_id`) VALUES
(4, '2025-04-12', 'CLIENT', 'ENTRE', 60, 5000.00, 'FAC-00D-WQW', '2025-04-11 23:24:10', '2025-04-11 23:24:10', NULL);

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
(32, 22, 'dihld8m3us08fm0qop5ld5dlju', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-04 20:48:26', '2025-04-04 20:48:26'),
(33, 21, 'gti66rts6f3sfv0182l05vna0c', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-05 10:45:27', '2025-04-05 10:45:27'),
(34, 20, 'guqn2susb01g6453nch0blb05u', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-06 12:50:03', '2025-04-06 12:50:03'),
(36, 21, 'll2ru4aqr3odf8fr95nurmipf6', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-06 13:00:53', '2025-04-06 13:00:53'),
(37, 21, '8fga46vfaid51bqv9na7tgvdif', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-06 14:19:12', '2025-04-06 14:19:12'),
(39, 20, 'c01mjrv9ue2kfpmuir62echopu', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-06 15:55:58', '2025-04-06 15:55:58'),
(40, 33, '6tlk7jqniq8hqetq1t5s50454m', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-06 16:58:47', '2025-04-06 16:58:47'),
(42, 35, '4fentfom6fr1apka9bnmnrjpqi', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-06 17:21:22', '2025-04-06 17:21:22'),
(45, 21, '0ic2ivof8253nb7fs3vfsh2e1f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-06 20:48:40', '2025-04-06 20:48:40'),
(46, 35, 'sk6b50pdrdqk48em2klt941kvs', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-06 21:00:07', '2025-04-06 21:00:07'),
(47, 35, '07m0q7hkpion4a2a4vdqpmbubl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 06:36:48', '2025-04-07 06:36:48'),
(48, 35, 'bns1ng6plpqfrmhib8brlvh889', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 07:23:13', '2025-04-07 07:23:13'),
(49, 35, '3obbch7059ackic8c9jg7pmetm', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 12:56:06', '2025-04-07 12:56:06'),
(50, 35, 'eusaj3mve63sp24h2318jbf0ha', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 15:28:37', '2025-04-07 15:28:37'),
(51, 35, 'eqsrke4l4ut6lskubk7bcecthp', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 16:00:27', '2025-04-07 16:00:27'),
(53, 20, 'ptjpv747so572ankr82vf18esn', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '2025-04-07 17:07:57', '2025-04-07 17:07:57'),
(55, 36, 'elfms44quepk2d9tgjbfcvgtbt', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 11:19:09', '2025-04-08 11:19:09'),
(56, 20, 'tu59e310nluni3rdq3rs05gvkm', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-08 11:59:42', '2025-04-08 11:59:42'),
(58, 36, 'tgf5ov0j4t0ruapvumdsdiielu', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 12:44:51', '2025-04-08 12:44:51'),
(59, 36, 'cc2qgbeau5ug696mfolq01m17m', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 14:35:05', '2025-04-08 14:35:05'),
(60, 20, '54h71o7ia1q9vvtiadhn53kvtq', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-08 14:51:20', '2025-04-08 14:51:20'),
(61, 36, 'l2osan5vgjt8ku5sib6e624p5a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 15:35:17', '2025-04-08 15:35:17'),
(62, 36, 'aupo8jiekvs8jm05mubnjn4235', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-08 20:00:37', '2025-04-08 20:00:37'),
(67, 20, '7tbk8irf9e5i7e4dkcf4tkog8f', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 00:16:47', '2025-04-09 00:16:47'),
(68, 33, 'u7uihb9shgh2vgconf3hgga1jl', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 00:53:51', '2025-04-09 00:53:51'),
(70, 21, 'fspmqs3g7o02f31oj0k1e91odg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 10:05:15', '2025-04-09 10:05:15'),
(71, 35, '1ddfoccq6hrrv38n429qjodahe', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 10:17:05', '2025-04-09 10:17:05'),
(72, 35, '302094umafav0p1mmdnqruvh4a', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 14:53:42', '2025-04-09 14:53:42'),
(73, 20, 'n9h1arl4d6g3mk9n7n421lbg57', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 15:10:20', '2025-04-09 15:10:20'),
(74, 35, 'sjm1h5e27qic4noi809b31pu4g', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 16:42:52', '2025-04-09 16:42:52'),
(75, 20, 'u60614eqlkg7naf052lkkh3amj', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 17:06:26', '2025-04-09 17:06:26'),
(79, 40, '2pdvd5rjsklhui2nbqvchdg9n2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 21:16:43', '2025-04-09 21:16:43'),
(84, 40, 'qsd3c6hddg86hnr30rq9d3v6bc', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-09 22:43:39', '2025-04-09 22:43:39'),
(85, 39, '8ajkg2n73si8vbsvgegd31sp0m', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-09 23:07:02', '2025-04-09 23:07:02'),
(86, 39, 'o9bp0romk6pv0umolf4uhge2a0', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 08:12:49', '2025-04-10 08:12:49'),
(87, 39, 'i8frjqhmb76mo4cjnmd8mt5vtm', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-10 21:29:20', '2025-04-10 21:29:20'),
(88, 39, 'ur6ukdbbvirv57ua70ujs2e2ou', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 11:14:27', '2025-04-11 11:14:27'),
(89, 41, 'amavit4uhs9j5erjktbsqg6cdg', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-11 11:19:48', '2025-04-11 11:19:48'),
(90, 41, '61hu6o8n8jdfk6i4o6s5mvv2gj', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 21:14:15', '2025-04-11 21:14:15'),
(91, 39, 'tqlu14tlhr50u3oecghaal3obj', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-11 21:39:11', '2025-04-11 21:39:11'),
(92, 39, '414dho2dfiilbhiquga6faja03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-11 22:04:43', '2025-04-11 22:04:43'),
(93, 39, 'rd16kor799kdku23hsr31939i4', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-12 00:30:48', '2025-04-12 00:30:48'),
(94, 40, 'vlgn1obaeostuksve4usi7uvub', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-12 00:40:56', '2025-04-12 00:40:56'),
(95, 40, '8q7dp5ff5lfrfed5e039kft6gp', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-12 01:16:16', '2025-04-12 01:16:16'),
(96, 40, '94b8ts7unti6uhleqdgkpc9j8v', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-12 10:23:14', '2025-04-12 10:23:14'),
(97, 41, '2sthkeuobu4j2f087anuv794pu', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-12 11:39:24', '2025-04-12 11:39:24'),
(98, 40, '8a0v7krcc1b0pn6ur183gadpev', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0', '2025-04-12 12:32:17', '2025-04-12 12:32:17');

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

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `section` enum('maternelle','primaire','secondaire') DEFAULT NULL,
  `prof_id` int(11) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `titulaire` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `section`, `prof_id`, `niveau`, `titulaire`) VALUES
(1, '1er', NULL, NULL, NULL, NULL),
(2, '2ieme', NULL, NULL, NULL, NULL),
(3, '3eme', 'maternelle', NULL, NULL, NULL),
(4, '4eme', 'primaire', NULL, NULL, NULL),
(5, '7eme', 'secondaire', NULL, NULL, NULL),
(6, '8eme', 'secondaire', NULL, NULL, NULL),
(7, 'C.G', 'secondaire', NULL, NULL, NULL),
(8, 'Scientifique', 'secondaire', 1, '1ère', 'sky elle');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `coefficient` int(11) NOT NULL DEFAULT 1,
  `heures_semaine` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `titre`, `description`, `professeur_id`, `classe_id`, `section`, `option_`, `created_at`, `coefficient`, `heures_semaine`) VALUES
(8, 'Francais ', 'Niveau 2', 1, 6, 'secondaire', '', '2025-04-08 12:53:24', 1, 2);

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
  `option_id` int(11) DEFAULT NULL,
  `nom_pere` varchar(100) DEFAULT NULL,
  `nom_mere` varchar(100) DEFAULT NULL,
  `contact_pere` varchar(15) DEFAULT NULL,
  `contact_mere` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `classe_id` int(11) DEFAULT NULL,
  `option` enum('EB','scientifique','commerciale','mecanique auto','mecanique generale','literature','electronique','electricite','pedagogie genrale') DEFAULT NULL,
  `session_scolaire_id` int(11) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'actif',
  `matricule` varchar(20) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT 'dist/img/default-student.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `nom`, `post_nom`, `prenom`, `date_naissance`, `sexe`, `lieu_naissance`, `adresse`, `section`, `option_id`, `nom_pere`, `nom_mere`, `contact_pere`, `contact_mere`, `created_at`, `updated_at`, `classe_id`, `option`, `session_scolaire_id`, `statut`, `matricule`, `photo`) VALUES
(64, 'tshisola', 'Ndumba', 'Mael', '2021-09-09', 'M', 'Kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'maternelle', NULL, 'Tshisola', 'Kaj', '+243 97 90 99 0', '+243 89 07 91 9', '2025-04-08 22:07:07', '2025-04-08 22:07:07', 3, NULL, 1, 'actif', 'SGS-2025-3886', 'uploads/eleves/SGS-2025-3886_1744150027.jpg'),
(65, 'tshisola', 'Ndumba', 'Mael', '2020-07-09', 'M', 'Kolwezi', 'AV. Mbembe, Q.KAMANYOLA No. 48', 'secondaire', 8, 'Tshisola', 'Kaj', '+243 97 90 99 0', '+243 89 07 91 9', '2025-04-09 08:06:53', '2025-04-09 08:06:53', NULL, NULL, 1, 'actif', 'SGS-2025-9095', 'uploads/eleves/SGS-2025-9095_1744186013.jpg'),
(66, 'Kasong', 'Tshisola', 'Malika', '2022-04-03', 'M', 'Kolwezi', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'maternelle', NULL, 'chris', 'Kaj', '+243 97 90 99 0', '+243 89 07 91 9', '2025-04-11 09:25:18', '2025-04-11 09:25:18', 1, NULL, 1, 'actif', 'SGS-2025-9404', 'uploads/eleves/SGS-2025-9404_1744363518.jpg');

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
(9, 'Ochiwa', 'Manga', 'manga02@gmail.com', '+(243) 9098-99031', 'vendeuse', '2025-03-22 18:43:37', 'Kolwezi.manika, Moïse Tshombe, mbembe,48');

-- --------------------------------------------------------

--
-- Structure de la table `erreurs_horaires`
--

CREATE TABLE `erreurs_horaires` (
  `id` int(11) NOT NULL,
  `jour` varchar(20) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `cours_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `date_detection` datetime DEFAULT current_timestamp(),
  `statut` enum('Non résolu','En cours','Résolu') DEFAULT 'Non résolu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenements_scolaires`
--

CREATE TABLE `evenements_scolaires` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
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

INSERT INTO `evenements_scolaires` (`id`, `titre`, `type`, `description`, `date_debut`, `date_fin`, `lieu`, `responsable`, `couleur`, `statut`, `date_creation`, `date_modification`, `user_id`) VALUES
(1, 'Maths', NULL, 'jkj', '2025-04-04 12:56:00', '2025-04-18 19:59:00', 'kj', NULL, '', 'planifie', '2025-04-04 10:56:48', '2025-04-04 10:58:36', NULL),
(2, 'journee scientifique', 'Autre', 'jkk', '2025-04-11 09:57:00', '2025-04-12 15:58:00', 'La joie', NULL, 'Mr key', 'planifie', '2025-04-09 13:03:39', '2025-04-09 13:11:07', NULL),
(3, 'journee scientifique', 'Examen', '', '2025-04-10 08:08:00', '2025-04-23 08:08:00', 'Ecole', 'chris', '#f56954', 'planifie', '2025-04-09 21:09:01', '2025-04-09 21:09:01', NULL);

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
(10, 65.00, 'Frais mensuel_maternelle', 'maternelle', '2025-04-02 17:27:50'),
(12, 100.00, 'Mensuels', 'primaire', '2025-04-06 10:59:03'),
(13, 50.00, 'FIP', 'secondaire', '2025-04-06 10:59:33');

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
(16, 20, 'Suppression', '2025-04-02 17:26:09'),
(17, 20, 'Suppression', '2025-04-06 10:58:38'),
(18, 20, 'Suppression', '2025-04-06 13:56:30'),
(19, 20, 'Suppression', '2025-04-07 15:10:59'),
(20, 20, 'Suppression', '2025-04-08 21:35:54'),
(21, 20, 'Suppression', '2025-04-08 21:35:59'),
(22, 20, 'Suppression', '2025-04-08 21:36:04'),
(23, 20, 'Suppression', '2025-04-08 21:36:08'),
(24, 20, 'Suppression', '2025-04-08 21:36:14'),
(25, 20, 'Suppression', '2025-04-08 21:36:17'),
(26, 20, 'Suppression', '2025-04-08 21:36:20'),
(27, 20, 'Suppression', '2025-04-08 21:36:24'),
(28, 20, 'Suppression', '2025-04-08 21:36:28'),
(29, 20, 'Suppression', '2025-04-08 21:39:19'),
(30, 20, 'Suppression', '2025-04-08 21:39:23'),
(31, 20, 'Suppression', '2025-04-08 21:39:26'),
(32, 20, 'Suppression', '2025-04-08 21:39:29'),
(33, 20, 'Suppression', '2025-04-08 21:39:33'),
(34, 40, 'Suppression', '2025-04-12 09:08:08');

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
(125, 0, 'INFO', 'Exou', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Exou\",\"ip\":\"::1\"}', '2025-04-04 20:48:26'),
(126, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-05 10:45:27'),
(127, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-05 10:45:27'),
(128, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-05 10:45:27'),
(129, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-06 12:50:03'),
(130, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-06 12:50:03'),
(131, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-06 12:50:03'),
(132, 0, 'INFO', 'Exou', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-06 12:59:55'),
(133, 22, 'INFO', 'Exou', 'Connexion réussie', '', NULL, NULL, '2025-04-06 12:59:55'),
(134, 0, 'INFO', 'Exou', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Exou\",\"ip\":\"::1\"}', '2025-04-06 12:59:55'),
(135, 22, 'INFO', 'Exou', 'Déconnexion', '', NULL, NULL, '2025-04-06 13:00:14'),
(136, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-06 13:00:36'),
(137, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-06 13:00:53'),
(138, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-06 13:00:53'),
(139, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-06 13:00:53'),
(140, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-06 14:19:12'),
(141, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-06 14:19:12'),
(142, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-06 14:19:12'),
(143, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-06 15:12:44'),
(144, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-06 15:14:00'),
(145, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-06 15:14:00'),
(146, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-06 15:14:00'),
(147, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-06 15:54:17'),
(148, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-06 15:55:58'),
(149, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-06 15:55:58'),
(150, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-06 15:55:58'),
(151, 21, 'INFO', 'Daniel', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-06 16:48:47'),
(152, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 16:58:47'),
(153, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-06 16:58:47'),
(154, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-06 16:58:47'),
(155, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 17:18:21'),
(156, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-06 17:18:21'),
(157, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-06 17:18:21'),
(158, 33, 'INFO', 'Plamedie', 'Déconnexion', '', NULL, NULL, '2025-04-06 17:19:52'),
(159, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"chris\",\"role\":\"directrice\"}', '2025-04-06 17:20:56'),
(160, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 17:21:22'),
(161, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-06 17:21:22'),
(162, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-06 17:21:22'),
(163, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-06 17:34:10'),
(164, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 17:34:29'),
(165, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-06 17:34:29'),
(166, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-06 17:34:29'),
(167, 35, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-06 19:21:48'),
(168, 35, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-06 20:47:01'),
(169, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 20:47:05'),
(170, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-06 20:47:05'),
(171, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-06 20:47:05'),
(172, 33, 'INFO', 'Plamedie', 'Déconnexion', '', NULL, NULL, '2025-04-06 20:48:34'),
(173, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-06 20:48:40'),
(174, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-06 20:48:40'),
(175, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-06 20:48:40'),
(176, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-06 21:00:07'),
(177, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-06 21:00:07'),
(178, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-06 21:00:07'),
(179, 35, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-07 06:36:25'),
(180, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-07 06:36:48'),
(181, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-07 06:36:48'),
(182, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-07 06:36:48'),
(183, 35, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-07 07:22:47'),
(184, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-07 07:23:13'),
(185, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-07 07:23:13'),
(186, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-07 07:23:13'),
(187, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-07 12:56:06'),
(188, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-07 12:56:06'),
(189, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-07 12:56:06'),
(190, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-07 15:28:37'),
(191, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-07 15:28:38'),
(192, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-07 15:28:38'),
(193, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-07 16:00:27'),
(194, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-07 16:00:27'),
(195, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-07 16:00:27'),
(196, 35, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-07 17:03:57'),
(197, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"fabkap\",\"role\":\"prefet\"}', '2025-04-07 17:05:50'),
(198, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-07 17:06:03'),
(199, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-07 17:06:03'),
(200, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-07 17:06:03'),
(201, 36, 'INFO', 'fabkap', 'Déconnexion', '', NULL, NULL, '2025-04-07 17:07:36'),
(202, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-07 17:07:57'),
(203, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-07 17:07:57'),
(204, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-07 17:07:57'),
(205, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-08 11:16:01'),
(206, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-08 11:16:01'),
(207, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-08 11:16:01'),
(208, 35, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-08 11:18:48'),
(209, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 11:19:09'),
(210, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 11:19:09'),
(211, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 11:19:09'),
(212, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-08 11:59:42'),
(213, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-08 11:59:42'),
(214, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-08 11:59:42'),
(215, 36, 'INFO', 'fabkap', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-08 12:42:28'),
(216, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-08 12:42:50'),
(217, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-08 12:42:50'),
(218, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-08 12:42:50'),
(219, 35, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-08 12:44:32'),
(220, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 12:44:51'),
(221, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 12:44:51'),
(222, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 12:44:51'),
(223, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 14:35:05'),
(224, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 14:35:05'),
(225, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 14:35:06'),
(226, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-08 14:51:20'),
(227, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-08 14:51:20'),
(228, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-08 14:51:20'),
(229, 36, 'INFO', 'fabkap', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-08 15:35:02'),
(230, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 15:35:17'),
(231, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 15:35:17'),
(232, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 15:35:17'),
(233, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 20:00:23'),
(234, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 20:00:37'),
(235, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 20:00:37'),
(236, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 20:00:37'),
(237, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 23:09:20'),
(238, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-08 23:09:30'),
(239, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-08 23:09:30'),
(240, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-08 23:09:30'),
(241, 36, 'INFO', 'fabkap', 'Déconnexion', '', NULL, NULL, '2025-04-08 23:19:18'),
(242, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-08 23:19:30'),
(243, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-08 23:19:30'),
(244, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-08 23:19:30'),
(245, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-08 23:33:08'),
(246, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-08 23:33:08'),
(247, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-08 23:33:08'),
(248, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-08 23:54:50'),
(249, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-08 23:54:55'),
(250, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-08 23:54:55'),
(251, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-08 23:54:55'),
(252, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-09 00:16:36'),
(253, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 00:16:47'),
(254, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 00:16:47'),
(255, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-09 00:16:47'),
(256, 21, 'INFO', 'Daniel', 'Déconnexion', '', NULL, NULL, '2025-04-09 00:53:44'),
(257, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 00:53:51'),
(258, 33, 'INFO', 'Plamedie', 'Connexion réussie', '', NULL, NULL, '2025-04-09 00:53:51'),
(259, 0, 'INFO', 'Plamedie', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-09 00:53:51'),
(260, 0, 'INFO', 'fabkap', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"prefet\"}', '2025-04-09 10:01:33'),
(261, 36, 'INFO', 'fabkap', 'Connexion réussie', '', NULL, NULL, '2025-04-09 10:01:33'),
(262, 0, 'INFO', 'fabkap', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"fabkap\",\"ip\":\"::1\"}', '2025-04-09 10:01:33'),
(263, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-09 10:05:15'),
(264, 21, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-09 10:05:15'),
(265, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-09 10:05:15'),
(266, 36, 'INFO', 'fabkap', 'Déconnexion', '', NULL, NULL, '2025-04-09 10:16:33'),
(267, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 10:17:05'),
(268, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 10:17:05'),
(269, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 10:17:05'),
(270, 35, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-09 14:53:17'),
(271, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 14:53:42'),
(272, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 14:53:42'),
(273, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 14:53:42'),
(274, 21, 'INFO', 'Daniel', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-09 15:10:13'),
(275, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 15:10:20'),
(276, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 15:10:20'),
(277, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-09 15:10:20'),
(278, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 16:42:52'),
(279, 35, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 16:42:52'),
(280, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 16:42:52'),
(281, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 17:06:26'),
(282, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 17:06:26'),
(283, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-09 17:06:26'),
(284, 35, 'INFO', '', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-09 20:09:14'),
(285, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:10:56'),
(286, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:11:11'),
(287, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:11:31'),
(288, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 20:11:53'),
(289, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 20:11:53'),
(290, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Ciella\",\"ip\":\"::1\"}', '2025-04-09 20:11:53'),
(291, 20, 'INFO', 'Ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-09 20:12:43'),
(292, 0, 'INFO', 'Ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 20:12:46'),
(293, 20, 'INFO', 'Ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 20:12:46'),
(294, 0, 'INFO', 'Ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-09 20:12:46'),
(295, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-09 20:13:05'),
(296, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:13:25'),
(297, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:14:09'),
(298, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:14:25'),
(299, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chirs\",\"ip\":\"::1\"}', '2025-04-09 20:14:46'),
(300, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:14:55'),
(301, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"chris\",\"role\":\"directrice\"}', '2025-04-09 20:19:08'),
(302, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:25:09'),
(303, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:25:56'),
(304, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:26:18'),
(305, 20, 'INFO', 'Ciella', 'Déconnexion', '', NULL, NULL, '2025-04-09 20:27:33'),
(306, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:27:33'),
(307, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:27:44'),
(308, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:27:46'),
(309, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:01'),
(310, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:04'),
(311, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:24'),
(312, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:49'),
(313, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:50'),
(314, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:28:52'),
(315, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:31:01'),
(316, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:41:52'),
(317, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:41:56'),
(318, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:43:52'),
(319, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:43:55'),
(320, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:43:57'),
(321, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:43:59'),
(322, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:44:00'),
(323, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:44:47'),
(324, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"Glo\",\"role\":\"directrice\"}', '2025-04-09 20:46:03'),
(325, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:46:03'),
(326, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:46:24'),
(327, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:47:47'),
(328, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:48:42'),
(329, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:48:44'),
(330, 0, 'SECURITY', 'Anonyme', 'Action utilisateur', 'Tentative de connexion depuis une IP bloquée', '::1', ' {\"ip\":\"::1\"}', '2025-04-09 20:51:20'),
(331, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Glo\",\"ip\":\"::1\"}', '2025-04-09 20:56:58'),
(332, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"chris\",\"role\":\"directrice\"}', '2025-04-09 20:58:06'),
(333, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 20:58:31'),
(334, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 20:58:31'),
(335, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 20:58:31'),
(336, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"ciella\",\"role\":\"admin\"}', '2025-04-09 21:16:39'),
(337, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 21:16:43'),
(338, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 21:16:43'),
(339, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-09 21:16:43'),
(340, 39, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-09 21:33:22'),
(341, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 21:33:32'),
(342, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 21:33:32'),
(343, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 21:33:32'),
(344, 39, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-09 21:33:36'),
(345, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 21:33:48'),
(346, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 21:33:48'),
(347, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 21:33:48'),
(348, 39, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-09 21:33:52'),
(349, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 21:33:58'),
(350, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 21:33:58'),
(351, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 21:33:58'),
(352, 39, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-09 21:34:02'),
(353, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 21:34:08'),
(354, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 21:34:08'),
(355, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 21:34:08'),
(356, 39, 'INFO', 'chris', 'Déconnexion', '', NULL, NULL, '2025-04-09 21:34:17'),
(357, 40, 'INFO', 'ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-09 22:43:34'),
(358, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-09 22:43:39'),
(359, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-09 22:43:39'),
(360, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-09 22:43:39'),
(361, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-09 23:07:02'),
(362, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-09 23:07:02'),
(363, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-09 23:07:02'),
(364, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-10 08:12:49'),
(365, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-10 08:12:49'),
(366, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-10 08:12:49'),
(367, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-10 21:29:20'),
(368, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-10 21:29:20'),
(369, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-10 21:29:20'),
(370, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-11 11:14:27'),
(371, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-11 11:14:27'),
(372, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-11 11:14:27'),
(373, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-11 11:16:23'),
(374, 0, 'INFO', 'Anonyme', 'Action utilisateur', 'Nouvel utilisateur enregistré', '::1', ' {\"username\":\"Daniel\",\"role\":\"comptable\"}', '2025-04-11 11:19:03'),
(375, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-11 11:19:48'),
(376, 41, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-11 11:19:48'),
(377, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-11 11:19:48'),
(378, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-11 21:14:15'),
(379, 41, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-11 21:14:15'),
(380, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-11 21:14:15'),
(381, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-11 21:39:11'),
(382, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-11 21:39:11'),
(383, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-11 21:39:11'),
(384, 41, 'INFO', 'Daniel', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-11 22:04:29'),
(385, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-11 22:04:43'),
(386, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-11 22:04:43'),
(387, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-11 22:04:43'),
(388, 39, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-12 00:30:03'),
(389, 39, 'INFO', 'chris', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-12 00:30:36'),
(390, 0, 'INFO', 'chris', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"directrice\"}', '2025-04-12 00:30:48'),
(391, 39, 'INFO', 'chris', 'Connexion réussie', '', NULL, NULL, '2025-04-12 00:30:48'),
(392, 0, 'INFO', 'chris', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"chris\",\"ip\":\"::1\"}', '2025-04-12 00:30:48'),
(393, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-12 00:40:36'),
(394, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Plamedie\",\"ip\":\"::1\"}', '2025-04-12 00:40:47'),
(395, 0, 'WARNING', 'Anonyme', 'Action utilisateur', 'Échec de connexion', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-12 00:40:55'),
(396, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-12 00:40:56'),
(397, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-12 00:40:56'),
(398, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-12 00:40:56'),
(399, 40, 'INFO', 'ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-12 01:16:12'),
(400, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-12 01:16:16'),
(401, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-12 01:16:16'),
(402, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-12 01:16:16'),
(403, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-12 10:23:14'),
(404, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-12 10:23:14'),
(405, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-12 10:23:14'),
(406, 0, 'INFO', 'Daniel', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"comptable\"}', '2025-04-12 11:39:24'),
(407, 41, 'INFO', 'Daniel', 'Connexion réussie', '', NULL, NULL, '2025-04-12 11:39:24'),
(408, 0, 'INFO', 'Daniel', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"Daniel\",\"ip\":\"::1\"}', '2025-04-12 11:39:24'),
(409, 40, 'INFO', 'ciella', 'Déconnexion automatique (inactivité)', '', NULL, NULL, '2025-04-12 12:32:13'),
(410, 0, 'INFO', 'ciella', 'Action utilisateur', 'User role for redirection', '::1', ' {\"role\":\"admin\"}', '2025-04-12 12:32:17');
INSERT INTO `logs` (`id`, `user_id`, `level`, `username`, `action`, `message`, `ip_address`, `context`, `date`) VALUES
(411, 40, 'INFO', 'ciella', 'Connexion réussie', '', NULL, NULL, '2025-04-12 12:32:17'),
(412, 0, 'INFO', 'ciella', 'Action utilisateur', 'Connexion réussie', '::1', ' {\"username\":\"ciella\",\"ip\":\"::1\"}', '2025-04-12 12:32:17');

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
  `classe_id` int(11) DEFAULT NULL,
  `option_id` int(11) DEFAULT NULL,
  `section` enum('maternelle','primaire','secondaire') DEFAULT NULL,
  `frais_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements_frais`
--

INSERT INTO `paiements_frais` (`id`, `eleve_id`, `amount_paid`, `payment_date`, `created_at`, `moi_id`, `classe_id`, `option_id`, `section`, `frais_id`) VALUES
(43, 64, 65.00, '0000-00-00', '0000-00-00 00:00:00', 1, 3, NULL, 'maternelle', 10),
(44, 66, 65.00, '0000-00-00', '0000-00-00 00:00:00', 1, 1, NULL, 'maternelle', 10);

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
  `section` enum('primaire','secondaire','maternel') DEFAULT NULL,
  `date_embauche` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `professeurs`
--

INSERT INTO `professeurs` (`id`, `nom`, `prenom`, `contact`, `email`, `adresse`, `classe_id`, `cours_id`, `section`, `date_embauche`) VALUES
(1, 'sky', 'elle', '0979099031', 'skyelle@gmail.com', 'ikuku', 5, 0, 'secondaire', NULL);

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
(1, 2024, 2025, 'Annees-scolaire', 1, '2025-04-12 09:54:17');

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
(2, 'Mr sky board', 'Fourniture', 7, 10, '9', NULL, '2025-04-03 22:00:00', '2025-04-04 10:36:37', NULL),
(3, 'Tenu de gymnastique', 'Fourniture', 60, 10, 'direction', NULL, '2025-04-05 22:00:00', '2025-04-06 10:53:11', NULL);

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
(76, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:52:45'),
(77, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:54:20'),
(78, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:54:51'),
(79, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:56:53'),
(80, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:58:19'),
(81, NULL, 'chris', '', NULL, '::1', '2025-04-06 15:59:57'),
(82, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:02:42'),
(83, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:03:11'),
(84, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:03:14'),
(85, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:20:44'),
(86, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:21:02'),
(87, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:21:22'),
(88, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:21:28'),
(89, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:21:41'),
(90, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:21:57'),
(91, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:23:14'),
(92, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:23:17'),
(93, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:23:19'),
(94, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:26:04'),
(95, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:29:23'),
(96, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:29:38'),
(97, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:33:00'),
(98, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:33:03'),
(99, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:36'),
(100, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:37'),
(101, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:38'),
(102, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:38'),
(103, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:38'),
(104, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:36:39'),
(105, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:44:42'),
(106, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:44:43'),
(107, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:44:43'),
(108, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:44:44'),
(109, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:44:49'),
(110, NULL, 'chris', '', NULL, '::1', '2025-04-06 16:47:09'),
(111, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:07:52'),
(112, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:08:10'),
(113, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:08:12'),
(114, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:09:12'),
(115, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:09:44'),
(116, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:10:47'),
(117, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:11:15'),
(118, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:11:28'),
(119, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:11:51'),
(120, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:12:01'),
(121, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:06'),
(122, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:11'),
(123, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:15'),
(124, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:25'),
(125, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:34'),
(126, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:14:44'),
(127, NULL, 'chris', '', NULL, '::1', '2025-04-06 17:21:01'),
(128, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:21:51'),
(129, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:21:57'),
(130, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:22:11'),
(131, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:22:26'),
(132, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:25:26'),
(133, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:27:54'),
(134, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:32:04'),
(135, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:32:26'),
(136, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:32:43'),
(137, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:34:50'),
(138, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:36:20'),
(139, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:37:20'),
(140, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:37:21'),
(141, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:38:09'),
(142, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:39:46'),
(143, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:44:50'),
(144, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:45:13'),
(145, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:45:14'),
(146, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:50:11'),
(147, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 17:59:39'),
(148, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:00:02'),
(149, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:00:47'),
(150, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:00:55'),
(151, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:02:02'),
(152, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:06:29'),
(153, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:06:34'),
(154, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:08:11'),
(155, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:33:15'),
(156, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:36:46'),
(157, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:37:45'),
(158, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:40:25'),
(159, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:46:28'),
(160, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:05'),
(161, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:12'),
(162, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:15'),
(163, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:18'),
(164, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:23'),
(165, NULL, 'Plamedie', '', NULL, '::1', '2025-04-06 18:47:25'),
(166, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:52:39'),
(167, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: Mujing asnat avec matricule: SGS-2025-4727', '::1', '2025-04-06 18:53:58'),
(168, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:54:05'),
(169, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:56:32'),
(170, NULL, 'Utilisateur inconnu', '', NULL, '::1', '2025-04-06 18:58:15'),
(171, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:00:15'),
(172, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:00:27'),
(173, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:00:40'),
(174, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:00:46'),
(175, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:12'),
(176, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:14'),
(177, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:16'),
(178, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:19'),
(179, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:23'),
(180, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:39'),
(181, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:01:42'),
(182, NULL, 'chris', '', NULL, '::1', '2025-04-06 19:05:15'),
(183, NULL, 'chris', '', NULL, '::1', '2025-04-07 04:36:54'),
(184, NULL, 'chris', '', NULL, '::1', '2025-04-07 04:37:04'),
(185, NULL, 'chris', '', NULL, '::1', '2025-04-07 04:43:29'),
(186, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:23:16'),
(187, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:23:26'),
(188, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:24:01'),
(189, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:24:08'),
(190, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:25:00'),
(191, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:33:47'),
(192, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:33:49'),
(193, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:33:52'),
(194, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:33:59'),
(195, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:34:09'),
(196, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:34:24'),
(197, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:34:28'),
(198, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:35:31'),
(199, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:35:34'),
(200, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:59:22'),
(201, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:59:26'),
(202, NULL, 'chris', '', NULL, '::1', '2025-04-07 05:59:28'),
(203, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:12'),
(204, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:29'),
(205, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:30'),
(206, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:31'),
(207, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:36'),
(208, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:56:59'),
(209, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:19'),
(210, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:21'),
(211, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:22'),
(212, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:38'),
(213, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:39'),
(214, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:43'),
(215, NULL, 'chris', '', NULL, '::1', '2025-04-07 10:57:46'),
(216, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:28:55'),
(217, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:29:07'),
(218, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:29:20'),
(219, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:29:24'),
(220, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:29:30'),
(221, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:39:14'),
(222, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:39:17'),
(223, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:39:23'),
(224, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:39:55'),
(225, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:59:04'),
(226, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:59:07'),
(227, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:59:08'),
(228, NULL, 'chris', '', NULL, '::1', '2025-04-07 13:59:11'),
(229, NULL, 'chris', '', NULL, '::1', '2025-04-07 14:00:30'),
(230, NULL, 'chris', '', NULL, '::1', '2025-04-07 14:10:35'),
(231, NULL, 'fabkap', '', NULL, '::1', '2025-04-07 15:07:13'),
(232, NULL, 'fabkap', '', NULL, '::1', '2025-04-07 15:07:17'),
(233, NULL, 'chris', '', NULL, '::1', '2025-04-08 09:16:06'),
(234, NULL, 'chris', '', NULL, '::1', '2025-04-08 09:16:16'),
(235, NULL, 'chris', '', NULL, '::1', '2025-04-08 09:16:20'),
(236, NULL, 'chris', '', NULL, '::1', '2025-04-08 09:17:12'),
(237, NULL, 'chris', '', NULL, '::1', '2025-04-08 09:17:25'),
(238, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:42:52'),
(239, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:43:08'),
(240, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:43:09'),
(241, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:43:12'),
(242, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:43:41'),
(243, NULL, 'chris', '', NULL, '::1', '2025-04-08 10:43:43'),
(244, NULL, 'Ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-08 21:37:08'),
(245, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: Mujing asnat avec matricule: SGS-2025-0788', '::1', '2025-04-08 21:56:44'),
(246, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: tshisola Mael avec matricule: SGS-2025-3886', '::1', '2025-04-08 22:07:07'),
(247, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:53:51'),
(248, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:55:12'),
(249, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:56:13'),
(250, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:57:50'),
(251, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:58:06'),
(252, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:58:35'),
(253, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:58:58'),
(254, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:59:00'),
(255, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:59:13'),
(256, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:59:21'),
(257, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 22:59:51'),
(258, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:00:46'),
(259, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:01:31'),
(260, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:03:26'),
(261, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:05:59'),
(262, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:06:00'),
(263, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:08:14'),
(264, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:08:16'),
(265, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:11:30'),
(266, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:12:26'),
(267, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:13:03'),
(268, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:15:14'),
(269, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:15:33'),
(270, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:15:39'),
(271, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:15:41'),
(272, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:19:43'),
(273, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:19:45'),
(274, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:19:57'),
(275, NULL, 'Plamedie', '', NULL, '::1', '2025-04-08 23:20:01'),
(276, 21, 'Daniel', 'add', 'Ajout d\'un nouvel élève: tshisola Mael avec matricule: SGS-2025-9095', '::1', '2025-04-09 08:06:53'),
(277, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:17:08'),
(278, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:17:12'),
(279, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:21:00'),
(280, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:21:57'),
(281, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:22:39'),
(282, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:23:09'),
(283, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:24:28'),
(284, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:24:32'),
(285, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:24:38'),
(286, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:27:43'),
(287, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:37:51'),
(288, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:48:37'),
(289, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:59:22'),
(290, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:59:23'),
(291, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:59:23'),
(292, NULL, 'chris', '', NULL, '::1', '2025-04-09 08:59:23'),
(293, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:00:39'),
(294, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:01:23'),
(295, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:01:30'),
(296, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:01:37'),
(297, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:02:33'),
(298, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:03:47'),
(299, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:03:54'),
(300, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:04:01'),
(301, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:04:09'),
(302, NULL, 'chris', '', NULL, '::1', '2025-04-09 09:04:48'),
(303, NULL, 'chris', '', NULL, '::1', '2025-04-09 12:53:45'),
(304, NULL, 'chris', '', NULL, '::1', '2025-04-09 12:54:08'),
(305, NULL, 'chris', '', NULL, '::1', '2025-04-09 12:54:11'),
(306, NULL, 'chris', '', NULL, '::1', '2025-04-09 12:54:23'),
(307, NULL, 'Ciella', NULL, 'Mise à jour d\'un événement scolaire via AJAX: journee scientifique', '::1', '2025-04-09 13:11:07'),
(308, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:15:36'),
(309, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:14'),
(310, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:15'),
(311, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:15'),
(312, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:16'),
(313, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:16'),
(314, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:16'),
(315, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:16'),
(316, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:16'),
(317, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:17'),
(318, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:17'),
(319, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:17'),
(320, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:17'),
(321, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:17'),
(322, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:18'),
(323, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:18'),
(324, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:18'),
(325, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:18'),
(326, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:17:19'),
(327, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:19:15'),
(328, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:20:37'),
(329, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:22:48'),
(330, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:24:08'),
(331, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:24:12'),
(332, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:26:35'),
(333, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:26:46'),
(334, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:26:47'),
(335, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:26:49'),
(336, NULL, 'chris', '', NULL, '::1', '2025-04-09 13:26:52'),
(337, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:42:59'),
(338, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:09'),
(339, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:13'),
(340, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:15'),
(341, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:29'),
(342, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:35'),
(343, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:49'),
(344, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:53'),
(345, NULL, 'chris', '', NULL, '::1', '2025-04-09 14:43:55'),
(346, NULL, '', '', NULL, '::1', '2025-04-09 15:17:28'),
(347, NULL, '', '', NULL, '::1', '2025-04-09 15:17:41'),
(348, NULL, '', '', NULL, '::1', '2025-04-09 15:18:57'),
(349, NULL, '', '', NULL, '::1', '2025-04-09 15:20:35'),
(350, NULL, '', '', NULL, '::1', '2025-04-09 15:20:36'),
(351, NULL, '', '', NULL, '::1', '2025-04-09 15:20:37'),
(352, NULL, '', '', NULL, '::1', '2025-04-09 15:23:10'),
(353, NULL, '', '', NULL, '::1', '2025-04-09 15:23:19'),
(354, NULL, '', '', NULL, '::1', '2025-04-09 15:23:22'),
(355, NULL, '', '', NULL, '::1', '2025-04-09 15:23:45'),
(356, NULL, '', '', NULL, '::1', '2025-04-09 15:24:00'),
(357, NULL, 'chris', '', NULL, '::1', '2025-04-09 18:58:34'),
(358, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:01:28'),
(359, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:01:39'),
(360, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:01:41'),
(361, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:01:42'),
(362, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:04:42'),
(363, NULL, 'chris', '', NULL, '::1', '2025-04-09 19:34:10'),
(364, NULL, 'chris', '', NULL, '::1', '2025-04-09 21:07:05'),
(365, NULL, 'chris', '', NULL, '::1', '2025-04-09 21:56:28'),
(366, NULL, 'chris', '', NULL, '::1', '2025-04-09 22:15:45'),
(367, NULL, 'chris', '', NULL, '::1', '2025-04-10 06:12:51'),
(368, NULL, 'chris', '', NULL, '::1', '2025-04-10 19:29:23'),
(369, NULL, 'chris', '', NULL, '::1', '2025-04-10 20:16:42'),
(370, NULL, 'chris', '', NULL, '::1', '2025-04-10 20:16:51'),
(371, NULL, 'chris', '', NULL, '::1', '2025-04-10 20:16:52'),
(372, NULL, 'chris', '', NULL, '::1', '2025-04-10 20:17:05'),
(373, NULL, 'chris', '', NULL, '::1', '2025-04-10 20:19:44'),
(374, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:14:30'),
(375, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:15:45'),
(376, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:15:47'),
(377, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:15:50'),
(378, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:20:13'),
(379, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:20:15'),
(380, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:20:29'),
(381, 41, 'Daniel', 'add', 'Ajout d\'un nouvel élève: Kasong Malika avec matricule: SGS-2025-9404', '::1', '2025-04-11 09:25:18'),
(382, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:25:51'),
(383, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:25:59'),
(384, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:26:11'),
(385, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:26:14'),
(386, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:26:23'),
(387, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:26:31'),
(388, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:26:59'),
(389, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:29:00'),
(390, NULL, 'chris', '', NULL, '::1', '2025-04-11 09:29:02'),
(391, NULL, 'ciella', NULL, 'Ajout d\'un achat de fourniture: ENTRE', '::1', '2025-04-11 23:24:10'),
(392, NULL, 'ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-11 23:26:55'),
(393, NULL, 'ciella', NULL, 'Consultation du rapport d\'actions', '::1', '2025-04-11 23:28:25');

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
(39, 'chris', '$2y$10$FKMWd/4GtS5/7k0zxIBezO4xYkpsqTwyoxeCQ3QZQXDVZgLDUgD.m', 'directrice', '2025-04-09 18:58:06', 'chris@gmail.com', 'dist/img/users/user_1744225086_6747.jpg', NULL, '0979099031', 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 0, NULL, '0000-00-00 00:00:00', 90),
(40, 'ciella', '$2y$10$E2hwQrkn5V88stsW/UMFiO6j.Nc5x6Z3ZtE3jGM0LKglF4x2X9R1m', 'admin', '2025-04-09 19:16:39', 'ciella@mail.com', 'uploads/avatars/avatar_40_1744413638.jpg', NULL, '0979099031', '71105', 0, NULL, '0000-00-00 00:00:00', 90),
(41, 'Daniel', '$2y$10$31PFBbc8cIgZCPsXg4FMLenkNVK4GRilGWsh/vK2n3mer.340V6Y6', 'comptable', '2025-04-11 09:19:03', 'moilui@gmail.com', 'dist/img/users/user_1744363142_4151.jpg', NULL, '0979099031', '71105', 0, NULL, '0000-00-00 00:00:00', 90);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_classes_professeurs` (`prof_id`);

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
  ADD KEY `fk_eleve_session_scolaire` (`session_scolaire_id`),
  ADD KEY `fk_eleve_classe` (`classe_id`);

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
-- Index pour la table `erreurs_horaires`
--
ALTER TABLE `erreurs_horaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cours_id` (`cours_id`);

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
  ADD KEY `classe_id` (`classe_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT pour la table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `comptable`
--
ALTER TABLE `comptable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
-- AUTO_INCREMENT pour la table `erreurs_horaires`
--
ALTER TABLE `erreurs_horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evenements_scolaires`
--
ALTER TABLE `evenements_scolaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `frais`
--
ALTER TABLE `frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `horaires`
--
ALTER TABLE `horaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=413;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `stock_mouvements`
--
ALTER TABLE `stock_mouvements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

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
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_classes_professeurs` FOREIGN KEY (`prof_id`) REFERENCES `professeurs` (`id`);

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
  ADD CONSTRAINT `fk_eleve_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_eleve_session_scolaire` FOREIGN KEY (`session_scolaire_id`) REFERENCES `sessions_scolaires` (`id`);

--
-- Contraintes pour la table `erreurs_horaires`
--
ALTER TABLE `erreurs_horaires`
  ADD CONSTRAINT `erreurs_horaires_ibfk_1` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE;

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
-- Contraintes pour la table `paiements_frais`
--
ALTER TABLE `paiements_frais`
  ADD CONSTRAINT `fk_paiements_classe` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`);

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
