<?php 
session_start();
include '../db.php';
include '../headerlogin.php';

// Vérifier si le numéro de salle est passé en paramètre
if (isset($_GET['numero'])) {
    $numero = $_GET['numero'];

    // Préparer la requête pour récupérer les détails de la salle
    $stmt = $pdo->prepare("SELECT * FROM salle WHERE numero = :numero");
    $stmt->bindParam(':numero', $numero);
    $stmt->execute();
    $salle = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la salle existe
    if (!$salle) {
        echo "Salle non trouvée.";
        exit;
    }
} else {
    echo "Numéro de salle manquant.";
    exit;
}

// Récupérer les réservations existantes pour la salle
$stmt_reservations = $pdo->prepare("SELECT * FROM reservations WHERE numero_salle = :salle_numero ORDER BY date_reservation, heure_reservation");
$stmt_reservations->bindParam(':salle_numero', $numero);
$stmt_reservations->execute();
$reservations = $stmt_reservations->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la réservation si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_ticket'])) {
        $description = $_POST['description'];
        $origine = $_SESSION['username'];

        // Préparer la requête d'insertion dans la table problème
        $stmt_ticket = $pdo->prepare("INSERT INTO probleme (salle, description, origine) VALUES (:salle, :description, :origine)");
        $stmt_ticket->bindParam(':salle', $numero);
        $stmt_ticket->bindParam(':description', $description);
        $stmt_ticket->bindParam(':origine', $origine);
        
        if ($stmt_ticket->execute()) {
            echo "<p>Ticket créé avec succès.</p>";
        } else {
            echo "<p>Erreur lors de la création du ticket.</p>";
        }
    } else {
        $date = $_POST['date'];
        $heure_reservation = $_POST['heure_reservation'];
        $motif = $_POST['motif']; // Récupérer le motif

        // Vérifier si la date est valide
        $date_reservation = DateTime::createFromFormat('Y-m-d', $date);
        if (!$date_reservation) {
            echo "<p>Erreur : Date invalide.</p>";
            echo $date_reservation;
            exit; // Sortir si la date est invalide
        }

        // Vérifier que la date n'est pas dans le passé
        $date_actuelle = new DateTime();
        if ($date_reservation < $date_actuelle) {
            echo "<p>Erreur : Vous ne pouvez pas réserver une salle dans le passé.</p>";
            exit; // Sortir si la date est dans le passé
        }

        // Vérifier que la date ne dépasse pas une semaine
        $date_limite = (clone $date_actuelle)->modify('+7 days');
        if ($date_reservation > $date_limite) {
            echo "<p>Erreur : Vous ne pouvez pas réserver plus d'une semaine à l'avance.</p>";
            exit; // Sortir si la date est trop éloignée
        }

        // Calculer l'heure de réservation
        $date_heure_reservation = DateTime::createFromFormat('Y-m-d H:i', "$date $heure_reservation");
        if (!$date_heure_reservation) {
            echo "<p>Erreur : Heure invalide.</p>";
            exit; // Sortir si l'heure est invalide
        }

        // Calculer l'heure de fin (1 heure plus tard)
        $date_heure_reservation_fin = clone $date_heure_reservation;
        $date_heure_reservation_fin->modify('+1 hour');

        // Vérifier si l'heure est déjà réservée
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE numero_salle = :salle_numero AND date_reservation = :date_reservation AND heure_reservation = :heure_reservation");
        
        $salle_numero_var = $numero;
        $date_reservation_str = $date_reservation->format('Y-m-d');
        $heure_reservation_str = $date_heure_reservation->format('H:i:s');

        // Passer les variables à bindParam
        $stmt_check->bindParam(':salle_numero', $salle_numero_var);
        $stmt_check->bindParam(':date_reservation', $date_reservation_str);
        $stmt_check->bindParam(':heure_reservation', $heure_reservation_str);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            echo "<p>Erreur : Cette réservation existe déjà pour la salle " . htmlspecialchars($salle['numero']) . " à l'heure " . htmlspecialchars($heure_reservation_str) . ".</p>";
        } else {
            // Récupérer le nom d'utilisateur de la session
            $origine = $_SESSION['username'];

            // Préparer les valeurs à insérer
            $salle_numero = $numero;
            $date_reservation_str = $date_heure_reservation->format('Y-m-d ');
            $heure_reservation_fin_str = $date_heure_reservation_fin->format('H:i:s');

            // Préparer la requête d'insertion dans la table reservations
            $stmt = $pdo->prepare("INSERT INTO reservations (numero_salle, date_reservation, heure_reservation, heure_reservation_fin, origine, motif, statut) VALUES (:salle_numero, :date_reservation, :heure_reservation, :heure_reservation_fin, :origine, :motif, :statut)");
            $stmt->bindParam(':salle_numero', $salle_numero);
            $stmt->bindParam(':date_reservation', $date_reservation_str);
            $stmt->bindParam(':heure_reservation', $heure_reservation_str);
            $stmt->bindParam(':heure_reservation_fin', $heure_reservation_fin_str);
            $stmt->bindParam(':origine', $origine);
            $stmt->bindParam(':motif', $motif); 
            $stmt->bindParam(':statut', $statut);
            
            if ($stmt->execute()) {
                echo "<p>Réservation réussie pour la salle " . htmlspecialchars($salle['numero']) . ".</p>";
                // Recharger les réservations après une nouvelle réservation
                $reservations[] = [
                    'date_reservation' => $date_reservation_str,
                    'heure_reservation' => $heure_reservation_str,
                    'heure_reservation_fin' => $heure_reservation_fin_str,
                    'origine' => $origine,
                    'statut' => $statut,
                ];
            } else {
                echo "<p>Erreur lors de la réservation.</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Salle</title>
    <link rel="stylesheet" href="../Styles/edt.css">
    <link rel="stylesheet" type="text/css" href="../Styles/styles.css">
</head>
<body>
<script>
function openModal(message) {
    document.getElementById('confirmationMessage').innerText = message;
    document.getElementById('confirmationModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}
</script>


<button class="button-spacing" onclick="window.location.href='mapsRDC.php'">Retour à la Carte</button>
<div>
    <button class="button-spacing" onclick="document.getElementById('ticketForm').style.display='block'">Soumettre une réclamation</button>
</div>

<div class="mastersalle">
<div class="salle-details">
    <h1>Détails de la Salle</h1>
    <h2>Salle numéro : <?php echo htmlspecialchars($salle['numero']); ?></h2>
    <p><strong>Etage :</strong> <?php echo htmlspecialchars($salle['etage']); ?></p>
    <p><strong>Capacité :</strong> <?php echo htmlspecialchars($salle['capacite']); ?> places</p>
</div>

<div id="ticketForm" style="display:none;">

    <form method="post" action="">
            <h2>Soumettre une réclamation</h2>
        <label for="description">Description :</label>
        <textarea id="description" name="description" required style="width: 359px; height: 120px; resize: none;"></textarea>

        <button type="submit" name="create_ticket">Soumettre la réclamation</button>
        <button type="button" onclick="document.getElementById('ticketForm').style.display='none'">Annuler</button>
    </form>
</div>


<form method="post" action="" >
    <h2>Réserver la salle</h2>
    <label for="date">Date :</label>
    <input type="date" id="date" name="date" required>

    <label for="heure_reservation">Heure de réservation :</label>
    <select id="heure_reservation" name="heure_reservation" required>
        <?php
        // Générer les heures disponibles (par exemple, de 9h à 18h)
        for ($i = 9; $i <= 18; $i++) {
    $heure = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
    // Vérifier si l'heure est déjà réservée
    $est_reserve = false;
    foreach ($reservations as $reservation) {
        if ($reservation['date_reservation'] == $date && $reservation['heure_reservation'] == $heure) {
            // Permettre la réservation si le statut est "refusé" (statut 2)
            if ($reservation['statut'] != 2) {
                $est_reserve = true;
            }
            break;
        }
    }
    if (!$est_reserve) {
        echo "<option value=\"$heure\">$heure</option>";
    }
}
        ?>
    </select>

    <label for="motif">Motif :</label>
    <input type="text" id="motif" name="motif" required>

    <button type="submit">Réserver</button>
</form>
</div>

<h2>Réservations actuelles</h2>
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Origine</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($reservations) > 0): ?>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td>
    <?php 
 
    
    // Nettoyer et vérifier la date
    $date_reservation_str = trim($reservation['date_reservation']);
    $date_reservation = DateTime::createFromFormat('Y-m-d', $date_reservation_str);
    
    // Vérifier si la date a été correctement créée
    if (!$date_reservation) {
        echo "Date invalide : " . htmlspecialchars($date_reservation_str);

    } else {
        echo htmlspecialchars($date_reservation->format('d/m/Y')); 
    }
    ?>
</td>


                    <td><?php echo htmlspecialchars($reservation['heure_reservation']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['heure_reservation_fin']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['origine']); ?></td>
                    <td class="<?php 
                        $statusText = 'Inconnu'; 
                        if ($reservation['statut'] == 0) {
                            echo 'statut-en-attente';
                            $statusText = 'En attente';
                        } elseif ($reservation['statut'] == 1) {
                            echo 'statut-accepte';
                            $statusText = 'Accepté';
                        } elseif ($reservation['statut'] == 2) {
                            echo 'statut-refuse';
                            $statusText = 'Refusé';
                        } else {
                            echo 'statut-inconnu';
                        }
                    ?>">
                    <?php echo $statusText; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Aucune réservation trouvée.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>



</body>
</html>
