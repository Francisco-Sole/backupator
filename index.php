<!DOCTYPE>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Magic Backup</title>
	<link rel="stylesheet" href="">
	<script src="jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="jquery-ui.js" type="text/javascript" charset="utf-8"></script>
	<script src="Chart.js" type="text/javascript" charset="utf-8"></script>

	<style type="text/css" media="screen">
		@font-face{
			font-family: "cuerpo";
			src: url(FutuBk__.ttf) format("truetype");
		}

		*{
			font-family: "cuerpo";
		}

		.rotated {
			-webkit-transform: rotate(180deg);  /* Chrome, Safari 3.1+ */
			-moz-transform: rotate(180deg);  /* Firefox 3.5-15 */
			-ms-transform: rotate(180deg);  /* IE 9 */
			-o-transform: rotate(180deg);  /* Opera 10.50-12.00 */
			transform: rotate(180deg);  /* Firefox 16+, IE 10+, Opera 12.10+ */
		}

		.current-text{
			font-weight: 900;
		}

		.sombra{
			-webkit-box-shadow: -1px 6px 8px 1px rgba(0,0,0,0.75);
			-moz-box-shadow: -1px 6px 8px 1px rgba(0,0,0,0.75);
			box-shadow: -1px 6px 8px 1px rgba(0,0,0,0.75);
		}

		.legend {
			list-style:none;
			font-size: 10pt;
			margin-left: 25px;
			cursor: default;
			float: left;
			display: flex;
		}

		.legend ul{
			display: flex;
		}

		.legend ul li{
			list-style:none;
			margin-right: 50px;
			text-indent: -25px;
		}

		.legend ul li span{ 
			width:20px; 
			height:8px;
			display:inline-block; 
			margin-right:5px; 
			border-radius: 15px;
		}

		.selectable{
			cursor: pointer;
			padding: 5px;
		}

		.selectable:hover{
			background-color: rgba(0,0,0,0.1);
		}

	</style>
</head>
<body style="margin:0; padding: 0; border:0; width: 100%;overflow-x: hidden;">
	<script>
		var ii = 0;
		var timer;
		var timer2;
		var indice = 0;
		var numDeTablas = 0;
		var tablasLocal = [];
		var tamanos = [];
		var tamanos0 = [];
		var datos;
		var con0 = 0;
		var segLapso = 0;
		var minLapso = 0;
		var horaLapso = 0;
		var totalRows = 0;
		var oksreg = 0;
		var solicitudTramos = 0;
		var temporizadoFinal;
		var globalPequenas = 0;
		var maxGlobalpequenas = 0;
		var globalGrandes = 0;
		var maxGlobalGrandes = 0;
		var numTablaPequenas = 0;
		var primeraVez = false;
		var avisado = false;
		var timer3;
		window.name = 'parent';
		var structDescargadas = 0;
		var totalRowsCalculos = 0;
		var myChart;
		var myChart2;
		var dataG1 = [0];
		var dataG2 = [0];
		var labelsG1 = [];
		var zipper = 0;
		var maxTablasGrandes = 0;
		var tablasGrandes = 0;

		reloj();
		dameTodasTablas();

		function cargaCountTablas(tabla){
			var datos = {"tabla": tabla};
			$.ajax({
				url: 'cargar_count_tablas.php',
				data: datos,
				type: 'GET',
				success: function (data) {

					indice++;
					$("#porcentajefilascarga").html(((parseInt(indice)/parseInt(numDeTablas))*100).toFixed(2)+"%");
					if (indice > numDeTablas-1) {
						setTimeout(function(){ repintarFormulario(); }, 500);
					}
					$("#totalTablas").html(parseInt(tablasLocal.length));
					if (data.count == 0) {
						con0++;
					}
					$("#totalTablas0registros").html(con0);
					$("#totalTablas0registros").html(con0);
					$("#porcentageTablas").html("0%");
					$("#porcentagefilas").html("0%");
					$("#horaI").html("---");
					$("#horaF").html("---");
					$("#lapso").html("---");
					$("#totalfilas").html(totalRows);
					$("#efectividad").html("0.00%");


					totalRows+= parseInt(data.count);
					
					tablasLocal.push(data);
					if (data.count > 0) {
						var bucle = Math.ceil(data.count/1000);
						
						var objeto = new Object();
						objeto.nombre = data.nombre;
						objeto.count = parseInt(data.count);
						objeto.tramos = bucle;

						var tramos = [];
						for (var i = 0; i < bucle; i++) {
							tramos.push(0);
						}

						objeto.realizado = tramos;
						totalRowsCalculos += parseInt(tramos.length);
						tamanos.push(objeto);
					}else{
						var objeto = new Object();
						objeto.nombre = data.nombre;
						objeto.count = 0;
						objeto.tramos = 0;
						var tramos = [];
						objeto.realizado = tramos;
						tamanos0.push(objeto);
						
					}
					
					var html = "";
					html = "<div style='width:25%;float:left;margin-bottom:5px;'><div style='float:left; width: 100%;margin-left:50px;margin-top: -8px;position:absolute;' id='row_"+data.nombre+"'><span style='font-size:9px;' id='nombreTabla_"+data.nombre+"'>"+data.nombre+"</span><span id='countTabla_"+data.nombre+"'></div><div style='height:6px;float:left; width:80%; margin-left:10%; border: 1px solid black'><div id='carga_"+data.nombre+"' class='barraCarga' cargado='"+((0/data.count)*100) +"' style='height:6px; overflow: hidden;max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/data.count)*100) +"%'>&nbsp;</div></div></div>"
					$("#contenido").append(html);	
					if (data.count == 0) {
						$("#carga_"+data.nombre).css("background-color","TOMATO");
					}else{
						$("#carga_"+data.nombre).css("background-color","LEVENDER");
					}	
				},
				dataType : 'JSON'
			});
		}

		function dameTodasTablas(){
			//$("#info-log").append("--> Solicitando informacion de tablas...<br>");
			$.ajax({
				url: 'dame_tablas.php',
				success: function (data) {
					numDeTablas = data.length;
					$("#info-cargando-1").hide('blind');
					$("#estados-cargando").append('<span id="info-cargando-2" style="float: left; width: 100%;margin-top: 5px;" class="current-text">Leyendo numero filas por tabla <span id="porcentajefilascarga">0.00%</span></span>').addClass('current-text');
					for (var i = 0; i < data.length; i++) {
						// $("#info-log").append("--> Solicitando informacion de tabla: "+ data[i] + "<br>");
						// $('#info-log').animate({
						// 	scrollTop: $('#info-log')[0].scrollHeight
						// }, 15);
						cargaCountTablas(data[i]);

					}
				},
				dataType : 'JSON'
			});
		}

		function reloj(){
			timer = setInterval(function(){ 
				$("#cargando").append('.');
				if (ii%3 == 0) {
					$("#cargando").html('.');
				}
				ii++;
			}, 250);		
		}

		function backup(){
			$("#backup").attr("disabled", "disabled").css("cursor", "not-allowed");

			var d = new Date();
			var hora = d.getHours();
			var min = d.getMinutes();
			var seg = d.getSeconds();
			if (hora.toString().length == 1) {
				hora = "0"+hora.toString();
			}
			if (min.toString().length == 1) {
				min = "0"+min.toString();
			}
			if (seg.toString().length == 1) {
				seg = "0"+seg.toString();
			}
			$("#horaI").html(hora+":"+min+":"+seg);
			temporizadoFinal = setInterval(function(){ 
				var d1 = new Date();
				var hora1 = d1.getHours();
				var min1 = d1.getMinutes();
				var seg1 = d1.getSeconds();
				if (hora1.toString().length == 1) {
					hora1 = "0"+hora1.toString();
				}
				if (min1.toString().length == 1) {
					min1 = "0"+min1.toString();
				}
				if (seg1.toString().length == 1) {
					seg1 = "0"+seg1.toString();
				}
				$("#horaF").html(hora1+":"+min1+":"+seg1);

				//para actulizar bien el reloj
				var hora11 = ($("#horaI").html()).split(":");
				var hora2 = ($("#horaF").html()).split(":");
				var t1 = new Date();
				var t2 = new Date();

				t1.setHours(hora11[0], hora11[1], hora11[2]);
				t2.setHours(hora2[0], hora2[1], hora2[2]);

				//Aquí hago la resta
				t1.setHours(t2.getHours() - t1.getHours(), t2.getMinutes() - t1.getMinutes(), t2.getSeconds() - t1.getSeconds());

				segLapso = t1.getSeconds();
				minLapso = t1.getMinutes();
				horaLapso = t1.getHours();
				
				if (segLapso.toString().length == 1) {
					segLapso = "0"+segLapso.toString();
				}

				if (minLapso.toString().length == 1) {
					minLapso = "0"+minLapso.toString();
				}

				if (horaLapso.toString().length == 1) {
					horaLapso = "0"+horaLapso.toString();
				}
				$("#lapso").html(horaLapso+":"+minLapso+":"+segLapso);
				
			}, 1000);


			//$("#info-log").append("--> Iniciando backup (Tablas pequeñas)...<br>");
			
			for (var i = 0; i < tablasLocal.length; i++) {
				estructura(tablasLocal[i].nombre);
			}

			functions();
			
			procedures();
			
			for (var i = 0; i < tamanos.length; i++) {
				if (parseInt(tamanos[i].tramos) <= 200) {
					maxGlobalpequenas+=parseInt(tamanos[i].tramos);
					numTablaPequenas++;
					do_backup(tamanos[i].nombre, tamanos[i].count, 0, 0, i);
				}
			}

			temporizadorGraficos = setInterval(function(){ 
				actulizaGraficos();
			}, 5000);

			temporizadorGraficos1 = setInterval(function(){ 
				actualizaGrafico1();
			}, 2000);
			
		}	

		function estructura(tabla){
			var datos = {"tabla": tabla};
			$.ajax({
				url: 'struct.php',
				data: datos,
				type: 'GET',
				success: function () {
					structDescargadas++;
					//pintamos barras
					$("#contador_struct").html(parseInt(parseInt(structDescargadas)));
					$("#carga_struct").css("width", ((parseInt(structDescargadas)/parseInt(tablasLocal.length-1))*100+"%"));
					if ((parseInt(structDescargadas)/parseInt(tablasLocal.length-1))*100 == 100) {
					}
					
				}
			});
		}

		function functions(){
			$.ajax({
				url: 'functions.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					//pintamos barras no sabemos cuanta hay
					$("#contador_functions").html(data);
					$("#contadorMAX_functions").html(data);
					$("#carga_functions").css("width", "100%");

				}
			});
		}

		function procedures(){
			$.ajax({
				url: 'procedures.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					//pintamos barras no sabemos cuanta hay
					$("#contador_procedures").html(data);
					$("#contadorMAX_procedures").html(data);
					$("#carga_procedures").css("width", "100%");
				}
			});
		}

		function do_backup(nombre, count, current, variable, indice){
			solicitudTramos++;
			actualizaDatos();
			var datos = {"tabla": nombre, "count":count, "current": current, "variable": variable, "indice": indice};
			$.ajax({
				url: 'backup.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					//actulizo el numero de tramos descargados
					tamanos[data.indice].realizado[parseInt(data.variable)] = 1;
					oksreg++;
					globalPequenas++; 

					var estanBajadas = 0;
					for (var i = 0; i < tamanos[data.indice].realizado.length; i++) {
						if (tamanos[data.indice].realizado[i] == 1) {
							estanBajadas++;
						}
					}
					
					//pintamos barras
					$("#contador_"+data.tabla).html(estanBajadas*1000);
					$("#carga_"+data.tabla).css("width", ((estanBajadas*1000)/parseInt(data.count))*100+"%");
					$("#carga_"+data.tabla).attr("cargado", ((estanBajadas*1000)/parseInt(data.count))*100);
					
					if (tamanos[data.indice].realizado[parseInt(data.variable)+1] == 0) {
						do_backup(tamanos[data.indice].nombre, tamanos[data.indice].count, data.current , parseInt(data.variable)+1, data.indice);
					}else{
						$("#contador_"+data.tabla).html(data.count);
						$("#carga_"+data.tabla).css("width", "100%");
						$("#carga_"+data.tabla).attr("cargado", "100");
					}
					actualizaDatos();
				},
				dataType : 'JSON'
			});
		}

		function do_backupG(nombre, count, current, variable, indice){
			solicitudTramos+=10;
			actualizaDatos();
			var datos = {"tabla": nombre, "count":count, "current": current, "variable": variable, "indice": indice};
			$.ajax({
				url: 'backupN.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					for (var i = 0; i < 10; i++) {
						if (tamanos[data.indice].realizado[parseInt(data.variable)+i] == 0) {
							tamanos[data.indice].realizado[parseInt(data.variable)+i] = 1;
							oksreg++;
						}
					}

					var estanBajadas = 0;
					for (var i = 0; i < tamanos[data.indice].realizado.length; i++) {
						if (tamanos[data.indice].realizado[i] == 1) {
							estanBajadas++;
						}
					}

					//pintamos barras
					$("#contador_"+data.tabla).html(estanBajadas*1000);
					$("#carga_"+data.tabla).css("width", ((estanBajadas*1000)/parseInt(data.count))*100+"%");
					$("#carga_"+data.tabla).attr("cargado", ((estanBajadas*1000)/parseInt(data.count))*100);
					
					if (tamanos[data.indice].realizado[parseInt(data.variable)+10] == 0) {
						do_backupG(tamanos[data.indice].nombre, tamanos[data.indice].count, data.current , parseInt(data.variable)+10, data.indice);
					}
					else{
						var estanBajadas2 = 0;
						for (var i = 0; i < tamanos[data.indice].realizado.length; i++) {
							if (tamanos[data.indice].realizado[i] == 1) {
								estanBajadas2++;
							}
						}
						if (estanBajadas2 >= tamanos[data.indice].realizado.length-1) {
							$("#contador_"+data.tabla).html(data.count);
							$("#carga_"+data.tabla).css("width", "100%");
							$("#carga_"+data.tabla).attr("cargado", "100");
							tablasGrandes++;
						}
					}
					actualizaDatos();
				},
				dataType : 'JSON'
			});
		}

		function do_backupPartG(nombre, count, current, variable, indice){
			var tramosGrandes = tamanos[indice].tramos;
			tramosGrandes = Math.ceil(tramosGrandes/10);
			var iteraciones = Math.ceil(tramosGrandes/10);
			
			for (var i = 0; i < iteraciones; i++) {
				do_backupG(nombre, count, (i*100000), (i*100), indice);				
			}
		}

		function actualizaDatos(){
			var oks = 0;
			$(".barraCarga").each(function(index) {
				if($(this).attr("cargado") == 100) {
					oks++;
					$(this).css("background-color","GOLD");
					$(this).css("overflow","hidden");
					$(this).parent().css("border-color", "GOLD");
					var n = $(this).attr("id").split("carga_")[1];
					$("#row_"+n).parent().css("transform", "scale(0.9)");
				};
			});
			var porcentaje = (oks / (tamanos.length) )*100;
			$("#porcentageTablas").html(porcentaje.toFixed(2)+"%");

			var totalRegistros = Math.ceil(parseInt($("#totalfilas").html())/1000);

			var porReg = (oksreg/totalRowsCalculos)*100;
			$("#porcentagefilas").html(porReg.toFixed(2)+"%");

			var efec = (oksreg/(solicitudTramos))*100;
			$("#efectividad").html(efec.toFixed(2)+"%");

			if ( porcentaje == 100 && porReg >= 100 ) {
				clearInterval(temporizadoFinal);
				clearInterval(temporizadorGraficos);
				clearInterval(temporizadorGraficos1);

				$("#efectividad").html("100%");
				console.log("Proceso finalizado.\nTiempo invertido: " + $("#lapso").html());
			}

			if (primeraVez == false && (oks >= numTablaPequenas)) {
				console.log("Tablas peuqeñas terminadas... Empieza Backup grande...");
				primeraVez = true;
				for (var i = 0; i < tamanos.length; i++) {
					if (parseInt(tamanos[i].tramos) <= 200) {
						//nada
					}else{
						maxTablasGrandes++;
						do_backupPartG(tamanos[i].nombre, tamanos[i].count, 0, 0, i);
					}
				}
			}

			var numero_porcentaje_grafico = (parseFloat($("#porcentageTablas").html())+parseFloat($("#porcentagefilas").html()))/2;
			$("#numProgreso").html(numero_porcentaje_grafico.toFixed(2) + "%");

			if (primeraVez == true && oks >= numTablaPequenas && maxTablasGrandes == tablasGrandes) {
				myChart.data.datasets[2].data[0] = parseInt($("#porcentageTablas").html());
				myChart.data.datasets[2].data[1] = 100-parseInt($("#porcentageTablas").html());

				myChart.data.datasets[1].data[0] = parseInt($("#porcentagefilas").html());
				myChart.data.datasets[1].data[1] = 100-parseInt($("#porcentagefilas").html());

				myChart.data.datasets[0].data[0] = (parseInt($("#porcentageTablas").html())+parseInt($("#porcentagefilas").html()))/2;
				myChart.data.datasets[0].data[1] = 100-(parseInt($("#porcentageTablas").html())+parseInt($("#porcentagefilas").html()))/2;

				myChart.update();
				bajaZip();
			}
		}

		function repintarFormulario(){
			clearInterval(timer);
			var html = "";
			//blanqueamos la pagina por un momento.
			$("#contenido0").html("<div id='controlador0' style='margin-left: 50px;margin-bottom: 5px;'><img id='contenedorSinRegistros1' style='float:left; height:15px;;cursor:pointer; margin-right: 5px;' src='expandido.png'/><span style='font-weight:900;'>Tablas sin registros</span></div><div id='contenidoBBDD0' style='float:left; width: 100%;'></div>");
			$("#contenido").html("<div style='margin-left: 50px;margin-bottom: 5px;'><img id='contenedorConRegistros1' style='float:left; height:15px;;cursor:pointer; margin-right: 5px;' src='expandido.png'/><span style='font-weight:900;'>Tablas con registros</span></div><div id='contenidoBBDD1' style='float:left; width: 100%;'></div>");

			$("#contenedorSinRegistros1").click(function(event) {
				$(this).toggleClass("rotated ");
				$("#contenidoBBDD0").toggle("blind");		
			});

			$("#contenedorConRegistros1").click(function(event) {
				$(this).toggleClass("rotated ");
				$("#contenidoBBDD1").toggle("blind");
			});

			clearInterval(timer);
			$("#info-cargando").hide();
			$("#info-boton").show();
			$("#totalTablas").html(parseInt(tablasLocal.length));
			$("#totalTablas0registros").html(con0);
			$("#porcentageTablas").html("0%");
			$("#totalfilas").html(totalRows);
			$("#porcentagefilas").html("0%");
			$("#horaI").html("---");
			$("#horaF").html("---");
			$("#lapso").html("---");

			console.info("Reordenando y repintando.");
			html = "<div style='width:calc(33% - 50px);float:left;margin-bottom:5px;margin-right: 50px;'>";
			html +=		"<div style='float:left; width: 100%;margin-left:35px;font-weight:900;position:absolute; margin-top:1px;' id='row_struct'>";
			html +=			"<span id='nombreTabla_struct' style='margin-left:5px;'>Estructuras </span>";
			html +=			"<span id='countTabla_struct' style='font-weight:400; font-style: italic;'><span id='contador_struct'> 0</span>/"
			html +=			"<span id='contadorMAX_struct'>"+(tablasLocal.length)+"</span>"
			html += 	"</div>";
			html +=		"<div style='float:left; width:100%; margin-left:35px;border: 1px solid black; border-radius: 8px;'>";
			html +=			"<div id='carga_struct' class='barraCarga' cargado='"+((0/parseInt(tablasLocal.length-1))*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/parseInt(tablasLocal.length-1))*100) +"%; border-radius: 7px;'> &nbsp;"
			html +=			"</div>";
			html +=		"</div>";
			html +="</div>";
			$("#contenido00").html(html);

			html = "<div style='width:calc(33% - 50px);float:left;margin-bottom:5px;margin-right: 50px;'>";
			html +=		"<div style='float:left; width: 100%;margin-left:35px;font-weight:900;position:absolute; margin-top:1px;' id='row_functions'>";
			html +=			"<span id='nombreTabla_functions' style='margin-left:5px;'>Functions </span>";
			html +=			"<span id='countTabla_functions' style='font-weight:400; font-style: italic;'><span id='contador_functions'> 0</span>/"
			html +=			"<span id='contadorMAX_functions'> 0</span>"
			html += 	"</div>";
			html +=		"<div style='float:left; width:100%; margin-left:35px;border: 1px solid black; border-radius: 8px;'>";
			html +=			"<div id='carga_functions' class='barraCarga' cargado='"+((0/parseInt(tablasLocal.length-1))*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/parseInt(tablasLocal.length-1))*100) +"%; border-radius: 7px;'> &nbsp;"
			html +=			"</div>";
			html +=		"</div>";
			html +="</div>";
			$("#contenido00").append(html);

			html = "<div style='width:calc(33% - 50px);float:left;margin-bottom:5px;'>";
			html +=		"<div style='float:left; width: 100%;margin-left:35px;font-weight:900;position:absolute; margin-top:1px;' id='row_functions'>";
			html +=			"<span id='nombreTabla_functions' style='margin-left:5px;'>Procedures </span>";
			html +=			"<span id='countTabla_functions' style='font-weight:400; font-style: italic;'><span id='contador_procedures'> 0</span>/"
			html +=			"<span id='contadorMAX_procedures'> 0</span>"
			html += 	"</div>";
			html +=		"<div style='float:left; width:100%; margin-left:35px;border: 1px solid black; border-radius: 8px;'>";
			html +=			"<div id='carga_procedures' class='barraCarga' cargado='"+((0/parseInt(tablasLocal.length-1))*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/parseInt(tablasLocal.length-1))*100) +"%; border-radius: 7px;'> &nbsp;"
			html +=			"</div>";
			html +=		"</div>";
			html +="</div>";
			$("#contenido00").append(html);

			//reordenamos
			tamanos0.sort(dynamicSort("nombre"));

			for (var i = 0; i < tamanos0.length; i++) {
				html =  "<div style='width:25%;float:left;margin-bottom:5px;'>";
				html +=		"<div style='float:left; width: 100%;margin-left:50px;font-weight:900;position:absolute; margin-top:1px;' id='row_"+tamanos0[i].nombre+"'>";
				html +=			"<span id='nombreTabla_"+tamanos0[i].nombre+"' style='margin-left:5px;'>"+tamanos0[i].nombre+"</span>";
				html +=			"<span id='countTabla_"+tamanos0[i].nombre+"' style='font-weight:400; font-style: italic;'> ";
				html +=			"<span id='contador_"+tamanos0[i].nombre+"'>0</span> / ";
				html +=			"<span id='contadorMAX_"+tamanos0[i].nombre+"'>"+tamanos0[i].count+"</span>";
				html +=		"</div>";
				html +=		"<div style='float:left; width:80%; margin-left:10%; border: 1px solid black; border-radius: 8px;'>";
				html +=			"<div id='carga_"+tamanos0[i].nombre+"' class='barraCarga' cargado='"+((0/tamanos0[i].count)*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/tamanos0[i].count)*100) +"%; border-radius: 7px;'>&nbsp;";
				html +=			"</div>";
				html +=		"</div>";
				html +=	"</div>";
				$("#contenidoBBDD0").append(html);	
				$("#carga_"+tamanos0[i].nombre).css("background-color","TOMATO");
				//LIGHTSEAGREEN
			}

			//reordenamos
			tamanos.sort(dynamicSort("nombre"));

			for (var i = 0; i < tamanos.length; i++) {
				html =  "<div style='width:25%;float:left;margin-bottom:5px;'>";
				html +=		"<div style='float:left; width: 100%;margin-left:50px;font-weight:900;position:absolute; margin-top:1px;' id='row_"+tamanos[i].nombre+"'>";
				html +=			"<span id='nombreTabla_"+tamanos[i].nombre+"' style='margin-left:5px;'>"+tamanos[i].nombre+"</span>";
				html +=			"<span id='countTabla_"+tamanos[i].nombre+"' style='font-weight:400; font-style: italic;'> ";
				html +=			"<span id='contador_"+tamanos[i].nombre+"'>0</span> / ";
				html +=			"<span id='contadorMAX_"+tamanos[i].nombre+"'>"+tamanos[i].count+"</span>";
				html +=		"</div>";
				html +=		"<div style='float:left; width:80%; margin-left:10%; border: 1px solid black; border-radius: 8px;'>";
				html +=			"<div id='carga_"+tamanos[i].nombre+"' class='barraCarga' cargado='"+((0/tamanos[i].count)*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/tamanos[i].count)*100) +"%; border-radius: 7px;'>&nbsp;";
				html +=			"</div>";
				html +=		"</div>";
				html +=	"</div>";
				$("#contenidoBBDD1").append(html);	
			}

			creargrafico1();
			creargrafico2();

		}

		function dynamicSort(property) {
			var sortOrder = 1;

			if(property[0] === "-") {
				sortOrder = -1;
				property = property.substr(1);
			}
			return function (a,b) {
				if(sortOrder == -1){
					return b[property].localeCompare(a[property]);
				}else{
					return a[property].localeCompare(b[property]);
				}        
			}
		}


		function creargrafico2(){
			dataG1.push(0);
			dataG2.push(0);

			var config = {
				type: 'line',
				data: {
					datasets: [{
						lineTension: 0,
						data: dataG1,
						backgroundColor: "#00BCEB",
						borderColor: "#00BCEB",
						fill: false,
						pointRadius: '0.5',
						label: "Num. Peticiones",
					},
					{
						lineTension: 0,
						data: dataG2,
						backgroundColor: "#FFC335",
						borderColor: "#FFC335",
						fill: false,
						pointRadius: '0.5',
						label: "Num. Tramos descargados",  
					}],
					labels: labelsG1
				},
				options: {
					bezierCurve: false,
					legend:{
						display: false,
					},
					labels:{
						display: true 
					},
					maintainAspectRatio: true,
					responsive: false,
					animation: false,
					scales: {
						xAxes: [{
							stacked: true,
							beginAtZero: true,
							gridLines: {
								display:true
							},
							display: true 
						}],
						yAxes: [{
							display: true 
						}]
					},
					tooltips: {
						callbacks: {
							label: function(tooltipItem, data) {
								return tooltipItem.yLabel.toFixed(2);
							}
						}
					},
				}
			};


			var ctx = document.getElementById('canvasPeticiones').getContext('2d');
			myChart2 = new Chart(ctx,config);

			$("#leyenda2").html(myChart2.generateLegend());
		}


		function creargrafico1(){
			var dataTablas = [0,100];
			var dataRows = [0,100];
			var dataTotal = [0,100];
			var colors1 = ["#1AC1F0", "#d1d1d1"];
			var colors2 = ["#FFC335", "#d1d1d1"];
			var colors3 = ["#EA7777", "#d1d1d1"];
			var borders =["black","black"];
			var labels = ["Tablas", "Filas", "Total"];

			var config = {
				type: 'doughnut',
				data: {
					datasets: [
					{
						data: dataTotal,
						backgroundColor: colors1,
						// borderColor: "black",
						// borderWidth: 1,
						labels: ["Descarga Total (%)","Pendiente Total (%)"]
						//hiddenLegend: true,
					},{
						data: dataRows,
						backgroundColor: colors2,
						// borderColor: "black",
						// borderWidth: 1,
						labels: ["Descarga Filas (%)","Pendiente Filas (%)"]
						
						
						//hiddenLegend: true,
					},{
						data: dataTablas,
						backgroundColor: colors3,
						// borderColor: "black",
						// borderWidth: 1,
						labels: ["Descarga Tablas (%)","Pendiente Tablas (%)"]
						
						
						//hiddenLegend: true,
					},

					],
					labels: labels,

				},
				options: {

					showAllTooltips: true,
					legendCallback: function(chart) {
						var text = [];
						var legs = [];
						for( var j=0; j<chart.data.datasets.length;j++)
						{
							for (var i = 0; i < chart.data.datasets[j].data.length; i++) 
							{
								var newd = { label: chart.data.datasets[j].labels[i] , color: chart.data.datasets[j].backgroundColor[i]  };

								if( !containsObject (newd,legs) )
								{
									legs.push(newd);
								}          
							} 
						}

						text.push('<ul class="Mylegend ' + chart.id + '-legend">');
						for( var k =0;k<legs.length;k++)
						{
							text.push('<li><span style="background-color:' + legs[k].color + '"></span>');
							text.push(legs[k].label);
							text.push('</li>');    
						}    
						text.push('</ul>');
						return text.join("");
					},  

					legend:{
						display: false
					},tooltips: {
						//enabled: false

						callbacks: {
							label: function(tooltipItem, data) {
								var dataset = data.datasets[tooltipItem.datasetIndex];
								var index = tooltipItem.index;
								return dataset.labels[index] + ': ' + dataset.data[index];
							}
						}

					},
					maintainAspectRatio: true,
					responsive: false,
					cutoutPercentage: 50,
					//showTooltips: false,
					animation: false,
				}
			};
			var ctx = document.getElementById('canvasResumen').getContext('2d');
			myChart = new Chart(ctx,config);
		}

		function actualizaGrafico1(){
			myChart.data.datasets[2].data[0] = parseInt($("#porcentageTablas").html());
			myChart.data.datasets[2].data[1] = 100-parseInt($("#porcentageTablas").html());

			myChart.data.datasets[1].data[0] = parseInt($("#porcentagefilas").html());
			myChart.data.datasets[1].data[1] = 100-parseInt($("#porcentagefilas").html());

			myChart.data.datasets[0].data[0] = (parseInt($("#porcentageTablas").html())+parseInt($("#porcentagefilas").html()))/2;
			myChart.data.datasets[0].data[1] = 100-(parseInt($("#porcentageTablas").html())+parseInt($("#porcentagefilas").html()))/2;

			myChart.update();
		}

		function actulizaGraficos(){

			var d1 = new Date();
			var hora1 = d1.getHours();
			var min1 = d1.getMinutes();
			var seg1 = d1.getSeconds();
			if (hora1.toString().length == 1) {
				hora1 = "0"+hora1.toString();
			}
			if (min1.toString().length == 1) {
				min1 = "0"+min1.toString();
			}
			if (seg1.toString().length == 1) {
				seg1 = "0"+seg1.toString();
			}
			f = hora1 + ":" + min1 + ":" + seg1;

			dataG1.push(solicitudTramos);
			dataG2.push(oksreg);
			labelsG1.push(f);
			myChart2.update();

		}

		function addToZip(folderName){
			var d1 = new Date();
			var hora1 = d1.getHours();
			var min1 = d1.getMinutes();
			var seg1 = d1.getSeconds();
			if (hora1.toString().length == 1) {
				hora1 = "0"+hora1.toString();
			}
			if (min1.toString().length == 1) {
				min1 = "0"+min1.toString();
			}
			if (seg1.toString().length == 1) {
				seg1 = "0"+seg1.toString();
			}
			f = hora1 + ":" + min1 + ":" + seg1;
			console.log(f,"-Inicio ZIP de: ", folderName);
			var datos = {"tabla": folderName};
			$.ajax({
				url: 'zip.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					var d1 = new Date();
					var hora1 = d1.getHours();
					var min1 = d1.getMinutes();
					var seg1 = d1.getSeconds();
					if (hora1.toString().length == 1) {
						hora1 = "0"+hora1.toString();
					}
					if (min1.toString().length == 1) {
						min1 = "0"+min1.toString();
					}
					if (seg1.toString().length == 1) {
						seg1 = "0"+seg1.toString();
					}
					f = hora1 + ":" + min1 + ":" + seg1;
					zipper++;
					console.log(f,"-Fin ZIP de: ", data);

					if (zipper == tamanos.length) {
						bajaZip();
					}					
				},
				dataType : 'JSON'
			});
		}

		function addToZipDDL(name, path){
			var datos = {"tabla": name, "path": path};
			$.ajax({
				url: 'zipDDL.php',
				data: datos,
				type: 'GET',
				success: function (data) {

				},
				dataType : 'JSON'
			});
		}

		function bajaZip(){
			var d1 = new Date();
			var hora1 = d1.getHours();
			var min1 = d1.getMinutes();
			var seg1 = d1.getSeconds();
			if (hora1.toString().length == 1) {
				hora1 = "0"+hora1.toString();
			}
			if (min1.toString().length == 1) {
				min1 = "0"+min1.toString();
			}
			if (seg1.toString().length == 1) {
				seg1 = "0"+seg1.toString();
			}
			f = hora1 + ":" + min1 + ":" + seg1;
			console.log(f,"-Generacion ZIP TODO");
			window.open("bajaZip.php");
			// $.ajax({
			// 	url: 'bajaZip.php',
			// 	success: function (data) {

			// 	}
			// });	
		}

		//limpiara las carpetas de trabajo.
		function reset(){
			$.ajax({
				url: 'resetWP.php',
				success: function (data) {
					console.log("Borrado con exito...");
				}
			});	
		}
		
		$(document).ready(function() {
			//sombra boton menu (redondo)
			$("#redondo_btn_menu").hover(function() {

				$("#redondo_btn_menu").css({
					"-webkit-box-shadow":"inset 0px 0px 0px 16px rgba(0,0,0,0.1)",
					"-moz-box-shadow":"inset 0px 0px 0px 16px rgba(0,0,0,0.1)",
					"box-shadow":"inset 0px 0px 0px 16px rgba(0,0,0,0.1)",
					"transition": "box-shadow 0.4s"
				})
			}, function() {
				$("#redondo_btn_menu").css({
					"-webkit-box-shadow":"inset 0px 0px 0px 0px rgba(0,0,0,0.1)",
					"-moz-box-shadow":"inset 0px 0px 0px 0px rgba(0,0,0,0.1)",
					"box-shadow":"inset 0px 0px 0px 16px 0(0,0,0,0.1)",
					"transition": "box-shadow 0.4s"
				})
			});

			//evento click boton menu
			$("#btn_menu").click(function(event) {
				//cambiamos el icono por el de cerrar (X)
				$(this).hide();
				$("#btn_menu_close").show();
				$("#btn_menu_opciones").show("blind");
				var html = "<div>";
				html += "<div id_menu='0' class='selectable'>Configurar conexión</div>";
				html += "<div id_menu='1' class='selectable'>Configuración general</div>";
				html += "<div id_menu='2' class='selectable'>Configuración de graficos</div>";
				html += "<div id_menu='3' class='selectable'>Densidad de visual</div>";
				html += "<div id_menu='4' class='selectable'>LOG</div>";
				html += "<div class='estetico'><hr></div>";
				html += "<div id_menu='5' class='selectable'>Idioma</div>";
				html += "</div>";
				$("#btn_menu_opciones").html(html);
			});

			$("#btn_menu_close").click(function(event){
				$(this).hide();
				$("#btn_menu").show();
				$("#btn_menu_opciones").hide("blind");
			})
		});

	</script>

	<div id="info-cargando" style="display: block;position: fixed;width: 100%; height: 100%; font-weight: 900;background-color: rgba(0,0,0,0.3); z-index: 9999;">
		<div style="background-color: white;background-color: white;margin-left: 25%;padding: 25px;width: 50%;margin-top: 15%;z-index: 99999;border: 2px solid black;text-align: center;position: fixed;">
			Cargando informacion de la base de datos<span id="cargando"></span>	
			<div id="estados-cargando" style="float: left; width: 100%;margin-top: 5px; margin-bottom: 10px;">
				<span id='info-cargando-1' style="float: left; width: 100%;margin-top: 5px;" class="current-text">Leyendo numero de tablas</span>
			</div>
		</div>
	</div>
	<div id="info-tablas" style="position: fixed;width: 100%;padding-bottom: 10px;border-bottom: 1px solid black;background-color: white;z-index: 1;height: 41px;" class="sombra">
		<div id="div_redondo_btn_menu" style="margin-left: 5px; float: left;margin-right: 5px;width: 35px;height: 35px;">
			<div id="redondo_btn_menu" style="border-radius: 50%; width: 100%; height: 100%;margin-top: 8px;">
				<img id="btn_menu" src="menu.png" alt="icono de menu." height="25px;" style="margin-top: 5px; margin-left: 5.5px;cursor: pointer">
				<img id="btn_menu_close" src="cerrar.png" alt="icono de menu cerrar." height="25px;" style="margin-top: 5px; margin-left: 5.5px;cursor: pointer; display: none">	
			</div>
		</div>
		<div id="btn_menu_opciones" style="display: none;float: left;background-color: white;width: calc(100% - 20px);top: 52px; padding: 10px;position: fixed;box-shadow: 3px 3px 4pc #000;-webkit-box-shadow: 3px 3px 4px #000;-moz-box-shadow: 3px 3px 4px #000;"></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Total tablas: </b><span id="totalTablas"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Tablas sin registros: </b><span id="totalTablas0registros"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>% Tablas descargadas: </b><span id="porcentageTablas"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Total filas: </b><span id="totalfilas"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>% Filas descargadas: </b><span id="porcentagefilas"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Efectividad(%): </b><span id="efectividad"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Hora inicio: </b><span id="horaI"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Hora fin: </b><span id="horaF"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px; margin-top: 10px;"><b>Lapso: </b><span id="lapso"></span></div>
		<div id="info-boton" style="display: none; width: 98%; margin-bottom: 10px; margin-left: 10px;margin-top: 10px">
			<input type="button" id="backup" name="backup" value="BACKUP!"  style="padding: 5px;width: 100px;float: right;cursor: pointer;margin-left: 10px;" onclick="backup();">
			<input type="button" id="reset" name="reset" value="RESET"  style="padding: 5px;width: 100px;float: right;cursor: pointer;" onclick="reset();">
		</div>
	</div>
	

	<div style="float: left; width: 100%;margin-top: 70px;">
		<canvas id="canvasPeticiones" style="float: left; height: 250px; width: calc(100% - 350px);margin-left: 25px;">
		</canvas>
		<canvas id="canvasResumen" style="float: left; height: 250px; width: 250px;margin-left: 50px; border: 1px solid silver">
		</canvas>

		<div id="leyenda2" style="float: left;width: 100%;" class="legend">
			
		</div>
		
	</div>
	<div id="numProgreso" style="width: 130px;float:left;position: absolute; margin-left: calc(100% - 216px);margin-top:182px;text-align: center;font-size: 30px;">
		0.00%
	</div>

	<div style="float: left; width: 100%;margin-top: 70px;">
		
	</div>

	<div id="contenido00" style="float: left; width: 100%;margin-top: 15px;"></div>
	<div id="contenido0" style="float: left; width: 100%;margin-top: 15px;"></div>
	<div id="contenido" style="float: left; width: 100%;margin-bottom: 90px;"></div>
	<!-- <div id="info-log" style="overflow-y: auto;float: left; width: calc(100% - 4px);height: 80px;position: fixed; border:2px solid black; top: calc(100% - 84px);background-color: GAINSBORO"></div> -->
</body>
</html>