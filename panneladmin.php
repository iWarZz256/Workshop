<?php 
session_start();
include 'headerlogin.php'; 
include 'db.php';

// Vérifier les alertes pour les tickets
$ticket_alerts = $pdo->query("SELECT COUNT(*) FROM probleme WHERE statut = 0")->fetchColumn();

// Vérifier les alertes pour les réservations
$reservation_alerts = $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut is NULL")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="../Styles/styles.css">
    <style>
        .dashboard {
            display: flex;
            justify-content: space-around;
            margin: 20px;
        }

        .box {
            
            border-radius: 5px;
            padding: 20px;
            width: 30%;
            text-align: center;
        }

        .alert {
            color: red;
            font-weight: bold;
        }

        .hidden-link{
        display: inline-block;
        text-decoration: none;
        padding: 7.5px 15px;
        font-family: arial;
        color: #FFFFFF;
        text-align: center;
        background-color: #4f81bd;
        border-radius: none;
        -webkit-border-radius: 24px;
        -moz-border-radius: 24px;
        margin: 10px;
        border: white;

}
    </style>
</head>
<body>

<h1>Tableau de bord</h1>

<div class="dashboard">
    <div class="box">
        <h2><a href="ticket.php" class="hidden-link">Tickets</a></h2>
        <?php if ($ticket_alerts > 0): ?>
            <span class="alert">Alerte: <?php echo $ticket_alerts; ?> nouveau(x) ticket(s) en attente !</span>
        <?php endif; ?>
    </div>
    <div class="box">
        <h2><a href="reservations.php" class="hidden-link">Réservations</a></h2>
        <?php if ($reservation_alerts > 0): ?>
            <span class="alert">Alerte: <?php echo $reservation_alerts; ?> nouvelle(s) reservation(s) en attente !</span>
        <?php endif; ?>
    </div>
    <div class="box">
        <h2><a href="liste_utilisateurs.php" class="hidden-link">Liste utilisateurs</a></h2>
    </div>
</div>

</body>
</html>
