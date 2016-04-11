<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>SMTPapps - Búsqueda de registros de envíos</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"><link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="mainForm">
<div id="formHeader">
	<h2 class="formInfo">SMTPapps</h2>
	<p class="formInfo">Búsqueda de registros de envíos</p>
</div>

<BR/><ul class=mainForm><li class=mainForm><label class=formFieldQuestion>

<?php

	require_once("session_start.php");
	
	// PostgreSLQ connection & query
	$dbconn_cola = pg_connect("host=".$_SESSION['config']['postgresql']['host']." dbname=".$_SESSION['config']['postgresql']['dbname']." user=".$_SESSION['config']['postgresql']['user']." password=".$_SESSION['config']['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());

	$query_cola = "SELECT DISTINCT postfix.id_log, \"from\".desde, postfix.para, postfix.\"time\", postfix.status
	FROM fluentd.\"from\", fluentd.postfix
	WHERE \"from\".queue_id = postfix.queue_id
	AND postfix.queue_id = '" . $_GET['id'] . "'
	ORDER BY postfix.\"time\" ASC";


	$result_cola = pg_exec($query_cola) or die('La consulta ha fallado: ' . pg_last_error());

	// Imprimiendo los resultados en HTML
	echo "ID de cola: " . $_GET['id'];
	echo "<p>";
	echo "<table border=1px>\n";
	echo "\t<tr>\n";
	echo "\t<td>id</td>\n";
	echo "\t<td>Desde</td>\n";
	echo "\t<td>Para</td>\n";
	echo "\t<td>Fecha & hora</td>\n";
	echo "\t<td>Estado</td>\n";
	echo "\t<td>Detalles</td>\n";
	echo "\t</tr>\n";
	while ($line = pg_fetch_array($result_cola, null, PGSQL_ASSOC)) {
		echo "\t<tr>\n";
		foreach ($line as $col_value) {
			echo "\t\t<td>$col_value</td>\n";
		}
		echo "\t\t<td><a href=\"detalle.php?id=$line[id_log]\">Ver</a></td>\n";
		echo "\t</tr>\n";
	}
	echo "</table>\n";
	echo "<br>";
	// echo $rows . " resultados.";
	
	// Liberando el conjunto de resultados
	pg_free_result($result_cola);
	
	// Cerrando la conexión
	pg_close($dbconn_cola);
	
	?>
	
</label></li></ul>
<BR/>
</div>
<div id="footer">
<p class="footer"><a class=footer href=form.php>Inicio</a></p>
</div>

</body>
</html>