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
	$dbconn = pg_connect("host=".$_SESSION['config']['postgresql']['host']." dbname=".$_SESSION['config']['postgresql']['dbname']." user=".$_SESSION['config']['postgresql']['user']." password=".$_SESSION['config']['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());

	$query = 'SELECT postfix.id_log, "from".desde, postfix.para, postfix."time", postfix.status
	FROM fluentd."from", fluentd.postfix 
	WHERE "from".queue_id = postfix.queue_id';

	if (!empty($_POST['field_1'])) {				// field_1 = remitente
		$remitente = ' AND "from".desde = ' . '\'' . $_POST['field_1'] . '\'';
		$query .= $remitente;
	}
	if (!empty($_POST['field_2'])) {				// field_2 = destinatario
		$destinatario = ' AND postfix.para = ' . '\'' . $_POST['field_2'] . '\'';
		$query .= $destinatario;
	}
	if (!empty($_POST['field_3']))				// field_3 = fecha de inicio
		$finicio = ' AND postfix."time" >= ' . '\'' . $_POST['field_3'];
	else
		$finicio = ' AND postfix."time" >= ' . '\'2016-03-10';
	$query .= $finicio;

	if (!empty($_POST['field_4'])) 				// field_4 = hora de inicio
		$hinicio = " " . $_POST['field_4'] . "'";
	else
			$hinicio = " 0:00'";
	$query .= $hinicio;

	if (!empty($_POST['field_5']))				// field_5 = fecha de fin
		$ffin = ' AND postfix."time" <= ' . '\'' . $_POST['field_5'];
	else
		$ffin = ' AND postfix."time" <= ' . '\'2050-12-31';
	$query .= $ffin;

	if (!empty($_POST['field_6'])) 				// field_6 = hora de fin
		$hfin = " " . $_POST['field_6'] . "'";
	else
		$hfin = " 23:59'";

	$query .= $hfin;
	/*$query .= $hfin . " ORDER BY postfix.\"time\" ASC";*/

	$result = pg_exec($query) or die('La consulta original ha fallado: ' . pg_last_error());

	if (($rows = pg_num_rows($result)) == 0) {}		// 0 resultados.

	/* Debug:
	echo "Query: " . $query;
	echo "<br>";
	echo "Post field_1 (remitente): " .  $_POST['field_1'];
	echo "<br>";
	echo "Post field_2 (destinatario): " .  $_POST['field_2'];
	echo "<br>";
	echo "Post field_3 (fecha inicio): " .  $_POST['field_3'];
	echo "<br>";
	echo "Post field_4 (hora inicio): " .  $_POST['field_4'];
	echo "<br>";
	echo "Post field_5 (fecha fin): " .  $_POST['field_5'];
	echo "<br>";
	echo "Post field_6 (hora fin): " .  $_POST['field_6'];
	echo "<br>";
	echo "<br>";
	*/

	// Imprimiendo los resultados en HTML
	echo "<table border=1px>\n";
	echo "\t<tr>\n";
	echo "\t<td>id</td>\n";
	echo "\t<td>Desde</td>\n";
	echo "\t<td>Para</td>\n";
	echo "\t<td>Fecha & hora</td>\n";
	echo "\t<td>Estado</td>\n";
	// echo "\t<td>Detalles</td>\n";
	echo "\t</tr>\n";
	while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
		echo "\t<tr>\n";
		foreach ($line as $col_value) {
			echo "\t\t<td>$col_value</td>\n";
		}
		echo "\t\t<td><a href=\"detalle.php?id=$line[id_log]\">Ver</a></td>\n";
		echo "\t</tr>\n";
	}
	echo "</table>\n";
	echo "<br>";
	echo $rows . " resultados.";

	// Liberando el conjunto de resultados
	pg_free_result($result);

	// Cerrando la conexión
	pg_close($dbconn);

?>

</label></li></ul>
<BR/>
</div>
<div id="footer">
<p class="footer"><a class=footer href=form.php>Volver</a></p>
</div>

</body>
</html>