<?php
namespace App\Model\Table;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class categoryTable extends Table{
	public function initialize(array $config)
	{
		$this->addBehavior("Timestamp");		
	}	
}