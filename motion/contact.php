<?php
// contact.php

// Le session_start() n'est pas nécessaire sur cette page si header.php
// le gère de manière conditionnelle et qu'il n'y a pas de logique de session
// spécifique AVANT l'inclusion du header.
// Si tu suis la dernière recommandation, tu ne devrais PAS avoir session_start() ici.
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

include 'includes/db.php';      // Inclure la connexion à la base de données si nécessaire
include 'includes/header.php';   // Inclure l'en-tête (qui gère session_start())

// Vous pouvez ajouter une logique de traitement de formulaire ici si vous voulez
// que les utilisateurs envoient des messages via un formulaire.
// Pour l'instant, c'est une page d'information statique.

?>

<h2>Contactez-nous</h2>

<p>
    Nous sommes là pour répondre à toutes vos questions et vous aider !<br>
    N'hésitez pas à nous contacter par les moyens suivants :
</p>

<h3>Informations de contact :</h3>
<ul>
    <li><strong>Email :</strong> <a href="mailto:contact@motion.com">contact@motion.com</a></li>
    <li><strong>Téléphone :</strong> <a href="tel:+33123456789">01 23 45 67 89</a></li>
</ul>

<h3>Horaires de Contact :</h3>
<p>
    Du lundi au vendredi : 9h00 - 18h00<br>
    Samedi : 10h00 - 14h00<br>
    Dimanche : Fermé
</p>

<?php include 'includes/footer.php'; // Inclure le pied de page ?>