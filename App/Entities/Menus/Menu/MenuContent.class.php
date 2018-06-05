<?php
/**
* @name MenuContent.class.php Service de récupération des données JSON de l'entité "menu"
* @author IDea Factory (dev-team@ideafactory.fr) - Jan. 2018
* @package App\Entities\Menus\Menu
* @version 0.1.0
**/
namespace App\Entities\Menus\Menu;

use \wp\Database\Entities\JSONContent\JSONContent as JSON;

class MenuContent extends JSON {
	
	public function __construct(string $content){
		$this->content($content);
	}
}