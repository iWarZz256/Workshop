<?php

session_start(); 
include "headerlogin.php";
require_once 'db.php';


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); 
    exit;
}




$currentUsername = $_SESSION['username'];

$sql = "SELECT username, nom, prenom, ecole, admin FROM compte WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $currentUsername, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Aucun utilisateur trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="Styles/profil.css"> 
</head>
<body>
    <h1>Profil de <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
    <div class="profile-info">
        <img src="images/pdf.jpg" alt="Photo de profil" class="profile-pic">
        <p><strong>Nom d'utilisateur:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
        <p><strong>Nom:</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
        <p><strong>Prénom:</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
        <p><strong>École:</strong> <?php echo htmlspecialchars($user['ecole']); ?></p>
        <p><strong>Rôle:</strong> <?php echo htmlspecialchars($user['admin'] == 1 ? 'Administrateur' : ($user['admin'] == 0 ? 'Étudiant' : 'Inconnu')); ?></p>

    </div>
</body>
</html>
