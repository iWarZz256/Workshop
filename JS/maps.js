function reserveRoom(roomNumber, reservationTime) {
    if (!reservationTime) {
        alert("Veuillez sélectionner une heure de réservation.");
        return;
    }

    // Vérifier la disponibilité de la salle
    checkRoomAvailability(roomNumber, reservationTime);
}

function checkRoomAvailability(roomNumber, reservationTime) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_availability.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            const response = JSON.parse(xhr.responseText);
            if (response.available) {
                // Réserver la salle
                // Appel à une fonction de réservation à implémenter
                console.log(`Salle ${roomNumber} réservée à ${reservationTime}`);
                // Ajoutez ici le code pour mettre à jour la base de données avec la réservation
            } else {
                alert('La salle est déjà réservée à cette heure.');
            }
        }
    };
    xhr.send(`roomNumber=${roomNumber}&reservationTime=${reservationTime}&reservationDate=${new Date().toISOString().split('T')[0]}`);
}
function openModal(roomNumber, floor, reserved, occupied, reservations) {
    document.getElementById('modal-room-number').textContent = roomNumber;
    document.getElementById('modal-floor').textContent = floor;
    document.getElementById('modal-reserved').textContent = reserved;
    document.getElementById('modal-occupied').textContent = occupied;

    // Mettre à jour le planning de la semaine
    const scheduleBody = document.getElementById('schedule-body');
    scheduleBody.innerHTML = ''; // Réinitialiser le tableau

    if (reservations.length === 0) {
        scheduleBody.innerHTML = '<tr><td colspan="2">Aucune réservation cette semaine.</td></tr>';
    } else {
        reservations.forEach(function(reservation) {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${reservation.date_reservation}</td><td>${reservation.heure_reservation}</td>`;
            scheduleBody.appendChild(row);
        });
    }

    document.getElementById('myModal').style.display = "block";
}
function toggleNav() {
    var navbar = document.getElementById("navbar");
    if (navbar.style.left === "0px") {
        navbar.style.left = "-250px"; // Cache la navbar
    } else {
        navbar.style.left = "0px"; // Affiche la navbar
    }
}
