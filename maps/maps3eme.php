<?php 
session_start();
require_once '../db.php';
include '../veriflogin.php';

$current_page = basename($_SERVER['PHP_SELF']);

// Requête pour récupérer le nombre de tickets en statut 0
$ticket_count = 0;
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM probleme WHERE statut = 0");
    $stmt->execute();
    $ticket_count = $stmt->fetchColumn(); 
}
$reservation_count = 0;
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE statut is NULL");
    $stmt->execute();
    $reservation_count = $stmt->fetchColumn(); 
}

// Récupérer les salles
$sql = "SELECT numero, coords, capacite, etage FROM salle WHERE etage = 3";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$salles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte du rez-de-chaussée</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../Styles/notif.css">
    <link rel="stylesheet" type="text/css" href="../Styles/mapsnav.css">
</head>
<body>
<div>
<div class="navbar" style="text-align: center;" id="navbar">
    <a class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="/Workshop/index.php">Accueil</a>
    <a class="<?php echo ($current_page == 'mapsRDC.php' || $current_page == 'detailsalle.php') ? 'active' : ''; ?>" href="/Workshop/maps/mapsRDC.php">Maps</a>
    <a class="<?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="/Workshop/contact.php">Contact</a>
    
    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <a class="<?php echo $current_page == 'profil.php' ? 'active' : ''; ?>" href="/Workshop/profil.php">Profil</a>
        
        <?php if ($_SESSION['admin'] == 1): ?>
            <a class="<?php echo $current_page == 'panneladmin.php' ? 'active' : ''; ?>" href="/Workshop/panneladmin.php">Panel admin 
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
 <div class="navbar" style="text-align: center;" id="navbar1">
        <nav>
            <a class="<?php echo $current_page == 'mapsRDC.php' ? 'active' : ''; ?>" href="/Workshop/maps/mapsRDC.php">Rez-de-chaussée</a>
            
            <a class="<?php echo $current_page == 'maps1er.php' ? 'active' : ''; ?>" href="/Workshop/maps/maps1er.php">1er étage</a>
            <a class="<?php echo $current_page == 'maps2eme.php' ? 'active' : ''; ?>" href="/Workshop/maps/maps2eme.php">2ème étage</a>
            <a class="<?php echo $current_page == 'maps3eme.php' ? 'active' : ''; ?>" href="/Workshop/maps/maps3eme.php">3ème étage</a>
        </nav>
    </div>

<h1>Carte du rez-de-chaussée</h1>

<!-- Carte Leaflet -->
<div id="map"></div>
<?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
<!-- Zone pour afficher les coordonnées récupérées -->
<textarea id="coords" rows="5" cols="50" placeholder="Les coordonnées apparaîtront ici..."></textarea>
<?php endif; ?>
<!-- Scripts -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

<script>
    // Initialiser la carte Leaflet
    var map = L.map('map', {
        crs: L.CRS.Simple,
        minZoom: -1,
        maxZoom: 4
    });

    // Dimensions de votre image de fond
    var bounds = [[0, 0], [1000, 2000]];  
    L.imageOverlay('../images/3emeetage.jpg', bounds).addTo(map);
    map.fitBounds(bounds);

    // Créer un groupe pour les éléments dessinés
    var drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

     <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        // Ajouter le contrôle de dessin uniquement pour les admins
        var drawControl = new L.Control.Draw({
            draw: {
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false
            },
            edit: {
                featureGroup: drawnItems
            }
        });
        map.addControl(drawControl);
    <?php endif; ?>

    // Fonction pour formater les coordonnées
    function formatCoordinates(latlngs) {
        var coords = latlngs.map(function(latlng) {
            return [Math.round(latlng.lat), Math.round(latlng.lng)];
        });
        return coords.flat().join(',');
    }

    // Événement déclenché lorsqu'un polygone est dessiné
    map.on(L.Draw.Event.CREATED, function (event) {
        var layer = event.layer;
        var coords = formatCoordinates(layer.getLatLngs()[0]);

        // Ajouter le polygone à la carte
        drawnItems.addLayer(layer);

        // Afficher les coordonnées dans la zone de texte
        document.getElementById('coords').value = coords;
    });


    // Charger les salles existantes depuis la base de données sous forme de polygones
    // Charger les salles existantes depuis la base de données sous forme de polygones
// Charger les salles existantes depuis la base de données sous forme de polygones
// Charger les salles existantes depuis la base de données sous forme de polygones
// Charger les salles existantes depuis la base de données sous forme de polygones
var salles = <?php echo json_encode($salles); ?>; // Encodez en JSON

salles.forEach(function(salle) {
    var coordsArray = salle.coords.split(',').map(Number);
    var points = [];
    for (var i = 0; i < coordsArray.length; i += 2) {
        points.push([coordsArray[i], coordsArray[i + 1]]);
    }

    // Créer le polygone avec les points récupérés
    if (points.length > 0) {
        var polygon = L.polygon(points, {
            color: "transparent", // Couleur du polygone existant
            weight: 0,
            fillOpacity: 0.5
        }).addTo(map);

        // Contenu de l'info-bulle
        var popupContent = "<strong>Salle numéro:</strong> " + salle.numero + "<br>" +
                           "<strong>Étage:</strong> " + salle.etage + "<br>" +
                           "<strong>Capacité:</strong> " + salle.capacite;

        // Lier l'info-bulle au polygone sans l'ouvrir tout de suite
        polygon.bindPopup(popupContent);

        // Variable pour gérer l'ouverture de l'info-bulle
        var popupTimeout;

        // Ajout d'événements pour changer la couleur lors du survol et de la sortie
        polygon.on('mouseover', function() {
            this.setStyle({ color: 'green' }); // Changer la couleur en vert lors du survol

            // Ouvrir l'info-bulle après un léger délai
            popupTimeout = setTimeout(() => {
                this.openPopup(); // Ouvrir l'info-bulle après le délai
            }, 200); // Ajustez le délai si nécessaire
        });

        polygon.on('mouseout', function() {
            this.setStyle({ color: 'transparent' }); // Revenir à la couleur transparente par défaut

            // Fermer l'info-bulle et annuler le délai s'il existe
            this.closePopup();
            clearTimeout(popupTimeout); // Annuler le délai si la souris sort avant l'ouverture
        });

        // Événement pour le clic
        polygon.on('click', function() {
            // Redirection vers une page spécifique, par exemple une page de détails de la salle
            window.location.href = '/Workshop/maps/detailsalle.php?numero=' + salle.numero;
        });
    }
});




</script>

</body>
</html>
