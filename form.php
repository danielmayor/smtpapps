<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>SMTPapps - Búsqueda de registros de envíos</title>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"><link href="css/style.css" rel="stylesheet" type="text/css">
	<!-- calendar stuff -->
	      <link rel="stylesheet" type="text/css" href="calendar/calendar-blue2.css" />
	      <script type="text/javascript" src="calendar/calendar.js"></script>
	      <script type="text/javascript" src="calendar/calendar-en.js"></script>
	      <script type="text/javascript" src="calendar/calendar-setup.js"></script>
	<!-- END calendar stuff -->

    <!-- expand/collapse function -->
    <SCRIPT type=text/javascript>
	<!--
	function collapseElem(obj)
	{
		var el = document.getElementById(obj);
		el.style.display = 'none';
	}

	function expandElem(obj)
	{
		var el = document.getElementById(obj);
		el.style.display = '';
	}

	//-->
	</SCRIPT>
	<!-- expand/collapse function -->

	<!-- expand/collapse function -->
	    <SCRIPT type=text/javascript>
		<!--

		// collapse all elements, except the first one
		function collapseAll()
		{
			var numFormPages = 1;
				for(i=2; i <= numFormPages; i++)
			{
				currPageId = ('mainForm_' + i);
				collapseElem(currPageId);
			}
		}

		//-->
		</SCRIPT>
	<!-- expand/collapse function -->

	 <!-- validate -->
	<SCRIPT type=text/javascript>
	<!--
		function validateField(fieldId, fieldBoxId, fieldType, required)
		{
			fieldBox = document.getElementById(fieldBoxId);
			fieldObj = document.getElementById(fieldId);
			if(fieldType == 'text'  ||  fieldType == 'textarea'  ||  fieldType == 'password'  ||  fieldType == 'file'  ||  fieldType == 'phone'  || fieldType == 'website')
			{	
				if(required == 1 && fieldObj.value == '')
				{
					fieldObj.setAttribute("class","mainFormError");
					fieldObj.setAttribute("className","mainFormError");
					fieldObj.focus();
					return false;					
				}
			}

			else if(fieldType == 'menu'  || fieldType == 'country'  || fieldType == 'state')
			{	
				if(required == 1 && fieldObj.selectedIndex == 0)
				{				
					fieldObj.setAttribute("class","mainFormError");
					fieldObj.setAttribute("className","mainFormError");
					fieldObj.focus();
					return false;					
				}

				}


				else if(fieldType == 'email')
				{	
					if((required == 1 && fieldObj.value=='')  ||  (fieldObj.value!=''  && !validate_email(fieldObj.value)))
					{				
						fieldObj.setAttribute("class","mainFormError");
						fieldObj.setAttribute("className","mainFormError");
						fieldObj.focus();
						return false;					
					}

				}
		}

		function validate_email(emailStr)
		{		
			apos=emailStr.indexOf("@");
			dotpos=emailStr.lastIndexOf(".");
			if (apos<1||dotpos-apos<2) 
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		function validateDate(fieldId, fieldBoxId, fieldType, required,  minDateStr, maxDateStr)
		{
			retValue = true;
			fieldBox = document.getElementById(fieldBoxId);
			fieldObj = document.getElementById(fieldId);	
			dateStr = fieldObj.value;

			if(required == 0  && dateStr == '')
			{
				return true;
			}

			if(dateStr.charAt(2) != '/'  || dateStr.charAt(5) != '/' || dateStr.length != 10)
			{
				retValue = false;
			}	
			else	// format's okay; check max, min
			{
				currDays = parseInt(dateStr.substr(0,2),10) + parseInt(dateStr.substr(3,2),10)*30  + parseInt(dateStr.substr(6,4),10)*365;
				//alert(currDays);
				if(maxDateStr != '')
				{
					maxDays = parseInt(maxDateStr.substr(0,2),10) + parseInt(maxDateStr.substr(3,2),10)*30  + parseInt(maxDateStr.substr(6,4),10)*365;
					//alert(maxDays);
					if(currDays > maxDays)
							retValue = false;
				}

				if(minDateStr != '')
				{
					minDays = parseInt(minDateStr.substr(0,2),10) + parseInt(minDateStr.substr(3,2),10)*30  + parseInt(minDateStr.substr(6,4),10)*365;
					//alert(minDays);
					if(currDays < minDays)
						retValue = false;
				}
			}

			if(retValue == false)
			{
				fieldObj.setAttribute("class","mainFormError");
				fieldObj.setAttribute("className","mainFormError");
				fieldObj.focus();
				return false;
			}
		}
		//-->
		</SCRIPT>
		<!-- end validate -->

</head>

<body onLoad="collapseAll()">

	<div id="mainForm">

		<div id="formHeader">
			<h2 class="formInfo">SMTPapps</h2>
			<p class="formInfo">Búsqueda de registros de envíos</p>
		</div>

		<BR/><!-- begin form -->
		<form method=post enctype=multipart/form-data action=processor.php onSubmit="return validatePage1();"><ul class=mainForm id="mainForm_1">

				<li class="mainForm" id="fieldBox_1">
				<label class="formFieldQuestion">E-mail del remitente</label>
				<select class="mainForm" name="field_1" id="field_1">
<?php

	session_start();
	
	$_SESSION['conf'] = parse_ini_file(__DIR__."/../../config.ini", true);
	echo "SESSION conf host: " .$_SESSION['conf']['postgresql']['host'];

	// PostgreSLQ connection & query
	$dbconn_menu = pg_connect("host=".$_SESSION['conf']['postgresql']['host']." dbname=".$_SESSION['conf']['postgresql']['dbname']." user=".$_SESSION['conf']['postgresql']['user']." password=".$_SESSION['conf']['postgresql']['password']) or die("No se ha podido conectar: " . pg_last_error());
	$query_from = "SELECT distinct \"from\".desde FROM fluentd.\"from\" ORDER BY desde ASC";
	$result_from = pg_exec($dbconn_menu,$query_from);

	while ($row = pg_fetch_assoc($result_from))
        echo "<option value=\"" . htmlspecialchars($row['desde']) . "\">" . htmlspecialchars($row['desde']) . "</option>";
	
	pg_free_result($result_from);

	echo "</select>";
	echo "</li>";
	echo "<li class=\"mainForm\" id=\"fieldBox_2\">";
	echo "<label class=\"formFieldQuestion\">E-mail del destinatario</label>";
	echo "<select class=\"mainForm\" name=\"field_2\" id=\"field_2\">";
	$query_para = "SELECT distinct postfix.\"para\" FROM fluentd.postfix ORDER BY para ASC";
	$result_para = pg_exec($dbconn_menu,$query_para);

	echo "<option value=\"\"></option>";
	while ($row = pg_fetch_assoc($result_para))
        echo "<option value=\"" . htmlspecialchars($row['para']) . "\">" . htmlspecialchars($row['para']) . "</option>";
	
	pg_free_result($result_para);
	pg_close($dbconn_menu);

?>
</select>
</li>
	<li class="mainForm" id="fieldBox_3">
		<label class="formFieldQuestion">Fecha de inicio</label>
		<input type=text  name=field_3 id=field_3 value=""><button type=reset class=calendarStyle id=fieldDateTrigger_3></button>
		<script type='text/javascript'>   Calendar.setup({
					inputField     :    "field_3",   
					ifFormat       :    "%Y-%m-%d",   
					showsTime      :    false,          
					button         :    "fieldDateTrigger_3",
					singleClick    :    true,           
					step           :    1                
					});</script></li>
	<li class="mainForm" id="fieldBox_4">
		<label class="formFieldQuestion">Hora de inicio&nbsp;<a class=info href=#><img src=imgs/tip_small.png border=0>
		<span class=infobox>Formato hh:mm:ss</span></a></label>
		<input class=mainForm type=text name=field_4 id=field_4 size='8' value='00:00:00'></li>

	<li class="mainForm" id="fieldBox_5">
		<label class="formFieldQuestion">Fecha de fin</label>
		<input type=text  name=field_5 id=field_5 value=""><button type=reset class=calendarStyle id=fieldDateTrigger_5></button>
		<script type='text/javascript'>   Calendar.setup({
					inputField     :    "field_5",   
					ifFormat       :    "%Y-%m-%d",   
					showsTime      :    false,          
					button         :    "fieldDateTrigger_5",
					singleClick    :    true,           
					step           :    1                
					});</script></li>

	<li class="mainForm" id="fieldBox_6">
		<label class="formFieldQuestion">Hora de fin&nbsp;<a class=info href=#><img src=imgs/tip_small.png border=0>
		<span class=infobox>Formato hh:mm:ss</span></a></label>
		<input class=mainForm type=text name=field_6 id=field_6 size='8' value='23:59:59'></li>
		
		<!-- end of this page -->

		<!-- page validation -->
		<script type=text/javascript>
		<!--
/*			function validatePage1()
			{
				retVal = true;
				if (validateField('field_1','fieldBox_1','text',0) == false)
				 retVal=false;
				if (validateField('field_2','fieldBox_2','text',0) == false)
				 retVal=false;
				if (validateDate('field_3','fieldBox_3','date',0,'','') == false)
				 retVal=false;
				if (validateField('field_4','fieldBox_4','text',0) == false)
				 retVal=false;
				if (validateDate('field_5','fieldBox_5','date',0,'','') == false)
				 retVal=false;
				if (validateField('field_6','fieldBox_6','text',0) == false)
				 retVal=false;
				if(retVal == false)
				{
					alert('Al menos un campo debe ser rellenado.');
					return false;
				}
				return retVal;
			}
*/		-->
		</script>

		<!-- end page validaton -->



		<!-- next page buttons -->
				<li class="mainForm">
					<input id="saveForm" class="mainForm" type="submit" value="Enviar" />
				</li>

			</form>
			<!-- end of form -->
		<!-- close the display stuff for this page -->
		</ul></div>
		
		<div id="footer">
			<p class="footer"><a class=footer href=http://phpformgen.sourceforge.net>Powered by phpFormGenerator</a></p>
		</div>

</body>
</html>