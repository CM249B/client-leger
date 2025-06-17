<?php
// admin_dashboard.php

// Démarrer la session en premier, de manière conditionnelle
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php'; // Inclure la connexion à la base de données
include 'includes/header.php'; // Inclure l'en-tête (qui gère session_start())

// --- Protection d'accès pour l'admin uniquement ---
if (!isset($_SESSION['utilisateur_id']) || $_SESSION['role'] !== 'admin') {
    // Si l'utilisateur n'est pas connecté OU n'est pas un admin, rediriger
    header('Location: index.php?message=Accès non autorisé.');
    exit;
}
// --- Fin de la protection d'accès ---

$message = '';
$nb_clients = 0;
$nb_produits = 0;
$commandes = [];

try {
    // 1. Compter le nombre de clients
    $stmt = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'client'");
    $nb_clients = $stmt->fetchColumn();

    // 2. Compter le nombre de produits
    $stmt = $pdo->query("SELECT COUNT(*) FROM produits");
    $nb_produits = $stmt->fetchColumn();

    // 3. Récupérer toutes les commandes avec les infos utilisateur
    $stmt = $pdo->prepare("
        SELECT
            c.id AS commande_id,
            c.date_commande,
            c.statut,
            c.total_final,
            u.nom_utilisateur,
            u.email,
            c.adresse_livraison_ville,
            c.adresse_livraison_code_postal
        FROM
            commandes c
        JOIN
            utilisateurs u ON c.utilisateur_id = u.id
        ORDER BY
            c.date_commande DESC
    ");
    $stmt->execute();
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Erreur de base de données : " . $e->getMessage();
    // En production, vous logueriez l'erreur complète mais n'afficheriez pas les détails à l'utilisateur.
}
?>

<h2>Tableau de bord Administrateur</h2>

<?php if ($message): ?>
    <p style="color: red;"><?php echo $message; ?></p>
<?php endif; ?>

<h3>Statistiques Rapides :</h3>
<ul>
    <li>Nombre total de clients : <strong><?php echo $nb_clients; ?></strong></li>
    <li>Nombre total de produits : <strong><?php echo $nb_produits; ?></strong></li>
</ul>

<h3>Toutes les commandes :</h3>

<?php if (empty($commandes)): ?>
    <p>Aucune commande n'a été passée pour le moment.</p>
<?php else: ?>
    <table border="1" style="width:100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr>
                <th style="padding: 8px; text-align: left;">ID Commande</th>
                <th style="padding: 8px; text-align: left;">Date</th>
                <th style="padding: 8px; text-align: left;">Client</th>
                <th style="padding: 8px; text-align: left;">Email Client</th>
                <th style="padding: 8px; text-align: left;">Ville</th>
                <th style="padding: 8px; text-align: left;">Code Postal</th>
                <th style="padding: 8px; text-align: left;">Total Final</th>
                <th style="padding: 8px; text-align: left;">Statut</th>
                <th style="padding: 8px; text-align: left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande): ?>
                <tr>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['commande_id']); ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['date_commande']); ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['nom_utilisateur']); ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['email']); ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['adresse_livraison_ville']); ?></td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['adresse_livraison_code_postal']); ?></td>
                    <td style="padding: 8px;"><?php echo number_format($commande['total_final'], 2); ?> €</td>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($commande['statut']); ?></td>
                    <td style="padding: 8px;">
                        <a href="admin_commande_details.php?id=<?php echo $commande['commande_id']; ?>">Détails</a>
                        </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include 'includes/footer.php'; // Inclut le pied de page et ferme le HTML ?>