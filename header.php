<?php $current_page = basename($_SERVER['PHP_SELF']);?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon site</title>
    <link rel="stylesheet" type="text/css" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
	
<h1>Workshop</h1>

<div class="navbar" style="text-align: center;">
    <a class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Accueil</a>        
    <a class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="contact.php">Contact</a>
    <a class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>" href="login.php">Se connecter</a>
</div>

<script src="JS/modal.js"></script>
<script src="JS/script.js"></script>
</body>
</html>