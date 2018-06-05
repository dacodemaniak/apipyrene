<?php
/**
* @name CustomersActiveRecord.class.php Définition d'une ligne de la table physique "customers"
* @author IDea Factory (dev-team@ideafactory.com) - Jan. 2018
* @package App\Entities\Customers
* @version 0.1.0
**/

namespace App\Entities\Customers;

use \wp\Database\Entities\ActiveRecord as ActiveRecord;
use \App\Entities\Customers\CustomersEntity as Customers;
use \wp\Database\Query\DoDelete as Delete;
use \wp\Database\Query\DoUpdate as Update;
use \wp\Database\Query\DoInsert as Insert;

class CustomersActiveRecord extends ActiveRecord {

	/**
	 * @property int $id Identifiant
	 * @property string $login Identifiant de connexion
	 * @property string $password Mot de passe
	 * @property string $salt Sel de renforcement du mot de passe
	 * @property string $statut Etat de l'utilisateur
	 * @property int $date_inscription Date d'inscription
	 * @property int $validation_inscription Date de validation de l'inscription
	 * @property int $derniere_connexion Dernière connexion
	 * @property string $content Définition du contenu
	 */
	
	/**
	 * Constructeur de la classe
	 */
	public function __construct(\wp\Database\Interfaces\IEntity $entity){
		$this->entity = $entity;
	}
	
	/**
	 * Retourne les données souhaitées sous forme de tableau associatif
	 * @return array
	 */
	public function toArray(){
		$array["nom"] = $this->nom;
		$array["prenom"] = $this->prenom;
		$array["civilite"] = $this->civilite;
		$array["telephone"] = $this->telephone;
		$array["lastLogin"] = $this->derniere_connexion;
		$array["statut"] = $this->statut;
		
		return $array;
	}

	/**
	 * Supprime l'enregistrement actif courant
	 * @param void
	 * @return boolean
	 */
	public function delete(){
		$primaryCol = $this->entity->getPrimaryCol();
		
		if($primaryCol){
			$this->query = "DELETE FROM " . $this->entity->getName() .
				"WHERE " . $primaryCol . " = :" . $primaryCol . ";";
			
			$query = Delete::get();
			
			$query->SQL($this->query);
			
			$query->queryParams(array(":" . $this->entity->getPrimaryCol() => $this->{$this->entity->getPrimaryCol()}));
			
			$this->statement = $query->process();
			
			return $this->statement;
		}
		
		return false;
	}
	
	/**
	 * Dispatche vers la méthode de création ou de mise à jour
	 * @param void
	 * @return boolean
	 */
	public function save(){
		$primaryCol = $this->entity->getPrimaryCol();
		
		if($primaryCol){
			if(!is_null($this->{$primaryCol})){
				return $this->update($primaryCol);
			}
		}
		return $this->insert();
	}
	
	/**
	 * Crée et exécute une requête INSERT pour l'enregistrement actif courant
	 * @param void
	 * @return boolean
	 */
	public function insert(){
		$dataMapper = [];
		
		$this->query = "INSERT INTO " . $this->entity->getName() . " (";
		
		// Boucle sur le schéma à l'exception de la clé primaire
		foreach($this->entity->getScheme() as $column => $definition){
			if($definition->primary()){
				continue;
			}
			$this->query .= $definition->name() . ",";
			// Alimente le mapper de données
			$dataMapper[":" . $definition->name()] = $this->{$definition->name()};
		}
		// Supprime la dernière virgule inutile
		$this->query = substr($this->query, 0, strlen($this->query) - 1);
		$this->query .= ") VALUES (";

		// Boucle sur le schéma à l'exception de la clé primaire
		foreach($this->entity->getScheme() as $column => $definition){
			if($definition->primary()){
				continue;
			}
			$this->query .= ":" . $definition->name() . ",";
		}
		// Supprime la dernière virgule inutile
		$this->query = substr($this->query, 0, strlen($this->query) - 1);
		$this->query .= ");";
		
		$query = Insert::get();
		
		$query->SQL($this->query);
		
		$query->queryParams($dataMapper);
		
		$this->statement = $query->process();
		
		return $this->statement;
		
	}
	
	/**
	 * Crée et exécute une requête UPDATE pour l'enregistrement actif courant
	 * @param string $primaryCol Nom de la clé primaire de la table
	 * @return boolean
	 */
	public function update(string $primaryCol){
		$dataMapper = [];
		
		$this->query = "UPDATE " . $this->entity->getName() . " SET ";
		
		// Boucle sur le schéma à l'exception de la clé primaire
		foreach($this->entity->getScheme() as $column => $definition){
			if($definition->primary()){
				continue;
			}
			$this->query .= $definition->name() . " = :" . $definition->name() . ",";
			// Alimente le mapper de données
			$dataMapper[":" . $definition->name()] = $this->{$definition->name()};
		}
		// Supprime la dernière virgule inutile
		$this->query = substr($this->query, 0, strlen($this->query) - 1);
		
		// Ajoute la contrainte
		$this->query .= " WHERE " . $primaryCol . " = :" . $primaryCol . ";";
		$dataMapper[":" . $primaryCol] = $this->{$primaryCol};
		
		$query = Update::get();
		
		$query->SQL($this->query);
		
		$query->queryParams($dataMapper);
		
		$this->statement = $query->process();
		
		return $this->statement;
	}
	
	/**
	 * Retourne une instance d'objet de contenu JSON
	 * @return \wp\Database\Entities\JSONContent\JSONContent|boolean
	 */
	protected function getJSONObject(){
		if(($JSONObject = $this->entity->getScheme()->findByType("jsonObject")) !== false && !is_array($this->entity->getScheme()->findByType("jsonObject"))){
			$JSONClass = "\\App\\Entities\\Customers\\" . $JSONObject->jsonObject();
			
			// Instanciation de la classe
			$JSONContent = new $JSONClass($this->{$JSONObject->name()});
			
			return $JSONContent;
		}
		
		return false;
	}
}