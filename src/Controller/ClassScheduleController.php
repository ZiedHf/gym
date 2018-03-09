<?php
namespace App\Controller;
use App\Controller\AppController;

class ClassScheduleController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");
	}

	public function classList()
	{
		// $data = $this->ClassSchedule->find("all")->hydrate(false)->toArray();
		$session = $this->request->session()->read("User");
		$data = array();
		$data = $this->ClassSchedule->find();
		$data = $data->contain(["GymMember", "ClassScheduleList"])->hydrate(false)->toArray();
		/*if($session["role_name"]=="member")
		{
			$classes = $this->ClassSchedule->find()->hydrate(false)->toArray();
			debug($classes);die();
			if(!empty($classes))
			{
				foreach($classes as $class)
				{
					$assign_class[] = $class["assign_class"];
				}
				$data = $this->ClassSchedule->find()->contain(["ClassScheduleList"])->where(["ClassSchedule.id IN "=>$assign_class]);
				$data = $data->contain(["GymMember"])->select(["ClassSchedule.id","ClassSchedule.class_name","ClassSchedule.assign_staff_mem","ClassSchedule.start_time","ClassSchedule.end_time","ClassSchedule.location","GymMember.first_name","GymMember.last_name"])->hydrate(false)->toArray();

			}
		}
		else{
			$data = $this->ClassSchedule->find();
			$data = $data->contain(["GymMember", "ClassScheduleList"])->hydrate(false)->toArray();
			//debug($data); die();
			//$schedule_list = $this->ClassSchedule->ClassScheduleList->find()->hydrate(false)->toArray();
		}
		*/
		//$schedule_list = $this->ClassSchedule->ClassScheduleList->find()->where(["class_id"=>$id])->hydrate(false)->toArray();
		$this->set("data",$data);
	}

	public function addClass()
	{
		$this->set("edit",false);
		$this->set("title",__("Add Class Schedule"));
		$session = $this->request->session()->read("User");

		$staff = $this->ClassSchedule->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"staff_member"]);
		$staff = $staff->select(["id","name"=>$staff->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])]);
		$staff = $staff->toArray();
		$this->set("staff",$staff);
		$this->set("assistant_staff",$staff);
		if($this->request->is("post"))
		{
			if(!isset($this->request->data["time_list"])){
				$this->Flash->error(__("Please add time to this class before saving."));
				return $this->redirect(["action"=>"addClass"]);
			}

			$time_list = $this->request->data["time_list"];

			$class = $this->ClassSchedule->newEntity();

			//$this->request->data['days'] = json_encode($this->request->data['days']);

			//$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'].":".$this->request->data['start_ampm'];
			$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'];
			$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'];
			$this->request->data["created_date"] = date("Y-m-d");
			$this->request->data["created_by"] = $session["id"];

			$class = $this->ClassSchedule->patchEntity($class,$this->request->data);

			if($this->ClassSchedule->save($class))
			{
				$class_id = $class->id;
				foreach($time_list as $time)
				{
					$schedule = array();
					$time = json_decode($time);
					$schedule["class_id"] = $class_id;

					//$schedule["days"] = json_decode($time[0]);

					$schedule["days"] = str_replace('/', '-', $time[0]);
					$schedule["days"] = date("Y-m-d",strtotime($schedule["days"]));

					$schedule["start_time"] = $time[1];
					$schedule["end_time"] = $time[2];
					$schedule_row = $this->ClassSchedule->ClassScheduleList->newEntity();
					$schedule_row = $this->ClassSchedule->ClassScheduleList->patchEntity($schedule_row,$schedule);
					$this->ClassSchedule->ClassScheduleList->save($schedule_row);
				}
				$this->Flash->success(__("Success! Record Saved Successfully"));

			}else{
				$this->Flash->error(__("Error! There was an error while updating,Please try again later."));
			}
			return $this->redirect(["action"=>"classList"]);
		}
	}

	public function editClass($id)
	{
		$this->set("edit",true);
		$this->set("title",__("Edit Class Schedule"));
		$row = $this->ClassSchedule->get($id)->toArray();

		$row['start_hrs'] =  explode(":",$row['start_time'])[0];
		@$row['start_min'] =  explode(":",$row['start_time'])[1];
		//@$row['start_ampm'] =  explode(":",$row['start_time'])[2];

		$row['end_hrs'] =  explode(":",$row['end_time'])[0];
		$row['end_min'] =  explode(":",$row['end_time'])[1];
		//$row['end_ampm'] =  explode(":",$row['end_time'])[2];

		$schedule_list = $this->ClassSchedule->ClassScheduleList->find()->where(["class_id"=>$id])->hydrate(false)->toArray();

		$this->set("schedule_list",$schedule_list);
		$this->set("data",$row);
		$staff = $this->ClassSchedule->GymMember->find("list",["keyField"=>"id","valueField"=>"name"])->where(["role_name"=>"staff_member"]);
		$staff = $staff->select(["id","name"=>$staff->func()->concat(["first_name"=>"literal"," ","last_name"=>"literal"])]);
		$staff = $staff->toArray();
		$this->set("staff",$staff);
		$this->set("assistant_staff",$staff);
		$this->render("addClass");


		if($this->request->is("post"))
		{
			$time_list = $this->request->data["time_list"];
			$class = $this->ClassSchedule->get($id);

			//$this->request->data['days'] = json_encode($this->request->data['days']);
			//$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'].":".$this->request->data['start_ampm'];
			$this->request->data['start_time'] = $this->request->data['start_hrs'].":".$this->request->data['start_min'];
			//$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'].":".$this->request->data['end_ampm'];
			$this->request->data['end_time'] = $this->request->data['end_hrs'].":".$this->request->data['end_min'];

			$class = $this->ClassSchedule->patchEntity($class,$this->request->data());

			if($this->ClassSchedule->save($class))
			{
				$this->ClassSchedule->ClassScheduleList->deleteAll(["class_id"=>$id]);

				foreach($time_list as $time)
				{
					$schedule = array();
					$time = json_decode($time);
					$schedule["class_id"] = $id;

					//$schedule["days"] = json_encode($time[0]);
					$schedule["days"] = str_replace('/', '-', $time[0]);
					$schedule["days"] = date("Y-m-d",strtotime($schedule["days"]));

					$schedule["start_time"] = $time[1];
					$schedule["end_time"] = $time[2];
					$schedule_row = $this->ClassSchedule->ClassScheduleList->newEntity();
					$schedule_row = $this->ClassSchedule->ClassScheduleList->patchEntity($schedule_row,$schedule);
					$this->ClassSchedule->ClassScheduleList->save($schedule_row);
				}
			}
			$this->Flash->success(__("Success! Record Updated Successfully"));
			return $this->redirect(["action"=>"classList"]);
		}
	}

	public function deleteClass($id)
	{
		$row = $this->ClassSchedule->get($id);
		if($this->ClassSchedule->delete($row))
		{
			$this->Flash->success(__("Success! Class Deleted Successfully."));
			return $this->redirect($this->referer());
		}
	}

	public function viewSchedule()
	{
		return $this->redirect(["action"=>"classList"]);

		if(count($this->request->params['pass'])> 0) {
			$club = $this->request->params['pass'][0];
		}

		// $classes = $this->ClassSchedule->find("all")->hydrate(false)->toArray();
		$session = $this->request->session()->read("User");
		if($session["role_name"]=="member")
		{
			$classes_list = $this->ClassSchedule->GymMemberClass->find()->where(["member_id"=>$session["id"]])->hydrate(false)->toArray();
			if(!empty($classes_list))
			{
				foreach($classes_list as $class)
				{
					if(isset($club)) {
						 list($class_schedule) = $this->ClassSchedule->find("all")->where(["id"=>$class["assign_class"]])->hydrate(false)->toArray();
						if($class_schedule['location']==$club) $assign_class[] = $class["assign_class"];
					}
					else $assign_class[] = $class["assign_class"];
				}
				// var_dump($assign_class);die;

				if(count($assign_class)>0) $classes = $this->ClassSchedule->ClassScheduleList->find("all")->where(["class_id IN"=>$assign_class])->hydrate(false)->toArray();

			}
		}
		else{
			if(isset($club))
				$tClassSchedule = $this->ClassSchedule->find("all")->where(["location"=>$club])->hydrate(false)->toArray();
			else
			 	$tClassSchedule = $this->ClassSchedule->find("all")->hydrate(false)->toArray();

			if(!empty($tClassSchedule)) {
				foreach($tClassSchedule as $classe) {
					$classe_query = $this->ClassSchedule->ClassScheduleList->find("all")->where(["class_id"=>$classe['id']])->hydrate(false)->toArray();
					if($classe_query){
						list($c) = 	$classe_query;
						$classes[] = $c;
					}
				}
			}
			//$classes = $this->ClassSchedule->ClassScheduleList->find("all")->hydrate(false)->toArray();
		}
		$this->set("classes",$classes);

	}

	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$members_actions = ["classList","viewSchedule"];
		$staff_acc_actions = ["classList","viewSchedule","addClass","editClass"];
		switch($role_name)
		{
			CASE "member":
				if(in_array($curr_action,$members_actions))
				{return true;}else{return false;}
			break;

			CASE "staff_member":
				if(in_array($curr_action,$staff_acc_actions))
				{return true;}else{ return false;}
			break;

			CASE "accountant":
				if(in_array($curr_action,$staff_acc_actions))
				{return true;}else{return false;}
			break;
		}
		return parent::isAuthorized($user);
	}
}
