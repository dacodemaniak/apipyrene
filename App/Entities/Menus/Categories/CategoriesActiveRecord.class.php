<?php
/**
* @name CategoriesActiveRecord.class.php Définition d'une ligne de la table physique "categories"
* @author IDea Factory (dev-team@ideafactory.com) - Jan. 2018
* @package App\Entities\Menus\Categories
* @version 0.1.0
**/

namespace App\Entities\Menus\Categories;

use \wp\Database\Entities\ActiveRecord as ActiveRecord;
use \App\Entities\Menus\Categories\CategoriessEntity as Categories;

class CategoriesActiveRecord extends ActiveRecord {
	
	/**
	 * Constructeur de la classe
	 */
	public function __construct($scheme){
		$this->setScheme($scheme);
	}
	
	/**
	 * Retourne les données souhaitées sous forme de tableau associatif
	 * @return array
	 */
	public function toArray(){
		$array["nom"] = $this->nom;
		$array["prenom"] = $this->prenom;
		$array["lastLogin"] = $this->derniere_connexion;
		$array["statut"] = $this->statut;
		
		return $array;
	}
	
	/**
	 * Retourne une instance d'objet de contenu JSON
	 * @return \wp\Database\Entities\JSONContent\JSONContent|boolean
	 */
	protected function getJSONObject(){
		if(($JSONObject = $this->scheme->findByType("jsonObject")) !== false && !is_array($this->scheme->findByType("jsonObject"))){
			$JSONClass = "\\App\\Entities\\Menus\\Categories\\" . $JSONObject->jsonObject();
			
			// Instanciation de la classe
			$JSONContent = new $JSONClass($JSONObject->value());
			
			return $JSONContent;
		}
		
		return false;
	}
}