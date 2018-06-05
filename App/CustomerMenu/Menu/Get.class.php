<?php
/**
 * @name Get.class.php Contrôleur pour la récupération du menu Client
 * @author IDea Factory (dev-team@ideafactory.fr) - Fév. 2018
 * @package App/Customer/Login
 * @version 0.1.0
**/
namespace App\CustomerMenu\Menu;

use \wp\wp as framework;
use \wp\Controller\REST\Controller as Controller;
use \wp\Http\Request\requestData as Request;
use \wp\Database\Entities\ActiveRecords as ActiveRecords;
use \App\Entities\Menus\MenuToCategories\MenuToCategoriesEntity as Entity;
use \App\Entities\Menus\MenuToCategories\MenuToCategoriesActiveRecord as ActiveRecord;


class Get extends Controller {
	
	/**
	 * Instance de l'entité Customers
	 * @var \Entity
	 */
	private $menu = null;
	
	/**
	 * Instance du menu actif
	 * @var ActiveRecord
	 */
	private $activeRecord = null;
	
	/**
	 * Insancie le contrôleur par défaut
	 * @param Request $data
	 */
	public function __construct(Request $data){
				
		$this->request();
		
		
		$this->responseType("json");
		
		$this->process();
		
	}
	
	/**
	 * Coeur du contrôleur
	**/
	public function process(){
		$this->menu = new Entity();
		$this->menu->setMainEntity("Categories");
		$this->menu->setParentEntity("Menu")->slug = "monCompte";
		
		$this->activeRecord = new ActiveRecords($this->menu);
		
		$this->activeRecord->findBy();
		
		if($this->activeRecord->length() === 1){
			$menu = $this->activeRecord->get();
			return $menu->toArray();
		} else {
			return array(
				"error" => "Aucun menu Client n'a été défini"
			);
		}
	}
}