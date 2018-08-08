<?php

session_id('q7c37qg6btglsr4774pg7cvjr6');

require_once("config.php");

session_regenerate_id();

echo session_id() . "<br>";

var_dump($_SESSION);

?>