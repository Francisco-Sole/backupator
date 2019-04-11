<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Magic Backup</title>
	<link rel="stylesheet" href="">
	<script src="jquery.js" type="text/javascript" charset="utf-8"></script>
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
		var datos;
		var con0 = 0;
		var segLapso = 0;
		var minLapso = 0;
		var horaLapso = 0;
		var totalRows = 0;
		var oksreg = 0;

		reloj();
		dameTodasTablas()

		function cargaCountTablas(tabla){
			var datos = {"tabla": tabla};
			$.ajax({
				url: 'cargar_count_tablas.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					indice++;
					if (indice > numDeTablas) {
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

					totalRows+= parseInt(data.count);
					
					tablasLocal.push(data);
					var temp = Array(Math.ceil(data.count/1000)).fill(0);
					tamanos[data.nombre] = Array(1).fill(temp);
					
					var html = "";
					html = "<div style='width:25%;float:left;margin-bottom:5px;'><div style='float:left; width: 100%;margin-left:50px;font-weight:900;position:absolute; margin-top:1px;' id='row_"+data.nombre+"'><span id='nombreTabla_"+data.nombre+"'>"+data.nombre+"</span><span id='countTabla_"+data.nombre+"' style='font-weight:400; font-style: italic;'> <span id='contador_"+data.nombre+"'>0</span>/<span id='contadorMAX_"+data.nombre+"'>"+data.count+"</span></div><div style='float:left; width:80%; margin-left:10%; border: 1px solid black'><div id='carga_"+data.nombre+"' class='barraCarga' cargado='"+((0/data.count)*100) +"' style='max-width:100%; overflow-x:hidden;background-color: KHAKI; width:"+((0/data.count)*100) +"'>&nbsp;</div></div></div>"
					$("#info-tablas").append(html);	
					if (data.count == 0) {
						$("#carga_"+data.nombre).css("background-color","tomato");
					}	
				},
				dataType : 'JSON'
			});
		}

		function dameTodasTablas(){
			$.ajax({
				url: 'dame_tablas.php',
				success: function (data) {
					numDeTablas = data.length-1;
					for (var i = 0; i < data.length; i++) {
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
			var temporizadoFinal = setInterval(function(){ 
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
			for (var i = 0; i < tablasLocal.length; i++) {
				do_backup(tablasLocal[i].nombre, tablasLocal[i].count, 0, 0);
				console.log("Lanzada la peticion de: "+ tablasLocal[i].nombre );
			}
			console.log("Lanzadas todas las peticiones.");
		}		

		function do_backup(nombre, count, current, index){
			actualizaDatos();
			var datos = {"tabla": nombre, "count":count, "current": current, "indice": index};
			$.ajax({
				url: 'backup.php',
				data: datos,
				type: 'GET',
				success: function (data) {
					if (data.indice == 0) {
						tamanos[data.tabla][0][0] = 1;
						oksreg++;
					}

					if (parseInt(data.count) > parseInt(data.current)+10000) 
					{
						if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] == 0){
							tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] = 1;
							oksreg++;

							do_backup(data.tabla, data.count, parseInt(data.current)+1000, 1);
							$("#contador_"+data.tabla).html(parseInt(data.current)+1000);
							$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+1000)/parseInt(data.count))*100+"%");
							$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+1000)/parseInt(data.count))*100);
						}
						if (parseInt(data.count) > parseInt(data.current)+2000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+2000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+2000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+2000,2);
								$("#contador_"+data.tabla).html(parseInt(data.current)+2000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+2000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+2000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+3000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+3000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+3000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+3000,3);
								$("#contador_"+data.tabla).html(parseInt(data.current)+3000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+3000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+3000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+4000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+4000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+4000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+4000,4);
								$("#contador_"+data.tabla).html(parseInt(data.current)+4000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+4000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+4000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+5000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+5000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+5000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+5000,5);
								$("#contador_"+data.tabla).html(parseInt(data.current)+5000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+5000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+5000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+6000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+6000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+6000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+6000,6);
								$("#contador_"+data.tabla).html(parseInt(data.current)+6000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+6000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+6000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+7000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+7000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+7000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+7000,7);
								$("#contador_"+data.tabla).html(parseInt(data.current)+7000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+7000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+7000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+8000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+8000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+8000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+8000,8);
								$("#contador_"+data.tabla).html(parseInt(data.current)+8000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+8000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+8000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+9000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+9000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+9000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+9000,9);
								$("#contador_"+data.tabla).html(parseInt(data.current)+9000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+9000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+9000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+10000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+10000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+10000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+10000,10);
								$("#contador_"+data.tabla).html(parseInt(data.current)+10000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+10000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+10000)/parseInt(data.count))*100);
							}
						}
					}
					else if (parseInt(data.count) > parseInt(data.current)+5000) 
					{
						if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] == 0){
							tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] = 1;
							oksreg++;

							do_backup(data.tabla, data.count, parseInt(data.current)+1000, 1);
							$("#contador_"+data.tabla).html(parseInt(data.current)+1000);
							$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+1000)/parseInt(data.count))*100+"%");
							$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+1000)/parseInt(data.count))*100);
						}
						if (parseInt(data.count) > parseInt(data.current)+2000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+2000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+2000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+2000,2);
								$("#contador_"+data.tabla).html(parseInt(data.current)+2000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+2000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+2000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+3000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+3000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+3000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+3000,3);
								$("#contador_"+data.tabla).html(parseInt(data.current)+3000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+3000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+3000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+4000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+4000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+4000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+4000,4);
								$("#contador_"+data.tabla).html(parseInt(data.current)+4000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+4000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+4000)/parseInt(data.count))*100);
							}
						}
						if (parseInt(data.count) > parseInt(data.current)+5000) {
							if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+5000)/1000)] == 0){
								tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+5000)/1000)] = 1;
								oksreg++;

								do_backup(data.tabla, data.count, parseInt(data.current)+5000,5);
								$("#contador_"+data.tabla).html(parseInt(data.current)+5000);
								$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+5000)/parseInt(data.count))*100+"%");
								$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+5000)/parseInt(data.count))*100);
							}
						}
					}else if (parseInt(data.count) > parseInt(data.current)+1000)
					{
						if (tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] == 0){
							tamanos[data.tabla][0][Math.ceil((parseInt(data.current)+1000)/1000)] = 1;
							oksreg++;

							do_backup(data.tabla, data.count, parseInt(data.current)+1000, 1);
							$("#contador_"+data.tabla).html(parseInt(data.current)+1000);
							$("#carga_"+data.tabla).css("width", ((parseInt(data.current)+1000)/parseInt(data.count))*100+"%");
							$("#carga_"+data.tabla).attr("cargado", ((parseInt(data.current)+1000)/parseInt(data.count))*100);
						}
					}
					else
					{
						$("#contador_"+data.tabla).html(data.count);
						$("#carga_"+data.tabla).css("width", "100%");
						$("#carga_"+data.tabla).attr("cargado", "100");
					}
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
	var porcentaje = (oks / (tablasLocal.length-1) )*100;
	$("#porcentageTablas").html(porcentaje.toFixed(2)+"%");

	var totalRegistros = Math.ceil(parseInt($("#totalfilas").html())/1000);

			//console.log(oksreg);
	//console.log(totalRegistros);

	var porReg = (oksreg/(totalRegistros))*100;
	$("#porcentagefilas").html(porReg.toFixed(2)+"%");
}


</script>
<div id="info-tablas">
	<div id="info-cargando" style="display: block; font-weight: 900; margin-bottom: 10px;margin-top: 10px; margin-left: 10px;">
		Cargando informacion de la base de datos<span id="cargando"></span>
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
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Hora inicio: </b><span id="horaI"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Hora fin: </b><span id="horaF"></span></div>
		<div style="border: 1px solid black; float: left;margin-right: 5px;padding: 5px;"><b>Lapso: </b><span id="lapso"></span></div>
	</div>
</div>
</body>
</html>