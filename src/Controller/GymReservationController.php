<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
// use App\Controller\AppController;

class GymReservationController extends AppController
{
	public function reservationList(){
		$session = $this->request->session()->read("User");
		$memberReservationNumber = 0;
		$data = $this->GymReservation->find("all")->contain(["GymEventPlace", "Members", "ReservationList"])->select($this->GymReservation)->select(["GymEventPlace.place"])->hydrate(false)->toArray();
		// Delete old reservation
		foreach ($data as $keyevent => $event) {
			$return = true;
			foreach ($event['reservation_list'] as $keyItem => $item) {
				if(strtotime($item['date']) < time()){
					unset($data[$keyevent]['reservation_list'][$keyItem]);
				}
			}
			if(count($data[$keyevent]['reservation_list']) === 0){
				unset($data[$keyevent]);
			}else{
				if($this->isMemberInThisReservation($event['id'], $session['id'])){
					$memberReservationNumber++;
				}
			}
		}
		$this->set("data",$data);
		$this->set("memberReservationNumber", $memberReservationNumber);
	}

	private function isMemberInThisReservation($rid, $mid){
		// from gymHelper
		$res_mem_table = TableRegistry::get("ReservationMember");
		$row = $res_mem_table->find()->where(["reservation_id"=>$rid,"member_id"=>$mid])->hydrate(false)->toArray();
		if(count($row) > 0){
			return true;
		}
		else{
			return false;
		}
	}

	public function addReservation()
    {
		$session = $this->request->session()->read("User");
		$this->set("edit",false);
		$event_places = $this->GymReservation->GymEventPlace->find("list",["keyField"=>"id","valueField"=>"place"])->hydrate(false);
		$this->set("event_places",$event_places);

		if($this->request->is("post"))
		{
			if(!isset($this->request->data["time_list"])){
				$this->Flash->error(__("Please add time to this reservation before saving."));
				return $this->redirect(["action"=>"addReservation"]);
			}

			$time_list = $this->request->data["time_list"];

			$row = $this->GymReservation->newEntity();

			$this->request->data["created_by"] = $session["id"];
			$this->request->data["created_date"] = date("Y-m-d");
			$this->request->data["event_date"] = str_replace('/', '-', $this->request->data["event_date"]);
			$this->request->data["event_date"] = date("Y-m-d",strtotime($this->request->data["event_date"]));
			//$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'].":".$this->request->data['start_ampm'];
			$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'];
			//$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'].":".$this->request->data['end_ampm'];
			$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'];
			$row = $this->GymReservation->patchEntity($row,$this->request->data);
			if($this->GymReservation->save($row))
			{
				$reservation_id = $row->id;
				foreach($time_list as $time)
				{
					$reservation = array();
					$time = json_decode($time);
					$reservation["reservation_id"] = $reservation_id;
					$reservation["date"] = str_replace('/', '-', $time[0]);
					//$reservation["date"] = $reservation["date"]->format("Y-m-d");
					$reservation["date"] = date("Y-m-d",strtotime($reservation["date"]));
					//$reservation["date"] = json_encode($time[0]);
					$reservation["start_time"] = $time[1];
					$reservation["end_time"] = $time[2];
					$reservation_row = $this->GymReservation->ReservationList->newEntity();
					$reservation_row = $this->GymReservation->ReservationList->patchEntity($reservation_row,$reservation);
					$this->GymReservation->ReservationList->save($reservation_row);
				}

				$this->Flash->success(__("Success! Record Saved Successfully"));
				return $this->redirect(["action"=>"reservationList"]);
			}else{
				$this->Flash->error(__("Error! There was an error while updating, Please try again later."));
			}
			return $this->redirect(["action"=>"reservationList"]);
		}
    }
	 public function editReservation($id)
    {
		$this->set("edit",true);
		$row = $this->GymReservation->get($id, [
		    'contain' => ['Members.GymMember']
		]);
		//debug($row->toArray());die();
		$row['start_hrs'] =  explode(":",$row['start_time'])[0];
		@$row['start_min'] =  explode(":",$row['start_time'])[1];
		//@$row['start_ampm'] =  explode(":",$row['start_time'])[2];
		$row['end_hrs'] =  explode(":",$row['end_time'])[0];
		$row['end_min'] =  explode(":",$row['end_time'])[1];
		//$row['end_ampm'] =  explode(":",$row['end_time'])[2];

		$reservation_list = $this->GymReservation->ReservationList->find()->where(["reservation_id"=>$id])->order(['date' => 'ASC'])->hydrate(false)->toArray();
		$this->set("reservation_list",$reservation_list);

		$this->set("data",$row->toArray());
		$event_places = $this->GymReservation->GymEventPlace->find("list",["keyField"=>"id","valueField"=>"place"])->hydrate(false);
		$this->set("event_places",$event_places);
		$this->render("addReservation");
		$row = "";
		if($this->request->is("post"))
		{
			if(!isset($this->request->data["time_list"])){
				$this->Flash->error(__("Please add time to this reservation before saving."));
				return $this->redirect(["action"=>"addReservation"]);
			}

			$time_list = $this->request->data["time_list"];
/*
			$reservation = array();
			$time = json_decode($time_list[1]);
			//$reservation["reservation_id"] = $reservation_id;
			$reservation["date"] = str_replace('/', '-', $time[0]);
			$reservation["date"] = date("Y-m-d",strtotime($reservation["date"]));
			$reservation["start_time"] = $time[1];
			$reservation["end_time"] = $time[2];
			debug($reservation); die();
*/
			$row = $this->GymReservation->get($id);
			$this->request->data["event_date"] = str_replace('/', '-', $this->request->data["event_date"]);
			$this->request->data["event_date"] = date("Y-m-d",strtotime($this->request->data["event_date"]));
			//$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'].":".$this->request->data['start_ampm'];
			$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'];
			//$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'].":".$this->request->data['end_ampm'];
			$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'];

			$row = $this->GymReservation->patchEntity($row,$this->request->data);
			if($this->GymReservation->save($row))
			{
				$reservation_id = $row->id;
				$this->GymReservation->ReservationList->deleteAll(["reservation_id"=>$reservation_id]);
				//debug($time_list); die();
				foreach($time_list as $time)
				{
					//debug($time); die();
					$reservation = array();

					$time = json_decode($time);
					//debug($time); die();
					$reservation["reservation_id"] = $reservation_id;
					$reservation["date"] = str_replace('/', '-', $time[0]);
					//$reservation["date"] = $reservation["date"]->format("Y-m-d");
					$reservation["date"] = date("Y-m-d",strtotime($reservation["date"]));
					//$reservation["date"] = json_encode($time[0]);
					$reservation["start_time"] = $time[1];
					$reservation["end_time"] = $time[2];

					//debug($time); die();

					$reservation_row = $this->GymReservation->ReservationList->newEntity();
					$reservation_row = $this->GymReservation->ReservationList->patchEntity($reservation_row,$reservation);
					$this->GymReservation->ReservationList->save($reservation_row);
				}

				$this->Flash->success(__("Success! Record Saved Successfully"));
				return $this->redirect(["action"=>"reservationList"]);
			}else{
				$this->Flash->error(__("Error! There was an error while updating, Please try again later."));
			}
			return $this->redirect(["action"=>"editReservation", $reservation_id]);
		}
  }

	public function deleteReservation($did)
    {
		$drow = $this->GymReservation->get($did);
		if($this->GymReservation->delete($drow))
		{
			$this->Flash->success(__("Success! Record Deleted Successfully"));
			return $this->redirect(["action"=>"reservationList"]);
		}
    }


		public function memberToReservationToggle($rid, $mid){
			$members_table = TableRegistry::get("GymMember");
			$gymMember_row = $members_table->get($mid);
			$gymReservation_row = $this->GymReservation->get($rid);

			if(($gymMember_row)&&($gymReservation_row)){
				$res_mem_table = TableRegistry::get("ReservationMember");
				$row = $res_mem_table->find()->where(["reservation_id"=>$rid,"member_id"=>$mid]);
				if($row->count() === 1){
					$row = $row->first();
					$lineToDelete = $res_mem_table->get($row['id']);
					echo ($res_mem_table->delete($lineToDelete)) ? true : false;

					$gymReservation_row->number_participants = $gymReservation_row->number_participants - 1;
					$this->GymReservation->save($gymReservation_row);
				}elseif(($row->count() === 0)&&($gymReservation_row->max_members > $gymReservation_row->number_participants)){
					$res_member = $res_mem_table->newEntity();
					$res_member->member_id = $mid;
					$res_member->reservation_id = $rid;
					echo ($res_mem_table->save($res_member)) ? true : false;

					$gymReservation_row->number_participants = $gymReservation_row->number_participants + 1;
					$this->GymReservation->save($gymReservation_row);
				}elseif($gymReservation_row->max_members > $gymReservation_row->number_participants){
					//cant subscribe
				}
			}
			return $this->redirect(["action" => "reservationList"]);
		}

	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$members_actions = ["reservationList", "memberToReservationToggle"];
		// $staff__acc_actions = ["productList","addProduct","editProduct"];
		switch($role_name)
		{
			CASE "member":
				if(in_array($curr_action,$members_actions))
				{return true;}else{return false;}
			break;

			// CASE "staff_member":
				// if(in_array($curr_action,$staff__acc_actions))
				// {return true;}else{ return false;}
			// break;

			// CASE "accountant":
				// if(in_array($curr_action,$staff__acc_actions))
				// {return true;}else{return false;}
			// break;
		}
		return parent::isAuthorized($user);
	}
}
