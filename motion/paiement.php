<?php
// paiement.php

// Démarrer la session en premier, de manière conditionnelle
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php'; // Inclure la connexion à la base de données

// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php?redirect_to_inscription=true&message=Veuillez vous connecter pour procéder au paiement.');
    exit;
}

// Vérifier si le panier est vide
if (empty($_SESSION['panier'])) {
    header('Location: panier.php?message=Votre panier est vide. Impossible de procéder au paiement.');
    exit;
}

$message = ''; // Message général (succès ou erreur)
$type_message = ''; // 'success' ou 'error'

$frais_livraison = 5.00; // Définir les frais de livraison (exemple)
$total_produits = 0;
$produits_dans_panier = [];

// Calculer le total des produits et récupérer leurs détails
foreach ($_SESSION['panier'] as $produit_id => $quantite) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$produit_id]);
    $produit = $stmt->fetch();

    if ($produit) {
        $produit['quantite_panier'] = $quantite;
        $produit['sous_total'] = $quantite * $produit['prix'];
        $total_produits += $produit['sous_total'];
        $produits_dans_panier[] = $produit; // Stocke les produits avec leur quantité panier et sous-total
    } else {
        // Optionnel: Gérer le cas où un produit n'existe plus (ex: le retirer du panier)
        unset($_SESSION['panier'][$produit_id]);
    }
}

$total_final = $total_produits + $frais_livraison;

// Traitement du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_paiement'])) {
    $adresse_rue = trim($_POST['adresse_rue']);
    $adresse_ville = trim($_POST['adresse_ville']);
    $adresse_code_postal = trim($_POST['adresse_code_postal']);
    $adresse_pays = trim($_POST['adresse_pays']);

    if (empty($adresse_rue) || empty($adresse_ville) || empty($adresse_code_postal) || empty($adresse_pays)) {
        $message = "Veuillez remplir tous les champs de l'adresse de livraison.";
        $type_message = 'error';
    } else {
        try {
            $pdo->beginTransaction(); // Démarre une transaction pour s'assurer que tout est enregistré ou rien

            // 1. Insérer la commande principale
            $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total_produits, frais_livraison, total_final, adresse_livraison_rue, adresse_livraison_ville, adresse_livraison_code_postal, adresse_livraison_pays, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'validée')");
            $stmt->execute([
                $_SESSION['utilisateur_id'],
                $total_produits,
                $frais_livraison,
                $total_final,
                $adresse_rue,
                $adresse_ville,
                $adresse_code_postal,
                $adresse_pays
            ]);
            $commande_id = $pdo->lastInsertId(); // Récupère l'ID de la commande insérée

            // 2. Insérer les produits de la commande dans la table commande_produits
            $stmt_produits = $pdo->prepare("INSERT INTO commande_produits (commande_id, produit_id, quantite, prix_unitaire_au_moment_achat) VALUES (?, ?, ?, ?)");
            foreach ($produits_dans_panier as $prod) {
                $stmt_produits->execute([
                    $commande_id,
                    $prod['id'],
                    $prod['quantite_panier'],
                    $prod['prix'] // On enregistre le prix actuel du produit
                ]);
            }

            $pdo->commit(); // Valide la transaction

            // Vider le panier après une commande réussie
            $_SESSION['panier'] = [];
            $message = "Votre commande #{$commande_id} a été passée avec succès !";
            $type_message = 'success';

            // IMPORTANT: Rediriger vers la même page pour vider le POST et afficher le message.
            // Sinon, si l'utilisateur rafraîchit, il pourrait soumettre la commande à nouveau.
            // On peut passer le message et le type via les paramètres GET si on veut qu'il persiste après rafraîchissement.
            header('Location: paiement.php?status=success&message=' . urlencode($message));
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack(); // Annule la transaction en cas d'erreur
            $message = "Une erreur est survenue lors de la validation de votre commande. Veuillez réessayer. Erreur: " . $e->getMessage();
            $type_message = 'error';
            // En production, vous logueriez $e->getMessage() mais n'afficheriez pas les détails à l'utilisateur.
        }
    }
}

// Récupérer le message et le type s'ils proviennent d'une redirection GET
if (isset($_GET['status']) && isset($_GET['message'])) {
    $type_message = htmlspecialchars($_GET['status']);
    $message = htmlspecialchars($_GET['message']);
}


include 'includes/header.php'; // Inclut la navbar et le début du HTML
?>

<h2>Paiement de la commande</h2>

<?php if ($message): ?>
    <p style="color: <?php echo ($type_message === 'success' ? 'green' : 'red'); ?>; font-weight: bold;">
        <?php echo $message; ?>
    </p>
<?php endif; ?>

<?php if (empty($_SESSION['panier'])): // Si le panier a été vidé (commande réussie) ou était vide au départ ?>
    <p>Votre panier est maintenant vide. <a href="catalogue.php">Continuez vos achats</a>.</p>
<?php else: // Sinon, afficher le récapitulatif et le formulaire ?>

    <h3>Récapitulatif de votre commande :</h3>
    <table border="1" style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="padding: 8px; text-align: left;">Produit</th>
                <th style="padding: 8px; text-align: left;">Quantité</th>
                <th style="padding: 8px; text-align: left;">Prix Unitaire</th>
                <th style="padding: 8px; text-align: left;">Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits_dans_panier as $prod): ?>
                <tr>
                    <td style="padding: 8px;"><?php echo htmlspecialchars($prod['nom']); ?></td>
                    <td style="padding: 8px;"><?php echo $prod['quantite_panier']; ?></td>
                    <td style="padding: 8px;"><?php echo number_format($prod['prix'], 2); ?> €</td>
                    <td style="padding: 8px;"><?php echo number_format($prod['sous_total'], 2); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold;">Total des produits :</td>
                <td style="padding: 8px; font-weight: bold;"><?php echo number_format($total_produits, 2); ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold;">Frais de livraison :</td>
                <td style="padding: 8px; font-weight: bold;"><?php echo number_format($frais_livraison, 2); ?> €</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 8px; text-align: right; font-weight: bold; font-size: 1.2em;">Total Final :</td>
                <td style="padding: 8px; font-weight: bold; font-size: 1.2em; color: #007bff;"><?php echo number_format($total_final, 2); ?> €</td>
            </tr>
        </tfoot>
    </table>

    <h3>Informations de livraison :</h3>
    <form action="paiement.php" method="post">
        <label for="adresse_rue">Rue et numéro :</label><br>
        <input type="text" id="adresse_rue" name="adresse_rue" required value="<?php echo htmlspecialchars($_POST['adresse_rue'] ?? ''); ?>"><br><br>

        <label for="adresse_code_postal">Code Postal :</label><br>
        <input type="text" id="adresse_code_postal" name="adresse_code_postal" required value="<?php echo htmlspecialchars($_POST['adresse_code_postal'] ?? ''); ?>"><br><br>

        <label for="adresse_ville">Ville :</label><br>
        <input type="text" id="adresse_ville" name="adresse_ville" required value="<?php echo htmlspecialchars($_POST['adresse_ville'] ?? ''); ?>"><br><br>

        <label for="adresse_pays">Pays :</label><br>
        <input type="text" id="adresse_pays" name="adresse_pays" required value="<?php echo htmlspecialchars($_POST['adresse_pays'] ?? 'France'); ?>"><br><br>

        <button type="submit" name="submit_paiement">Confirmer et Payer (<?php echo number_format($total_final, 2); ?> €)</button>
    </form>

<?php endif; ?>

<?php include 'includes/footer.php'; // Inclut le pied de page et ferme le HTML ?>
