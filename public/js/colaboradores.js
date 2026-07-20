// Obtener los elementos del modal
let modal = document.getElementById("miModal");
let modalImg = document.getElementById("imgModal");
let enlaces = document.getElementsByClassName("enlace-modal-foto");

// Recorrer todos los enlaces y añadirles el evento de clic
for (let i = 0; i < enlaces.length; i++) {
    enlaces[i].onclick = function(e) {
        e.preventDefault(); // Prevenir la acción por defecto del enlace
        modal.style.display = "block";
        modalImg.src = this.href;
    }
}

// Obtener el elemento <span> que cierra el modal
let span = document.getElementsByClassName("close")[0];

// Cuando el usuario hace clic en <span> (x), cerrar el modal
span.onclick = function() {
    modal.style.display = "none";
}

// También cerrar el modal si se hace clic fuera de la imagen
window.onclick = function(event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
