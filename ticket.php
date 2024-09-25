<?php
session_start(); 
include "headerlogin.php";
include "db.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); 
    exit;
}

$sql = "SELECT id, salle, origine, description, statut, responsable FROM probleme";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_ticket'])) {
    $salle = $_POST['salle'];
    $origine = $_POST['origine']; // Changer type en origine
    $description = $_POST['description'];
    $statut = 0; 
    $sql = "INSERT INTO probleme (salle, origine, description, statut) VALUES (:salle, :origine, :description, :statut)"; // Changer type en origine
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':salle', $salle);
    $stmt->bindParam(':origine', $origine); // Changer type en origine
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':statut', $statut);
    $stmt->execute();
    header('Location: ticket.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_ticket'])) {
    $ticketId = $_POST['ticket_id'];
    $responsable = $_SESSION['username']; 
    $sql = "UPDATE probleme SET responsable = :responsable, statut = 1 WHERE id = :id"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':responsable', $responsable);
    $stmt->bindParam(':id', $ticketId);
    $stmt->execute();
    header('Location: ticket.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_ticket'])) {
    $ticketId = $_POST['ticket_id'];

    $sql = "UPDATE probleme SET statut = 2 WHERE id = :id"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $ticketId);
    $stmt->execute();
    header('Location: ticket.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets</title>
    <link rel="stylesheet" href="Styles/ticket.css"> 
</head>
<body>
    <h1>Liste des Tickets</h1>
    <button id="openModal">Créer un Ticket</button>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Créer un Ticket</h2>
            <form action="ticket.php" method="post">
                <input type="text" name="salle" placeholder="Salle" required>
                <input type="text" name="origine" placeholder="Origine" required> <!-- Changer type en origine -->
                <textarea name="description" placeholder="Description" required></textarea>
                <button type="submit" name="create_ticket">Envoyer Ticket</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Salle</th>
                <th>Origine</th> <!-- Changer type en origine -->
                <th>Description</th>
                <th>Responsable</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($tickets as $ticket): ?>
        <?php if ($ticket['statut'] != 2):  ?>
            <tr>
                <td><?php echo htmlspecialchars($ticket['salle']); ?></td>
                <td><?php echo htmlspecialchars($ticket['origine']); ?></td> <!-- Changer type en origine -->
                <td><?php echo htmlspecialchars($ticket['description']); ?></td>
                <td><?php echo htmlspecialchars($ticket['responsable']); ?></td>
                <td class="<?php echo 'status-' . htmlspecialchars($ticket['statut']); ?>">
                    <?php echo htmlspecialchars($ticket['statut']); ?>
                </td>
                <td>
                    <?php if ($ticket['statut'] == 0): ?>
                        <form action="ticket.php" method="post" style="display:inline;">
                            <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                            <button type="submit" name="assign_ticket">Assigner</button>
                        </form>
                    <?php elseif ($ticket['statut'] == 1 && $ticket['responsable'] == $_SESSION['username']): ?>
                        <form action="ticket.php" method="post" style="display:inline;">
                            <input type="hidden" name="ticket_id" value="<?php echo htmlspecialchars($ticket['id']); ?>">
                            <button type="submit" name="close_ticket">Fermer</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</tbody>

    </table>

    <script>
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModal");
        var span = document.getElementsByClassName("close")[0];
        btn.onclick = function() {
            modal.style.display = "block";
        }
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
