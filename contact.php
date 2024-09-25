<?php session_start(); 

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    include 'headerlogin.php'; 
} else {
    include 'header.php'; 
} ?>


<html>
<div class="container">
    <div class="block" onclick="openModal('DAUMONT Frédéric', 'Directeur du campus - Référent handicap et égalité femmes/hommes', 'frederic.daumont@campus-cd.com', 'https://www.linkedin.com/in/fr%C3%A9d%C3%A9ric-daumont-805919113/ ')">
        <img src="images/photo/daumont.png" alt="Personne 1" class="photo">
        <h3>DAUMONT Frédéric</h3>
        <p>Directeur du campus - Référent handicap et égalité femmes/hommes</p>
    </div>
    <div class="block" onclick="openModal('DEVRED Johann', 'Chef de projet MyDil', 'johann.devred@campus-cd.com', 'https://www.linkedin.com/in/johann-devred/ ')">
        <img src="images/photo/devred.png" alt="Personne 2" class="photo">
        <h3>DEVRED Johann</h3>
        <p>Chef de projet MyDil</p>
    </div>
    <div class="block" onclick="openModal('KAYSIA GOMES', 'Chargée de développement', 'kaysia.gomes@campus-cd.com','https://www.linkedin.com/in/kaysia-gomes-a16688195/')">
        <img src="images/photo/gomes.png" alt="Personne 3" class="photo">
        <h3>KAYSIA GOMES</h3>
        <p>Chargée de développement</p>
    </div>
    <div class="block" onclick="openModal('ANSOIRIAT SAID ALI', 'Chargée de développement et de relations entreprises', 'ansoiriat.saidali@campus-cd.com','https://www.linkedin.com/in/ansoiriat-said-ali-34102124a/ ')">
        <img src="images/photo/saidali.png" alt="Personne 4" class="photo">
        <h3>ANSOIRIAT SAID ALI</h3>
        <p>Chargée de développement et de relations entreprises</p>
    </div>
    <div class="block" onclick="openModal('LUCIE POUPÉE', 'Coordinatrice pédagogique', 'lucie.poupee@campus-cd.com','https://www.linkedin.com/in/lucie-poup%C3%A9e-b95a0723a/ ')">
        <img src="images/photo/poupee.png" alt="Personne 5" class="photo">
        <h3>LUCIE POUPÉE</h3>
        <p>Coordinatrice pédagogique</p>
    </div>
    <div class="block" onclick="openModal('THIBAULT VINCHENT', 'Formateur permanent', 'thibault.vinchent@campus-cd.com','https://www.linkedin.com/in/tvinchent/ ')">
        <img src="images/photo/vinchent.png" alt="Personne 6" class="photo">
        <h3>THIBAULT VINCHENT</h3>
        <p>Formateur permanent</p>
    </div>
</div>

<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modal-name"></h2>
        <h3 id="modal-role"></h3>
        <ul id="modal-details">
            <li><i class="fas fa-envelope"></i> Email : <span id="modal-email"></span></li>
            <li><i class="fa-brands fa-linkedin-in"></i> LinkedIn : <a id="modal-linkedin" href="#" target="_blank"></a></li>
        </ul>
    </div>
</div>




</html>

