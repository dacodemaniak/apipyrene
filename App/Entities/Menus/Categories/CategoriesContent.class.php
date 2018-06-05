<?php
/**
* @name CustomersContent.class.php Service de récupération des données JSON de l'entité "customers"
* @author IDea Factory (dev-team@ideafactory.fr) - Jan. 2018
* @package App\Entities\Customers
* @version 0.1.0
**/
namespace App\Entities\Customers;

use \wp\Database\Entities\JSONContent\JSONContent as JSON;

class CustomersContent extends JSON {
	
	public function __construct(string $content){
		$this->content($content);
	}
}