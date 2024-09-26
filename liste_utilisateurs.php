<?php
session_start();
require_once 'db.php';
include 'headerlogin.php';
include 'veriflogin.php'; 

// Récupérer les utilisateurs
$sql = "SELECT id, username, nom, prenom, ecole, admin, bloque FROM compte ORDER BY nom ASC"; // Ajout de 'bloque'
$stmt = $pdo->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mettre à jour le statut d'administrateur ou de blocage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_admin'])) {
        $user_id = $_POST['user_id'];
        $current_status = $_POST['current_status'];
        $new_status = $current_status == 0 ? 1 : 0;

        $update_sql = "UPDATE compte SET admin = :new_status WHERE id = :user_id";
        $update_stmt = $pdo->prepare($update_sql);
        $update_stmt->execute(['new_status' => $new_status, 'user_id' => $user_id]);

    } elseif (isset($_POST['update_bloque'])) {
        $user_id = $_POST['user_id'];
        $current_bloque_status = $_POST['current_bloque_status'];
        $new_bloque_status = $current_bloque_status == 0 ? 1 : 0;

        $update_bloque_sql = "UPDATE compte SET bloque = :new_bloque_status WHERE id = :user_id";
        $update_bloque_stmt = $pdo->prepare($update_bloque_sql);
        $update_bloque_stmt->execute(['new_bloque_status' => $new_bloque_status, 'user_id' => $user_id]);
    }

    // Redirection pour éviter la soumission multiple
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link rel="stylesheet" href="../Styles/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .table-container {
    margin: auto;
    max-width: 80%;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #36b12d;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            color: white;
            background-color: #4CAF50;
            cursor: pointer;
            border-radius: 3px;
        }
        .btn-danger {
            background-color: #f44336;
        }
    </style>
</head>
<body>

<h1>Liste des Utilisateurs</h1>

<div class="table-container">
    <?php if (count($utilisateurs) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom d'Utilisateur</th>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Ecole</th>
                    <th>Rôle</th>
                    <th>Bloqué</th>
                    <th>Action</th>
                    <th>Bloquer/Débloquer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $utilisateur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($utilisateur['id']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['username']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['ecole']); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['admin'] == 1 ? 'Administrateur' : 'Utilisateur'); ?></td>
                        <td><?php echo htmlspecialchars($utilisateur['bloque'] == 1 ? 'Oui' : 'Non'); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                                <input type="hidden" name="current_status" value="<?php echo htmlspecialchars($utilisateur['admin']); ?>">
                                <button type="submit" name="update_admin" class="btn <?php echo $utilisateur['admin'] == 1 ? 'btn-danger' : ''; ?>">
                                    <?php echo $utilisateur['admin'] == 1 ? 'Retirer Admin' : 'Devenir Admin'; ?>
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($utilisateur['id']); ?>">
                                <input type="hidden" name="current_bloque_status" value="<?php echo htmlspecialchars($utilisateur['bloque']); ?>">
                                <button type="submit" name="update_bloque" class="btn <?php echo $utilisateur['bloque'] == 0 ? 'btn-danger' : 'btn-sucess'; ?>">
                                    <?php echo $utilisateur['bloque'] == 1 ? 'Débloquer' : 'Bloquer'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php endif; ?>
</div>

</body>
</html>
