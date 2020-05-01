$(document).ready(function() {
    $('#Aviso').modal('toggle')
    $('#Aviso').trigger('focus')
});
$('#seguir').click(function() {
    $('#Aviso').modal('hide')
})