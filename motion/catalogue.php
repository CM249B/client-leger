<?php
include 'includes/db.php';
include 'includes/header.php';

echo "<h2>Catalogue des coques</h2>";

$stmt = $pdo->query("SELECT * FROM produits");

echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

while ($prod = $stmt->fetch()) {
    echo "<div style='border:1px solid #ccc; padding:10px; width:200px;'>
        <img src='{$prod['image']}' alt='{$prod['nom']}' style='width:100%; height:auto;'>
        <h3>{$prod['nom']}</h3>
        <p>{$prod['prix']} €</p>
        <a href='produit.php?id={$prod['id']}'>Voir le produit</a>
    </div>";
}

echo "</div>";

include 'includes/footer.php';
?>
