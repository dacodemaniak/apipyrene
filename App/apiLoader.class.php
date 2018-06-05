<?php
/**
 * @name appLoader.class.php Service de chargement de l'application
 * @author web-Projet.com (jean-luc.aubert@web-projet.com) - Juin 2016
 * @package App
 * @version 0.1.0
**/
namespace App;

use \App\appLoader as AppLoader;
use \wp\Utilities\Pathes\pathes as Pathes;
use \wp\Http\Response\jsonResponse as Response;

class apiLoader extends AppLoader {
	
	/**
	 * Instancie une nouvelle application
	 * @param string $appRootPath
	 */
	public function __construct($appRootPath){
		
		$this->toRoot = $this->toRoot($appRootPath);
		
		// Lit les fichiers de configuration de l'application
		$this->readConfiguration();
		
		self::$controllers = array();
		self::$activeRecords = array();
		
		date_default_timezone_set("Europe/Paris");
		
		// Charge le framework global
		$fullFrameworkPath = $this->appConfig->framework->root . "webprojet.framework-" . $this->appConfig->framework->version . "/wp.class.php";
		if(file_exists($fullFrameworkPath)){
			require_once($fullFrameworkPath);
			self::$wp = \wp\wp::getWp();
		} else {
			die("Le \"core\" : $fullFrameworkPath ne peut pas être chargé. Le chemin est introuvable !");
		}
		
		// Définit le chemin de l'application
		//self::$wp->pathes->addPath("App",$appRootPath);
		self::$wp->pathes->addPath("App",$_SERVER["DOCUMENT_ROOT"]);
		
		// Charge la liste des routes de l'application
		self::$wp->routes();
		
		// Charge la requête
		#begin_debug
		#echo self::$wp->request();
		#end_debug
		
		if(property_exists($this->appConfig,"languages")){
			self::$wp->languages($this->appConfig->languages);
		} else {
			self::$wp->languages();
		}
		
		// Charge le contrôleur défini
		$factory = new \wp\Patterns\ClassFactory\controller(self::$wp->request()->getRoute());
		$this->controller = $factory->getInstance();
			
		$response = new Response();
		$response->addController($this->controller);
		$response->process();
	}
}