-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 juin 2025 à 00:42
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
-- Base de données : `motion`
--

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `statut` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'en_attente',
  `total_produits` decimal(10,2) NOT NULL,
  `frais_livraison` decimal(10,2) NOT NULL,
  `total_final` decimal(10,2) NOT NULL,
  `adresse_livraison_rue` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_livraison_ville` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_livraison_code_postal` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse_livraison_pays` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'France',
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `utilisateur_id`, `date_commande`, `statut`, `total_produits`, `frais_livraison`, `total_final`, `adresse_livraison_rue`, `adresse_livraison_ville`, `adresse_livraison_code_postal`, `adresse_livraison_pays`) VALUES
(1, 3, '2025-06-17 02:10:18', 'validée', 64.00, 5.00, 69.00, '24 sente des cuverons', 'Bagneux', '92220', 'France'),
(2, 3, '2025-06-17 02:12:02', 'validée', 20.00, 5.00, 25.00, '24 sente des cuverons', 'Bagneux', '92220', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `commande_produits`
--

DROP TABLE IF EXISTS `commande_produits`;
CREATE TABLE IF NOT EXISTS `commande_produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `produit_id` int NOT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire_au_moment_achat` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`),
  KEY `produit_id` (`produit_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commande_produits`
--

INSERT INTO `commande_produits` (`id`, `commande_id`, `produit_id`, `quantite`, `prix_unitaire_au_moment_achat`) VALUES
(1, 1, 4, 1, 50.00),
(2, 1, 2, 1, 14.00),
(3, 2, 1, 1, 20.00);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

DROP TABLE IF EXISTS `produits`;
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `prix` decimal(6,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `modele` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `couleur` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `image`, `modele`, `couleur`) VALUES
(1, 'Coque Monkey D. Luffy One Piece Iphone 16', 'Coque resistant a la chute, au rayure et tous autres imprevus du quotidien. impression HD et ayant des boutons receptifs.', 20.00, 'https://encrypted-tbn3.gstatic.com/shopping?q=tbn:ANd9GcT19DjinvoONmG-Mlr5IjfApjwiai5v3yPeEtYoTzpq1jvxgg8PbnNnAOSZ_QYeM4RXmbP1ngdUFx0XtyuEdi04L6ncai7yBh-2r6i8my9czYdj2GEkgozyXNY&usqp=CAc', 'Iphone 16', 'blanc'),
(2, 'Coque Naruto Sasuke Akatsuki', 'Découvrez l’excitation et l’adrénaline du puissant monde des Shinobi avec notre Coque Naruto Sasuke Akatsuki. Cette coque éblouissante vous immerge directement dans l’intensité du célèbre manga Naruto.\r\n\r\nElle représente l’iconique Sasuke en mode Akatsuki, reflétant puissance et détermination.', 14.00, 'https://www.laboutiquenaruto.fr/wp-content/uploads/2024/08/sef4ff52ea2744e7588d97c0095162eddc-11.webp', 'Iphone 16', 'noir'),
(3, 'Coque Xiaomi Redmi Note 14 4G Silicone\r\n', 'Découvrez notre coque protectrice de haute qualité pour Xiaomi Redmi Note 14 4G. Fabriquée en TPU résistant de 2,0 mm d\'épaisseur, cette coque offre une protection optimale contre les chocs et les rayures. Son design ergonomique avec des bords arrondis assure une prise en main confortable, tandis que ses rebords surélevés protègent l\'écran et la caméra.', 14.00, 'https://www.macoque.com/3423351-large_default/coque-xiaomi-redmi-note-14-4g-silicone.jpg', 'Xiaomi Redmi Note 14', 'noir'),
(4, 'Coque en silicone avec lanière pour Samsung Galaxy S24 Ultra Taupe', 'Une protection optimale avec lanière adaptée pour une prise en main confortable et pratique !\r\n\r\nHabillez élégamment votre appareil\r\nLa finition fine et souple de cette coque en silicone protège votre appareil tout en offrant une prise en main confortable et un toucher plaisant.\r\n\r\nParez votre smartphone d\'une protection optimale contre les chocs et rayures\r\nLa coque en silicone Samsung est dotée d\'un design fin permettant de glisser facilement votre appareil dans votre poche. Robuste, elle offre une protection optimale tout en épousant parfaitement les courbes de votre appareil.\r\n\r\nUne lanière pour une prise en main confortable et pratique\r\nLa coque est équipée d\'une lanière qui vous permet de sécuriser votre appareil sur vos doigts et de le maintenir pour faire des selfie ou écrire un message.\r\n\r\nUne touche de couleur\r\nDisponible dans plusieurs coloris, assortissez votre style à celui de votre mobile.', 50.00, 'https://static.fnac-static.com/multimedia/Images/FR/MDM/ef/11/5a/22680047/1540-1/tsp20250615091904/Coque-en-silicone-avec-laniere-pour-Samsung-Galaxy-S24-Ultra-Taupe.jpg', 'Samsung Galaxy S24 Ultra', 'blanc cassé'),
(5, 'Coque Vieille vague japonaise kanagawa pixel pro 8', 'Le design vieille vague japonaise kanagawa est un accessoire incontournable pour tous les amateurs de culture japonaise. Avec son design inspiré de la célèbre vague bleue de Hokusai, cette coque de téléphone apportera une touche d\'élégance à votre téléphone. Le fond noir met en valeur les détails de la chute d\'eau, créant un contraste saisissant.\r\n\r\nFabriquée avec la technologie Motion, cette coque est conçue pour absorber les chocs et protéger votre téléphone des rayures. Les coins renforcés assurent une protection supplémentaire pour les zones les plus vulnérables de votre appareil.', 15.00, 'https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcSCiB7ybePiVf8J6Y36-e634Q_kjr5AtHJrFnEG6j7tgA3iRJqQ8P-V7BPUsncbgaQl3KyYBCq93WshpXY3pYjG3uqv4NNsIyvftcyTSzw3TQ2rsUaKWu4iFEDuC3bS49Q5qY3QQ3k&usqp=CAc', 'google pixel 8 pro', 'blanc');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'client',
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`),
  UNIQUE KEY `email` (`email`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom_utilisateur`, `email`, `mot_de_passe`, `role`, `date_inscription`) VALUES
(1, 'admin', 'admin@motion.com', '$2y$10$tJ9S2x.kQ5W3B4Z1N7D8A2u5D.f3c4d5e6f7g8h9i0j1k2l3m4n5o6p7q8r9s0t1u2', 'admin', '2025-06-17 01:30:29'),
(2, 'client1', 'client1@gmail.com', '$2y$10$U2V3W4X5Y6Z7A8B9C0D1E2F3G4H5I6J7K8L9M0N1O2P3Q4R5S6T7U8V9W0X1Y2Z3', 'client', '2025-06-17 01:30:29'),
(3, 'cheguevara', 'nyaleve.che@gmail.com', '$2y$10$TrFWXa98mO7atz.rTJqXuOJxYXcn/rV.eTW2ImNDtJv/WxcZKP1hq', 'admin', '2025-06-17 01:37:35');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
