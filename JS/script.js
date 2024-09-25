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
                makeReservation(roomNumber, reservationTime);
            } else {
                alert('La salle est déjà réservée à cette heure.');
            }
        }
    };
    xhr.send(`roomNumber=${roomNumber}&reservationTime=${reservationTime}&reservationDate=${new Date().toISOString().split('T')[0]}`);
}

function makeReservation(roomNumber, reservationTime) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'reserve_room.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert(`Salle ${roomNumber} réservée à ${reservationTime}.`);
                closeModal(); // Fermer le modal après réservation
                location.reload(); // Recharger la page pour mettre à jour les réservations
            } else {
                alert('Erreur lors de la réservation.');
            }
        }
    };
    xhr.send(`roomNumber=${roomNumber}&reservationTime=${reservationTime}&reservationDate=${new Date().toISOString().split('T')[0]}`);
}
