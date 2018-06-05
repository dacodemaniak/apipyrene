<?php
/**
* @name MenuToCategories.class.php Services de gestion de l'association "menutocategories"
* @autor IDea Factory (dev-team@ideafactory.fr) - Fév. 2018
* @package \App\Entities\Menu\MenuToCategories
* @version 0.1.0
**/
namespace App\Entities\Menus\MenuToCategories;

use \wp\Database\Entities\ManyToMany as Association;
use \wp\Database\Entities\Columns\Columns as Columns;
use \wp\Database\Entities\Columns\Column as Column;
use \App\Entities\Menus\Categories\Categories as Categories;
use \App\Entities\Menus\Menu\Menu as Menu;

class MenuToCategoriesEntity extends Association {
	
	/**
	 * Constructeur de l'Association
	 */
	public function __construct(){
		$classPath = explode("\\", __CLASS__);
		$className = array_pop($classPath);
		
		$this->name(strtolower(substr($className,0, -6)))
		->alias("mtc");
		
		$this->columns = new Columns();
		
		$this->setScheme();
	}
	
	/**
	 * Définit l'entité principale de l'association
	 * @param string $entity Nom de l'entité principale
	 */
	public function setMainEntity(string $entity){
		if(is_null($this->mainEntity))
			$this->mainEntity = $this->entity($entity);	
	}
	
	public function setParentEntity(string $entity){
		if(is_null($this->parentEntity))
			$this->parentEntity = $this->entity($entity);	
	}
	
	/**
	 * Retourne une nouvelle instance d'enregistrement actif
	 * @return \App\Entities\Site\SiteActiveRecord
	 */
	public function getActiveRecordInstance(){
		return new ActiveRecord($this->columns);
	}
	
	/**
	 * Retourne la collection des colonnes de l'entité courante
	 * @return \wp\Database\Entities\Columns\Columns
	 */
	public function getScheme(){
		return $this->columns;
	}
	
	protected function setScheme(){
		$column = new Column();
		$column->name("id")
			->alias($this->name . "_" . $column->name())
			->type("int")
			->primary(true)
			->auto(true);
		$this->hydrate($column);
		
		$column = new Column();
		$column->name("order")
			->alias($this->name . "_" . $column->name())
			->type("smallint");
		$this->hydrate($column);
		
		$column = new Column();
		$column->name("categories_id")
			->alias($this->name . "_" . $column->name())
			->type("int")
			->parentEntity("Categories")
			->ns("\\App\\Entities\\Menus\\Categories\\")
			->nullAuto(false);
		$this->hydrate($column);
		
		$column = new Column();
		$column->name("menu_id")
			->alias($this->name . "_" . $column->name())
			->type("int")
			->parentEntity("Menu")
			->ns("\\App\\Entities\\Menus\\Menu\\")
			->nullAuto(false);
		$this->hydrate($column);
	}
}