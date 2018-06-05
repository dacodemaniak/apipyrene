<?php
/**
* @name Controller.class.php Service REST pour la gestion des utilisateurs
* @author IDEA Factory (dev-team@ideafactory.fr) - Jan. 2018
* @package App\Customer
* @version 0.1.0
**/

namespace App\Customer;

use \wp\wp as framework;
use \wp\Controller\REST\Controller as RestController;
use \wp\Http\Request\request as Request;
use \wp\Http\Request\requestData as RequestData;

class Controller extends RestController {
	
	/**
	 * Constructeur du contrôleur REST pour les produits
	 */
	public function __construct(){

		// @see \wp\Controller\REST\Controller::request()
		$this->request();
		
		// Exécute l'appel REST
		$this->run();
	}
	
	/**
	 * Coeur du contrôleur lui-même
	 */
	private function run(){
		$controller = "";
		$instance = null;
		
		switch($this->request()->getMethod()){
			case "GET":
				if(!$this->requestData()->id){
					$controller = "\\App\\Customer\\Login\\Login";
					$instance = new $controller($this->requestData());
				} else {
					$controller = "\\App\\Customer\\Login\\AutoLogin";
					$instance = new $controller($this->requestData());
				}
			break;
			
			case "POST":
			break;
			
			case "PUT":
			case "OPTIONS":
				$controller = "\\App\\Customer\\Signin\\Signin";
				$instance = new $controller($this->requestData());
			break;
			
			case "DELETE":
			case "OPTIONS":
			break;
		}
		
		if(!is_null($instance))
			$this->result["data"] = $instance->process();
		else 
			$this->result["data"] = array("error" => "Impossible d'instancier " . $controller);
		$this->result["HTTP_VERB"] = $this->request()->getMethod();
	}
}