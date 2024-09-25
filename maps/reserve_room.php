<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomNumber = $_POST['roomNumber'];
    $reservationTime = $_POST['reservationTime'];
    $reservationDate = $_POST['reservationDate'];

    // Insérer la réservation dans la base de données
    $sql = "INSERT INTO reservations (numero_salle, date_reservation, heure_reservation) VALUES (:roomNumber, :reservationDate, :reservationTime)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'roomNumber' => $roomNumber,
        'reservationDate' => $reservationDate,
        'reservationTime' => $reservationTime
    ]);

    echo json_encode(['success' => true]);
}
?>
