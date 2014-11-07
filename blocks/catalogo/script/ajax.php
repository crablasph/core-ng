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
$pagina="pagina=".$this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar =  $pagina;
$cadenaACodificar.="&procesarAjax=true";
$cadenaACodificar.="&action=index.php";
$cadenaACodificar.="&bloqueNombre=".$esteBloque["nombre"];
$cadenaACodificar.="&bloqueGrupo=".$esteBloque["grupo"];

//Codificar las variables
$enlace=$this->miConfigurador->getVariableConfiguracion("enlace");

//Cadena codificada para listar Catalogos

$cadena0=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($pagina,$enlace);

//Cadena codificada para listar Catalogos
$cadenaACodificar1=$cadenaACodificar."&funcion=agregar";
$cadena1=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar1,$enlace);

//Cadena codificada para listar Catalogos
$cadenaACodificar2=$cadenaACodificar."&funcion=crearCatalogo";
$cadena2=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar2,$enlace);

//Cadena codificada para listar Catalogos
$cadenaACodificar3=$cadenaACodificar."&funcion=eliminarCatalogo";
$cadena3=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar3,$enlace);

//Cadena codificada para listar Catalogos
$cadenaACodificar4=$cadenaACodificar."&funcion=editarCatalogo";
$cadena4=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar4,$enlace);


$cadenaACodificar5=$cadenaACodificar."&funcion=agregarElementoCatalogo";
$cadena5=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar5,$enlace);


$cadenaACodificar6=$cadenaACodificar."&funcion=guardarEdicionElementoCatalogo";
$cadena6=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar6,$enlace);


$cadenaACodificar7=$cadenaACodificar."&funcion=cambiarNombreCatalogo";
$cadena7=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar7,$enlace);



$cadenaACodificar8=$cadenaACodificar."&funcion=mostrarCatalogo";
$cadena8=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar8,$enlace);


$cadenaACodificar9=$cadenaACodificar."&funcion=eliminarElementoCatalogo";
$cadena9=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar9,$enlace);


//URL definitiva
$addLista=$url.$cadena1;
$crearCatalogo=$url.$cadena2;
$delLista=$url.$cadena3;
$ediLista=$url.$cadena4;
$addCatal=$url.$cadena5;
$ediCatal=$url.$cadena6;
$nomCatal=$url.$cadena7;
$mosLista=$url.$cadena8;
$delCatal=$url.$cadena9;
$casa =  $url.$cadena0;
?>

<script type='text/javascript'>

	    function irACasa(){
	    	window.location = '<?php echo $casa; ?>';
		}
	
		function agregarElementoLista(){
			$(document).tooltip('destroy');
			$.ajax({
	            url: "<?php echo $addLista;?>",
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
	                
		  			var div = document.getElementById("marcoTrabajo");
		  			div.innerHTML = jresp;
		  			
			       }
	        });
			$(document).tooltip();
		}

		function mostrarElementoLista(id){
			$(document).tooltip('destroy');
			$("[id^=contenido]").each(function () {
				$(this).html('');
		    });
			$.ajax({
	            url: "<?php echo $mosLista;?>"+"&idCatalogo="+id,
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
	                
		  			var div = document.getElementById("contenidoCatalogoListas");
		  			div.innerHTML = "";
		  			div.innerHTML = jresp;
		  			
		  			
			       }
	        });
			$(document).tooltip();
		}

		function eliminarElementoCatalogo(id,idPadre,codigo,idCatalogo){
			var r = confirm("¿Esta seguro que desea eliminar el elemento?");
			if (r == true) {
				$(document).tooltip('destroy');
				var str = "&idCatalogo="+idCatalogo+"&id="+codigo+"&idPadre="+idPadre+"&idReg="+id;
				$.ajax({
		            url: "<?php echo $delCatal;?>"+str,
		            type:"post",
		            dataType: "html",
		            success: function(jresp){
		            	if(jQuery.isNumeric(jresp)){
				  			a = document.createElement("div");
				  			a.id = "el"+jresp;	
			            	editarElementoLista(a);
		                }else{
		                	var div = document.getElementById("arbol");
				  			div.innerHTML += jresp;
		                } 			
			  			
			  			
				   }
		        });
				$(document).tooltip();
			}
		}

		
		
	function agregarElementoCatalogo(){
		$(document).tooltip('destroy');
		if($("#catalogo").validationEngine('validate')!=false){
			$.ajax({
	            url: "<?php echo $addCatal;?>"+"&"+$( "#catalogo" ).serialize(),
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
		            
	                if(jQuery.isNumeric(jresp)){

			  			a = document.createElement("div");
			  			a.id = "el"+jresp;	
		            	editarElementoLista(a);
	                }else{
	                	var div = document.getElementById("arbol");
			  			div.innerHTML = jresp;
	                } 			
		            
		  			
			       }
	        });
		}
		$(document).tooltip();
	}

	function guardarEdicionElementos(idd){
		$(document).tooltip('destroy');
		if($("#catalogo").validationEngine('validate')!=false){
			$.ajax({
	            url: "<?php echo $ediCatal;?>"+"&idElementoEd="+idd+"&"+$( "#catalogo" ).serialize(),
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
		            
	                if(jQuery.isNumeric(jresp)){

			  			a = document.createElement("div");
			  			a.id = "el"+jresp;	
		            	editarElementoLista(a);
	                }else{
	                	var div = document.getElementById("arbol");
			  			div.innerHTML = jresp;
	                } 			
		            
		  			
			       }
	        });
		}
		$(document).tooltip();
	}

	function cambiarNombreCatalogo(){
		$(document).tooltip('destroy');
		if($("#catalogo_1").validationEngine('validate')!=false){
			
			$.ajax({
	            url: "<?php echo $nomCatal;?>"+"&idCatalogo="+$('#idCatalogo').val()+"&"+$( "#catalogo_1" ).serialize(),
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
		            
	                if(jQuery.isNumeric(jresp)){

			  			a = document.createElement("div");
			  			a.id = "el"+jresp;	
		            	editarElementoLista(a);
	                }else{
	                	var div = document.getElementById("arbol");
			  			div.innerHTML = jresp;
			  			
	                } 			
		            
		  			
			       }
	        });
		}
		$(document).tooltip();
	}
	
	function eliminarElementoLista(el){
			var r = confirm("¿Esta seguro que desea eliminar el elemento?");
			
				var id =  el.id.substring(2);
				if (r == true) {
				    
				
					$(document).tooltip('destroy');
					$.ajax({
			            url: "<?php echo $delLista;?>"+"&idCatalogo="+id,
			            type:"post",
			            dataType: "html",
			            success: function(jresp){
			                
				  			var div = document.getElementById("marcoTrabajo");
				  			div.innerHTML = jresp;
				  			
					       }
			        });
					$(document).tooltip();
		}
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

	function editarElementoCatalogo(id,padre,codigo,nombre,idCatalogo){
		$('#idPadre').val(padre);
		$('#id').val(codigo);
		$('#nombreElemento').val(nombre);
		$('#idCatalogo').val(idCatalogo);
		$('#lidPadre').html(padre);
		$('#idReg').val(id);
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
		

	function editarElementoLista(el){
		$(document).tooltip('destroy');
		var id =  el.id.substring(2);
		
		$.ajax({
            url: "<?php echo $ediLista;?>"+"&idCatalogo="+id,
            type:"post",
            dataType: "html",
            success: function(jresp){
                
	  			var div = document.getElementById("marcoTrabajo");
	  			div.innerHTML = jresp;
	  			
		       }
        });
		$(document).tooltip();
}

	

	
		

	function crearCatalogo(){
		$(document).tooltip('destroy');
		if($("#catalogo").validationEngine('validate')!=false){
			
			$.ajax({
	            url: "<?php echo $crearCatalogo;?>"+"&"+$( "#catalogo" ).serialize(),
	            type:"post",
	            dataType: "html",
	            success: function(jresp){
	                
		  			var div = document.getElementById("marcoTrabajo");
		  			div.innerHTML = jresp;
		  			
			       }
	        });
		}
		$(document).tooltip();
	}


</script>