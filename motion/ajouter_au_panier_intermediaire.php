<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['produit_id'])) {
    header('Location: catalogue.php'); // Redirige si pas d'ID de produit
    exit;
}

$produit_id = (int)$_GET['produit_id'];

// Stocker l'ID du produit que l'utilisateur voulait ajouter, pour après la connexion/inscription
$_SESSION['produit_en_attente'] = $produit_id;

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['utilisateur_id'])) {
    // L'utilisateur est connecté, ajouter directement le produit au panier
    // Assure-toi que $_SESSION['panier'] existe et est bien initialisé pour cet utilisateur
    // Si tu veux un panier persistant en DB, ce serait l'endroit pour le charger/mettre à jour
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    $_SESSION['panier'][$produit_id] = ($_SESSION['panier'][$produit_id] ?? 0) + 1;

    // Nettoyer la variable de session du produit en attente
    unset($_SESSION['produit_en_attente']);

    header('Location: panier.php'); // Rediriger vers le panier
    exit;
} else {
    // L'utilisateur n'est pas connecté, le rediriger vers la page d'inscription/connexion
    // avec un message pour l'inciter à se connecter/s'inscrire
    header('Location: index.php?redirect_to_inscription=true&message=Veuillez vous connecter ou créer un compte pour ajouter ce produit au panier.');
    exit;
}
?>