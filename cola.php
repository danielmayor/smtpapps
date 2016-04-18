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

	<br><ul class=mainForm><li class=mainForm>

	<?php

		require_once("session_start.php");
	
		// PostgreSLQ connection & query
		$dbconn_cola = pg_connect("host=".$_SESSION['config']['postgresql']['host']." dbname=".$_SESSION['config']['postgresql']['dbname']." user=".$_SESSION['config']['postgresql']['user']." password=".$_SESSION['config']['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());

		$query_cola = "SELECT DISTINCT postfix.id_log, \"from\".desde, postfix.para, postfix.\"time\", postfix.status
		FROM fluentd.\"from\", fluentd.postfix
		WHERE \"from\".queue_id = postfix.queue_id
		AND postfix.queue_id = '" . $_GET['id'] . "'
		ORDER BY postfix.id_log ASC";

		$result_cola = pg_exec($query_cola) or die('La consulta ha fallado: ' . pg_last_error());

		// Imprimiendo los resultados en HTML
		echo "ID de cola: " . $_GET['id'];
		echo "<p>";
		echo "<table class='resultTable'>\n";
		echo "\t<tr>\n";
		echo "\t<td class='cabecera'>id</td>\n";
		echo "\t<td class='cabecera'>Desde</td>\n";
		echo "\t<td class='cabecera'>Para</td>\n";
		echo "\t<td class='cabecera'>Fecha & hora</td>\n";
		echo "\t<td class='cabecera'>Estado</td>\n";
		echo "\t<td class='cabecera'>Detalles</td>\n";
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
		</li></ul>
		<br>
	</div>
	<div id="footer">
	<p class="footer"><a class=footer href=form.php>Inicio</a></p>
	</div>

</body>
</html>
