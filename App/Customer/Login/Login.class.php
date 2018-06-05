<?php
/**
 * @name Login.class.php Contrôleur pour l'identification d'un client
 * @author IDea Factory (dev-team@ideafactory.fr) - Fév. 2018
 * @package App/Customer/Login
 * @version 0.1.0
**/
namespace App\Customer\Login;

use \wp\wp as framework;
use \wp\Controller\REST\Controller as Controller;
use \wp\Http\Request\requestData as Request;
use \wp\Database\Entities\ActiveRecords as ActiveRecords;
use \App\Entities\Customers\CustomersEntity as Entity;
use \App\Entities\Customers\CustomersActiveRecord as ActiveRecord;


class Login extends Controller {
	
	/**
	 * Instance de l'entité Customers
	 * @var \Entity
	 */
	private $customer = null;
	
	/**
	 * Instance d'un client actif
	 * @var ActiveRecord
	 */
	private $activeRecord = null;
	
	/**
	 * Insancie le contrôleur par défaut
	 * @param Request $data
	 */
	public function __construct(Request $data){
				
		$this->request();
		
		$this->sendHeaders();
		
		$this->responseType("json");
		
		$this->process();
		
	}
	
	/**
	 * Coeur du contrôleur
	**/
	public function process(){
		$repository = \wp\Database\Entities\Repository::getRepository(new Entity());
		$repository->login = $this->requestData()->identifiant;
		$repository->selectBy();
		
		$activeUser = $repository->get(); 

		
		if($activeUser){
			// Un Client avec cet identifiant a été trouvé
			if(\wp\Helpers\String\Helper::hash($this->requestData()->password, $activeUser->salt) === $activeUser->password){
				// La signature est correcte
				return $activeUser->toArray();
			} else {
				return array(
					"error" => "Désolé, l'identification avec le mot de passe fournis a échoué."		
				);
			}
		} else {
			return array(
				"error" => "Désolé, mais aucun Client ne correspond à l'adresse email fournie !"
			);
		}
	}
}