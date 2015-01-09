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

//Cadena codificada evaluar
$cadenaACodificar6=$cadenaACodificar."&funcion=evaluar";
$cadena6=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar6,$enlace);

//Cadena codificada autocompletar
$cadenaACodificar7=$cadenaACodificar."&funcion=autocomplete";
$cadena7=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar7,$enlace);


//Cadena codificada autocompletar
$cadenaACodificar8=$cadenaACodificar."&funcion=evaluarTexto";
$cadena8=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar8,$enlace);


//URL definitiva
$consultarFormulario=$url.$cadena1;
$cambiarEstado = $url.$cadena2;
$duplicar = $url.$cadena3;
$crearFormulario = $url.$cadena4;
$guardar = $url.$cadena5;
$evaluar = $url.$cadena6;
$autocompletar = $url.$cadena7;
$evaluarTexto = $url.$cadena8;
?>

<script type='text/javascript'>

var listaIds =  [];
var listaNombres = [];
var listaAlias = [];

        function setObjeto(idObjeto,aliasObjeto){


        	listaIds =  [];
        	listaNombres = [];
        	listaAlias = [];
			            
        	if(idObjeto!=0){
        		$('#objetoId').val(idObjeto);
        		$('#objetoSeleccionado').html('<span class="ui-button-text">'+aliasObjeto+'</span>');
        		$('#selectedItems').val('');
        		getFormularioConsulta(true);
            }
        	
        }

        function autocompletar(elemento){


            if($('#formularioCreacionEdicion').length>0&&elemento!='proceso') return false;;
        	
        	if(typeof listaIds[elemento]=='undefined'){

        	$( "#"+elemento+'Nombre' ).attr('disabled',true);
            	
        	listaIds[elemento] =  [];
        	listaNombres[elemento] = [];
        	listaAlias[elemento] = [];       	

        	var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
        	data +=  "&field="+elemento;

        	
        	
        	$.ajax({
	            url: "<?php echo $autocompletar;?>",
	            type:"post",
	            data:data,
	            dataType: "json",
	            success: function(jresp){
	            	 
	            	
		  			for(i=0;i<jresp.length;i++){
		  				listaIds[elemento].push( jresp[i].id);
		  				listaNombres[elemento].push( jresp[i].nombre);
		  				listaAlias[elemento].push( jresp[i].alias); 
		  			}  
			       }
	        });

        	
        	$( "#"+elemento+'Nombre' ).autocomplete({
        	      source: listaNombres[elemento]
        	    });
        	    
        	$( "#"+elemento+'Nombre' ).change(function() {
        		   
        	    	var indice = listaNombres[elemento].indexOf(this.value);
        	    	if(typeof listaIds[elemento][indice] == 'undefined') $( "#"+elemento).val($( "#"+elemento+'Nombre' ).val());
                	else $( "#"+elemento).val(listaIds[elemento][indice]);
                	$( "#"+elemento).trigger("change");
        	    	
            	    });

        	$( "#"+elemento+'Nombre' ).attr('disabled',false);
        	}

        	
        	//$( "#"+elemento+'Nombre' ).trigger("change");
        	var indice = listaNombres[elemento].indexOf($( "#"+elemento+'Nombre' ).val());
        	if(typeof listaIds[elemento][indice] == 'undefined') $( "#"+elemento).val($( "#"+elemento+'Nombre' ).val());
        	else $( "#"+elemento).val(listaIds[elemento][indice]);
        	
        	
        	
        	return 0;
        	
        	
           }	

        function cambiarValoresAutocomplete(valor,id){
	    	var indice = listaNombres[elemento].indexOf(String(valor));
	    	if(typeof listaIds[elemento][indice] == 'undefined') $( "#"+elemento).val($( "#"+elemento+'Nombre' ).val());
        	else $( "#"+elemento).val(listaIds[elemento][indice]);
	    	$( "#"+elemento).trigger("change");
	    	
        
        }
            
        function validarValorLista(valor,id){
            
            var elemento = id.replace('Nombre','');
            autocompletar(elemento);
            //console.log(listaNombres[elemento]);
            if(typeof listaNombres[elemento] == 'undefined') autocompletar(elemento);
            //for(i=0;i<listaNombres[elemento].length;i++) 
            //    console.log(typeof valor, typeof listaNombres[elemento],valor,listaNombres[elemento]);
            //console.log(listaNombres[elemento].length, elemento,valor,listaNombres[elemento],listaNombres[elemento].indexOf(String(valor)));
        	return listaNombres[elemento].indexOf(String(valor))<0?false:true;
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

        function validarElemento(){

        	if($("#objetoId").val()==6) return false;

        	$(".ui-dialog-content").dialog("close");
        	
        	var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
        	data += "&"+ $( "#seleccionFormulario" ).serialize();

        	

			
			var validar =  false;
			if($("#formVariablesEvaluar").length>0){
				data += "&"+ $( "#formVariablesEvaluar" ).serialize();
				
				validar= true;
				}
        	
			if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();

			

        	var div = document.getElementById("espacioMensaje");
			div.innerHTML = '<div id="loading"></div>';
			$(".ui-dialog-content").dialog('destroy').remove();
	        	$.ajax({
		            url: "<?php echo $evaluar;?>",
		            type:"post",
		            data:data,
		            dataType: "html",
		            success: function(jresp){
		                
		                var respuesta = $(jresp);
		                
		                //if(jresp.indexOf('formVariablesEvaluar')>0){
		                	div.innerHTML = '';
		                	$(jresp).dialog();
		                	
		                //}else {
		                //	$(".ui-dialog-content").dialog('destroy').remove();
			             //   getFormularioConsulta(true, jresp);
			                
		                //}
		            	
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

		    		

					
					//si esta editando y justificacion sea diferente de ''
					if($('#justificacion').length>0) data += "&justificacion="+$('#justificacion').val();
					
					if($('#selectedItems').val()!=''){
						if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
						if( $('#justificacion').length==0 || $('#justificacion').val()==''){

							
							justificacion =  '<form id="formJustificacion"><label><span>Justificaci&oacuten</span></label><textarea class="validate[required]" id="justificacion" name = "justificacion"></textarea></form>';

							$(".ui-dialog-content").dialog('destroy').remove();
							
							$(justificacion).dialog({
								
								dialogClass: "no-close",
									
								buttons: {
							        "Aceptar": function() {

								      if($("#formJustificacion").validationEngine('validate')==false) return false;

							          $( this ).dialog( "close" );
							          
							          guardarElemento();
								      
							          
							        },
							        "Cancelar":function() {
							        	$( this ).dialog( "close" );
							        	$(".ui-dialog-content").dialog('destroy').remove();

								        }
								}
						   });
							return 0;	   
						}

						$(".ui-dialog-content").dialog('destroy').remove();	
						
						
					}
					
	                var div = document.getElementById("espacioMensaje");
					div.innerHTML = '<div id="loading"></div>';
                    
					 
		        	
					if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();

		        	
		        	$.ajax({
			            url: "<?php echo $guardar;?>",
			            type:"post",
			            data:data,
			            dataType: "html",
			            success: function(jresp){
			            	if(jresp.indexOf('Error')>0){
			                	div.innerHTML = '';
			                	$(jresp).dialog();
			                }else {
			            	 getFormularioConsulta(true, jresp);
			            	 $('#selectedItems').val('');
					       }
			            }
			        });
        	}
        	
           }

         function evaluarTexto(){
            
        	if($("#formularioCreacionEdicion").validationEngine('validate')!=false){

	        	    var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
		        	data += "&"+ $( "#seleccionFormulario" ).serialize();
		        	data += "&"+ $( "#formularioCreacionEdicion" ).serialize(); 

		        	var validar =  false;
					if($("#formVariablesEvaluar").length>0){
						data += "&"+ $( "#formVariablesEvaluar" ).serialize();
						
						validar= true;
						}
		    		
	                var div = document.getElementById("espacioMensaje");
					div.innerHTML = '<div id="loading"></div>';

					
		        	
					
					if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
					$(".ui-dialog-content").dialog('destroy').remove();
		        	$.ajax({
			            url: "<?php echo $evaluarTexto;?>",
			            type:"post",
			            data:data,
			            dataType: "html",
			            success: function(jresp){
			            	
			                	div.innerHTML = '';
			                	$(jresp).dialog();
			                
			            }
			        });
        	}
        	
           }

        function getFormularioConsulta(skip, mensaje){

        	listaIds =  [];
        	listaNombres = [];
        	listaAlias = [];
        	
        	idObjeto = $('#idObjeto').val();
            
            var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
			if($( "#formularioConsulta" ).serialize().length>0&&!skip) data += "&"+ $( "#formularioConsulta" ).serialize() ;
			
            
            var div = document.getElementById("espacioMensaje");
			div.innerHTML = '<div id="loading"></div>';
			$('#selectedItems').val('');
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
			  		    }, 20000);
			  	        goToByScroll($("#divMensaje").attr("id"));
		  			}
		  			else div.innerHTML="";
		  			var elemento= 'fecha_registro';
		  			activarRangoFecha(elemento);
		  			$("button").button().click(function(event) {
                		event.preventDefault();
                	});
			       }
	        });

            }

        function getFormularioCreacionEdicion(skip, mensaje){
            idObjeto = $('#idObjeto').val();
            if($('#formularioConsulta').length>0) $('#formularioConsulta')[0].reset();
            if($('#formularioCreacionEdicion').length>0) $('#formularioCreacionEdicion')[0].reset();
            var data =  $( "#objetosFormulario" ).serialize()+"&"+$( "#identificacionFormulario" ).serialize();
            if($('#selectedItems').val()!=''&&!skip) data += "&"+ $( "#seleccionFormulario" ).serialize();
            //else $('#selectedItems').val('');
            
            
	            var div = document.getElementById("espacioMensaje");
				div.innerHTML = '<div id="loading"></div>';

					//$('#selectedItems').val(''); 

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
				  		    }, 20000);
			  			}
			  			else div.innerHTML="";
			  			
			  			
			  			if(document.getElementById('categoria')&&$( "#categoria").length>0)cambiarCategoria('');
			  			
			  			if(document.getElementById('tabsListas')&&$( "#tabsListas").length>0){
				  			$( "#tabsListas").tabs();
				  			 crearBotonesCalculadora();
				  			//$( "#tabsListas" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
				  		    //$( "#tabsListas li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
				       }
			  			
			  			cambiarRango('');
			  			$("button").button().click(function(event) {
	                		event.preventDefault();
	                	});

			  			
				  		if(document.getElementById('proceso'))	autocompletar('proceso');
			  			
			  			
			  			

			  			
		            }
		        });

            

            }
    
        
	function tablaConsulta(){
		
		
			$( "#tabla" ).selectable({
				filter:'.fila',
		      stop: function() {
		       var conteo = 0;
		       var seleccion =  [];
		        $( ".ui-selected", this ).each(function() {
		          var index = $( "#tabla tr.fila" ).index( this );
		          conteo++;
		          seleccion.push($(this).children(":first").html());

		        });
		        
		        if(conteo>0) $('#selectedItems').val(seleccion.join());
		        else $('#selectedItems').val('');
		        
		        
		      }
		    });

			$('#tabla').DataTable({"jQueryUI": true, responsive: true	});
	
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

	function activarFechaValor(){
		elemento =  'valor';
		$( '#'+elemento ).datepicker({
		      defaultDate: "+1w",
		      changeMonth: true,
		      numberOfMonths: 3,
		      dateFormat: "dd/mm/yy",
		      onClose: function( selectedDate ) {
		        $( '#max'+toTitleCase(elemento) ).datepicker( "option", "minDate", selectedDate );
		      }
		    });
	}

	function desactivarFechaValor(){
		elemento =  'valor';
		$('#'+elemento).removeAttr('disabled');
		}

	function cambiarCategoria(elemento){
		elemento=  'ruta';

		if($( "#categoria").length==0) return false;
		
		$('#'+elemento).show();
		switch($( "#categoria option:selected" ).text().toLowerCase()){
		case 'interna':
			  $('#'+elemento).hide();
			  $('#'+elemento).val(0);
			  
	    	  
			break;
		}
		codificarValor('ruta');
	}

	function cambiarClaseValidacion(idObjeto , valor){
		
		var objetosNo =  [3,4];
		var objetosWork =  parseInt($( "#objetoId" ).val());
		 if(idObjeto=='valor'&&objetosNo.indexOf( objetosWork)>=0) return false;
	     if($( "#"+idObjeto+"" ).length==0) return false;
	      var listaClass =  $( "#"+idObjeto+"" ).attr('class').split(' ');
	      var claseElegida = '';
	      var valorABuscar = 'validate';
	      valor =  valor.replace(/ /g,'');
	      for(i=0;i<listaClass.length;i++){
	    	  if(listaClass[i].search(valorABuscar)>=0) claseElegida = listaClass[i];
	    	  
		      }  
          
	      $( "#"+idObjeto+"").removeClass(claseElegida).addClass(valor);
	 
	}

	function cambiarRango(elemento){
		      
		      elemento = 'rango';
		      if(!document.getElementById('valor')) return 0;
		      var valorInicial = $( "#valor" ).val();
		      desactivarRangoFecha(elemento);
		      $('#min'+toTitleCase(elemento)).removeAttr('disabled');
		      $('#max'+toTitleCase(elemento)).removeAttr('disabled');
		      desactivarFechaValor();
		      alternarInput('valor','textarea');
		      
		      switch($( "#tipo option:selected" ).text().toLowerCase()){
			      case 'boleano':

			    	  var minimo = 0;
			    	  var maximo = 1;
			    	  var cadena = minimo + "," + maximo; 

			    	  
			    	  alternarInput('valor','boleano');
			    	  
			    	   
			    	  $('#'+elemento).val(cadena);
			    	  $('#'+elemento).hide();
			    	  
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  cambiarClaseValidacion('valor' , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required,custom[integer]]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required,custom[integer]]');
			    	  
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
			    	  cambiarClaseValidacion('valor' , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required,custom[integer]]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required,custom[integer]]');
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
			    	  cambiarClaseValidacion('valor' , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required,custom[flotante]]');
			    	  break;
			      case 'porcentaje':			    	  
			    	  
			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				      

			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", 'ej:0');
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", 'ej:100');
			    	  
			    	  $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  cambiarClaseValidacion('valor' , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required,custom[flotante]]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required,custom[flotante]]');
				      break;
			      case 'fecha':
			    	  if($( "#objetoId" ).val()!=4&&$( "#objetoId" ).val()!=3){
			    		  alternarInput('valor','text');
			    		  activarFechaValor();
			    	  }
			    	  
			    	  
			    	  var d = new Date();
			    	  var curr_date = d.getDate();
			    	  var curr_month = d.getMonth();
			    	  var curr_year = d.getFullYear();
			    	  var fecha = curr_date + "/" + curr_month + "/" + curr_year;
			    	  $('#min'+toTitleCase(elemento)).attr("placeholder", 'ej:'+fecha);
			    	  $('#max'+toTitleCase(elemento)).attr("placeholder", 'ej:1/01/2018');


			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena); 
			    	  
			    	  
			    	  $('#'+elemento).hide();
			    	  
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
                      
			    	  cambiarClaseValidacion('valor' , 'validate[required,custom[date]]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required,custom[date]]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required,custom[date]]');
			    	  activarRangoFecha(elemento);

				      
			    	  
				      break;
			      case 'texto':

			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
				       
			    	  var cadena = 'a,b';
			    	  $('#'+elemento).attr("placeholder", 'ej:'+cadena);
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  $('#'+elemento).show();
			    	  cambiarClaseValidacion('valor' , 'validate[required]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required]');
			    	  break;
			      case 'lista':

			    	  var minimo = $('#min'+toTitleCase(elemento)).val();
			    	  var maximo = $('#max'+toTitleCase(elemento)).val();;
			    	  var cadena = minimo + "," + maximo;
			    	  $('#'+elemento).val(cadena);
			    	  
			    	  var cadena = '1,2';
			    	  $('#'+elemento).attr("placeholder", 'ej:'+cadena);
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  $('#'+elemento).show();
			    	  cambiarClaseValidacion('valor' , 'validate[required]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required]');
			    	  break;
			      case 'nulo':
			    	  var cadena = 'null';
			    	  $('#'+elemento).val(cadena);
			    	  
			    	  $('#min'+toTitleCase(elemento)).hide();
			    	  $('#max'+toTitleCase(elemento)).hide();
			    	  $('#'+elemento).hide();
			    	  cambiarClaseValidacion('valor' , 'validate[required]');
			    	  cambiarClaseValidacion('min'+toTitleCase(elemento) , 'validate[required]');
			    	  cambiarClaseValidacion('max'+toTitleCase(elemento) , 'validate[required]');
				      break;
				  default:
				      $('#'+elemento).hide();
			    	  $('#min'+toTitleCase(elemento)).show();
			    	  $('#max'+toTitleCase(elemento)).show();
			    	  
					      break;
			      }

		      $( "#valor" ).val(valorInicial);
		}

	function alternarInput(elemento,opcion){
		var select = $("#"+elemento);

        
		var objetosNo =  [3,4];
		var objetosWork =  parseInt($( "#objetoId" ).val());
		
		 if(elemento=='valor'&&objetosNo.indexOf( objetosWork)>=0) return false;
		
		var attributes = $("#"+elemento).prop("attributes");
        var cadenaAtributos = '';
        $valor = '';
		// loop through <select> attributes and apply them on <div>
		$.each(attributes, function() {
		    //$div.attr(this.name, this.value);
		    cadenaAtributos += " "+this.name+'="'+this.value+'"';
		    if(this.name=='value')$valor = this.value;
		});
		
		var reemplazo = $('<textarea type="text" '+cadenaAtributos+'></textarea>');
		var textbox = $('<input type="text" '+cadenaAtributos+'></input>');

		switch (opcion){
			case 'text':
				var reemplazo = $('<input type="text" '+cadenaAtributos+'></input>');
				$("#"+elemento).replaceWith($('<input type="text" '+cadenaAtributos+'></input>'));
				break;
			case 'textarea':
				var reemplazo = $('<textarea type="text" '+cadenaAtributos+'></textarea>');
				$("#"+elemento).replaceWith($('<textarea type="text" '+cadenaAtributos+'></textarea>'));
				break;
			case 'checkbox':
				var reemplazo = $('<input type="checkbox"  '+cadenaAtributos+'></input>');
				$("#"+elemento).replaceWith($('<input type="checkbox" '+cadenaAtributos+'></input>'));
				break;
			case 'boleano':
				
				var reemplazo = $('<select   '+cadenaAtributos+'><option value="0">false</option><option value="1">true</option></select>');
				$("#"+elemento).replaceWith(reemplazo);
				if($valor==1){
					$('#'+elemento+' option:contains("1")').prop('selected', true);
					$("#"+elemento).val(1);
				}else{
					$('#'+elemento+' option:contains("0")').prop('selected', true);
					$("#"+elemento).val(0);
				}
				codificarValor('valor');
				
				break;
			default:
				var reemplazo = $('<textarea type="text"  '+cadenaAtributos+'></textarea>');
			   $("#"+elemento).replaceWith($('<textarea type="text"  '+cadenaAtributos+'></textarea>'));
				break;
		}
        
        
		
		
	}

	function insertarValorTextBox(nombre){
		$('#valor').insertAtCaret(" "+nombre+" ");
		codificarValor('valor');
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


			//if((fmin instanceof Date));
            
			
			//var fmin = $.datepicker.parseDate( "dd/mm/yy", minimo);
			//var fmax = $.datepicker.parseDate( "dd/mm/yy", maximo);

			skip =  false;
			try {
				if(($.datepicker.parseDate( "dd/mm/yy", minimo) instanceof Date)) {
					skip = true;
		            			    
				    }
			}
			catch(err) {
			}


			try {
				if(($.datepicker.parseDate( "dd/mm/yy", maximo) instanceof Date)) {
					skip = true;			    
				    }
			}
			catch(err) {
			}
            
			if(skip==false){
				if(Number(minimo)>Number(maximo)){
					
					$('#'+elemento).val('0,1');
					$('#min'+toTitleCase(elemento)).val('');
					$('#max'+toTitleCase(elemento)).val('');
					$("<div><span>Rango Inv&aacutelido</span></div>").dialog();
				}
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


	//Get cursor en el text area
	//http://stackoverflow.com/questions/512528/set-cursor-position-in-html-textbox
	function getCaretPosition (ctrlId) {

	    var CaretPos = 0;
	    var ctrl = document.getElementById(ctrlId);
	    // IE Support
	    if (document.selection) {

	        ctrl.focus ();
	        var Sel = document.selection.createRange ();

	        Sel.moveStart ('character', -ctrl.value.length);

	        CaretPos = Sel.text.length;
	    }
	    // Firefox support
	    else if (ctrl.selectionStart || ctrl.selectionStart == '0')
	        CaretPos = ctrl.selectionStart;

	    return (CaretPos);

	}

	//Set cursor en el text area
	function setCaretPosition(elemId, caretPos) {
	    var elem = document.getElementById(elemId);
	    var range;

	    if (elem.createTextRange) {
	        range = elem.createTextRange();
	        range.move('character', caretPos);
	        range.select();
	    } else {
	        elem.focus();
	        if (elem.selectionStart !== undefined) {
	            elem.setSelectionRange(caretPos, caretPos);
	        }
	    }
	}

	function cambiarVisibilidadBusqueda(){
		$('#contenedorBuscador').toggle();
		$('#botones').toggle();
	}

	function crearBotonesCalculadora(){
		$( ".elementoListaCalculadora" ).each(function( index ) {
			  $( this ).button() ;
			});
	}

	function buscarBotonesCalculadora(div ,input){
		var texto =  $("#"+input).val();
		
		$( "."+div ).each(function( index ) {
			  if($(this).html().indexOf(texto)<0) $(this).hide() ;
			  else $(this).show() ;
			});
	}

	function formularioReset(elemento){
		$('#'+elemento)[0].reset();
		cambiarCategoria('categoria');
		cambiarRango('tipo');
	}

	function formularioClean(elemento){
		$('#'+elemento).clearForm()
		cambiarCategoria('categoria');
		cambiarRango('tipo');
	}
		


</script>