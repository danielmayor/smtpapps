#!/usr/bin/php
<?php
	// daily crontab script: it sends an error summary of the messages from the previous day
	// Please put config.ini file outside your document root
	$conf = parse_ini_file (__DIR__."/../../config.ini", true);
 
	// Yesterday's date
	$date = new DateTime();
	$date->sub(new DateInterval('P1D'));
	$ayer = $date->format('Y-m-d');
	$fecha_legible = $date->format('d/m/Y');

	// PostgreSLQ connection & query
	$dbconn = pg_connect("host=".$conf['postgresql']['host']." dbname=".$conf['postgresql']['dbname']." user=".$conf['postgresql']['user']." password=".$conf['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());
	
	$query = 'SELECT postfix.id_log, "from".desde, postfix.para, postfix."time", postfix.status
	FROM fluentd."from", fluentd.postfix
	WHERE "from".queue_id = postfix.queue_id
	AND postfix."time" >= \'' . $ayer . ' 00:00:00\'
        AND postfix."time" <= \'' . $ayer . ' 23:59:59\'
        AND status != \'sent\'';

	iconv_set_encoding("internal_encoding", "utf-8");

	$resultado = pg_exec($query) or die('La consulta original ha fallado: ' . pg_last_error());
	if (($rows = pg_num_rows($resultado)) != 0) {
		$mensaje = "El " . $fecha_legible . " ha habido problemas con " . $rows . " mensajes.<p>";
		$mensaje .= "<table border=1px>\n";
		$mensaje .= "\t<tr>\n";
		$mensaje .= "\t<td>id</td>\n";
		$mensaje .= "\t<td>Desde</td>\n";
		$mensaje .= "\t<td>Para</td>\n";
		$mensaje .= "\t<td>Fecha & hora</td>\n";
		$mensaje .= "\t<td>Estado</td>\n";
		$mensaje .= "\t<td>Detalles</td>\n";
		$mensaje .= "\t</tr>\n";
		while ($line = pg_fetch_array($resultado, null, PGSQL_ASSOC)) {
	       		$mensaje .= "\t<tr>\n";
		        foreach ($line as $col_value) {
				$mensaje .= "\t\t<td>$col_value</td>\n";
			}
			$mensaje .= "\t\t<td><a href=\"".$conf['server']['url']."detalle.php?id=$line[id_log]\">Ver</a></td>\n";
			$mensaje .= "\t</tr>\n";
		}
		$mensaje .= "</table>\n";
		$mensaje .= "<br>";
	
		// Result set release
		pg_free_result($resultado);

		// DB connection closure
		pg_close($dbconn);

		// Email generator
		$para = $conf['mail']['para'];
		$asunto = $conf['mail']['asunto'] . $fecha_legible;
		$cabeceras = 'From: ' .$conf['mail']['desde'] . "\r\n" .
			'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=ISO-8859-1' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
  
		// Email dispatch
		echo $enviado=mail($para, utf8_decode($asunto), $mensaje, $cabeceras) . "\n";
	}
 
?>