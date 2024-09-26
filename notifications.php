<?php 
session_start();
require_once 'db.php';
include 'veriflogin.php';
include 'headerlogin.php';

$current_page = basename($_SERVER['PHP_SELF']);
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
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}

// Récupérer le nom d'utilisateur de la session
$username = $_SESSION['username']; // Assurez-vous que le nom d'utilisateur est stocké dans la session

// Requête pour récupérer les réservations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE origine = :origine AND statut IN (1, 2)");
$stmt->execute(['origine' => $username]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCampus</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="Styles/notif.css">
</head>
<body>

    <h1>Mes réservations</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Salle</th>
                <th>Date</th>
                <th>Heure de début</th>
                <th>Durée</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($reservations) > 0): ?>
                <?php foreach ($reservations as $reservation): ?>
                    <tr class="<?php echo $reservation['statut'] == 1 ? 'validée' : 'refusée'; ?>">
                        <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['numero_salle']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['date_reservation']); ?></td>
                        <td><?php echo htmlspecialchars(date('H:i', strtotime($reservation['heure_reservation']))); ?></td>
                        <td><?php echo htmlspecialchars($reservation['duree']); ?> minutes</td>
                        <td><?php echo htmlspecialchars($reservation['statut'] == 1 ? 'Validée' : 'Refusée'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Aucune réservation trouvée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
