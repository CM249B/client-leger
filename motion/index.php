<?php
// index.php

// PAS DE session_start() ICI ! Il est géré par includes/header.php de manière conditionnelle.
include 'includes/db.php';
include 'includes/header.php'; // Inclut la navbar et le début du HTML, et gère session_start()

$message = '';

// Gérer les messages d'information/erreur via les paramètres GET
if (isset($_GET['redirect_to_inscription']) && $_GET['redirect_to_inscription'] == 'true') {
    $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : "Veuillez vous connecter ou créer un compte pour continuer.";
}
if (isset($_GET['inscription_reussie']) && $_GET['inscription_reussie'] == 'true') {
    $message = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
}
if (isset($_GET['login_error']) && $_GET['login_error'] == 'true') {
    $message = "Nom d'utilisateur ou mot de passe incorrect.";
}
if (isset($_GET['deconnexion_reussie']) && $_GET['deconnexion_reussie'] == 'true') {
    $message = "Vous avez été déconnecté avec succès.";
}
if (isset($_GET['message'])) { // Pour les messages génériques comme "Accès non autorisé"
    $message = htmlspecialchars($_GET['message']);
}


// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        // Rediriger vers la même page avec un message d'erreur via GET
        header('Location: index.php?message=' . urlencode("Veuillez entrer votre nom d'utilisateur et votre mot de passe."));
        exit;
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur = ?");
        $stmt->execute([$nom_utilisateur]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['utilisateur_id'] = $utilisateur['id'];
            $_SESSION['nom_utilisateur'] = $utilisateur['nom_utilisateur'];
            $_SESSION['role'] = $utilisateur['role']; // Stocke le rôle dans la session

            // Vérifier s'il y avait un produit en attente d'ajout au panier après redirection
            if (isset($_SESSION['produit_en_attente'])) {
                $produit_id_attente = (int)$_SESSION['produit_en_attente'];
                // Assure-toi que $_SESSION['panier'] est initialisé
                if (!isset($_SESSION['panier'])) {
                    $_SESSION['panier'] = [];
                }
                $_SESSION['panier'][$produit_id_attente] = ($_SESSION['panier'][$produit_id_attente] ?? 0) + 1;
                unset($_SESSION['produit_en_attente']); // Nettoyer l'ID après l'ajout

                header('Location: panier.php'); // Rediriger vers le panier avec le produit ajouté
                exit;
            } else {
                // Pas de produit en attente, rediriger vers l'accueil post-connexion (ici, index.php lui-même)
                header('Location: index.php');
                exit;
            }

        } else {
            // Échec de la connexion
            // Rediriger pour afficher le message d'erreur via GET (plus propre)
            header('Location: index.php?login_error=true');
            exit;
        }
    }
}

// Traitement de la déconnexion
if (isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
    session_destroy();
    // Rediriger après déconnexion pour nettoyer la session et l'URL
    header('Location: index.php?deconnexion_reussie=true');
    exit;
}
?>

<?php if (isset($_SESSION['utilisateur_id'])): ?>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?> !</h1>
    <p>Vous êtes connecté en tant que : <?php echo htmlspecialchars($_SESSION['role']); ?></p>

    <?php if ($message): // Afficher des messages même après connexion si l'URL le contient ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($_SESSION['role'] === 'admin'): ?>
        <p>Options Administrateur :</p>
        <ul>
            <li><a href="admin_dashboard.php">Tableau de bord Admin</a></li>
            </ul>
    <?php endif; ?>

    <p>Options Générales :</p>
    <ul>
        <li><a href="catalogue.php">Voir les coques</a></li>
        <li><a href="panier.php">Votre panier</a></li>
        <li><a href="index.php?action=deconnexion">Se déconnecter</a></li>
    </ul>

<?php else: ?>
    <h1>Connexion à Motion</h1>
    <p>Découvrez notre collection de coques de qualité !</p>

    <?php if ($message): ?>
        <p style="color: red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="index.php" method="post">
        <label for="nom_utilisateur">Nom d'utilisateur :</label><br>
        <input type="text" id="nom_utilisateur" name="nom_utilisateur" required><br><br>

        <label for="mot_de_passe">Mot de passe :</label><br>
        <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>

        <button type="submit" name="login_submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="inscription.php">Créez-en un ici</a>.</p>

<?php endif; ?>

<?php include 'includes/footer.php'; // Inclut le pied de page et ferme le HTML ?>