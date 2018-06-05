<?php
/**
 * @name AutoLogin.class.php Contrôleur pour l'identification d'un client
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


class AutoLogin extends Controller {
	
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
		$this->customer = new Entity();
		$this->customer->id = $this->requestData()->id;
		
		$this->activeRecord = new ActiveRecords($this->customer);
		
		$this->activeRecord->findBy();
		
		if($this->activeRecord->length() === 1){
			$customer = $this->activeRecord->get();
			return $customer->toArray();
		} else {
			return array(
				"error" => "Le mode de connexion automatique a échoué"
			);
		}
	}
}