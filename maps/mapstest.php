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
$sql = "SELECT numero, coords, capacite, etage FROM salle WHERE etage = 1";
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
    <link rel="stylesheet" type="text/css" href="../Styles/styles.css">
    <link rel="stylesheet" type="text/css" href="../Styles/mapsnav.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            background-color: #333;
            padding: 10px;
        }

        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
        }

        .navbar a.active {
            background-color: #4CAF50; /* Couleur pour l'élément actif */
        }

        #map {
            width: 100%;
            height: 400px; /* Ajustez cette valeur selon votre besoin */
        }

        #coords {
            width: 90%; /* Largeur adaptée pour le téléphone */
            margin: 10px auto; /* Centrer le textarea */
            display: block;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>
<body>


    <h1>Carte du rez-de-chaussée</h1>

    <!-- Carte Leaflet -->
    <div id="map"></div>

    <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        <!-- Zone pour afficher les coordonnées récupérées -->
        <textarea id="coords" rows="5" placeholder="Les coordonnées apparaîtront ici..."></textarea>
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
        L.imageOverlay('../images/1eretage.jpg', bounds).addTo(map);
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

                polygon.on('mouseover', function(e) {
                    this.openPopup();
                });

                polygon.on('mouseout', function(e) {
                    this.closePopup();
                });
            }
        });
    </script>
</body>
</html>
