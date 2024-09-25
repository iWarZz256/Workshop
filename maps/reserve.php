<?php
session_start();
require_once '../db.php';
include '../veriflogin.php';

// Vérification que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données envoyées par le formulaire
    $roomNumber = $_POST['roomNumber'];
    $username = $_SESSION['username']; // L'utilisateur connecté
    $reservationDate = $_POST['reservationDate'];
    $reservationTime = $_POST['reservationTime'];
    
    // Vérification des données
    if (!empty($roomNumber) && !empty($reservationDate) && !empty($reservationTime)) {
        // Mise à jour de la base de données pour réserver la salle
        $sql = "UPDATE salle 
                SET reserve = 1, username = :username, date_reservation = :date_reservation, heure_reservation = :heure_reservation, duree_reservation = 1 
                WHERE numero = :roomNumber";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'username' => $username, 
            'date_reservation' => $reservationDate, 
            'heure_reservation' => $reservationTime, 
            'roomNumber' => $roomNumber
        ]);

        echo "Salle réservée avec succès pour la date et l'heure choisies.";
    } else {
        echo "Erreur: Informations manquantes.";
    }
} else {
    echo "Erreur: Méthode non autorisée.";
}
?>
