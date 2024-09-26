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

// Requête pour récupérer le nombre de réservations en statut NULL
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
    <title>SmartCampus</title>
    <link rel="stylesheet" type="text/css" href="/Workshop/Styles/notif.css">
    <link rel="stylesheet" type="text/css" href="/Workshop/Styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Ajout de style pour le bouton et la tooltip */
        .tooltip {
            display: none; /* Caché par défaut */
            position: absolute;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            z-index: 1;
        }
    </style>
</head>
<body>
<div style="display: flex;">
    <img src="/Workshop/images/epsi.png" style="width:100px;margin-top:-19px">
    <div class="navbar" id="navbar">
        
    <a class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="/Workshop/index.php">Accueil</a>
    <a class="<?php echo ($current_page == 'mapsRDC.php' || $current_page == 'detailsalle.php') ? 'active' : ''; ?>" href="/Workshop/maps/mapsRDC.php">Maps</a>
    <a class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="/Workshop/contact.php">Contact</a>
    
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <a class="<?php echo $current_page == 'profil.php' ? 'active' : ''; ?>" href="/Workshop/profil.php">Profil</a>
        
        <?php if ($_SESSION['admin'] == 1): ?>
            <a class="<?php echo ($current_page == 'panneladmin.php' || $current_page == 'ticket.php' || $current_page == 'reservations.php' || $current_page == 'liste_utilisateurs.php') ? 'active' : ''; ?>" href="/Workshop/panneladmin.php">Panel admin 
                <?php if ($ticket_count > 0 || $reservation_count > 0): ?>
                    <i class="fa-solid fa-circle-exclamation" style="color:red"></i>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        <a class="<?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>" href="/Workshop/notifications.php">Mes réservations</a>
        <a class="<?php echo $current_page == 'logout.php' ? 'active' : ''; ?>" href="/Workshop/logout.php">Se déconnecter</a>
        
        <!-- Nouveau bouton pour afficher le statut de réservation -->
        
    <?php else: ?>
        <a class="<?php echo $current_page == 'login.php' ? 'active' : ''; ?>" href="login.php">Se connecter</a>
    <?php endif; ?>
    </div>
    </div>
<script src="/Workshop/JS/modal.js"></script>
<script src="/Workshop/JS/script.js"></script>
<script>
    const tooltip = document.getElementById('reservationStatusTooltip');
    const button = document.getElementById('reservationStatusButton');

    button.addEventListener('click', function(event) {
        event.preventDefault(); // Empêche le comportement par défaut du lien
        // Positionner la tooltip juste en dessous du bouton
        const rect = button.getBoundingClientRect();
        tooltip.style.top = rect.bottom + window.scrollY + 'px'; // Position verticale
        tooltip.style.left = rect.left + window.scrollX + 'px'; // Position horizontale
        tooltip.style.display = tooltip.style.display === 'block' ? 'none' : 'block'; // Afficher ou cacher la tooltip
    });

</script>
</body>
</html>
