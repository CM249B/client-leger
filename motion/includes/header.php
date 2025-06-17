<?php
// includes/header.php

// Démarrer la session uniquement si elle n'est pas déjà active
// Cette ligne doit être la toute première instruction PHP exécutée dans ce fichier.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motion - Votre site de coques</title>
    <style>
        /* Styles de base pour la lisibilité */
        body {
            font-family: Arial, sans-serif;
            margin: 0; /* Enlève la marge par défaut du body pour la navbar */
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 960px;
            margin: 20px auto; /* Marge au-dessus et en dessous du contenu principal */
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #007bff;
        }
        p {
            line-height: 1.6;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
        }
        form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        form button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        form button:hover {
            background-color: #0056b3;
        }
        hr {
            border-top: 1px solid #eee;
            margin: 20px 0;
        }

        /* Styles pour la Navbar */
        .navbar {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .navbar .brand {
            font-size: 1.8em;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar .nav-links {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar .nav-links li {
            margin-left: 20px;
        }

        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .navbar .nav-links a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="catalogue.php" class="brand">MOTION</a>
        <ul class="nav-links">
            <li><a href="catalogue.php">Catalogue</a></li>
            <li><a href="panier.php">Panier</a></li>
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <li><span style="color: white; padding: 8px 12px;">Bonjour, <?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?></span></li>
                <li><a href="index.php?action=deconnexion">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="index.php">Connexion</a></li>
                <li><a href="inscription.php">Inscription</a></li>
            <?php endif; ?>
            <li><a href="contact.php">Contact</a></li> </ul>
    </nav>
    <div class="container">