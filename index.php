<?php session_start(); 
    include 'headerlogin.php'; 
    include 'veriflogin.php'; 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Présentation de notre site</title>
    <style>

        h1 {
            margin: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 20px; /* Espacement entre les sections */
        }
        .section-left {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd; /* Bordure autour de chaque section */
            width: 100%; /* Remplir la largeur du conteneur */
            max-width: 600px; /* Largeur maximum pour chaque section */
            margin: 0 auto; /* Centrer chaque section */
            order: -1; /* Mettre cette section en premier pour les sections de gauche */
        }
        .section-right {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd; /* Bordure autour de chaque section */
            width: 100%; /* Remplir la largeur du conteneur */
            max-width: 600px; /* Largeur maximum pour chaque section */
            margin: 0 auto; /* Centrer chaque section */
            order: 1; /* Mettre cette section en dernier pour les sections de droite */
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background: #35424a;
            color: #ffffff;
            position: relative;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <h1>Bienvenue sur le site du Workshop 2024
    </h1>
</header>

<div class="container">
    <section class="section-left">
        <h2>Qui sommes-nous ?</h2>
        <p>Nous sommes une équipe passionnée dédiée à offrir les meilleures solutions pour répondre à vos besoins. Notre objectif est de créer une expérience utilisateur exceptionnelle à travers notre site. Et bien sur dans le but de fournir nos services à l'ensemble des étudiants des écoles EPSI de tous le territoire français</p>
    </section>

    <section class="section-right">
        <h2>Nos Services</h2>
        <ul>
            <li>Service 1: Possibilités de réservation de salles.</li>
            <li>Service 2: Possibilités de reporter un problème dans une salle.</li>
            <li>Service 3: Possibilités d'administrer entièrement depuis le site.</li>
        </ul>
    </section>

    <section class="section-left">
        <h2>Contactez-nous</h2>
        <p>Pour plus d'informations, n'hésitez pas à nous contacter à l'adresse <strong>contactfc@lille-epsi.fr</strong>.</p>
    </section>
</div>


</body>
</html>

