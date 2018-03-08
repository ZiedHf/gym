<?php
namespace App\Model\Table;
use Cake\ORM\Table;
Class GymReservationTable extends Table
{
	public function initialize(array $config)
	{
		$this->belongsTo("GymEventPlace",["foreignKey"=>"place_id"]);
		$this->hasMany("Members",["className" => 'ReservationMember', "foreignKey"=>"reservation_id"]);
		$this->hasMany("ReservationList", ["foreignKey"=>"reservation_id", "dependent"=>true]);
	}

}
