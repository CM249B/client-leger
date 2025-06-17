<?php
// admin_commande_details.php

// Démarrer la session en premier, de manière conditionnelle
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';
include 'includes/header.php';

// --- Protection d'accès pour l'admin uniquement ---
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?message=Accès non autorisé.');
    exit;
}
// --- Fin de la protection d'accès ---

$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$commande = null;
$produits_commande = [];
$message = '';

if ($commande_id > 0) {
    try {
        // Récupérer les détails de la commande et de l'utilisateur
        $stmt = $pdo->prepare("
            SELECT
                c.*,
                u.nom_utilisateur,
                u.email
            FROM
                commandes c
            JOIN
                utilisateurs u ON c.utilisateur_id = u.id
            WHERE
                c.id = ?
        ");
        $stmt->execute([$commande_id]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($commande) {
            // Récupérer les produits associés à cette commande
            $stmt_produits = $pdo->prepare("
                SELECT
                    cp.quantite,
                    cp.prix_unitaire_au_moment_achat,
                    p.nom AS nom_produit,
                    p.image AS image_produit
                FROM
                    commande_produits cp
                JOIN
                    produits p ON cp.produit_id = p.id
                WHERE
                    cp.commande_id = ?
            ");
            $stmt_produits->execute([$commande_id]);
            $produits_commande = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $message = "Commande introuvable.";
        }

    } catch (PDOException $e) {
        $message = "Erreur de base de données : " . $e->getMessage();
    }
} else {
    $message = "ID de commande non spécifié.";
}
?>

<h2>Détails de la Commande #<?php echo htmlspecialchars($commande_id); ?></h2>

<?php if ($message): ?>
    <p style="color: red;"><?php echo $message; ?></p>
    <p><a href="admin_dashboard.php">Retour au tableau de bord Admin</a></p>
<?php elseif ($commande): ?>
    <h3>Informations de la Commande :</h3>
    <ul>
        <li><strong>Date de commande :</strong> <?php echo htmlspecialchars($commande['date_commande']); ?></li>
        <li><strong>Statut :</strong> <?php echo htmlspecialchars($commande['statut']); ?></li>
        <li><strong>Client :</strong> <?php echo htmlspecialchars($commande['nom_utilisateur']); ?> (<?php echo htmlspecialchars($commande['email']); ?>)</li>
        <li><strong>Adresse de livraison :</strong><br>
            <?php echo htmlspecialchars($commande['adresse_livraison_rue']); ?><br>
            <?php echo htmlspecialchars($commande['adresse_livraison_code_postal']); ?> <?php echo htmlspecialchars($commande['adresse_livraison_ville']); ?><br>
            <?php echo htmlspecialchars($commande['adresse_livraison_pays']); ?>
        </li>
        <li><strong>Total des produits :</strong> <?php echo number_format($commande['total_produits'], 2); ?> €</li>
        <li><strong>Frais de livraison :</strong> <?php echo number_format($commande['frais_livraison'], 2); ?> €</li>
        <li><strong>Total Final :</strong> <?php echo number_format($commande['total_final'], 2); ?> €</li>
    </ul>

    <h3>Produits commandés :</h3>
    <?php if (empty($produits_commande)): ?>
        <p>Aucun produit trouvé pour cette commande.</p>
    <?php else: ?>
        <table border="1" style="width:100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="padding: 8px; text-align: left;">Produit</th>
                    <th style="padding: 8px; text-align: left;">Image</th>
                    <th style="padding: 8px; text-align: left;">Quantité</th>
                    <th style="padding: 8px; text-align: left;">Prix Unitaire à l'achat</th>
                    <th style="padding: 8px; text-align: left;">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits_commande as $prod): ?>
                    <tr>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($prod['nom_produit']); ?></td>
                        <td style="padding: 8px;">
                            <img src="<?php echo htmlspecialchars($prod['image_produit']); ?>" alt="<?php echo htmlspecialchars($prod['nom_produit']); ?>" style="width: 50px; height: auto;">
                        </td>
                        <td style="padding: 8px;"><?php echo htmlspecialchars($prod['quantite']); ?></td>
                        <td style="padding: 8px;"><?php echo number_format($prod['prix_unitaire_au_moment_achat'], 2); ?> €</td>
                        <td style="padding: 8px;"><?php echo number_format($prod['quantite'] * $prod['prix_unitaire_au_moment_achat'], 2); ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p style="margin-top: 20px;"><a href="admin_dashboard.php">Retour au tableau de bord Admin</a></p>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>