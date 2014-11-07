
// Asociar el widget de validación al formulario
$("#formulario").validationEngine({promptPosition : "centerRight", scroll: false});
$( "[title]" ).tooltip();


var read; 
if(!document.getElementById("leido")){
	
	//machetazo el ready se ejecuta dos veces
	read = document.createElement("div");
	read.id="leido";
	document.body.appendChild(read);
	
	//inicia los tabs
	$( "#tabs" ).tabs({ collapsible: true });
	

	
	//Obtiene Interfaz de consulta
	consultarUsuarioInterfaz();
	
	$(window).keydown(function(event){
	    if(event.keyCode == 13) {
	      event.preventDefault();
	      return false;
	    }
	  });
	
	$( "#ui-id-2" ).click(function() {
		//var div2 = document.getElementById("rconsultas");
		//div2.innerHTML ="";
		var div = document.getElementById("consulta");
		div.innerHTML ="";
		cargarInterfazTab2();
	});
	
	$( "#ui-id-1" ).click(function() {
		var div = document.getElementById("edicion");
		div.innerHTML ="";
		consultarUsuarioInterfaz();
	});
	
	

	
	}

