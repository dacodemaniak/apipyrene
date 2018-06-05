<?php
/**
* @name Controller.class.php Service REST pour la gestion du menu Utilisateur
* @author IDEA Factory (dev-team@ideafactory.fr) - Jan. 2018
* @package App\CustomerMenu
* @version 0.1.0
**/

namespace App\CustomerMenu;

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
		switch($this->request()->getMethod()){
			case "GET":
				$controller = "\\App\\CustomerMenu\\Menu\\Get";
				$instance = new $controller($this->requestData());
			break;
			
			case "POST":
			break;
			
			case "PUT":
			break;
			
			case "DELETE":
			break;
		}
		$this->result["data"] = $instance->process();
		$this->result["HTTP_VERB"] = $this->request()->getMethod();
	}
}