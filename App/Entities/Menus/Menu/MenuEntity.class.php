<?php
/**
* @name MenuEntity.class.php Entité simple sur la table "menu" de la base de données
* @author IDea Factory (dev-team@ideafactory.fr) - Jan. 2018
* @package App\Entities\Menus\Menu
* @version 0.1.0
**/

namespace App\Entities\Menus\Menu;

use \wp\Database\Entities\Entity as Entity;
use \wp\Database\Entities\Columns\Columns as Columns;
use \wp\Database\Entities\Columns\Column as Column;
use \App\Entities\Menus\Menu\MenuActiveRecord as ActiveRecord;

class MenuEntity extends Entity {
	
	/**
	 * Constructeur de l'entité courante
	 * 	- Détermine le nom de l'entité physique à partir du nom de la classe
	 * 	- Définit le schéma de la classe
	 */
	public function __construct(){
		$classPath = explode("\\", __CLASS__);
		$className = array_pop($classPath);
		
		$this->name(strtolower(substr($className,0, -6)))
			->alias(strtoupper(substr($this->name(),0,1)));
		
		$this->columns = new Columns();
		
		$this->setScheme();
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
	
	/**
	 * Définit le schéma de l'entité courante
	 * @return void
	 */
	protected function setScheme(){
		$column = new Column();
		$column->name("id")
			->alias($this->name . "_" . $column->name())
			->type("int")
			->primary(true)
			->auto(true);
		$this->hydrate($column);

		$column = new Column();
		$column->name("slug")
		->type("varchar")
		->alias($this->name . "_" . $column->name())
		->length(255)
		->nullAuto(false);
		$this->hydrate($column);

		$column = new Column();
		$column->name("region")
		->type("varchar")
		->alias($this->name . "_" . $column->name())
		->length(75)
		->nullAuto(false);
		$this->hydrate($column);
		
		$column = new Column();
		$column->name("content")
		->alias($this->name . "_" . $column->name())
		->type("longtext")
		->jsonObject("SiteContent");
		$this->hydrate($column);
		
	}
}