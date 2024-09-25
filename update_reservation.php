<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if ($action == 'accept') {
        // Logique pour accepter la réservation
        $sql = "UPDATE reservations SET statut = 1 WHERE id = :id"; // 1 pour accepté
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $_SESSION['message'] = "Réservation acceptée.";
    } elseif ($action == 'refuse') {
        // Logique pour refuser la réservation (suppression)
        $sql = "DELETE FROM reservations WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $_SESSION['message'] = "Réservation refusée et supprimée.";
    }

    // Rediriger vers la page de liste des réservations
    header("Location: reservations.php"); // Remplacez par le nom de votre page de liste
    exit();
}
?>
