
// Asociar el widget de validación al formulario
$("#login").validationEngine({
	promptPosition : "centerRight",
	scroll : false
});

$("#catalogo").validationEngine({
	promptPosition : "centerRight",
	scroll : false
});


$('#usuario').keydown(function(e) {
    if (e.keyCode == 13) {
        $('#login').submit();
    }
});

$('#clave').keydown(function(e) {
    if (e.keyCode == 13) {
        $('#login').submit();
    }
});

$('#listadoCatalogo').DataTable({
	"jQueryUI":true
});




$(function() {
	$(document).tooltip({
		position : {
			my : "left+15 center",
			at : "right center"
		}
	},
	{ hide: { duration: 800 } }
	);
});

$(function() {
	$("button").button().click(function(event) {
		event.preventDefault();
	});
});

$(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });
