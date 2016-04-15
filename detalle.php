<!doctype html>
<html>
<head>
	<title>SMTPapps - Búsqueda de registros de envíos</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"><link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="mainForm">
	<div id="formHeader">
		<h2 class="formInfo">SMTPapps</h2>
		<p class="formInfo">Búsqueda de registros de envíos</p>
	</div>
	<br>
	<ul class=mainForm><li class=mainForm>

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
	echo "<table class='resultTable'>\n";
	$line = pg_fetch_array($result_detalle, null, PGSQL_ASSOC);
	echo "\t\t<tr><td class='cabecera'>Remitente</td><td>$line[desde]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Destinatario</td><td>$line[para]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Estado de envío</td><td>$line[status]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Detalle del estado</td><td>$line[status_detail]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Fecha y hora</td><td>$line[time]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Número de destinatarios</td><td>$line[nrcpt]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>ID de cola </td><td><a href=\"cola.php?id=$line[queue_id]\">$line[queue_id] (clic para más detalle)</a></td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Tamaño (bytes)</td><td>$line[size]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Dominio</td><td>$line[domain]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Relay</td><td>$line[relay]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Retardo (segundos)</td><td>$line[delay]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Retardo desglosado</td><td>$line[delays]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Notificaciones de estado de entrega (DSN)</td><td>$line[dsn]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Nombre del host</td><td>$line[hostname]</td></tr>\n";
	echo "\t\t<tr><td class='cabecera'>Proceso</td><td>$line[process]</td></tr>\n";
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

	</li></ul>
	<br>
</div>
<div id="footer">
	<p class="footer"><a class=footer href=form.php>Inicio</a></p>
</div>

</body>
</html>
