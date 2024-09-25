<?php 
session_start();
require_once 'db.php';
include 'headerlogin.php';

// Récupérer les réservations
$sql = "SELECT r.id, r.origine, r.numero_salle, r.date_reservation, r.heure_reservation, r.heure_reservation_fin, r.motif, r.statut, s.numero AS salle_numero, s.etage 
        FROM reservations r 
        JOIN salle s ON r.numero_salle = s.numero 
        WHERE r.statut = 0 OR r.statut is NULL  -- Filtrer uniquement les réservations en attente
        ORDER BY r.date_reservation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Réservations</title>
    <link rel="stylesheet" href="../Styles/styles.css">
    <style>
        /* Style global */
/* Style global */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

/* Titre */
h1 {
    text-align: center;
    color: #333;
    font-size: 24px;
}

/* Conteneur de la table */
.table-container {
    margin: auto;
    max-width: 1000px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f8f8;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Boutons de décision */
button {
    background-color: #007BFF;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

button:focus {
    outline: none;
}

/* Style du modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    padding-top: 60px;
}

.modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 90%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover, .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

.modal-buttons {
    text-align: center;
    margin-top: 20px;
}

/* Boutons dans le modal */
.modal-buttons button {
    margin: 0 15px;
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 5px;
    border: none; /* Ajouté pour retirer la bordure par défaut */
}

/* Couleurs des boutons */
.accepter {
    background-color: green; /* Vert pour Accepter */
    color: white;
}

.refuser {
    background-color: red; /* Rouge pour Refuser */
    color: white;
}

.modal-buttons button:hover {
    opacity: 0.9; /* Léger effet d'opacité au survol */
}

/* Fermeture automatique du modal */
.modal-buttons button:focus {
    outline: none; /* Suppression du contour lors de la mise au point */
}


    </style>
</head>
<body>

<h1>Liste des Réservations</h1>

<div class="table-container">
    <?php if (count($reservations) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Salle</th>
                    <th>Date</th>
                    <th>Heure de Début</th>
                    <th>Heure de Fin</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Décision</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['origine']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['salle_numero']); ?> (Étage: <?php echo htmlspecialchars($reservation['etage']); ?>)</td>
                        <td><?php echo htmlspecialchars($reservation['date_reservation']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['heure_reservation']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['heure_reservation_fin']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['motif']); ?></td>
                        <td class="<?php 
                            if ($reservation['statut'] == 0) {
                                echo 'statut-en-attente';
                                $statusText = 'En attente';
                            }
                        ?>"><?php echo $statusText; ?></td>
                        <td>
                            <button onclick="openModal(<?php echo $reservation['id']; ?>)">Décision</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune réservation trouvée.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="decisionModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Prendre une décision</h2>
        <p>ID de réservation : <span id="reservationId"></span></p>
        <div class="modal-buttons">
            <form method="post" action="update_reservation.php" style="display:inline;">
                <input type="hidden" name="id" id="modalReservationId">
                <button type="submit" name="action" value="accept" class="accepter">Accepter</button>
            </form>
            <form method="post" action="update_reservation.php" style="display:inline;">
                <input type="hidden" name="id" id="modalReservationIdRefuse">
                <button type="submit" name="action" value="refuse" class='refuser'>Refuser</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Ouvrir le modal avec l'ID de la réservation
    function openModal(reservationId) {
        document.getElementById("reservationId").innerText = reservationId;
        document.getElementById("modalReservationId").value = reservationId;
        document.getElementById("modalReservationIdRefuse").value = reservationId;
        document.getElementById("decisionModal").style.display = "block";
    }

    // Fermer le modal
    function closeModal() {
        document.getElementById("decisionModal").style.display = "none";
    }

    // Fermer le modal si on clique en dehors
    window.onclick = function(event) {
        var modal = document.getElementById("decisionModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>
