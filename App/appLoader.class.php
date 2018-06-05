<?php
/**
 * @name appLoader.class.php Service de chargement de l'application
 * @author web-Projet.com (jean-luc.aubert@web-projet.com) - Juin 2016
 * @package App
 * @version 1.0
**/
namespace App;

use \wp\Utilities\Pathes\pathes as Pathes;
use \wp\Http\Response\htmlResponse as Response;

class appLoader {
	
	/**
	 * Instance courante du framework global
	 * @var object
	 */
	protected static $wp;
	
	/**
	 * Tableau de stockage de la configuration de l'application
	 * @var object
	 */
	protected $appConfig;
	
	/**
	 * Instance du contrôleur à charger pour la gestion de la page
	 * @var \wp\Controller\controller
	 */
	private $controller;
	
	/**
	 * Ensemble des contrôleurs communs à charger
	 * @var array
	 */
	public static $controllers;
	
	/**
	 * Ensemble des données actives utilisables dans le site
	 * @var array
	 */
	public static $activeRecords;
	
	/**
	 * Instance du moteur de gestion de modèles
	 * @var object
	 */
	public static $tpl;
	
	/**
	 * Détermine le chemin vers la racine si l'appel est un appel Ajax par exemple
	 * @var string
	 */
	private $toRoot;
	
	/**
	 * Instancie une nouvelle application
	 * @param string $appRootPath
	 */
	public function __construct($appRootPath){
		ini_set("display_errors", true);
		error_reporting(E_ALL);
		
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
		
		// Définit le moteur de gestion de templates
		self::$tpl = self::$wp->templateEngine($this->appConfig->template);
		
		// Ajoute les ressources standard nécessaires (css, javascripts, etc...)
		self::$wp->defaultAssets($this->appConfig->assets);
		
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
		
		// Expose l'instance courante de l'application à la vue
		self::$tpl->setVar("app",$this);
		
		if(!self::$wp->request()->isAjaxCall()){
			// Charge les contrôleurs communs
			$this->load();
			
			// Charge les mappers de données par défaut
			$this->loadMappers();
		
			// Charge le contrôleur défini
			$factory = new \wp\Patterns\ClassFactory\controller(self::$wp->request()->getRoute());
			$this->controller = $factory->addInstance();
			
			$response = new Response();
			$response->addController($this->controller);
			$response->process();
		}
		
		//self::$tpl->setVar($this->controller->name(),$this->controller);
		
		//self::$wp->response();
	}
	
	/**
	 * Retourne l'objet conteneur du framework
	 */
	public function framework(){
		return self::$wp;
	}
	
	/**
	 * Retourne statiquement l'instance courante du framework
	 */
	public static final function wp(){
		return self::$wp;
	}
	
	/**
	 * Lit les fichiers de configuration de l'application
	 */
	protected function readConfiguration(){ 
		$config = file_get_contents($this->toRoot . "_commun/configs/appConfigure.json");
		$this->appConfig = json_decode($config);
	}
	
	/**
	 * Charge l'ensemble des contrôleurs communs dans le dossier App/Common
	 */
	protected function load($path = null){
		if(is_null($path))
			$path = self::$wp->pathes->getRootPath("App") . "App/Common/";
		
		#begin_debug
		#echo "Chemin racine de l'application : " . $path . "<br />\n";
		#end_debug
		
		// Boucle sur l'ensemble des contrôleurs "index.class.php" des dossiers
		$fileList = new \DirectoryIterator($path);
		foreach($fileList as $file){
			if($file->isDot()){
				continue;
			}
			
			if($file->isDir()){
				if($file->getFileName() == "_templates"){
					continue;
				} else {
					$this->load($path . $file->getFilename() . "/");
				}
			} else {
				if(strpos($file->getFilename(),"Controller") !== false){
					$controllerPath = str_replace(self::$wp->pathes->getRootPath("App"),"",$path);
					$parts = explode("/",$controllerPath);
					$namespace = join("::",$parts);
					$route = new \wp\Http\Routes\route();
					$route->setNamespace("::" . $namespace)
						->setClassName(substr($file->getFilename(),0,strpos($file->getFilename(),".class.php")));
					#begin_debug
					#echo "Charge le contrôleur : " . $namespace  . $file->getFilename() . "<br />\n";
					#end_debug
					$factory = new \wp\Patterns\ClassFactory\controller($route);
					$controller = $factory->addInstance();
					self::$controllers[] = $controller;
				}
			}
		}
	}
	
	/**
	 * Charge l'ensemble des Mappers communs dans le dossier App/Common/Mappers
	 */
	protected function loadMappers($path = null){
		if(is_null($path))
			$path = self::$wp->pathes->getRootPath("App") . "App/Common/Mappers/";
	
		#begin_debug
		#echo "Chemin racine de l'application : " . $path . "<br />\n";
		#end_debug
	
		// Boucle sur l'ensemble des contrôleurs "index.class.php" des dossiers
		$fileList = new \DirectoryIterator($path);
		foreach($fileList as $file){
			if($file->isDot()){
				continue;
			}
					
			if($file->isDir()){
				if($file->getFileName() == "_templates"){
					continue;
				} else {
					$this->loadMappers($path . $file->getFilename() . "/");
				}
			} else {
				if(strpos($file->getFilename(),"ActiveRecord") !== false){
					$mapperPath = str_replace(self::$wp->pathes->getRootPath("App"),"",$path);
					$parts = explode("/",$mapperPath);
					$namespace = join("::",$parts);
					$route = new \wp\Http\Routes\route();
					$route->setNamespace("::" . $namespace)
						->setClassName(substr($file->getFilename(),0,strpos($file->getFilename(),".class.php")));
					#begin_debug
					#echo "Charge le contrôleur : " . $namespace  . $file->getFilename() . "<br />\n";
					#end_debug
					$factory = new \wp\Patterns\ClassFactory\activeRecord($route);
					$activeRecord = $factory->addInstance();
					$activeRecord->query();
					self::$activeRecords[$activeRecord->name()] = $activeRecord;
					// Expose l'enregistrement actif aux vues
					self::$tpl->setVar($activeRecord->name(),$activeRecord);
				}
			}
		}
	}
	
	/**
	 * Retourne le chemin relatif vers la racine de l'application
	 * @param string $path
	 */
	protected function toRoot($path){
		$path = str_replace("\\","/",$path);
		$relativePath = "";
		
		if($path != $_SERVER["DOCUMENT_ROOT"]){
			$strippedPath = str_replace($_SERVER["DOCUMENT_ROOT"],"",$path);
			$pathes = explode("/", $strippedPath);
			$relative = sizeof($pathes) - 1;
			for($i=0; $i<$relative; $i++){
				$relativePath .= "../";
			}
			return $relativePath;
		}
		return "./";
	}
}