<?php
/**
 * @name index.php
 * @category Dispatcher
 * @author web-Projet.com (jean-luc.aubert@web-projet.com)
 * @version 0.1.0
 **/
use App\apiLoader as api;

ini_set("display_errors", true);
error_reporting(E_ALL);

if(file_exists("App/apiLoader.class.php")){
	require_once("App/appLoader.class.php");
	require_once("App/apiLoader.class.php");
} else
	die("Impossible de charger la classe : App/apiLoader.class.php");


	
$app = new api(dirname(__FILE__));
?>