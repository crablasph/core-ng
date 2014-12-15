
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

//Funcion para insertar despues del cursor en un text area
//fuente http://jsfiddle.net/rmDzu/2/
jQuery.fn.extend({
	insertAtCaret: function(myValue){
	  return this.each(function(i) {
	    if (document.selection) {
	      //For browsers like Internet Explorer
	      this.focus();
	      var sel = document.selection.createRange();
	      sel.text = myValue;
	      this.focus();
	    }
	    else if (this.selectionStart || this.selectionStart == '0') {
	      //For browsers like Firefox and Webkit based
	      var startPos = this.selectionStart;
	      var endPos = this.selectionEnd;
	      var scrollTop = this.scrollTop;
	      this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
	      this.focus();
	      this.selectionStart = startPos + myValue.length;
	      this.selectionEnd = startPos + myValue.length;
	      this.scrollTop = scrollTop;
	    } else {
	      this.value += myValue;
	      this.focus();
	    }
	  });
	}
	});
//http://jquery-howto.blogspot.com/2013/08/jquery-form-reset.html
$.fn.clearForm = function() {
	  return this.each(function() {
	    var type = this.type, tag = this.tagName.toLowerCase();
	    if (tag == 'form')
	      return $(':input',this).clearForm();
	    if (type == 'text' || type == 'password' || tag == 'textarea')
	      this.value = '';
	    else if (type == 'checkbox' || type == 'radio')
	      this.checked = false;
	    else if (tag == 'select')
	      this.selectedIndex = -1;
	  });
	};



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
	    	if($('#objetoId').val()!=0){
	    		getFormularioConsulta(true);
	    		cambiarVisibilidadBusqueda();
	    	}
	    });
	
	$( "#crear" ).button({
	      text: false,
	      icons: {
	      primary: "ui-icon-circle-plus"
	      }
	    }).click(function() {
	    	if($('#objetoId').val()!=0){
	    		$('#selectedItems').val('');
	    		getFormularioCreacionEdicion(true);
	    	}
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