<?php
// panier.php
// Doit être la première ligne exécutable pour gérer les sessions
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur_id'])) {
    // Si l'utilisateur est redirigé ici après avoir essayé d'ajouter un produit sans être connecté
    // ou s'il tente d'accéder directement au panier, on le renvoie à la page de connexion.
    header('Location: index.php?redirect_to_inscription=true&message=Veuillez vous connecter pour accéder à votre panier.');
    exit;
}

// Initialisation du panier si ce n'est pas déjà fait
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// *** La logique d'ajout direct via ?ajout= est maintenant gérée par ajouter_au_panier_intermediaire.php ***
// Par conséquent, les blocs 'if (isset($_GET['ajout']))' ne sont plus nécessaires ici
// car tous les ajouts passent par le script intermédiaire.

// Logique pour supprimer un produit du panier
if (isset($_GET['supprimer'])) {
    $id_a_supprimer = (int)$_GET['supprimer'];
    if (isset($_SESSION['panier'][$id_a_supprimer])) {
        unset($_SESSION['panier'][$id_a_supprimer]);
    }
    // Après la suppression, rediriger pour nettoyer l'URL
    header('Location: panier.php');
    exit;
}

include 'includes/header.php'; // Inclut la navbar et le début du HTML

echo "<h2>Votre panier</h2>";

if (empty($_SESSION['panier'])) {
    echo "<p>Votre panier est vide.</p>";
    echo "<p><a href='catalogue.php'>Continuer mes achats</a></p>";
} else {
    $total_general = 0;
    ?>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="padding: 8px; text-align: left;">Produit</th>
                <th style="padding: 8px; text-align: left;">Quantité</th>
                <th style="padding: 8px; text-align: left;">Prix Unitaire</th>
                <th style="padding: 8px; text-align: left;">Total</th>
                <th style="padding: 8px; text-align: left;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($_SESSION['panier'] as $produit_id => $quantite) {
            $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
            $stmt->execute([$produit_id]);
            $produit = $stmt->fetch();

            if ($produit) {
                $sous_total_produit = $quantite * $produit['prix'];
                $total_general += $sous_total_produit;
                ?>
                <tr>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td style="padding: 8px;"><?php echo $quantite; ?></td>
                    <td style="padding: 8px;"><?php echo number_format($produit['prix'], 2); ?> €</td>
                    <td style="padding: 8px;"><?php echo number_format($sous_total_produit, 2); ?> €</td>
                    <td style="padding: 8px;">
                        <a href='panier.php?supprimer=<?php echo $produit_id; ?>'>Supprimer</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold;">Total Général :</td>
                <td style="padding: 8px; font-weight: bold;"><?php echo number_format($total_general, 2); ?> €</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <p style="text-align: right; margin-top: 20px;">
        <a href="catalogue.php">Continuer mes achats</a> |
        <a href="paiement.php">Procéder au paiement</a>
    </p>
    <?php
}

include 'includes/footer.php'; // Inclut le pied de page et ferme le HTML
?>