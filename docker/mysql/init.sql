-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : jeu. 19 mars 2026 à 21:30
-- Version du serveur : 5.7.39
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `media_tracker`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualite`
--

CREATE TABLE `actualite` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `actualite`
--

INSERT INTO `actualite` (`id`, `titre`, `contenu`, `user_id`, `created_at`) VALUES
(1, 'Test', 'Actu 1', 0, '2026-02-09 00:30:20'),
(2, 'd', 'd', 0, '2026-03-13 23:43:32');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `favorite_moment` text,
  `image_url` varchar(1000) DEFAULT NULL,
  `type_media` enum('film','série') NOT NULL DEFAULT 'film',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `favorite` tinyint(1) DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `commentaire` text,
  `tmdb_rating` decimal(3,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `title`, `rating`, `favorite_moment`, `image_url`, `type_media`, `created_at`, `favorite`, `user_id`, `view_count`, `commentaire`, `tmdb_rating`) VALUES
(13, 'MAYDAY', '4.0', NULL, 'https://fr.web.img4.acsta.net/pictures/22/10/26/10/19/0944904.jpg', 'film', '2025-03-30 21:04:57', 0, 0, 0, NULL, NULL),
(14, 'Alien, le huitième passager', '4.0', NULL, 'https://cafedesimages.fr/wp-content/uploads/2023/02/affiche.jpg', 'film', '2025-04-01 13:48:26', 0, 0, 0, NULL, NULL),
(15, 'Aliens, le retour', '4.0', NULL, 'https://dunnozmovie.com/wp-content/uploads/2019/04/aliens_le_retour.jpg?w=683&amp;h=1024', 'film', '2025-04-01 13:49:21', 0, 0, 0, NULL, NULL),
(16, 'Alien 3', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/17/06/29/12/09/200767.jpg', 'film', '2025-04-01 13:50:36', 0, 0, 0, NULL, NULL),
(18, 'Prometheus', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/85/32/85/20022460.jpg', 'film', '2025-04-01 13:52:26', 0, 0, 0, NULL, NULL),
(19, 'Alien:Covenant', '3.0', NULL, 'https://fr.web.img2.acsta.net/pictures/17/03/30/20/52/133613.jpg', 'film', '2025-04-01 13:52:50', 0, 0, 0, NULL, NULL),
(21, 'Alien: Romulus', '5.0', NULL, 'https://fr.web.img3.acsta.net/img/aa/f4/aaf4c61052ffc2a8056eee68681b1832.jpg', 'film', '2025-04-01 13:54:11', 0, 0, 0, NULL, NULL),
(22, 'Ma famille d abord', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/21/04/21/13/20/0238240.jpg', 'série', '2025-04-02 15:29:23', 0, 0, 0, NULL, NULL),
(23, 'Supernatural', '5.0', NULL, 'https://m.media-amazon.com/images/M/MV5BMDFmMGZmMGItNGRjNC00NjVjLWI5ODEtNzhjMTE5MmJhN2FkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'série', '2025-04-02 15:30:55', 1, 0, 0, NULL, NULL),
(24, 'Scrubs', '4.0', NULL, 'https://m.media-amazon.com/images/I/71GcGeUODwL._AC_UF1000,1000_QL80_.jpg', 'série', '2025-04-02 15:31:44', 0, 0, 0, NULL, NULL),
(25, 'Breaking bad', '4.0', NULL, 'https://fr.web.img5.acsta.net/pictures/19/06/18/12/11/3956503.jpg', 'série', '2025-04-02 15:32:16', 0, 0, 0, NULL, NULL),
(26, 'peaky blinders', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/22/06/07/11/57/5231272.jpg', 'série', '2025-04-02 15:32:47', 0, 0, 0, NULL, NULL),
(27, 'the walking dead', '5.0', NULL, 'https://m.media-amazon.com/images/I/81Nzfpz9EpL._AC_UF894,1000_QL80_.jpg', 'série', '2025-04-02 15:33:46', 0, 0, 0, NULL, NULL),
(28, 'Deadpool', '5.0', NULL, 'https://fr.web.img4.acsta.net/pictures/16/01/19/16/49/249124.jpg', 'film', '2025-04-02 15:45:55', 0, 0, 0, NULL, NULL),
(29, 'Deadpool2', '4.0', NULL, 'https://fr.web.img4.acsta.net/pictures/18/04/06/16/26/2317955.jpg', 'film', '2025-04-02 15:46:22', 0, 0, 0, NULL, NULL),
(30, 'Deadpool et Wolverine', '5.0', NULL, 'https://musicart.xboxlive.com/7/7e206d00-0000-0000-0000-000000000002/504/image.jpg', 'film', '2025-04-02 15:47:04', 0, 0, 0, NULL, NULL),
(32, 'Devil may cry v2007/2025', '4.0', NULL, 'https://www.ecranlarge.com/content/uploads/2025/02/1740632206-cbbf9667149d859cf3e8b6803d3912291.jpg', 'série', '2025-04-06 12:05:19', 0, 0, 0, NULL, NULL),
(33, 'le voyage de chihiro', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/00/02/36/71/chihiro.jpg', 'film', '2025-04-06 18:50:17', 0, 0, 0, NULL, NULL),
(34, 'Révélation', '3.0', NULL, 'https://fr.web.img2.acsta.net/img/ff/34/ff347b7c7baf356ce91b8d911f612a45.jpg', 'film', '2025-04-06 21:57:34', 0, 0, 0, NULL, NULL),
(36, 'les segpas ski', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/23/10/24/15/24/3478223.jpg', 'film', '2025-04-10 22:57:28', 0, 0, 0, NULL, NULL),
(37, 'US', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/19/03/14/10/51/3421837.jpg', 'film', '2025-04-10 22:58:17', 0, 0, 0, NULL, NULL),
(38, 'Interstellar', '5.0', NULL, 'https://m.media-amazon.com/images/I/91vIHsL-zjL.jpg', 'film', '2025-04-10 22:59:00', 0, 0, 0, NULL, NULL),
(39, 'bullet train', '4.0', NULL, 'https://fr.web.img6.acsta.net/c_310_420/pictures/22/07/13/11/36/3514892.jpg', 'film', '2025-04-10 22:59:33', 0, 0, 0, NULL, NULL),
(40, 'Morbius', '2.0', NULL, 'https://fr.web.img6.acsta.net/pictures/22/03/28/09/03/5612671.jpg', 'film', '2025-04-10 23:00:02', 0, 0, 0, NULL, NULL),
(41, 'Les Voyages de Gulliver', '5.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/78/34/85/19623700.jpg', 'film', '2025-04-13 10:40:42', 0, 0, 0, NULL, NULL),
(42, 'l\'amour extra large', '5.0', NULL, 'https://m.media-amazon.com/images/I/61AH3rdn8kL._AC_UF1000,1000_QL80_.jpg', 'film', '2025-04-13 10:41:23', 0, 0, 0, NULL, NULL),
(43, 'Fury', '5.0', NULL, 'https://fr.web.img5.acsta.net/pictures/14/09/22/16/44/411457.jpg', 'film', '2025-04-16 13:29:00', 0, 0, 0, NULL, NULL),
(44, 'Terminator', '5.0', NULL, 'https://fr.web.img4.acsta.net/pictures/22/09/27/12/52/4744720.jpg', 'film', '2025-04-16 16:38:50', 0, 0, 0, NULL, NULL),
(45, 'Terminator 2', '4.0', NULL, 'https://fr.web.img6.acsta.net/pictures/22/09/27/12/58/1710433.jpg', 'film', '2025-04-16 16:41:23', 0, 0, 0, NULL, NULL),
(46, 'Terminator 3 : Le Soulèvement des machines', '4.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/00/02/57/86/affiche.jpg', 'film', '2025-04-16 18:07:23', 0, 0, 0, NULL, NULL),
(47, 'un p tit truc en plus', '5.0', NULL, 'https://fr.web.img6.acsta.net/pictures/24/03/01/11/14/2965930.jpg', 'film', '2025-04-17 21:33:44', 0, 0, 0, NULL, NULL),
(48, 'badland hunters', '4.0', NULL, 'https://upload.wikimedia.org/wikipedia/id/d/d5/Badland_Hunters_film_poster.jpg', 'film', '2025-04-20 16:02:48', 0, 0, 0, NULL, NULL),
(49, 'Les cinq légendes', '4.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/f9673f0f7ab4afe88eb6d80cc0d7ae34b6ce63263701f455263595164412a89b.jpg', 'film', '2025-04-20 17:15:06', 0, 0, 0, NULL, NULL),
(50, 'Bac Nord', '5.0', NULL, 'https://fr.web.img3.acsta.net/pictures/21/06/07/13/11/2832970.jpg', 'film', '2025-04-23 22:13:53', 0, 0, 0, NULL, NULL),
(51, 'how met your mother', '5.0', NULL, 'https://m.media-amazon.com/images/I/81XmuItZwyL._AC_UF894,1000_QL80_.jpg', 'série', '2025-05-13 16:56:58', 0, 0, 0, NULL, NULL),
(52, 'Grave Encounters 2011', '2.0', NULL, 'https://m.media-amazon.com/images/I/61mleh9fe5S._AC_UF894,1000_QL80_.jpg', 'film', '2025-05-25 22:26:53', 0, 0, 0, NULL, NULL),
(53, 'Cobra Kai', '5.0', NULL, 'https://m.media-amazon.com/images/M/MV5BYjA3NDkwNzktNjJkYi00ODNhLWFhYzQtYzk5NjU4MDM0OWZmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'série', '2025-05-25 23:29:17', 0, 0, 0, NULL, NULL),
(54, 'Grave Encounters 2', '3.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/93/25/64/20245702.jpg', 'film', '2025-05-27 21:21:36', 0, 0, 0, NULL, NULL),
(55, 'Projet Almanac (2014)', '4.0', NULL, 'https://m.media-amazon.com/images/I/91zb1UxvnjL._AC_UF894,1000_QL80_.jpg', 'film', '2025-05-28 21:49:03', 0, 0, 0, NULL, NULL),
(56, 'Destination final', '4.0', NULL, 'https://images.justwatch.com/poster/210375320/s718/destination-finale.jpg', 'film', '2025-05-31 15:52:51', 0, 0, 0, NULL, NULL),
(57, 'Scary Movie', '3.0', NULL, 'https://fr.web.img2.acsta.net/medias/04/97/17/049717_af.jpg', 'film', '2025-05-31 15:53:27', 0, 0, 0, NULL, NULL),
(58, 'scary movie 2', '4.0', NULL, 'https://fr.web.img5.acsta.net/c_310_420/medias/nmedia/00/02/29/19/69199596_af.jpg', 'film', '2025-05-31 15:53:56', 0, 0, 0, NULL, NULL),
(59, 'Host', '4.0', NULL, 'https://fr.web.img6.acsta.net/pictures/20/10/15/08/19/4745591.jpg', 'film', '2025-05-31 22:29:02', 0, 0, 0, NULL, NULL),
(60, 'Atrocious', '1.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/89/30/49/20045043.jpg', 'film', '2025-06-01 14:33:29', 0, 0, 0, NULL, NULL),
(61, 'Destination final 2', '4.0', NULL, 'https://play-lh.googleusercontent.com/drRpfW5GGcOtEdJenEQitlQTXrgh65U1Dk7Pzg57ahukvT9UqpTlvDiZdcM75xF5f9Y09H_MP6vTLnCfCbY=w240-h480-rw', 'film', '2025-06-01 23:09:18', 0, 0, 0, NULL, NULL),
(62, 'dexter', '5.0', NULL, 'https://fr.web.img4.acsta.net/pictures/21/10/26/08/44/5892242.jpg', 'série', '2025-06-25 18:47:39', 0, 0, 0, NULL, NULL),
(63, 'Squid Game', '5.0', NULL, 'https://images-cdn.ubuy.co.in/64271944a0278c4d900691f8-cinemaflix-squid-game-poster-tv-series.jpg', 'série', '2025-06-30 09:38:43', 0, 0, 0, NULL, NULL),
(64, 'Happiness therapy ', '3.0', NULL, 'https://fr.web.img5.acsta.net/c_310_420/medias/nmedia/18/94/09/83/20302958.jpg', 'film', '2025-07-02 23:11:48', 0, 0, 0, NULL, NULL),
(65, 'le silence des agneaux', '3.0', NULL, 'https://m.media-amazon.com/images/I/51xoA+SVEDL._UF1000,1000_QL80_.jpg', 'film', '2025-07-08 14:58:50', 0, 0, 0, NULL, NULL),
(66, 'destination final 3', '3.0', NULL, 'https://m.media-amazon.com/images/I/51nxBNNGROL._UF894,1000_QL80_.jpg', 'film', '2025-07-08 19:32:30', 0, 0, 0, NULL, NULL),
(67, 'destination final 4', '3.0', NULL, 'https://www.mediatheque.be/fichiers/4b/5c/4c/cover_vd0708_scale_500x500.jpg', 'film', '2025-07-08 19:34:10', 0, 0, 0, NULL, NULL),
(68, 'destination final 5', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/82/09/95/19807298.jpg', 'film', '2025-07-08 19:36:08', 0, 0, 0, NULL, NULL),
(69, 'destination final 6', '5.0', NULL, 'https://fr.web.img6.acsta.net/img/fa/f4/faf4fcf1713e130a51eb3204ec782c59.jpg', 'film', '2025-07-08 19:37:02', 0, 0, 0, NULL, NULL),
(70, 'Sinister', '4.0', NULL, 'https://fr.web.img5.acsta.net/medias/nmedia/18/91/03/05/20267340.jpg', 'film', '2025-07-08 19:38:56', 0, 0, 0, NULL, NULL),
(71, 'Sinister 2', '3.0', NULL, 'https://m.media-amazon.com/images/I/51Vttk-H1RL._UF894,1000_QL80_.jpg', 'film', '2025-07-08 19:40:47', 0, 0, 0, NULL, NULL),
(72, 'Sinners', '4.0', NULL, 'https://fr.web.img6.acsta.net/img/92/be/92becd382d51ea2183925a41c514180f.jpg', 'film', '2025-07-08 19:42:35', 0, 0, 0, NULL, NULL),
(73, 'ziam', '3.0', NULL, 'https://fr.web.img6.acsta.net/c_310_420/img/5c/a9/5ca9bc4be2fbf4d2078e250021aa2d5e.jpg', 'film', '2025-07-10 21:24:24', 0, 0, 0, NULL, NULL),
(74, 'Brick', '2.0', NULL, 'https://fr.web.img6.acsta.net/img/cd/fe/cdfeb09ef9b1b5e1d5b35269efd5735a.jpeg', 'film', '2025-07-11 17:05:56', 0, 0, 0, NULL, NULL),
(75, 'The Match', '4.0', NULL, 'https://media.senscritique.com/media/000022837341/0/the_match.png', 'film', '2025-07-11 21:50:30', 0, 0, 0, NULL, NULL),
(76, 'jeu d interieur', '2.0', NULL, 'https://fr.web.img5.acsta.net/img/1d/48/1d480e9cea142e297b6c6c98e290fa74.jpg', 'film', '2025-07-11 23:33:26', 0, 0, 0, NULL, NULL),
(77, 'Red Eye : Sous haute pression', '3.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/35/85/91/18443181.jpg', 'film', '2025-07-12 20:05:46', 0, 0, 0, NULL, NULL),
(78, 'Hostel', '3.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/36/07/57/18470658.jpg', 'film', '2025-07-13 13:09:41', 0, 0, 0, NULL, NULL),
(79, 'Hostel 2', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/63/32/89/18771163.jpg', 'film', '2025-07-13 14:27:54', 0, 0, 0, NULL, NULL),
(80, 'hostel 3', '2.0', NULL, 'https://m.media-amazon.com/images/M/MV5BOTdhZTVmMDItZTE5MC00NWU2LTk0MDEtYWYzZjk5MTk1ODc1XkEyXkFqcGc@._V1_.jpg', 'film', '2025-07-13 18:48:08', 0, 0, 0, NULL, NULL),
(82, 'Silenced', '5.0', NULL, 'https://play-lh.googleusercontent.com/Th8oVRRQF1zBDY-VmKmwxoITNR8mmw5p-Zzd2HNyBFNp3A6SpyFcHfrc1ZiCrIRPKOqt', 'film', '2025-07-13 20:48:25', 0, 0, 0, NULL, NULL),
(83, 'Wolf Man', '3.0', NULL, 'https://fr.web.img4.acsta.net/img/dc/f9/dcf9ee537fa030a222e231363c339f6f.jpg', 'film', '2025-07-15 14:55:04', 0, 0, 0, NULL, NULL),
(84, 'The gorge', '3.0', NULL, 'https://www.ecranlarge.com/content/uploads/2024/10/the-gorge-affiche-officielle.jpg', 'film', '2025-07-15 19:33:37', 0, 0, 0, NULL, NULL),
(85, 'Terrifier', '3.0', NULL, 'https://m.media-amazon.com/images/M/MV5BN2M5MzJlYzctNmZhOC00MTFmLWIxZmUtN2I4NzY5MTlmNDdmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'film', '2025-07-15 20:18:57', 0, 0, 0, NULL, NULL),
(86, 'Terrifier 2', '3.0', NULL, 'https://fr.web.img3.acsta.net/pictures/22/11/23/09/30/2654706.jpg', 'film', '2025-07-15 20:47:24', 0, 0, 0, NULL, NULL),
(87, '84 m2', '3.0', NULL, 'https://fr.web.img6.acsta.net/img/b8/e7/b8e7d5e682836c190da279d7aa0c23b4.jpg', 'film', '2025-07-18 21:30:07', 0, 0, 0, NULL, NULL),
(88, 'Paranormal Activity 1', '4.0', NULL, 'https://fr.web.img3.acsta.net/medias/nmedia/18/72/56/47/19190472.jpg', 'film', '2025-07-18 21:32:37', 0, 0, 0, NULL, NULL),
(89, 'Paranormal Activity 2', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/78/64/53/19518464.jpg', 'film', '2025-07-18 21:34:46', 0, 0, 0, NULL, NULL),
(90, 'Paranormal Activity 3', '4.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/85/36/41/19816515.jpg', 'film', '2025-07-18 21:36:12', 0, 0, 0, NULL, NULL),
(91, 'YOU', '4.0', NULL, 'https://m.media-amazon.com/images/M/MV5BODA0NDA1MzgtYmIyYS00NmYwLTlhZDYtMjczMTU1M2ZkYzdkXkEyXkFqcGc@._V1_.jpg', 'série', '2025-07-19 12:37:51', 0, 0, 0, NULL, NULL),
(92, 'The Office', '5.0', NULL, 'https://www.abusdecine.com/wp-content/uploads/critique-serie-the-office-us-affiche.jpg', 'série', '2025-07-19 12:40:14', 0, 0, 0, NULL, NULL),
(93, 'Dora et la Cité perdue', '2.0', NULL, 'https://www.ecran-et-toile.com/uploads/5/5/8/7/55875205/dora-dvd_orig.png', 'film', '2025-07-19 21:21:22', 0, 0, 0, NULL, NULL),
(94, 'The Substance', '3.0', NULL, 'https://thumb.canalplus.pro/http/unsafe/%7BresolutionXY%7D/filters:quality(%7BimageQualityPercentage%7D)/canalplus-cdn.canal-plus.io/p1/unit/26903200/canal-ouah_50001/JAQCANAL/myCANAL_Jaquette_MEA_600x800-PXdQ', 'film', '2025-07-20 19:34:55', 0, 0, 0, NULL, NULL),
(95, 'Pirates des Caraïbes : La Malédiction du Black Pearl', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/35/07/46/affiche2.jpg', 'film', '2025-07-21 21:17:42', 0, 0, 0, NULL, NULL),
(96, 'Pirates des Caraïbes : Le Secret du coffre maudit', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/36/00/56/18604499.jpg', 'film', '2025-07-22 21:08:07', 0, 0, 0, NULL, NULL),
(97, 'tu ne tueras point', '5.0', NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQf7Dh-IVkxnrIMVZIWQRq-wk2S_duIKkdvXg&amp;s', 'film', '2025-07-22 22:51:05', 0, 0, 0, NULL, NULL),
(98, 'Le Beau-père', '3.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/72/22/68/19202949.jpg', 'film', '2025-07-23 12:33:08', 0, 0, 0, NULL, NULL),
(99, 'Pirates des Caraïbes : Jusqu au Bout du Monde', '3.0', NULL, 'https://fr.web.img3.acsta.net/medias/nmedia/18/62/88/97/18754731.jpg', 'film', '2025-07-25 17:30:49', 0, 0, 0, NULL, NULL),
(100, 'Pirates des Caraïbes : la Fontaine de Jouvence', '4.0', NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ6OSJyyqcFzDxHLAH-PbTvuVFAxmJGhqap6w&amp;s', 'film', '2025-07-25 17:31:19', 0, 0, 0, NULL, NULL),
(101, 'Pirates des Caraïbes : La Vengeance de Salazar', '3.0', NULL, 'https://fr.web.img2.acsta.net/pictures/17/03/02/10/13/106609.jpg', 'film', '2025-07-25 19:43:53', 0, 0, 0, NULL, NULL),
(102, 'Dexter: New Blood', '4.0', NULL, 'https://imusic.b-cdn.net/images/item/original/824/5056453202824.jpg?dexter-new-blood-2022-dexter-new-blood-dvd&amp;class=scaled&amp;v=1645814412', 'série', '2025-07-28 19:44:57', 0, 0, 0, NULL, NULL),
(103, 'Dexter : Les Origines', '3.0', NULL, 'https://fr.web.img2.acsta.net/img/a5/2a/a52a8b620b970daee3c11855084d2594.jpg', 'série', '2025-07-29 21:03:10', 0, 0, 0, NULL, NULL),
(104, 'Vertige limits', '2.0', NULL, 'https://upload.wikimedia.org/wikipedia/en/8/8d/Vertical_Limit.jpg', 'film', '2025-07-30 17:05:51', 0, 0, 0, NULL, NULL),
(105, 'A cure for wellness', '3.0', NULL, 'https://blob.cede.ch/catalog/15334000/15334532_1_92.jpg?v=2', 'film', '2025-07-30 20:46:06', 0, 0, 0, '', NULL),
(106, 'La Ruine', '3.0', NULL, 'https://www.critikat.com/wp-content/uploads/2008/06/artoff2169.jpg', 'film', '2025-08-04 19:54:41', 0, 0, 0, NULL, NULL),
(107, 'Évanouis', '2.0', NULL, 'https://fr.web.img2.acsta.net/img/a9/34/a9349d0803d1c401e217faf417099347.jpg', 'film', '2025-08-06 23:26:42', 0, 0, 0, NULL, NULL),
(108, 'Les 4 Fantastiques : Premiers pas', '3.0', NULL, 'https://www.ecranlarge.com/content/uploads/2025/06/les-4-fantastiques-premiers-pas-affiche-officielle-scaled.jpg', 'film', '2025-08-07 21:10:24', 0, 0, 0, NULL, NULL),
(109, 'H', '4.0', NULL, 'https://images.justwatch.com/poster/36607915/s718/h.jpg', 'série', '2025-08-08 09:30:24', 0, 0, 0, NULL, NULL),
(110, 'Prey', '2.0', NULL, 'https://fr.web.img6.acsta.net/pictures/21/08/12/09/32/0815126.jpg', 'film', '2025-08-08 18:57:32', 0, 0, 0, NULL, NULL),
(111, 'baghead', '2.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/0edd6858aad4534ea7b82cd6937c345c3e2e281f00bf5ac5c3be90de4e6d0eec.jpg', 'film', '2025-08-09 21:24:43', 0, 0, 0, NULL, NULL),
(112, 'Green Room', '2.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/2ab3bd7c7780cd1c33bcd6c30950bcf0fc7d597339ddc4952c029a4dbdf13892.jpg', 'film', '2025-08-10 20:26:25', 0, 0, 0, NULL, NULL),
(113, 'Thunderbolts', '3.0', NULL, 'https://www.etat-critique.com/wp-content/uploads/2025/05/thunderbolts.jpg', 'film', '2025-08-11 16:42:43', 0, 0, 0, NULL, NULL),
(114, 'Barbaque', '3.0', NULL, 'https://fr.web.img6.acsta.net/pictures/21/09/09/15/43/3782383.jpg', 'film', '2025-08-21 19:01:50', 0, 0, 0, NULL, NULL),
(115, 'Gatsby le magnifique', '5.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/86/89/36/20531934.jpg', 'film', '2025-08-21 19:02:24', 0, 0, 0, NULL, NULL),
(116, 'Hunger Games', '4.0', NULL, 'https://fr.web.img5.acsta.net/medias/nmedia/18/85/51/91/20018884.jpg', 'film', '2025-08-21 19:03:17', 0, 0, 0, NULL, NULL),
(117, 'Hunger Games : L Embrasement', '4.0', NULL, 'https://fr.web.img3.acsta.net/pictures/210/453/21045319_2013101714250983.jpg', 'film', '2025-08-21 19:03:49', 0, 0, 0, NULL, NULL),
(118, 'La Guerre des mondes (found footage)', '1.0', NULL, 'https://m.media-amazon.com/images/M/MV5BOWVlZDUzNTgtYzcxNC00OTYyLThlZjYtZDc1MTM2ZmI1Y2IzXkEyXkFqcGc@._V1_.jpg', 'film', '2025-08-21 19:04:51', 0, 0, 0, NULL, NULL),
(119, 'Toy Story', '5.0', NULL, 'https://play-lh.googleusercontent.com/-au4Roa6YnUP5lEOPIEfOJltOVz9Glo2gmmwPy8-SNl1KVz6DAXwSETEKPOV3RLoHmdG', 'film', '2025-08-21 19:05:46', 0, 0, 0, NULL, NULL),
(120, 'Hunger Games : La Révolte, partie 1', '3.0', NULL, 'https://fr.web.img6.acsta.net/pictures/14/12/05/17/25/453259.jpg', 'film', '2025-08-21 20:41:42', 0, 0, 0, NULL, NULL),
(121, 'Hunger Games : La Révolte, partie 2', '2.0', NULL, 'https://fr.web.img3.acsta.net/pictures/15/10/01/10/16/256927.jpg', 'film', '2025-08-22 20:25:42', 0, 0, 0, NULL, NULL),
(122, 'The Babadook', '2.0', NULL, 'https://m.media-amazon.com/images/M/MV5BMTk0NzMzODc2NF5BMl5BanBnXkFtZTgwOTYzNTM1MzE@._V1_.jpg', 'film', '2025-08-22 20:29:52', 0, 0, 0, NULL, NULL),
(123, 'Le Monde de Narnia : Chapitre 1 - Le lion, la sorcière blanche et l armoire magique', '2.0', NULL, 'https://fr.web.img6.acsta.net/c_310_420/medias/nmedia/18/35/53/32/18463695.jpg', 'film', '2025-08-29 19:13:01', 0, 0, 0, NULL, NULL),
(124, 'Le Monde de Narnia : Le Prince Caspian', '2.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/63/95/39/18943276.jpg', 'film', '2025-08-29 19:13:33', 0, 0, 0, NULL, NULL),
(125, 'Le Monde de Narnia : L Odyssée du Passeur d\'aurore', '2.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/78/19/25/19539619.jpg', 'film', '2025-08-29 19:14:30', 0, 0, 0, NULL, NULL),
(126, 'Constantine', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/35/23/94/18401439.jpg', 'film', '2025-09-01 21:21:14', 0, 0, 0, NULL, NULL),
(127, 'Alice au pays des merveilles', '3.0', NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpy4Qqc_uGlxzJjfEpfxcR-4sBB81SofXpcg&amp;s', 'film', '2025-09-04 09:51:50', 1, 0, 0, NULL, NULL),
(128, 'La Nuit des clowns', '3.0', NULL, 'https://fr.web.img6.acsta.net/img/57/06/5706719cce92266c95d41f82b1eea398.jpg', 'film', '2025-09-04 11:40:54', 0, 0, 0, NULL, NULL),
(129, 'Shameless', '4.0', NULL, 'https://m.media-amazon.com/images/I/91Pa2ps9fmL.jpg', 'série', '2025-09-06 12:55:40', 0, 0, 0, NULL, NULL),
(130, 'Teen Wolf', '3.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/b58ec2b7132c341c4ac7efa13b42e72c4b1474276c90259d8741d0fc432469ce.jpg', 'série', '2025-09-06 13:26:14', 0, 0, 0, NULL, NULL),
(131, 'Umbrella Academy', '4.0', NULL, 'https://fr.web.img5.acsta.net/pictures/19/01/24/17/03/5457570.jpg', 'série', '2025-09-06 13:47:45', 0, 0, 0, NULL, NULL),
(132, 'Toy Story 2', '4.0', NULL, 'https://www.ecranlarge.com/content/uploads/2020/08/ldosztevyoudrdo5nvoq4cq28al-131-scaled.jpg', 'film', '2025-09-06 21:34:31', 0, 0, 0, NULL, NULL),
(133, 'Toy Story 3', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/63/96/06/19415330.jpg', 'film', '2025-09-07 18:08:29', 0, 0, 0, NULL, NULL),
(134, 'Toy story 4', '4.0', NULL, 'https://fr.web.img6.acsta.net/c_310_420/pictures/19/06/12/17/42/4485647.jpg', 'film', '2025-09-07 18:11:15', 0, 0, 0, NULL, NULL),
(135, 'le journal d\'un dégonflé', '3.0', NULL, 'https://fr.web.img3.acsta.net/medias/nmedia/18/79/83/95/19533569.jpg', 'film', '2025-09-07 20:09:14', 0, 0, 0, NULL, NULL),
(136, 'le journal d\'un dégonflé 2', '4.0', NULL, 'https://play-lh.googleusercontent.com/54yBb6OXrQHhWMkLQeQQwRRZ6_nhpPRh7zJ7JykLFOkafGHBYK3WCnhhTM_nIO6g1p4K', 'film', '2025-09-08 20:00:22', 0, 0, 0, NULL, NULL),
(137, 'le journal d\'un dégonflé 3', '3.0', NULL, 'https://fr.web.img2.acsta.net/c_310_420/medias/nmedia/18/93/61/08/20267393.jpg', 'film', '2025-09-12 19:56:33', 0, 0, 0, NULL, NULL),
(138, 'Cars', '4.0', NULL, 'https://m.media-amazon.com/images/I/71JEpBxonxL._UF894,1000_QL80_.jpg', 'film', '2025-09-12 19:56:51', 0, 0, 0, NULL, NULL),
(139, 'Cars 2', '5.0', NULL, 'https://images.justwatch.com/poster/302159180/s718/cars-2.jpg', 'film', '2025-09-12 21:35:54', 0, 0, 0, NULL, NULL),
(140, 'Night Shot', '4.0', NULL, 'https://fr.web.img4.acsta.net/img/3d/94/3d9489eaede5e59f4bfc814b8b6d825c.jpg', 'film', '2025-09-14 16:15:56', 0, 0, 0, NULL, NULL),
(141, 'Rocks Academy', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/18/35/17/90/18373323.jpg', 'film', '2025-09-15 20:56:39', 0, 0, 0, NULL, NULL),
(142, 'Monstres Academy', '4.0', NULL, 'https://fr.web.img3.acsta.net/medias/nmedia/18/91/41/04/20484519.jpg', 'film', '2025-09-16 20:39:41', 0, 0, 0, NULL, NULL),
(143, 'Monstres et Cie', '4.0', NULL, 'https://fr.web.img2.acsta.net/c_310_420/medias/nmedia/00/02/36/12/affcie.jpg', 'film', '2025-09-17 17:45:18', 0, 0, 0, NULL, NULL),
(144, 'L\'Âge de glace', '4.0', NULL, 'https://fr.web.img6.acsta.net/medias/nmedia/00/02/47/01/affiche.jpg', 'film', '2025-09-23 19:04:05', 0, 0, 0, NULL, NULL),
(145, 'L\'Âge de glace 2', '5.0', NULL, 'https://fr.web.img2.acsta.net/medias/nmedia/18/35/82/41/18603091.jpg', 'film', '2025-09-24 20:32:07', 0, 0, 0, NULL, NULL),
(146, 'Alice in Borderland', '4.0', NULL, 'https://fr.web.img6.acsta.net/pictures/20/11/06/12/24/4584296.jpg', 'série', '2025-09-26 17:48:37', 0, 0, 0, NULL, NULL),
(147, 'L\'Âge de glace 3 : Le Temps des dinosaures', '4.0', NULL, 'https://m.media-amazon.com/images/I/71UGhvrAUzL._UF894,1000_QL80_.jpg', 'film', '2025-09-26 19:06:11', 0, 0, 0, NULL, NULL),
(148, 'L\'Âge de glace 4 : La Dérive des continents', '4.0', NULL, 'https://m.media-amazon.com/images/I/81-QldGm0gL._UF1000,1000_QL80_.jpg', 'film', '2025-09-28 19:26:00', 0, 0, 0, NULL, NULL),
(149, 'L\'Âge de glace : Les Lois de l\'Univers', '5.0', NULL, 'https://fr.web.img3.acsta.net/c_310_420/pictures/16/06/06/17/22/067515.jpg', 'film', '2025-09-29 19:26:10', 0, 0, 0, NULL, NULL),
(150, 'Haunted Hotel', '4.0', NULL, 'https://m.media-amazon.com/images/M/MV5BYjlmZDkzMmEtYTY2OS00NDAyLWE5ZjQtZGZiYjNiM2NmNmJkXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'série', '2025-09-30 17:29:36', 0, 0, 0, NULL, NULL),
(151, 'Zootopie', '4.0', NULL, 'https://fr.web.img6.acsta.net/c_310_420/pictures/15/12/11/14/34/280851.jpg', 'film', '2025-09-30 19:36:52', 0, 0, 0, '', NULL),
(152, 'Chicken Little', '4.0', NULL, 'https://m.media-amazon.com/images/I/71CLWwHvS4L._UF894,1000_QL80_.jpg', 'film', '2025-10-01 18:47:39', 0, 0, 0, NULL, NULL),
(153, 'do not enter', '3.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/fac0fa0db0e3c3848b9371998db5945a790b3e5a98ab0ee6711a9d9c58890d1a.png', 'film', '2025-10-05 21:01:46', 0, 0, 0, NULL, NULL),
(154, 'The pool', '3.0', NULL, 'https://m.media-amazon.com/images/I/71GP9nsSFsL._UF894,1000_QL80_.jpg', 'film', '2025-10-07 21:07:18', 0, 0, 0, NULL, NULL),
(155, 'Paranormal Activity 4', '5.0', NULL, 'https://m.media-amazon.com/images/S/pv-target-images/7990eb53742e52e508df89293450814313f981ff55042ddb4dba1b4644789197.jpg', 'film', '2025-10-13 18:44:13', 0, 0, 0, NULL, NULL),
(156, 'paranormal activity the marked ones', '3.0', NULL, 'https://fr.web.img5.acsta.net/pictures/13/12/09/10/32/303852.jpg', 'film', '2025-10-13 19:39:59', 0, 0, 0, NULL, NULL),
(157, 'Paranormal Activity 5 : Ghost Dimension', '4.0', NULL, 'https://fr.web.img4.acsta.net/pictures/15/08/11/18/03/392147.jpg', 'film', '2025-10-16 19:59:00', 0, 0, 0, NULL, NULL),
(158, 'Paranormal Activity: Next of Kin', '2.0', NULL, 'https://fr.web.img3.acsta.net/c_310_420/pictures/21/10/07/07/50/4190459.jpg', 'film', '2025-10-16 19:59:31', 0, 0, 0, NULL, NULL),
(159, 'Bienvenue à Zombieland', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/71/12/95/19186117.jpg', 'film', '2025-10-22 20:26:12', 0, 0, 0, NULL, NULL),
(160, 'En quarantaine', '4.0', NULL, 'https://fr.web.img4.acsta.net/medias/nmedia/18/66/38/51/18955974.jpg', 'film', '2025-10-26 21:47:32', 0, 0, 0, NULL, NULL),
(161, 'As the gods will', '4.0', NULL, 'https://images.justwatch.com/poster/253801183/s718/as-the-gods-will.jpg', 'film', '2025-10-29 20:31:57', 0, 0, 0, NULL, NULL),
(162, 'This is the end', '3.0', NULL, 'https://m.media-amazon.com/images/I/51saW1JFAkL.jpg', 'film', '2025-10-29 22:21:15', 0, 0, 0, NULL, NULL),
(163, 'Once upon a time', '5.0', NULL, 'https://m.media-amazon.com/images/I/91XUvXEH-XL._AC_UF1000,1000_QL80_.jpg', 'série', '2025-11-01 11:23:28', 0, 0, 0, NULL, NULL),
(164, 'Nefarious', '3.0', NULL, 'https://fr.web.img4.acsta.net/img/94/72/94727a906fa66f0c1c9a1ceffa6dda7d.jpg', 'film', '2025-11-03 12:08:34', 0, 0, 0, NULL, NULL),
(165, 'Les Bad Guys', '4.0', NULL, 'https://encrypted-tbn1.gstatic.com/images?q=tbn:ANd9GcR-310PvdqpC3wDvQ_nz1-Oblk1dqFq3w1gKotQ9VwBVFB505uH', 'film', '2025-11-11 21:42:32', 0, 0, 0, NULL, NULL),
(166, 'test', '4.0', NULL, 'https://https://cdn-imgix.headout.com/media/images/c9db3cea62133b6a6bb70597326b4a34-388-dubai-img-worlds-of-adventure-tickets-01.jpg?auto=format&amp;amp;w=1222.3999999999999&amp;amp;h=687.6&amp;amp;q=90&amp;amp;ar=16%3A9&amp;amp;crop=faces&amp;amp;fit=crop/url?sa=i&amp;amp;amp;url=https%3A%2F%2Fwww.allocine.fr%2Ffilm%2Ffichefilm_gen_cfilm%3D229867.html&amp;amp;amp;psig=AOvVaw3kJRT2XrUAOtDaXg39zkh0&amp;amp;amp;ust=1763063485140000&amp;amp;amp;source=images&amp;amp;amp;cd=vfe&amp;amp;amp;opi=89978449&amp;amp;amp;ved=0CBUQjRxqFwoTCLDeuLex7ZADFQAAAAAdAAAAABAE', 'film', '2025-11-12 20:27:07', 0, 0, 0, NULL, NULL),
(167, 'Prison Break', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/nMsTJ4QwdyRcyfM3jqqkJme7jc2.jpg', 'série', '2025-11-15 12:58:49', 0, 0, 0, NULL, NULL),
(168, 'Battle Royale', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/laUmjb39U6glMrOKyaco4QPeGaC.jpg', 'film', '2025-11-16 17:19:24', 0, 0, 0, NULL, NULL),
(169, 'Arcane', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/ypS7R36Vjcn51zZsXsta5onnaCo.jpg', 'série', '2025-11-16 19:38:27', 0, 0, 0, NULL, NULL),
(170, 'Monster', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/7ZhHyA0IIQOyz1hzCXpTilXg4Q2.jpg', 'série', '2025-11-16 19:39:26', 0, 0, 0, NULL, NULL),
(171, 'The Crazies', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/m6cxWRMnmPOvAW2hmY3Lyu9L8w5.jpg', 'film', '2025-11-16 20:59:17', 0, 0, 0, NULL, NULL),
(172, 'BoJack Horseman', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/6JFWzlChcGgLiIUo2COgNlWGFKy.jpg', 'série', '2025-11-16 21:54:04', 0, 0, 0, NULL, NULL),
(174, 'Source Code', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/4z8PEhDJ38OepZcKaDN38GJ0jXv.jpg', 'film', '2025-11-18 20:18:34', 0, 0, 0, NULL, NULL),
(175, 'Love and Monsters', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/718NnyxyQuBQcGWt9sdelA1Zc3h.jpg', 'film', '2025-11-18 20:58:42', 0, 0, 0, NULL, NULL),
(176, 'World War Z', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/xqk3gTrNODmH90QEgeBgEIHN1Fu.jpg', 'film', '2025-11-20 21:23:29', 0, 0, 0, NULL, NULL),
(177, 'Resident Evil', '1.0', NULL, 'https://image.tmdb.org/t/p/w500/tQprVBqg9ANfZZ0KNN6WQ5QP6X4.jpg', 'série', '2025-11-20 21:27:54', 0, 0, 0, NULL, NULL),
(178, 'Le Labyrinthe', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/3uzKxpRpF5M8RGeFhCCR9OE3dZb.jpg', 'film', '2025-11-22 14:13:47', 0, 0, 0, NULL, NULL),
(179, 'John Wick', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/orB4dHfZ9rOJbKYow7W0etezchl.jpg', 'film', '2025-11-24 14:47:33', 0, 0, 0, NULL, NULL),
(180, 'American Psycho', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/2DWqsx0vafGcfytSIwhtL5nI1uV.jpg', 'film', '2025-11-24 14:51:27', 0, 0, 0, NULL, NULL),
(181, 'Minecraft, le film', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/cq9z69AyIXeL2H14bqHE5ukm3M9.jpg', 'film', '2025-11-28 22:04:07', 0, 0, 0, NULL, NULL),
(183, 'Les Simpson, le film', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/8RAHFsRCE4Jwa1jD00tWFCvrsIa.jpg', 'film', '2025-11-28 22:41:57', 0, 0, 0, NULL, NULL),
(184, 'Dragons 3 : Le monde caché', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/oki89nNxoecoOTyqdPe9MS8zbiX.jpg', 'film', '2025-11-30 15:19:49', 0, 0, 0, NULL, NULL),
(185, 'Lost : Les Disparus', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/lJpYSqKmae4Rk57InDZoFa6mPzU.jpg', 'série', '2025-12-02 17:53:50', 0, 0, 0, NULL, NULL),
(186, 'Twilight, chapitre 1 : Fascination', '1.0', NULL, 'https://image.tmdb.org/t/p/w500/8phJ4i9m1tBDJbOwwQPvdeWhN2h.jpg', 'film', '2025-12-02 20:51:11', 0, 0, 0, NULL, NULL),
(187, 'Sans un bruit', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/S3PKmsBviNmXDYmik7uanIqpzD.jpg', 'film', '2025-12-03 20:13:47', 0, 0, 0, NULL, NULL),
(188, 'Sans un bruit 2', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/1zSCVOFFkT7JtOoBInPmGQGJAfE.jpg', 'film', '2025-12-06 22:06:52', 0, 0, 0, NULL, NULL),
(189, 'Naruto', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/mLoI2Zto2JYUvSB8PpqvZIV7vWj.jpg', 'série', '2025-12-06 23:28:57', 0, 0, 0, NULL, NULL),
(190, 'Tokyo Ghoul', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/eKxblpRtHiMHLW5sjq7ZLGsGBOh.jpg', 'série', '2025-12-06 23:29:42', 0, 0, 0, NULL, NULL),
(191, 'One Piece', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/ohr8BDFA47MYIyGGENwjPkwihmo.jpg', 'série', '2025-12-06 23:29:58', 0, 0, 0, NULL, NULL),
(192, 'Cyberpunk : Edgerunners', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/74Oo4hRy9xadpDZGqsWu2XqoNje.jpg', 'série', '2025-12-06 23:40:26', 0, 0, 0, NULL, NULL),
(193, 'Great Teacher Onizuka', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/4jUhf9YpLmQMy00XfSR9w5p0HCO.jpg', 'série', '2025-12-06 23:42:16', 0, 0, 0, NULL, NULL),
(194, 'American Sniper', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/9BEN7zRpoLBaBU3t97kPJkHUgnv.jpg', 'film', '2025-12-07 22:20:36', 0, 0, 0, NULL, NULL),
(195, 'Sans un bruit : Jour 1', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ymMaqwKN3Ovy9nlIRmk5GsnxEkx.jpg', 'film', '2025-12-08 09:10:52', 0, 0, 0, NULL, NULL),
(196, 'Titanic', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/vpsvHLkoeKUjceIMeNSqCp3xEyY.jpg', 'film', '2025-12-08 09:13:04', 0, 0, 0, NULL, NULL),
(199, 'Alien, la résurrection', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ePPdAnGJk9mR1qNxHhGrIhLAo4Q.jpg', 'film', '2025-12-09 22:11:40', 0, 0, 0, NULL, NULL),
(202, 'Les Simpson', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/AoRfDyk5uTCtQn4O9Q8g5Bk2A1c.jpg', 'série', '2025-12-09 22:25:44', 0, 0, 0, NULL, NULL),
(203, 'Hannibal', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/zZg6fruTRO418hNDdLWo76VBveJ.jpg', 'film', '2025-12-10 09:25:36', 0, 0, 0, NULL, NULL),
(204, 'The Seven Deadly Sins', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/8CgrM1wNIowRfDIUOWpzdwW0vjY.jpg', 'série', '2025-12-11 08:36:42', 0, 0, 0, NULL, NULL),
(205, 'Death Note', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/chnG4pYo89weaBjXHJWvW68E35B.jpg', 'série', '2025-12-11 08:37:05', 0, 0, 0, NULL, NULL),
(206, 'Ça', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/3SUz0F0I2Bodcj9Ev2pYSWnE9zp.jpg', 'film', '2025-12-11 11:07:21', 0, 0, 0, NULL, NULL),
(207, 'Iron Man - Armored Adventures', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/kIJGgBwh37V2nfgnJFaN7zDIjdv.jpg', 'série', '2025-12-11 11:16:52', 0, 0, 0, NULL, NULL),
(208, 'The Human Centipede', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/hRDtyyexc0rl1sSCCylX5sNZLaH.jpg', 'film', '2025-12-11 14:38:17', 0, 0, 0, NULL, NULL),
(209, 'The Human Centipede 2', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/mauOjknVkgV7QTWzhMTtZC5UcZM.jpg', 'film', '2025-12-11 14:38:29', 0, 0, 0, NULL, NULL),
(210, 'The Human Centipede 3', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/4U9aXg76Deiq6geFep7QFa6G8fQ.jpg', 'film', '2025-12-11 14:39:32', 0, 0, 0, NULL, NULL),
(212, 'Imperium', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/fEwo3gZfbeoovdcTyf4o4wATE1r.jpg', 'film', '2025-12-14 23:26:24', 0, 0, 0, NULL, NULL),
(213, 'Imperium', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/fEwo3gZfbeoovdcTyf4o4wATE1r.jpg', 'film', '2025-12-15 13:41:50', 0, 5, 0, NULL, NULL),
(214, 'Engrenages', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/sQbPLPZaa5gcuPZCJbzT83SPoa3.jpg', 'série', '2025-12-15 16:43:36', 0, 6, 0, NULL, NULL),
(215, 'Mon voisin Totoro', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/eEpy8IiR8N0S6mgkdAjDCMlMYQO.jpg', 'film', '2025-12-16 09:18:48', 0, 0, 0, NULL, NULL),
(216, 'Mon voisin Totoro', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/eEpy8IiR8N0S6mgkdAjDCMlMYQO.jpg', 'film', '2025-12-16 09:23:34', 0, 7, 0, NULL, NULL),
(217, 'Black Friday !', '1.0', NULL, 'https://image.tmdb.org/t/p/w500/3qeeKw2WbhfECncOPpPcNIj9RN6.jpg', 'film', '2025-12-18 22:03:29', 0, 0, 0, NULL, NULL),
(218, 'Boruto : Naruto Next Generations', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/sYjqnL9z9v6z2sk8An61ZrNxPSt.jpg', 'série', '2025-12-18 22:03:56', 0, 0, 0, NULL, NULL),
(219, 'Guns Akimbo', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/vV23MzddmlZJ6TIXpmRUyGV9961.jpg', 'film', '2025-12-19 20:27:04', 0, 0, 0, NULL, NULL),
(220, 'Code Lyoko', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/tnbDoaiVjD6svXDIxJ6dMUTEBVK.jpg', 'série', '2025-12-19 20:28:27', 0, 0, 0, NULL, NULL),
(221, 'Akuma-kun', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/3frWTreENCV0ZQfLnNRYnCBwRUC.jpg', 'série', '2025-12-19 20:33:57', 0, 0, 0, NULL, NULL),
(222, 'Identity', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/jSSgqRcLaDLh56t5ko1ywAKq0q9.jpg', 'film', '2025-12-20 21:18:47', 0, 0, 0, NULL, NULL),
(223, 'Mentalist', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/tX9UXgTEVPg9w9hd3SHPfKh75zc.jpg', 'série', '2025-12-20 23:02:07', 0, 0, 0, NULL, NULL),
(224, 'Mayhem : Légitime Vengeance', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/dyZKPNQtR8SopfwujcCLreTZdtc.jpg', 'film', '2025-12-21 20:26:10', 0, 0, 0, NULL, NULL),
(225, 'Fast and Furious', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/gsW9S3K6oBLKVMMLuSvKwEPOKHc.jpg', 'film', '2025-12-24 21:48:42', 0, 8, 0, NULL, NULL),
(226, 'Avengers', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/ylsAO88v2tF0iXRFojPa0UaAJf1.jpg', 'film', '2025-12-24 21:54:05', 0, 0, 0, NULL, NULL),
(228, 'À couteaux tirés', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/qebfcMW8RDjoMTNLNeDFfMlVCGp.jpg', 'film', '2025-12-24 22:01:33', 0, 9, 0, NULL, NULL),
(229, 'L\'An 1 : Des débuts difficiles', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/1onlD9vNOuECXl4vduS0w48t2lw.jpg', 'film', '2025-12-27 18:15:43', 0, 0, 0, NULL, NULL),
(230, 'Malnazidos', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/4z9fsQTypbwzaWa4kLERtJjjM83.jpg', 'film', '2025-12-27 21:54:12', 0, 0, 0, NULL, NULL),
(231, 'Nightmare Island', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/mwAQx6TALxpDPHrpbqHXsbq67bq.jpg', 'film', '2025-12-29 21:18:42', 0, 0, 0, NULL, NULL),
(232, 'John Wick : Chapitre 2', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/r687UV1zQ5KDB9AxRokRscWIRvt.jpg', 'film', '2025-12-30 21:32:40', 0, 0, 0, NULL, NULL),
(233, 'Oppenheimer', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/boAUuJBeID7VNp4L7LNMQs8mfQS.jpg', 'film', '2025-12-31 20:56:14', 0, 0, 0, NULL, NULL),
(234, 'Coach Carter', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/8wbwNiKyostoqs0DLx0dqBhlDvy.jpg', 'film', '2025-12-31 21:01:05', 0, 10, 0, NULL, NULL),
(235, 'John Wick : Parabellum', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/8gd71hpzHIF3gCkmJBwV5egtu3k.jpg', 'film', '2026-01-01 11:09:53', 0, 0, 0, NULL, NULL),
(236, 'Stranger Things', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/4Y5ZXYnWBIV8Vpe8hcA0LH6hC80.jpg', 'série', '2026-01-01 14:28:45', 0, 0, 0, NULL, NULL),
(237, 'Drive', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/mUKm5eaYm30KYyaudRn5tA204ua.jpg', 'film', '2026-01-02 21:48:51', 0, 0, 0, NULL, NULL),
(238, 'Wake Up Dead Man : Une histoire à couteaux tirés', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/qlUqHz8M1x3a2gZSggXY2t1qhNo.jpg', 'film', '2026-01-03 21:11:45', 0, 0, 0, NULL, NULL),
(239, 'À couteaux tirés', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/qebfcMW8RDjoMTNLNeDFfMlVCGp.jpg', 'film', '2026-01-03 21:12:01', 0, 0, 0, NULL, NULL),
(240, 'Glass Onion : Une histoire à couteaux tirés', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/j8mGH7DzwUD9soODpUI64PWNumg.jpg', 'film', '2026-01-03 21:12:40', 0, 0, 0, NULL, NULL),
(241, 'John Wick : Chapitre 4', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/n1YTIyhAqqqFyDGFTzV7WaU1JfK.jpg', 'film', '2026-01-03 21:16:05', 0, 0, 0, NULL, NULL),
(243, 'Kill Bill : Volume 1', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/udRaQKzT0LG4iQFxHLaYjno9uAT.jpg', 'film', '2026-01-07 20:57:18', 0, 0, 0, '', NULL),
(244, '10 bonnes raisons de te larguer', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/2Ys0Wb2ZzgaqpyJ5zh10rWoA50t.jpg', 'film', '2026-01-08 18:40:30', 0, 0, 0, 'J\'ai adoré le film', NULL),
(245, 'Supernatural', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/iBR4U3MZelj5avBqqs1SJpIqArP.jpg', 'série', '2026-01-08 20:08:36', 0, 5, 0, 'jfznzjo', NULL),
(246, 'Urban Legend', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/dUvZDNU9G0njhM628NA5phASntq.jpg', 'film', '2026-01-08 21:01:28', 0, 0, 0, 'bonne DA', NULL),
(247, 'Urban Legend 2 : Coup de grâce', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/48fwTxdA2xFRdihAPLPDxqwMOym.jpg', 'film', '2026-01-09 20:51:33', 0, 0, 0, 'Pas ouf le 1er était meilleur', NULL),
(248, 'Archer', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/vhnrkTGYPqcB63ALcSJm0WoaKHT.jpg', 'série', '2026-01-09 23:01:31', 0, 0, 0, 'Cool', NULL),
(249, 'Zootopie 2', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/qq6MfHFDvBEzHhkE0Q5ozbkbde4.jpg', 'film', '2026-01-10 17:23:03', 0, 0, 0, 'Animation super', NULL),
(250, 'Snowpiercer : Le Transperceneige', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/vRr9z33hkI0Hp1gCCtV6s1jbf9.jpg', 'film', '2026-01-10 22:22:25', 0, 0, 0, '', NULL),
(251, 'Phénomènes', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/3HdIzwQcWJqIVG8qOQKvR8LMwCO.jpg', 'film', '2026-01-11 16:01:44', 0, 0, 0, 'Bof, trop de hype et au final rien', NULL),
(252, 'Admis à tout prix', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/qn3PyvQ4Pelud7fxpXo3TMpeDFL.jpg', 'film', '2026-01-11 20:32:13', 0, 0, 0, 'Les vieux films comme ça c\'est les meilleurs', NULL),
(253, 'Rebel Ridge', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/8fQFQRgkF3wDw2L2uVakSOlvvwj.jpg', 'film', '2026-01-12 21:20:04', 0, 0, 0, 'Très bon film mais la fin c\'est de la m*****', NULL),
(254, 'La Machine à démonter le temps', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/aPr9YVLImLOz2aMZ2qVJ9V16a03.jpg', 'film', '2026-01-13 20:09:04', 0, 0, 0, 'passable', NULL),
(255, 'The Dictator', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/zKcCaWewiEF2XGJDSureoXb2uoo.jpg', 'film', '2026-01-14 17:42:37', 0, 0, 0, 'c\'est bien', NULL),
(256, 'La Machine à Démonter le Temps 2', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/6W5cGwT28Z5GfolprlsyuKDVpyD.jpg', 'film', '2026-01-14 19:34:58', 0, 0, 0, 'bof', NULL),
(257, 'Les Grands Frères', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/zii7bV9QJ9423OOsAkDTzzgxvuP.jpg', 'film', '2026-01-14 20:48:56', 0, 0, 0, 'Très bon film', NULL),
(258, 'Speak No Evil', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ueffCcq485jkEQMY30yCDmGEksQ.jpg', 'film', '2026-01-15 19:16:01', 1, 0, 0, 'Pas mal ouais pas mal', NULL),
(259, 'Bref', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ddCxr3JOEh8tTebrHlXv31wY37r.jpg', 'série', '2026-01-15 19:18:57', 0, 0, 0, 'pas mal', NULL),
(260, 'Rio', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/6A4PpkdHvkRvrgY8FHkK61v3kka.jpg', 'film', '2026-01-15 20:52:35', 0, 0, 0, 'bon film d\'animation', NULL),
(261, 'Greenland : Migration', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/mu05wZK0MH2bYmuO7hXwZECqC1X.jpg', 'film', '2026-01-17 17:19:06', 0, 0, 0, 'Bon film', NULL),
(262, 'Sex Academy', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/vr7eTuiwCOyGJykT6PnoplFA3Oc.jpg', 'film', '2026-01-17 20:36:51', 0, 0, 0, 'Bonne ref à \"10 bonnes raisons de te larguer\"', NULL),
(263, 'La Princesse et la Grenouille', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/7ktgHsEtkOz80h1OkxKLaQYDCAa.jpg', 'film', '2026-01-17 20:54:36', 0, 0, 0, 'Très bien', NULL),
(264, 'Abigail', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/qvpOyAuB86vvCRK16WZGUwGioSd.jpg', 'film', '2026-01-18 22:29:27', 0, 0, 0, 'Pas ouf personnage chiant', NULL),
(265, 'Crazy, Stupid, Love', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/wYQ8LLOGoxqVtRTdKPmi1J59FWo.jpg', 'film', '2026-01-19 21:36:13', 0, 0, 0, 'Quand tu vois ryan gosling dans un film ça va être une masterclass', NULL),
(266, 'Madagascar', '5.0', NULL, 'https://image.tmdb.org/t/p/w500/aA1eZtISZ2rqUk2QIUgu1pXcHrm.jpg', 'film', '2026-01-19 22:55:15', 0, 0, 0, 'Incroyable', NULL),
(267, 'La Femme de ménage', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/szdrVdnS8XAzqFyzPDhYXaJk7EK.jpg', 'film', '2026-01-20 12:46:19', 0, 11, 0, 'Bonne adaptation', NULL),
(268, 'Madagascar 2', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/eLCEqcUYVjAyFHDmuteNPja5YuO.jpg', 'film', '2026-01-20 21:26:01', 0, 0, 0, 'Toujours aussi bien', NULL),
(269, 'BlacKkKlansman : J\'ai infiltré le Ku Klux Klan', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/z8vDfDI7SOVasPlqUT38Pm0wyEZ.jpg', 'film', '2026-01-21 20:29:03', 0, 0, 0, 'Très bon film rien à dire', NULL),
(270, 'Silent Hill', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/2I6UnjiJqmrYOzpsjIMjINcRSKF.jpg', 'film', '2026-01-22 23:34:25', 0, 0, 0, 'Bonne meta du film fidèle au jeu', NULL),
(271, 'Scream 2', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/tQjkmhosfaZRllklX7P92xBO62o.jpg', 'film', '2026-01-23 17:56:03', 0, 0, 0, 'Ça passe', NULL),
(272, 'Silent Hill : Révélation 3D', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/nKJAtXn1K6AODXxh5j4jqrSrsY6.jpg', 'film', '2026-01-23 20:56:01', 0, 0, 0, 'c\'est bien', NULL),
(273, '(500) jours ensemble', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/Au6ac2MEQuka3DtJMVxeyUaDNsh.jpg', 'film', '2026-01-25 10:32:46', 0, 0, 0, 'Que du destin', NULL),
(274, 'Tucker & Dale fightent le mal', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/oDrfUaVWRy2qbaj9FzPWwlFHPPP.jpg', 'film', '2026-01-25 10:34:11', 0, 0, 0, 'c\'est un 2 mais il est bien', NULL),
(275, 'Snowpiercer', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/uDBtG06dmhv6Ubyjx9gr3eUFQUa.jpg', 'série', '2026-01-29 21:36:55', 0, 0, 0, '1er saison nikel après le protagoniste est chiant à la fin de la s3', NULL),
(276, 'Inglourious Basterds', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/lPKwFzX4TiWLA4Mo5Bnf8aIIrJm.jpg', 'film', '2026-02-01 21:06:40', 0, 0, 0, 'Il est bien j\'avais trop d\'attentes sur le film mais un peu déçu', NULL),
(277, 'HIS & HERS', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/p0uRDsiYCgLSZwiXgX7Xgw3llwk.jpg', 'série', '2026-02-02 19:25:47', 0, 0, 0, 'Une fin inattendu', NULL),
(278, 'Sweetpea', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/feoDYCy0AUD031gLqBmA9gC4pw1.jpg', 'série', '2026-02-08 00:54:11', 0, 0, 0, '', NULL),
(279, 'Tetris', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/4F2QwCOYHJJjecSvdOjStuVLkpu.jpg', 'film', '2026-02-09 19:48:22', 0, 0, 0, 'Très bon film', NULL),
(280, 'Mickey 17', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/on60ASUpGg1xfiFucuMCv9wVqtq.jpg', 'film', '2026-02-09 21:28:06', 0, 0, 0, 'Bien', NULL),
(281, 'Miss Campus', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/ce3NNmhVZY5NkUe5Y8Je8CciKqf.jpg', 'film', '2026-02-11 12:34:53', 0, 0, 0, 'ça passe', NULL),
(282, 'La Chute de la maison Usher', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/1fpi8RzWJyEBSYKOZ4DiI3rDZwf.jpg', 'série', '2026-02-11 21:48:17', 0, 0, 0, 'cv j\'ai bien aimé', NULL),
(283, 'Legend', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/uFIBu8h4gFcbiGFrQVRbu03H1ku.jpg', 'film', '2026-02-12 11:10:13', 0, 0, 0, 'Très bon film', NULL),
(284, 'Kill Bill : Volume 2', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/852ZvIARe3CISnX9t96unwSHyoL.jpg', 'film', '2026-02-12 22:03:55', 0, 0, 0, 'bon film', NULL),
(285, 'Steve, bête de combat', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/iPPlCMYpHH1TCXV6DaXtoxmeows.jpg', 'film', '2026-02-14 21:39:38', 0, 0, 0, 'c\'est bien', NULL),
(286, 'Scream 3', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/8LRHJfLWUx3mRLymFwT7Oo1XuUx.jpg', 'film', '2026-02-14 21:40:37', 0, 0, 0, 'bof', NULL),
(287, 'Menteur, menteur', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/yvb0rSUVGv6dQJupdjcqkuo5kaV.jpg', 'film', '2026-02-24 21:54:15', 0, 0, 0, 'Très bon film avec jim carrey', NULL),
(288, 'Super blonde', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/tsxVcHIZfvuDeLtvnXUhAEor90v.jpg', 'film', '2026-02-26 23:40:13', 0, 0, 0, 'passable\r\n', NULL),
(289, 'The Truman Show', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/7p5MzMb4h0Y2WUn73r4MHKNeh3X.jpg', 'film', '2026-02-27 22:18:07', 0, 0, 0, 'Incroyable film', NULL),
(290, 'Resident Evil : Damnation', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/cu3WaOhHgvvyvJ0KN42YrgZpHrX.jpg', 'film', '2026-02-28 20:52:29', 0, 0, 0, 'très bon film d\'animation de Resident Evil ', NULL),
(291, 'Spider-Man : Across the Spider-Verse', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ydaJsTyrRimSQ1dTf2xvrmJKz5b.jpg', 'film', '2026-03-02 09:07:02', 0, 0, 0, 'Très bon film', NULL),
(292, 'Banlieusards', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/8x5zUAUaVH38Ec78eXgmTdYRENP.jpg', 'film', '2026-03-05 23:38:06', 0, 0, 0, 'Bon film', NULL),
(293, 'Delirium', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/j2jIkjrASnjtwCmCNDwh9LDWvq9.jpg', 'film', '2026-03-06 13:21:43', 0, 0, 0, 'ça passe', NULL),
(294, 'Avant toi', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/ooKtRBvopA78WkI7UJytG0bOQuK.jpg', 'film', '2026-03-06 21:23:13', 0, 0, 0, 'la larme à l\'oeil\r\n', NULL),
(295, 'La Nonne : La Malédiction de Sainte-Lucie', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/yOrY0pByezFe8Eo3AcxW5zCBj5w.jpg', 'film', '2026-03-08 21:52:27', 0, 0, 0, 'Déçue', NULL),
(296, 'La Nonne', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/e0XDl1xHWvtwmIQXVadQpiB7YN0.jpg', 'film', '2026-03-08 21:53:01', 0, 0, 0, 'Très bon film', NULL),
(297, 'Companion', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/gLXGCPOSm8qU5awxfDl1ztKoZXY.jpg', 'film', '2026-03-09 20:40:37', 0, 0, 0, 'Sympa le type de film', NULL),
(298, 'Exit 8', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/hJ9qCQ3Dx9vwet09Lbqs8eZTd8e.jpg', 'film', '2026-03-11 21:29:30', 0, 0, 0, 'Très bonne adaptation du jeu', NULL),
(299, 'Retour à Silent Hill', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/hm4HgeeQsPgsxkPU3wxZvosI5Dh.jpg', 'film', '2026-03-12 20:26:51', 0, 0, 0, 'Ça passe mais avoir deux fins différentes bonne idée', NULL),
(300, 'Marche ou crève', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/4KXidDT9Z8w23A4r0eoRwwDPYOD.jpg', 'film', '2026-03-14 09:50:34', 0, 0, 0, 'Très bon film', NULL),
(301, '28 Ans plus tard : Le Temple des morts', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/1SQVgXrkBVbwUWugj3ZZ6T26So2.jpg', 'film', '2026-03-14 13:43:23', 0, 0, 0, 'Un peu mieux que l\'autre mais bof', NULL),
(302, 'Scream 4', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/6b7QW0rseIiD6b5hqCFPqE378t.jpg', 'film', '2026-03-15 13:40:33', 0, 0, 0, 'Un peu trop long mais bon twist à la fin.', NULL),
(303, 'Scream', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/9LfX3IBeByGdFkmkmNztKBr4nYv.jpg', 'film', '2026-03-15 13:41:55', 0, 0, 0, 'Bon film d\'horreur, surement le meilleur de la franchise.', NULL),
(304, 'Reconnu coupable', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/roKKWFRmFMRzv5NhBhCmDW5GdQr.jpg', 'film', '2026-03-16 19:58:58', 0, 0, 0, 'Très bon film avec un bon scénario', NULL),
(305, 'Saw V', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/rA8w4g4eg0GcD0P8mZZR11r7r4X.jpg', 'film', '2026-03-16 21:57:43', 0, 0, 0, 'Acteur qui joue bien ', NULL),
(306, 'Ghost Bastards', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/iGQzqO9QF84Zz0920DqlgEwOceS.jpg', 'film', '2026-03-17 20:43:33', 0, 0, 0, 'très bon film', NULL),
(307, 'A Haunted House 2', '3.0', NULL, 'https://image.tmdb.org/t/p/w500/oV7M00fPXy5P0nbdeMbSUjYv0vx.jpg', 'film', '2026-03-17 20:44:00', 0, 0, 0, 'très bon film', NULL),
(308, 'War Machine', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/tlPgDzwIE7VYYIIAGCTUOnN4wI1.jpg', 'film', '2026-03-18 19:06:39', 0, 0, 0, 'Très bon acteur', NULL),
(309, 'Le Sifflet', '2.0', NULL, 'https://image.tmdb.org/t/p/w500/d3U7rzj3ReOjpxNBd2n09WAxSLH.jpg', 'film', '2026-03-18 20:08:54', 0, 0, 0, 'bof le concept était bien mais dommage il manque un truc ', NULL),
(310, 'Saw 3D : Chapitre final', '4.0', NULL, 'https://image.tmdb.org/t/p/w500/lVeg4c1XQMYeXXrXl2259qCDXUw.jpg', 'film', '2026-03-19 19:37:53', 0, 0, 0, 'L\'un des meilleurs SAW', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `media_to_watch`
--

CREATE TABLE `media_to_watch` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type_media` enum('film','série') NOT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `added_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `genres` text,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `media_to_watch`
--

INSERT INTO `media_to_watch` (`id`, `title`, `type_media`, `image_url`, `added_date`, `genres`, `user_id`) VALUES
(4, 'The Machinist', 'film', 'https://image.tmdb.org/t/p/w500/e9lzey90JYiW9LFGEccjvyW2btA.jpg', '2025-11-24 14:52:28', NULL, 0),
(5, 'Evidence', 'film', 'https://image.tmdb.org/t/p/w500/4bloj9VOXaZwoDqkdse5aq5pVZn.jpg', '2025-11-28 20:22:51', NULL, 0),
(6, 'Ça : Bienvenue à Derry', 'série', 'https://image.tmdb.org/t/p/w500/rsc88AZaxgk8dEGK3l0FIQu8lJQ.jpg', '2025-12-09 22:37:25', NULL, 0),
(8, 'Hannibal', 'série', 'https://image.tmdb.org/t/p/w500/pbV2eLnKSIm1epSZt473UYfqaeZ.jpg', '2025-12-10 08:36:06', NULL, 0),
(9, 'Divergente', 'film', 'https://image.tmdb.org/t/p/w500/3JpyVHMYrI7C9HUFcZecnlgVsXY.jpg', '2025-12-10 08:42:31', NULL, 0),
(11, 'La Grande Vadrouille', 'film', 'https://image.tmdb.org/t/p/w500/835Yw9f0gYyR8RSsDKL34VEgCjL.jpg', '2025-12-11 08:13:47', NULL, 0),
(12, 'Mais où est donc passée la 7ème compagnie ?', 'film', 'https://image.tmdb.org/t/p/w500/eGbSIQ9HumvikBxllond5uCVXjp.jpg', '2025-12-11 08:14:29', NULL, 0),
(13, 'Bad Boys', 'film', 'https://image.tmdb.org/t/p/w500/at4ZF98aNjML2IeauN0dnbm6aJ5.jpg', '2025-12-11 08:14:41', NULL, 0),
(14, 'Bad Boys 2', 'film', 'https://image.tmdb.org/t/p/w500/vWrJVeFSPprMnhycJShZOicHAPJ.jpg', '2025-12-11 08:15:09', NULL, 0),
(15, 'Le Seigneur des anneaux : La Communauté de l&#039;anneau', 'film', 'https://image.tmdb.org/t/p/w500/5OPg6M0yHr21Ovs1fni2H1xpKuF.jpg', '2025-12-11 08:15:32', NULL, 0),
(16, 'Le Seigneur des anneaux : Les Deux Tours', 'film', 'https://image.tmdb.org/t/p/w500/qVHBqQYLDRs7ESjP9q6o9iPHLWj.jpg', '2025-12-11 08:16:24', NULL, 0),
(18, 'Bad Boys : Ride or Die', 'film', 'https://image.tmdb.org/t/p/w500/zCZJXSDPZKGml4I5zvxNpdx8jra.jpg', '2025-12-11 11:10:04', NULL, 0),
(20, 'Le Prix des aveux', 'série', 'https://image.tmdb.org/t/p/w500/iSu9YNe17GqwwzZuIjYMn92ESkr.jpg', '2025-12-11 14:36:38', NULL, 0),
(21, 'Vendredi 13', 'film', 'https://image.tmdb.org/t/p/w500/buFpnvGDjGtmucc0DkodQsPwMjZ.jpg', '2025-12-11 15:27:07', NULL, 0),
(22, 'Good Doctor', 'série', 'https://image.tmdb.org/t/p/w500/53WqEWbwQQ3WsO6cOWkzNbym43.jpg', '2025-12-27 10:58:35', NULL, 0),
(29, 'Urban Legends 3 : Bloody Mary', 'film', 'https://image.tmdb.org/t/p/w500/Avlo5JZOBXWx4oymtkXfCaomV5Y.jpg', '2026-01-08 19:21:11', NULL, 0),
(30, 'The Batman', 'film', 'https://image.tmdb.org/t/p/w500/t9JGg10CW1DzXEdWL54ewkUko6N.jpg', '2026-01-15 19:14:25', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_message` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `username` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` enum('non_lu','lu') DEFAULT 'non_lu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `role`) VALUES
(0, 'Axel1639', 'axel77dion@gmail.com', '$2y$10$4q5d20CwRDIrO8RdzXoUWevIqKd5WKWE1OlcjPwwS5TsVoDphmfzW', '2025-12-15 00:04:21', 'admin'),
(4, 'sayad', 'sayadali.madarbukus@my-did', '$2y$10$diN4J0UFU4NaxGdLswcUS.2L9iasuDnR/xvevkDgdJVEk9au1Aqey', '2025-12-15 13:42:10', 'utilisateur'),
(5, 'nico', 'test1@gmail.com', '$2y$10$tAjVS1woeOFCDkf/L4lNE.DVprHOiBz0ZiKmQIBciqTLOzwsTH5ji', '2025-12-15 13:59:41', 'utilisateur'),
(6, 'phiphi', 'test2@gmail.com', '$2y$10$cg8BgFaVCW4.9oPLOD6OwOmTNL8xM2urxNZHPFFAScQbk6.BeOaYe', '2025-12-15 17:41:59', 'utilisateur'),
(7, 'Robines', 'test3@gmail.com', '$2y$10$UuSWSGcjtvvN1PQnlCjdyOMY954hKZrCI67LikwYrAEguxeR9OWAG', '2025-12-16 10:20:52', 'utilisateur'),
(8, 'keke', 'test4@gmail.com', '$2y$10$ATxy8HGAwT1s6ItUtQ7fi..V6NVQx8HpiXZG48FLjdOBuZH3cY8Cu', '2025-12-24 22:47:48', 'utilisateur'),
(9, 'momo', 'test6@gmail.com', '$2y$10$sMGOy3hEJ5s2ErR1S0bW3.xL3TqRP35YdVIcXlvZB9I9pDVcgXLdK', '2025-12-24 23:00:34', 'utilisateur'),
(10, 'dja', 'test7@gmail.com', '$2y$10$ggiRpMrkpOtJ.r/PJPxbM.NAPANyndtH4qDel5LFDIxKvbkxjbtd2', '2025-12-31 21:59:00', 'utilisateur'),
(11, 'chacha', 'test5@gmail.com', '$2y$10$dUohqnC7kL.M4liH1qCQsOEa4PwozU/ZcIIuTiq/JyVXCAogOfDgC', '2026-01-20 13:44:49', 'utilisateur'),
(12, 'AxelTest', 'axeltest@gmail.com', '$2y$10$MP5SLrqkknZr060jlMxE..GVjP780k/yloQtLIwNMi31wCbXfkTlG', '2026-01-21 11:15:16', 'utilisateur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `media_to_watch`
--
ALTER TABLE `media_to_watch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actualite`
--
ALTER TABLE `actualite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT pour la table `media_to_watch`
--
ALTER TABLE `media_to_watch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD CONSTRAINT `actualite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_to_watch`
--
ALTER TABLE `media_to_watch`
  ADD CONSTRAINT `media_to_watch_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
