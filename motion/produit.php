<?php
 // Assure-toi que la session est démarrée ici aussi
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Produit non spécifié.</p>";
    include 'includes/footer.php';
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$prod = $stmt->fetch();

if (!$prod) {
    echo "<p>Produit introuvable.</p>";
    include 'includes/footer.php';
    exit;
}

echo "<h2>{$prod['nom']}</h2>";
echo "<img src='{$prod['image']}' alt='{$prod['nom']}' style='max-width:300px;'>";
echo "<p><strong>Prix :</strong> {$prod['prix']} €</p>";
// Assure-toi que ces colonnes existent dans ta DB après les modifications précédentes
echo "<p><strong>Modèle :</strong> " . (isset($prod['modele']) ? htmlspecialchars($prod['modele']) : 'N/A') . "</p>";
echo "<p><strong>Couleur :</strong> " . (isset($prod['couleur']) ? htmlspecialchars($prod['couleur']) : 'N/A') . "</p>";
echo "<p>{$prod['description']}</p>";

// Modification ici : Le lien pour ajouter au panier
echo "<a href='ajouter_au_panier_intermediaire.php?produit_id={$prod['id']}'>Ajouter au panier</a>";

include 'includes/footer.php';
?>