<?php
// inscription.php

 // Doit être la première ligne exécutable
include 'includes/db.php';
include 'includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'];

    if (empty($nom_utilisateur) || empty($email) || empty($mot_de_passe) || empty($confirm_mot_de_passe)) {
        $message = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format d'email invalide.";
    } elseif ($mot_de_passe !== $confirm_mot_de_passe) {
        $message = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($mot_de_passe) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Vérifier si le nom d'utilisateur ou l'email existe déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE nom_utilisateur = ? OR email = ?");
        $stmt->execute([$nom_utilisateur, $email]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Ce nom d'utilisateur ou cet email est déjà utilisé.";
        } else {
            // Hacher le mot de passe avant de l'enregistrer
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom_utilisateur, email, mot_de_passe, role) VALUES (?, ?, ?, 'client')");
            if ($stmt->execute([$nom_utilisateur, $email, $mot_de_passe_hache])) {
                // Inscription réussie, connecter l'utilisateur immédiatement
                $_SESSION['utilisateur_id'] = $pdo->lastInsertId(); // Récupérer l'ID du nouvel utilisateur
                $_SESSION['nom_utilisateur'] = $nom_utilisateur;
                $_SESSION['role'] = 'client';

                // Vérifier s'il y avait un produit en attente d'ajout au panier
                if (isset($_SESSION['produit_en_attente'])) {
                    $produit_id_attente = (int)$_SESSION['produit_en_attente'];
                    if (!isset($_SESSION['panier'])) {
                        $_SESSION['panier'] = [];
                    }
                    $_SESSION['panier'][$produit_id_attente] = ($_SESSION['panier'][$produit_id_attente] ?? 0) + 1;
                    unset($_SESSION['produit_en_attente']); // Nettoyer l'ID après l'ajout

                    header('Location: panier.php'); // Rediriger vers le panier
                    exit;
                } else {
                    // Si pas de produit en attente, rediriger vers l'index avec un message de succès
                    header('Location: index.php?inscription_reussie=true');
                    exit;
                }

            } else {
                $message = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>

<h2>Inscription</h2>

<?php if ($message): ?>
    <p style="color: red;"><?php echo $message; ?></p>
<?php endif; ?>

<form action="inscription.php" method="post">
    <label for="nom_utilisateur">Nom d'utilisateur :</label><br>
    <input type="text" id="nom_utilisateur" name="nom_utilisateur" required><br><br>

    <label for="email">Email :</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="mot_de_passe">Mot de passe :</label><br>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

    <label for="confirm_mot_de_passe">Confirmer le mot de passe :</label><br>
    <input type="password" id="confirm_mot_de_passe" name="confirm_mot_de_passe" required><br><br>

    <button type="submit">S'inscrire</button>
</form>

<p>Déjà un compte ? <a href="index.php">Connectez-vous ici</a>.</p>

<?php include 'includes/footer.php'; ?>