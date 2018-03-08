<?php
namespace App\Model\Table;
use Cake\ORM\Table;
Class ReservationMemberTable extends Table
{
	public function initialize(array $config)
	{
		$this->belongsTo("GymReservation",["foreignKey"=>"reservation_id"]);
		$this->belongsTo("GymMember",["foreignKey"=>"member_id"]);
	}

}
