
// Asociar el widget de validación al formulario
$("#login").validationEngine({
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

$('#tabla').DataTable({"jQueryUI": true	});

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

$(function() {
	
	$( "#consultar" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-circle-zoomin"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0) 	getFormularioConsulta(true);
	    });
	
	$( "#crear" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-circle-plus"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0) 	getFormularioCreacionEdicion(true);
	    });;
	
	$( "#editar" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-pencil"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0&&$('#selectedItems').val()!='') 	getFormularioCreacionEdicion(false);
	    });;
	
	$( "#cambiarEstado" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-transferthick-e-w"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0&&$('#selectedItems').val()!='') 	cambiarEstadoElemento();
	    });;
	
	$( "#validar" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-circle-check"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0&&$('#selectedItems').val()!='') 	validarElemento();
	    });
	
	$( "#duplicar" ).button({
	      text: false,
	      icons: {
	        primary: "ui-icon-pause"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0&&$('#selectedItems').val()!='') 	duplicarElemento();
	    });
	
    
	
	
    $( "#objetoSeleccionado" )
    .button()
    .click(function() {
    	if($('#objetoId').val()!=0) 	getFormularioConsulta();
    })
    .next()
      .button({
        text: false,
        icons: {
          primary: "ui-icon-triangle-1-s"
        }
      })
      .click(function() {
        var menu = $( this ).parent().next().show().position({
          my: "left top",
          at: "left bottom",
          of: this
        });
        $( document ).one( "click", function() {
          menu.hide();
        });
        return false;
      })
      .parent()
        .buttonset()
        .next()
          .hide()
          .menu();
    
    if($('#objetoId').val()!=0) 	getFormularioConsulta(true);
	
	
});

//Datatable con jquery themeroller
//var table = $('#example').DataTable({
//	  "jQueryUI": true        	               
//	});