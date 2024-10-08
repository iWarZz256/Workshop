<?php

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $ecole = $_POST['ecole'];

    // Vérifier si le nom d'utilisateur existe déjà
    $checkUsernameSQL = "SELECT COUNT(*) FROM compte WHERE username = :username";
    $stmt = $pdo->prepare($checkUsernameSQL);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        $var = "Nom d'utilisateur déjà pris.";
    } else {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO compte (username, password, nom, prenom, ecole) VALUES (:username, :password, :nom, :prenom, :ecole)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':ecole', $ecole, PDO::PARAM_STR);
            $stmt->execute();
            echo "Inscription réussie !";
            header('Location: login.php'); 
            exit;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="Styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class='inscription'>
<img src="images/campus.png" class="campus" id='inscrit'>
    <div class="container">
        <form action="inscription.php" method="post">
        <h3>
            <a href="index.php" style="text-decoration: none; color: black; font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-arrow-left"></i> 
            </a>
        </h3>
        <h2>Créer un compte</h2>
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <select name="ecole" required>
                <option value="" disabled selected>Choisir une école</option>
                <option value="EPSI">EPSI</option>
                <option value="WIS">WIS</option>
            </select>
            <button type="submit">S'inscrire</button>
            <a href="login.php">Se connecter</a>
        </form>
    </div>
</body>
</html>
