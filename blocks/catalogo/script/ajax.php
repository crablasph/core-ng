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


$cadenaACodificar10=$cadenaACodificar."&funcion=autocompletar";
$cadena10=$this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar10,$enlace);


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
$autocompletar = $url.$cadena10;
?>

<script type='text/javascript'>

var listaIds =  [];
var listaNombres = [];
var listaAlias = [];

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
		  			autocompletar();
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
	                
		  			var div = document.getElementById("marcoTrabajo");
		  			div.innerHTML = "";
		  			div.innerHTML = jresp;

		  			$( "#volver" ).button().click(function() {
		  			  	irACasa();
		  			  	
		  		  });
		  			$( ".expandir" ).button({
		  			    text: false,
		  			    icons: {
		  			    primary: "ui-icon-plus"
		  			    }
		  			  });
		  			autocompletar();
		  			  
		  			
			       }
	        });
			$(document).tooltip();
		}

		function eliminarElementoCatalogo(id,idPadre,codigo,idCatalogo){
			var r = confirm("¿Está seguro que desea eliminar el elemento?");
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
			  			
		            	autocompletar();
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

	                $( "#volver" ).button().click(function() {
		  			  	irACasa();
		  			  	
		  		  }); 			
		            
	                autocompletar();
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

	                $( "#volver" ).button().click(function() {
		  			  	irACasa();
		  			  	
		  		  }); 			
	                autocompletar();
		            
		  			
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
		            
	                autocompletar();
			       }
	        });
		}
		$(document).tooltip();
	}
	
	function eliminarElementoLista(el){
			var r = confirm("¿Está seguro que desea eliminar el elemento?");
			
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
				  			autocompletar();
					       }
			        });
					$(document).tooltip();
		}
	}

	function accion(el,cod,id){
		
		
		$('#idPadre').val(cod);
		$('#lidPadre').val(cod);
		$('#idPadre').val(id);
		$('#idReg').val(id);
		autocompletar();
	}

	function cambioHijos(el,esto){
		
		$('.'+el).toggle();

		$(document).tooltip('destroy');
		
		var className = $(esto).attr('class');
		$( esto ).removeAttr( "title" );

		//ui-icon ui-icon-plus
		
		
		
		if($( esto ).children().hasClass('ui-icon-plus')){
			$( esto ).button({
			    text: false,
			    icons: {
			    primary: "ui-icon-minus"
			    }
			  }).attr('title', 'click para contraer elementos');
		
		}else{
			$( esto ).button({
			    text: false,
			    icons: {
			    primary: "ui-icon-plus"
			    }
			  }).attr('title', 'click para expandir elementos');
		
		}
		
		
		$(document).tooltip();
		
	}


	function autocompletar(elemento){


        
    	
    	if(typeof listaIds['lidPadre']=='undefined'){

    		
    	$( "#lidPadre" ).attr('disabled',true);
        	
    	listaIds['lidPadre'] =  [];
    	listaNombres['lidPadre'] = [];
    	listaAlias['lidPadre'] = [];       	

        data = "idCatalogo="+$("#idCatalogo").val();
    	
    	
    	$.ajax({
            url: "<?php echo $autocompletar;?>",
            type:"post",
            data:data,
            dataType: "json",
            success: function(jresp){
            	 
            	
	  			for(i=0;i<jresp.length;i++){
		  			
	  				listaIds['lidPadre'].push( jresp[i].id);
	  				listaNombres['lidPadre'].push( jresp[i].nombre);
	  				listaAlias['lidPadre'].push( jresp[i].alias);
	  				 
	  			}  
		       }
        });

    	
    	
    	
    	

    	
    	
    	}

    	$( "#lidPadre" ).autocomplete({
  	      source: listaNombres['lidPadre']
  	    });
    	    
    	$( "#lidPadre" ).change(function() {
    		   
    	    	var indice = listaNombres['lidPadre'].indexOf(this.value);
    	    	if(typeof listaIds['lidPadre'][indice] == 'undefined') $( "#idPadre").val($( "#lidPadre" ).val());
            	else $( "#idPadre").val(listaIds['lidPadre'][indice]);
            	
    	    	
        	    });

    	$( "#lidPadre" ).attr('disabled',false);
    	
    	var indice = listaNombres['lidPadre'].indexOf($( "#lidPadre" ).val());
    	if(typeof listaIds['lidPadre'][indice] == 'undefined') $( "#idPadre").val($( "#lidPadre" ).val());
    	else $( "#idPadre").val(listaIds['lidPadre'][indice]);
    	
    	
    	
    	return 0;
    	
    	
       }

    	
	function cambiarPadre(){
		var indice = listaNombres['lidPadre'].indexOf($( "#lidPadre" ).val());
    	if(typeof listaIds['lidPadre'][indice] == 'undefined') $( "#idPadre").val($( "#lidPadre" ).val());
    	else $( "#idPadre").val(listaIds['lidPadre'][indice]);

    	
    	
    
	}
	
	function validarValorLista(valor,id){
        
        
        if(valor==0) return true;
        autocompletar();
        if(typeof listaNombres['lidPadre'] == 'undefined') autocompletar();
     	return listaNombres['lidPadre'].indexOf(String(valor))<0?false:true;
    }

	function editarElementoCatalogo(id,padre,codigo,nombre,idCatalogo){
		$('#idPadre').val(padre);
		$('#id').val(codigo);
		$('#nombreElemento').val(nombre);
		$('#idCatalogo').val(idCatalogo);
		$('#lidPadre').val(padre);
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
		$('#lidPadre').val(0);
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

	  			$( "button" ).button();
	  			  
	  			
	  			$( "#volver" ).button().click(function() {
	  			  	irACasa();
	  			  	
	  		  });
	  			
	  			$( ".expandir" ).button({
	  			    text: false,
	  			    icons: {
	  			    primary: "ui-icon-plus"
	  			    }
	  			  });

	  			$( ".editar" ).button({
	  			    text: false,
	  			    icons: {
	  			    primary: "ui-icon-pencil"
	  			    }
	  			  });

	  			$( ".eliminar" ).button({
	  			    text: false,
	  			    icons: {
	  			    primary: "ui-icon-trash"
	  			    }
	  			  });

	  			autocompletar();

	  			
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

		  			autocompletar();
		  			
			       }

		       
	        });
		}
		$(document).tooltip();
	}


</script>