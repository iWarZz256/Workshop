<?php 
require_once 'db.php'; // Assurez-vous d'inclure votre fichier de connexion à la base de données
include 'veriflogin.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Requête pour récupérer le nombre de tickets en statut 0
$ticket_count = 0;
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM probleme WHERE statut = 0");
    $stmt->execute();
    $ticket_count = $stmt->fetchColumn(); 
}

// Requête pour récupérer le nombre de réservations en statut 0
$reservation_count = 0;
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE statut is NULL");
    $stmt->execute();
    $reservation_count = $stmt->fetchColumn(); 
}
?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon site</title>
    <link rel="stylesheet" type="text/css" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


<div class="navbar" style="text-align: center;margin-top: 20px;" id="navbar">
    <a class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="/Workshop/index.php">Accueil</a>
    <a class="<?php echo ($current_page == 'mapsRDC.php' || $current_page == 'detailsalle.php') ? 'active' : ''; ?>" href="/Workshop/maps/mapsRDC.php">Maps</a>
    <a class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="/Workshop/contact.php">Contact</a>
    
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <a class="<?php echo $current_page == 'profil.php' ? 'active' : ''; ?>" href="/Workshop/profil.php">Profil</a>
        
        <?php if ($_SESSION['admin'] == 1): ?>
            <a class="<?php echo $current_page == 'panneladmin.php' ? 'active' : ''; ?>" href="/Workshop/panneladmin.php">Panel admin 
                <?php if ($ticket_count > 0 ||$reservation_count > 0): ?>
                    <i class="fa-solid fa-circle-exclamation" style="color:red"></i>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        
        <a class="<?php echo $current_page == 'logout.php' ? 'active' : ''; ?>" href="/Workshop/logout.php">Se déconnecter</a>
    <?php else: ?>
        <a class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>" href="login.php">Se connecter</a>
    <?php endif; ?>
</div>

<script src="JS/modal.js"></script>
<script src="JS/script.js"></script>
</body>
</html>
