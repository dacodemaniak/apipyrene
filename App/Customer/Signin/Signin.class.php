<?php
/**
 * @name Signin Contrôleur pour la création d'un compte
 * @author IDea Factory (dev-team@ideafactory.fr) - Mars 2018
 * @package App/Customer/Login
 * @version 0.1.0
 **/
namespace App\Customer\Signin;

use \wp\wp as framework;
use \wp\Controller\REST\Controller as Controller;
use \wp\Http\Request\requestData as Request;
use \wp\Database\Entities\ActiveRecords as ActiveRecords;
use \App\Entities\Customers\CustomersEntity as Entity;
use \App\Entities\Customers\CustomersActiveRecord as ActiveRecord;


class Signin extends Controller {
	
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
		
		$today = new \Datetime();
		
		$activeCustomer = new ActiveRecord($this->customer->getScheme());
		
		// Définit les valeurs pour création
		$activeCustomer->login = $this->requestData()->login;
		$activeCustomer->salt = $this->_makeSalt();
		$activeCustomer->password = $this->_makePassword($activeCustomer->salt);
		$activeCustomer->date_inscription = $today->format("Y-m-d");
		$activeCustomer->validation_inscription = null;
		$activeCustomer->derniere_connexion = null;
		$activeCustomer->statut = 0;
		$activeCustomer->content = $this->requestData()->content;
		
		if ($activeCustomer->save()){
			// Envoyer le mail vers le contact
			
			return array(
				"content" => "Merci pour votre inscription.<br>Vous allez recevoir un e-mail qui vous permettra de valider la création de votre compte afin de profiter de votre boutique Trésors de Pyrène"
			);
		} else {
			// Une erreur est survenue lors de la création du compte
			return array(
					"error" => "Une erreur est survenue lors de la création de votre compte.<br>Veuillez essayer à nouveau..."
			);
		}
	}
	
	/**
	 * Crée un sel de manière aléatoire
	 * @return string
	 */
	private function _makeSalt(){
		return \wp\Helpers\String\Helper::makeSalt();
	}
	
	/**
	 * Crée le mot de passe définitif à partir du mot de passe saisi et du sel
	 * @param string $salt
	 * @return string
	 */
	private function _makePassword(string $salt){
		return \wp\Helpers\String\Helper::hash($this->requestData()->password, $salt);
	}
}