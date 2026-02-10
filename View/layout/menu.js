$(document).ready(function() {
// Funcionalidad para mostrar/ocultar el menú lateral
$("#menu-toggle").click(function() {
    $("#menu-lateral").toggleClass("open");
    $(".main-content").toggleClass("shift");
});

});