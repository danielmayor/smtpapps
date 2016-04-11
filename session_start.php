<?php
   session_start();
   $_SESSION['config'] = parse_ini_file(__DIR__."/../../config.ini", true);
?>