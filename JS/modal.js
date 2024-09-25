function openModal(name, role, email, linkedin) {
    document.getElementById('modal-name').textContent = name;
    document.getElementById('modal-role').textContent = role;
    document.getElementById('modal-email').textContent = email;
    document.getElementById('modal-linkedin').textContent = linkedin;
    const linkedinElement = document.getElementById('modal-linkedin');
    linkedinElement.textContent = linkedin; 
    linkedinElement.href = linkedin; 
    linkedinElement.target = "_blank"; 

    document.getElementById('myModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('myModal').style.display = 'none';
}


window.onclick = function(event) {
    const modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}