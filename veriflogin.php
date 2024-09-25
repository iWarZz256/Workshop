<?php


// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: /Workshop/login.php');
    exit; // Arrête l'exécution du script après la redirection
}
?>