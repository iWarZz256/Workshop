<?php
session_start();
require_once 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $sql = "SELECT * FROM compte WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['admin'] = $user['admin']; 
        header('Location: index.php');
        exit;
    } else {
        $error = 'Identifiants incorrects';
    }
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="Styles/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div>    
<div><img src="images/campus.png" class="campus"></div>
    <div class="container">
    
        <form action="login.php" method="post">
        <h3><a href="index.php" style="text-decoration: none; color: black; font-size: 20px; margin-bottom: 20px;">
            <i class="fas fa-arrow-left"></i></a></h3><h2>Se connecter</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Connexion</button>
            <a href="inscription.php"> S'inscrire</a>
        </form>

       
        
        <footer></footer>
    </div>
        </div>
</body>
</html>