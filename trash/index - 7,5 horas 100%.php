<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Magic Backup</title>
	<link rel="stylesheet" href="">
	<script src="jquery.js" type="text/javascript" charset="utf-8"></script>
	<script src="jquery-ui.js" type="text/javascript" charset="utf-8"></script>
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
				segLapso++;
				if (segLapso == 60) {
					segLapso = 0;
					minLapso++;
				}
				if (minLapso == 60) {
					minLapso = 0;
					horaLapso++;
				}
				
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

			for (var i = 0; i < tamanos.length; i++) {
				if (parseInt(tamanos[i].tramos) <= 200) {
					maxGlobalpequenas+=parseInt(tamanos[i].tramos);
					numTablaPequenas++;
					do_backup(tamanos[i].nombre, tamanos[i].count, 0, 0, i);
				}else{
					//$("#info-log").append("---> Tabla omitida: "+tamanos[i].nombre+". A la espera de la segunda parte.<br>");
				}
			}
		}		

		function do_backup(nombre, count, current, variable, indice){
			solicitudTramos++;
			//console.log(solicitudTramos, " / ", oksreg);
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
					
					//pintamos barras
					$("#contador_"+data.tabla).html(parseInt(data.current)-1000);
					$("#carga_"+data.tabla).css("width", ((parseInt(data.current)-1000)/parseInt(data.count))*100+"%");
					$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)-1000)/parseInt(data.count))*100);



					//$("#carga_"+tamanos[data.indice].nombre).attr("cargado", ((parseInt(data.current)-1000)/parseInt(data.count))*100);
					
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
			solicitudTramos += 10;
			//console.log(solicitudTramos, " / ", oksreg);
			var datos = {"tabla": nombre, "count":count, "current": current, "variable": variable, "indice": indice};
			$.ajax({
				url: 'backupG.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					//actulizo el numero de tramos descargados
					
					if (parseInt(data.variable)-20 < 0) {
						data.variable = 0;
					}else{
						data.variable -= 20;
					}

					for (var i = 0; i <= 20; i++) {
						tamanos[data.indice].realizado[parseInt(data.variable)+i] = 1;
						oksreg++;
						globalGrandes++; 
					}
					//pintamos barras
					$("#contador_"+data.tabla).html(parseInt(data.current));
					$("#carga_"+data.tabla).css("width", ((parseInt(data.current))/parseInt(data.count))*100+"%");
					$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current))/parseInt(data.count))*100);
					
					if (tamanos[data.indice].realizado[parseInt(data.variable)+21] == 0) {
						do_backupG(tamanos[data.indice].nombre, tamanos[data.indice].count, data.current , parseInt(data.variable)+21, data.indice);
					}else{
						do_backup(tamanos[data.indice].nombre, tamanos[data.indice].count, data.current , parseInt(data.variable)+21, data.indice);
					}
					actualizaDatos();
				},
				dataType : 'JSON'
			});
		}

		function actualizaDatos(){
			var oks = 0;
			$(".barraCarga").each(function(index) {
				if($(this).attr("cargado") == 100) {
					oks++;
				};
			});
			var porcentaje = (oks / (tamanos.length-1) )*100;
			$("#porcentageTablas").html(porcentaje.toFixed(2)+"%");

			var totalRegistros = Math.ceil(parseInt($("#totalfilas").html())/1000);

			var porReg = (oksreg/(totalRegistros))*100;
			$("#porcentagefilas").html(porReg.toFixed(2)+"%");

			var efec = (oksreg/(solicitudTramos))*100;
			$("#efectividad").html(efec.toFixed(2)+"%");
			if (parseInt(numTablaPequenas) == parseInt(oks) && !primeraVez) {
				primeraVez = true;
				console.log("final 1 parte.");
				$("#info-cargando div").html("1ª Fase completada. Tiempo invertido: " + $("#lapso").html() + "<br>Continuamos con la 2ª Fase...");
				$("#info-cargando").fadeIn();
				setTimeout(function(){ $("#info-cargando").fadeOut(); }, 3000);
				//$("#info-log").append("-----> FIN DE LA PRIMERA PARTE...<br>INICIAMOS SEGUNDA PARTE.....<br>");
				//$("#info-log").append("-----> Tiempo invertido: " + $("#lapso").html());
				//$("#info-log").stop();

				for (var i = 0; i < tamanos.length; i++) {
					if (parseInt(tamanos[i].tramos) > 200) {
						maxGlobalGrandes+=parseInt(tamanos[i].tramos);
						do_backupG(tamanos[i].nombre, tamanos[i].count, 0, 0, i);
					}
				}

				if ((parseInt(efec) == parseInt(100)) && (maxGlobalGrandes == globalGrandes )) {
					$("#info-cargando div").html("2ª Fase completada. Tiempo invertido: " + $("#lapso").html() + "<br>Fin del backup...");
					console.log("fin",$("#lapso").html());
					$("#info-cargando").fadeIn();
					setTimeout(function(){ $("#info-cargando").fadeOut(); }, 3000);
				}
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
			//reordenamos
			tamanos0.sort(dynamicSort("nombre"));
			for (var i = 0; i < tamanos0.length; i++) {
				html = "<div style='width:25%;float:left;margin-bottom:5px;'><div style='float:left; width: 100%;margin-left:50px;font-weight:900;position:absolute; margin-top:1px;' id='row_"+tamanos0[i].nombre+"'><span id='nombreTabla_"+tamanos0[i].nombre+"' style='margin-left:5px;'>"+tamanos0[i].nombre+"</span><span id='countTabla_"+tamanos0[i].nombre+"' style='font-weight:400; font-style: italic;'> <span id='contador_"+tamanos0[i].nombre+"'>0</span>/<span id='contadorMAX_"+tamanos0[i].nombre+"'>"+tamanos0[i].count+"</span></div><div style='float:left; width:80%; margin-left:10%; border: 1px solid black; border-radius: 8px;'><div id='carga_"+tamanos0[i].nombre+"' class='barraCarga' cargado='"+((0/tamanos0[i].count)*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/tamanos0[i].count)*100) +"%; border-radius: 7px;'>&nbsp;</div></div></div>";
				$("#contenidoBBDD0").append(html);	
				$("#carga_"+tamanos0[i].nombre).css("background-color","TOMATO");
				//LIGHTSEAGREEN
			}

			tamanos.sort(dynamicSort("nombre"));
			for (var i = 0; i < tamanos.length; i++) {
				html = "<div style='width:25%;float:left;margin-bottom:5px;'><div style='float:left; width: 100%;margin-left:50px;font-weight:900;position:absolute; margin-top:1px;' id='row_"+tamanos[i].nombre+"'><span id='nombreTabla_"+tamanos[i].nombre+"' style='margin-left:5px;'>"+tamanos[i].nombre+"</span><span id='countTabla_"+tamanos[i].nombre+"' style='font-weight:400; font-style: italic;'> <span id='contador_"+tamanos[i].nombre+"'>0</span>/<span id='contadorMAX_"+tamanos[i].nombre+"'>"+tamanos[i].count+"</span></div><div style='float:left; width:80%; margin-left:10%; border: 1px solid black; border-radius: 8px;'><div id='carga_"+tamanos[i].nombre+"' class='barraCarga' cargado='"+((0/tamanos[i].count)*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/tamanos[i].count)*100) +"%; border-radius: 7px;'>&nbsp;</div></div></div>";
				$("#contenidoBBDD1").append(html);	
			}

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

	</script>
	<div id="info-tablas">
		<div id="info-cargando" style="display: block;position: fixed;width: 100%; height: 100%; font-weight: 900;background-color: rgba(0,0,0,0.3); z-index: 9999;">
			<div style="background-color: white;background-color: white;margin-left: 25%;padding: 25px;width: 50%;margin-top: 15%;z-index: 99999;border: 2px solid black;text-align: center;position: fixed;">
				Cargando informacion de la base de datos<span id="cargando"></span>	
				<div id="estados-cargando" style="float: left; width: 100%;margin-top: 5px; margin-bottom: 10px;">
					<span id='info-cargando-1' style="float: left; width: 100%;margin-top: 5px;" class="current-text">Leyendo numero de tablas</span>
				</div>

			</div>
		</div>
		<div id="info-boton" style="display: none; width: 98%; margin-bottom: 10px; margin-left: 10px;margin-top: 10px">
			<input type="button" name="backup" value="backup" onclick="backup();">
		</div>
		<div style="width: 100%;float: left; margin-left: 48px;margin-bottom: 10px;">
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Total tablas: </b><span id="totalTablas"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Total tablas con 0 registros: </b><span id="totalTablas0registros"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Porcentaje tablas descargadas: </b><span id="porcentageTablas"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Total filas: </b><span id="totalfilas"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Porcentaje filas descargadas: </b><span id="porcentagefilas"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Efectividad: </b><span id="efectividad"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Hora inicio: </b><span id="horaI"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Hora fin: </b><span id="horaF"></span></div>
			<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Lapso: </b><span id="lapso"></span></div>
		</div>
		<div id="contenido0" style="float: left; width: 100%;"></div>
		<div id="contenido" style="float: left; width: 100%;margin-bottom: 90px;"></div>
		<!-- <div id="info-log" style="overflow-y: auto;float: left; width: calc(100% - 4px);height: 80px;position: fixed; border:2px solid black; top: calc(100% - 84px);background-color: GAINSBORO"></div> -->


	</div>
</body>
</html>