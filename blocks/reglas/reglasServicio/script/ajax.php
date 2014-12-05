<?php
/**
 *
 * Importante: Si se desean los datos del bloque estos se encuentran en el arreglo $esteBloque
 */

//URL base
$url=$this->miConfigurador->getVariableConfiguracion("host");
$url.=$this->miConfigurador->getVariableConfiguracion("site");
$url.="/index.php?";

//Variables
$cadenaACodificar="pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar.="&procesarAjax=true";
$cadenaACodificar.="&action=index.php";
$cadenaACodificar.="&bloqueNombre=".$esteBloque["nombre"];
$cadenaACodificar.="&bloqueGrupo=".$esteBloque["grupo"];


//Codificar las variables
$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");

//Cadena codificada para consultar
$cadenaACodificar1=$cadenaACodificar."&funcion=consultarForm";
$cadena1=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar1,$enlace);

//Cadena codificada para cambiar Estado
$cadenaACodificar2=$cadenaACodificar."&funcion=cambiarEstado";
$cadena2=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar2,$enlace);

//Cadena codificada para duplicar
$cadenaACodificar3=$cadenaACodificar."&funcion=duplicar";
$cadena3=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar3,$enlace);

//Cadena codificada para formulario creacion
$cadenaACodificar4=$cadenaACodificar."&funcion=crearForm";
$cadena4=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar4,$enlace);

//Cadena codificada guardar
$cadenaACodificar5=$cadenaACodificar."&funcion=guardarDatos";
$cadena5=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar5,$enlace);



//URL definitiva
$consultarFormulario=$url.$cadena1;
$cambiarEstado = $url.$cadena2;
$duplicar = $url.$cadena3;
$crearFormulario = $url.$cadena4;
$guardar = $url.$cadena5;


?>

<script type='text/javascript'>

        function setObjeto(idObjeto,aliasObjeto){
            
        	if(idObjeto!=0){
        		$('#objetoId').val(idObjeto);
        		$('#objetoSeleccionado').html('<span class="ui-button-text">'+aliasObjeto+'</span>');
        		getFormularioConsulta(true);
            }
        	
        }	

        function cambiarEstadoElemento(){
        	var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
        	data += "&"+ $( "#seleccionFormulario" ).serialize();

        	var div = document.getElementById("espacioMensaje");
			div.innerHTML = '<div id="loading"></div>';
			
        	
        	if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
        	$.ajax({
	            url: "<?php echo $cambiarEstado;?>",
	            type:"post",
	            data:data,
	            dataType: "html",
	            success: function(jresp){
	            	 
	            	getFormularioConsulta(true, jresp);
		  			
			       }
	        });
        	
           }

        function duplicarElemento(){
        	var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
        	data += "&"+ $( "#seleccionFormulario" ).serialize();

        	var div = document.getElementById("espacioMensaje");
			div.innerHTML = '<div id="loading"></div>';
			
        	
			if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
        	
        	$.ajax({
	            url: "<?php echo $duplicar;?>",
	            type:"post",
	            data:data,
	            dataType: "html",
	            success: function(jresp){
	                
	            	getFormularioConsulta(true, jresp);
	            	
			       }
	        });
        	
           }

        function guardarElemento(){
        	if($("#formularioCreacionEdicion").validationEngine('validate')!=false){
		        	var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
		        	data += "&"+ $( "#seleccionFormulario" ).serialize();
		        	data += "&"+ $( "#formularioCreacionEdicion" ).serialize(); 
		
		        	var div = document.getElementById("espacioMensaje");
					div.innerHTML = '<div id="loading"></div>';
					
		        	
					if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
		        	
		        	$.ajax({
			            url: "<?php echo $guardar;?>",
			            type:"post",
			            data:data,
			            dataType: "html",
			            success: function(jresp){
			                
			            	getFormularioConsulta(true, jresp);
			            	
					       }
			        });
        	}
        	
           }

        function getFormularioConsulta(skip, mensaje){
            idObjeto = $('#idObjeto').val();
            
            var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
			if($( "#formularioConsulta" ).serialize().length>0&&!skip) data += "&"+ $( "#formularioConsulta" ).serialize() ;
			
            
            var div = document.getElementById("espacioMensaje");
			div.innerHTML = '<div id="loading"></div>';
			
            $.ajax({
	            url: "<?php echo $consultarFormulario;?>",
	            type:"post",
	            data:data,
	            dataType: "html",
	            success: function(jresp){
	                
		  			var div = document.getElementById("espacioTrabajo");
		  			div.innerHTML = jresp;
                    tablaConsulta();
		  			var div = document.getElementById("espacioMensaje");
		  			if(mensaje&&mensaje.length>0){
			  			 div.innerHTML=mensaje;
			  			setTimeout(function() {
			  		        $("#divMensaje").hide('drop', {}, 500)
			  		    }, 5000);
			  	        goToByScroll($("#divMensaje").attr("id"));
		  			}
		  			else div.innerHTML="";
			       }
	        });

            }

        function getFormularioCreacionEdicion(skip, mensaje){
            idObjeto = $('#idObjeto').val();
            if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
            if($('#formularioCreacionEdicion').length>0) $('#formularioCreacionEdicion')[0].reset();
            var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
            if($('#selectedItems').val()!=''&&!skip) data += "&"+ $( "#seleccionFormulario" ).serialize();
            else $('#selectedItems').val('');
            
			
            //if($("#formularioCreacionEdicion").validationEngine('validate')!=false){
	            var div = document.getElementById("espacioMensaje");
				div.innerHTML = '<div id="loading"></div>';
				
	            $.ajax({
		            url: "<?php echo $crearFormulario;?>",
		            type:"post",
		            data:data,
		            dataType: "html",
		            success: function(jresp){
		                
			  			var div = document.getElementById("espacioTrabajo");
			  			div.innerHTML = jresp;
	                    tablaConsulta();
			  			var div = document.getElementById("espacioMensaje");
			  			if(mensaje&&mensaje.length>0){
				  			 div.innerHTML=mensaje;
				  			setTimeout(function() {
				  		        $("#divMensaje").hide('drop', {}, 500)
				  		    }, 5000);
			  			}
			  			else div.innerHTML="";
				       }
		        });

            //}

            }
    
        
	function tablaConsulta(){
		$('#tabla').DataTable({"jQueryUI": true	});
			$( "#tabla" ).selectable({
				filter:'tr',
		      stop: function() {
		       var conteo = 0;
		       var seleccion =  [];
		        $( ".ui-selected", this ).each(function() {
		          var index = $( "#tabla tr" ).index( this );
		          conteo++;
		          seleccion.push($(this).children(":first").html());

		        });
		        if(conteo>0) $('#selectedItems').val(seleccion.join());
		        else $('#selectedItems').val('');
		        
		        
		      }
		    });
	
		}
	function accion(el,cod,id){
		
		
		$('#idPadre').val(cod);
		$('#lidPadre').html(cod);
		$('#idReg').val(id);
	}

	function cambioHijos(el,esto){
		
		$('.'+el).toggle();

		$(document).tooltip('destroy');
		
		var className = $(esto).attr('class');
		$( esto ).removeAttr( "title" );

		if(className == 'disminuir'){
			$( esto ).removeClass( 'disminuir' );
			$( esto ).addClass( 'agregar' );
			$( esto ).attr('title', 'click para expandir elementos');
		}else{
			$( esto ).removeClass( 'agregar' );
			$( esto ).addClass( 'disminuir' );
			$( esto ).attr('title', 'click para contraer elementos');
		}

		$(document).tooltip();
		
	}

	function codificarValor(elemento){
		
		$('#'+elemento+'Codificado').val(btoa($('#'+elemento).val()));
		
	}

	function editarElementoCatalogo(id,padre,codigo,nombre,idCatalogo){
		$('#idPadre').val(padre);
		$('#id').val(codigo);
		$('#nombreElemento').val(nombre);
		$('#idCatalogo').val(idCatalogo);
		$("#agregarA").html("Guardar Cambios elemento "+codigo+" con padre "+padre+"")
		$("#agregarA").val("Guardar Cambios elemento "+codigo+" con padre "+padre+"");
		$("#agregarA").attr("onclick","guardarEdicionElementos("+id+")");
	}

	function reiniciarEdicion(idCatalogo){
		$("#agregarA").html("Agregar elemento");
		$("#agregarA").val("Agregar elemento");
		$("#agregarA").attr("onclick","agregarElementoCatalogo()");
		$('#idReg').val(0);
		$('#lidPadre').html('0');
		$('#catalogo')[0].reset();
		a = document.createElement("div");
			a.id = "el"+idCatalogo;	
    	editarElementoLista(a);
	}

	function toTitleCase(str)
	{
	    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}

	function cambiarRango(elemento){
		      
		      elemento = 'rango';
		      desactivarRangoFecha(elemento);
		      $('#min'+toTitleCase(elemento)).removeAttr('disabled');
		      $('#max'+toTitleCase(elemento)).removeAttr('disabled'); 
		      switch($( "#tipo option:selected" ).text().toLowerCase()){
			      case 'boleano':
			    	  var minimo = 0;
			    	  var maximo = 1;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      
			    	  $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  
			    	  break;
			      case 'entero':
			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      
			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", "ej:-1000");
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", "ej:1000");
			    	  
			    	  $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  break;
			      case 'doble':

			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      
			    	  
			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", 'ej:-1000.0');
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", 'ej:1000.0');
			    	  
			    	  $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  break;
			      case 'porcentaje':

			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      

			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", 'ej:0');
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", 'ej:0');
			    	  
			    	  $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  
				      break;
			      case 'fecha':

			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      
			    	  
			    	  var d = new Date();
			    	  var curr_date = d.getDate();
			    	  var curr_month = d.getMonth();
			    	  var curr_year = d.getFullYear();
			    	  var fecha = curr_date + "/" + curr_month + "/" + curr_year;
			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", 'ej:'+fecha);
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", 'ej:1/01/2018');
			    	  
			    	  $('#'+elemento).hide();
			    	  
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
                      
			    	  
			    	  activarRangoFecha(elemento);
				      break;
			      case 'texto':
				       
			    	  var cadena = 'a,b';
			    	  $('#'+elemento).attr("placeholder", 'ej:'+cadena);
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  
			    	  $('#'+elemento).show();
			      case 'lista':
			    	  var cadena = '1,2';
			    	  $('#'+elemento).attr("placeholder", 'ej:'+cadena);
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  $('#'+elemento).show();
			      case 'nulo':
			    	  var cadena = 'null';
			    	  $('#'+elemento).val(cadena);
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  $('#'+elemento).hide();
			    	  
				      break;
				  default:
				      $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  
					      break;
			      }
		}

	function activarRangoFecha(elemento){
		
		$( '#min'+toTitleCase(elemento) ).datepicker({
		      defaultDate: "+1w",
		      changeMonth: true,
		      numberOfMonths: 3,
		      dateFormat: "dd/mm/yy",
		      onClose: function( selectedDate ) {
		        $( '#max'+toTitleCase(elemento) ).datepicker( "option", "minDate", selectedDate );
		      }
		    });
		    $('#max'+toTitleCase(elemento) ).datepicker({
		      defaultDate: "+1w",
		      changeMonth: true,
		      numberOfMonths: 3,
		      dateFormat: "dd/mm/yy",
		      onClose: function( selectedDate ) {
		        $( '#min'+toTitleCase(elemento) ).datepicker( "option", "maxDate", selectedDate );
		      }
		    });
		}	

	function desactivarRangoFecha(elemento){
		$( '#min'+toTitleCase(elemento) ).datepicker( "option", "disabled", true );
		$( '#max'+toTitleCase(elemento) ).datepicker( "option", "disabled", true );
	}
	
	function setRango(elemento){
		if(elemento.length>0){
			var minimo = $('#min'+toTitleCase(elemento)).val();
			var maximo = $('#max'+toTitleCase(elemento)).val();
			var cadena = minimo + "," + maximo;
			
			var fmin = $.datepicker.parseDate( "dd/mm/yy", minimo);
			var fmax = $.datepicker.parseDate( "dd/mm/yy", minimo);
             
			if((fmin instanceof Date)&&(fmax instanceof Date)) {
				var minimo = $.datepicker.parseDate( "dd/mm/yy", minimo);
				var maximo = $.datepicker.parseDate( "dd/mm/yy", minimo);
	            			    
			}
			if(minimo>maximo){
				$('#'+elemento).val('0,1');
				$('#min'+toTitleCase(elemento)).val('');
				$('#max'+toTitleCase(elemento)).val('');
				$("<div><span>Rango Inv&aacutelido</span></div>").dialog();
			}
			
			$('#'+elemento).val(cadena);
			
			}
	}

    function goToByScroll(id){
        // Reove "link" from the ID
      id = id.replace("link", "");
        // Scroll
      $('html,body').animate({
          scrollTop: $("#"+id).offset().top},
          'slow');
  }
		


</script>