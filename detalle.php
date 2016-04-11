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
	<BR/>
	<ul class=mainForm><li class=mainForm><label class=formFieldQuestion>

<?php

	require_once("session_start.php");

	// PostgreSLQ connection & query
	$dbconn_detalle = pg_connect("host=".$_SESSION['config']['postgresql']['host']." dbname=".$_SESSION['config']['postgresql']['dbname']." user=".$_SESSION['config']['postgresql']['user']." password=".$_SESSION['config']['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());

	$query_detalle = "SELECT 
  \"from\".desde, 
  postfix.para, 
  postfix.status, 
  postfix.status_detail, 
  \"from\".queue_id, 
  \"from\".size, 
  \"from\".nrcpt, 
  postfix.domain, 
  postfix.relay, 
  postfix.delay, 
  postfix.delays, 
  postfix.dsn, 
  postfix.process, 
  postfix.hostname, 
  postfix.\"time\" 
  FROM fluentd.\"from\", fluentd.postfix 
  WHERE \"from\".queue_id = postfix.queue_id 
  AND postfix.id_log = '" . $_GET['id'] . "'";

	$result_detalle = pg_exec($query_detalle) or die('La consulta ha fallado: ' . pg_last_error());

	// Imprimiendo los resultados en HTML
	echo "<table border=1px>\n";
	$line = pg_fetch_array($result_detalle, null, PGSQL_ASSOC);
	echo "\t\t<tr><td><b>Remitente</td><td>$line[desde]</td></tr>\n";
	echo "\t\t<tr><td><b>Destinatario</td><td>$line[para]</td></tr>\n";
	echo "\t\t<tr><td><b>Estado de envío</td><td>$line[status]</td></tr>\n";
	echo "\t\t<tr><td><b>Detalle del estado</td><td>$line[status_detail]</td></tr>\n";
	echo "\t\t<tr><td><b>Fecha y hora</td><td>$line[time]</td></tr>\n";
	echo "\t\t<tr><td><b>Número de destinatarios</td><td>$line[nrcpt]</td></tr>\n";
	echo "\t\t<tr><td><b>ID de cola </td><td><a href=\"cola.php?id=$line[queue_id]\">$line[queue_id] (clic para más detalle)</a></td></tr>\n";
	echo "\t\t<tr><td><b>Tamaño (bytes)</td><td>$line[size]</td></tr>\n";
	echo "\t\t<tr><td><b>Dominio</td><td>$line[domain]</td></tr>\n";
	echo "\t\t<tr><td><b>Relay</td><td>$line[relay]</td></tr>\n";
	echo "\t\t<tr><td><b>Retardo (segundos)</td><td>$line[delay]</td></tr>\n";
	echo "\t\t<tr><td><b>Retardo desglosado</td><td>$line[delays]</td></tr>\n";
	echo "\t\t<tr><td><b>Notificaciones de estado de entrega (DSN)</td><td>$line[dsn]</td></tr>\n";
	echo "\t\t<tr><td><b>Nombre del host</td><td>$line[hostname]</td></tr>\n";
	echo "\t\t<tr><td><b>Proceso</td><td>$line[process]</td></tr>\n";
	echo "</table>\n";
	echo "<br>";
	echo "Leyenda:<br>";
	echo "Retardo desglosado: a/b/c/d<br>";
	echo "a=tiempo antes del gestor de colas, incluyendo tranmisión del mensaje.<br>";
	echo "b=tiempo en el gestor de colas.<br>";
	echo "c=tiempo de establecimiento de conexión, incluyendo DNS, HELO y TLS.<br>";
	echo "d=tiempo de transmisión del mensaje.<br>";

	// Liberando el conjunto de resultados
	pg_free_result($result_detalle);

	// Cerrando la conexión
	pg_close($dbconn_detalle);

?>

	</label></li></ul>
	<BR/>
</div>
<div id="footer">
	<p class="footer"><a class=footer href=form.php>Inicio</a></p>
</div>

</body>
</html>