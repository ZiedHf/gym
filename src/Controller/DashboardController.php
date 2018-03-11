<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use GoogleCharts;
use Cake\Routing\Router;
use Cake\I18n\Time;


class DashboardController extends AppController
{
	public function initialize()
	{
		parent::initialize();
		$this->loadComponent("GYMFunction");
		$this->user = $this->request->session()->read("User");

		require_once(ROOT . DS .'vendor' . DS  . 'chart' . DS . 'GoogleCharts.class.php');
	}

	public function index()
	{
		$session = $this->request->session()->read("User");
		switch($session["role_name"])
		{
			CASE "administrator":
				return $this->redirect(["action"=>"adminDashboard"]);
			break;
			CASE "member":
				return $this->redirect(["action"=>"memberDashboard"]);
			break;
			default:
				return $this->redirect(["action"=>"staffAccDashboard"]);
		}
	}

	public function adminDashboard()
	{
		$session = $this->request->session()->read("User");

		$conn = ConnectionManager::get('default');
		$this->autoRender = false;
		$mem_table = TableRegistry::get("GymMember");
		$grp_tbl = TableRegistry::get("GymGroup");
		$message_tbl = TableRegistry::get("GymMessage");
		$membership_tbl = TableRegistry::get("Membership");
		$notice_tbl = TableRegistry::get("gymNotice");

		$members = $mem_table->find("all")->where(["role_name"=>"member"]);
		$members = $members->count();

		$staff_members = $mem_table->find("all")->where(["role_name"=>"staff_member"]);
		$staff_members = $staff_members->count();

		$curr_id = intval($session["id"]);
		$messages = $message_tbl->find("all")->where(["receiver"=>$curr_id]);
		$messages = $messages->count();

		$groups = $grp_tbl->find("all");
		$groups = $groups->count();

		$membership = $membership_tbl->find("all")->limit(5)->select(["membership_label","gmgt_membershipimage"])->hydrate(false)->toArray();
		$groups_data = $grp_tbl->find("all")->limit(5)->select(["name","image"])->hydrate(false)->toArray();

		$cal_lang = $this->GYMFunction->getSettings("calendar_lang");

		$this->set("cal_lang",$cal_lang);
		$this->set("members",$members);
		$this->set("staff_members",$staff_members);
		$this->set("messages",$messages);
		$this->set("groups",$groups);
		$this->set("membership",$membership);
		$this->set("groups_data",$groups_data);
		$gymCalendar = isset($this->request->data['gymCalendar']) ? $this->request->data['gymCalendar'] : $this->getGymNameByDb($this->request->session()->read('database'));
		$this->set("gymCalendar", $gymCalendar);
		$gyms = unserialize(GYMS1);
		$this->set("gyms", $gyms);
		################################################

		$month =array('1'=>"January",'2'=>"February",'3'=>"March",'4'=>"April",
		'5'=>"May",'6'=>"June",'7'=>"July",'8'=>"August",
		'9'=>"September",'10'=>"Octomber",'11'=>"November",'12'=>"December",);
		$year = date('Y');

		/* $q="SELECT EXTRACT(MONTH FROM created_date) as date_d,sum(paid_amount) as count_c FROM `membership_payment` WHERE YEAR(created_date) = '".$year."' group by month(created_date) ORDER BY month(created_date) ASC";    NOT WORKING ON MYSQL 5.7/PHP 5.7*/
		$q="SELECT EXTRACT(MONTH FROM created_date) as date_d,sum(paid_amount) as count_c FROM `membership_payment` WHERE YEAR(created_date) = '".$year."' group by date_d ORDER BY date_d ASC";

		$result = $conn->execute($q);
		$result = $result->fetchAll('assoc');
		$chart_array_pay = array();
		$chart_array_pay[] = array('Month','Fee 	Payment');
		foreach($result as $r)
		{

			$chart_array_pay[]=array( $month[$r["date_d"]],(int)$r["count_c"]);
		}
		$this->set("chart_array_pay",$chart_array_pay);
		$this->set("result_pay",$result);





		################################################

		$chart_array = array();
		$report_2 ="SELECT  at.class_id,cl.class_name,
					SUM(case when `status` ='Present' then 1 else 0 end) as Present,
					SUM(case when `status` ='Absent' then 1 else 0 end) as Absent
					from `gym_attendance` as at,`class_schedule` as cl where at.attendance_date >  DATE_SUB(NOW(), INTERVAL 1 WEEK) AND at.class_id = cl.id  AND at.role_name = 'member' GROUP BY at.class_id";
		$report_2 = $conn->execute($report_2);
		$report_2 = $report_2->fetchAll('assoc');
		$report_2 = $report_2;
		$chart_array_at[] = array(__('Class'),__('Present'),__('Absent'));
		if(!empty($report_2))
		{
			foreach($report_2 as $result)
			{
				$cls = $result['class_name'];
				$chart_array_at[] = [$result['class_name'],(int)$result["Present"],(int)$result["Absent"]];
			}
		}
		$this->set("report_member",$report_2);
		$this->set("chart_array_at",$chart_array_at);

		##################STAFF ATTENDANCE REPORT#############################

		// $sdate = '2016-07-01';
		// $edate = '2016-08-12';
		$report_2 = null;

		$chart_array_staff = array();
		$report_2 ="SELECT  at.user_id,
				SUM(case when `status` ='Present' then 1 else 0 end) as Present,
				SUM(case when `status` ='Absent' then 1 else 0 end) as Absent
				from `gym_attendance` as at where at.attendance_date >  DATE_SUB(NOW(), INTERVAL 1 WEEK)  AND at.role_name = 'staff_member' GROUP BY at.user_id";

		$report_2 = $conn->execute($report_2);
		$report_2 = $report_2->fetchAll('assoc');

		$chart_array_staff[] = array(__('Staff Member'),__('Present'),__('Absent'));
		if(!empty($report_2))
		{
			foreach($report_2 as $result)
			{
				$user_name = $this->GYMFunction->get_user_name($result["user_id"]);
				$chart_array_staff[] = array("$user_name",(int)$result["Present"],(int)$result["Absent"]);
			}
		}
		$this->set("chart_array_staff",$chart_array_staff);
		$this->set("report_sataff",$report_2);

		$cal_array = $this->getCalendarData();
		$this->set("cal_array",$cal_array);

		$this->render("dashboard");
	}

	public function memberDashboard()
	{
		$session = $this->request->session()->read("User");
		$uid = intval($session["id"]);
		$conn = ConnectionManager::get('default');
		$mem_table = TableRegistry::get("GymMember");
		$grp_tbl = TableRegistry::get("GymGroup");
		$message_tbl = TableRegistry::get("GymMessage");
		$membership_tbl = TableRegistry::get("Membership");
		$res_tbl = TableRegistry::get("gymReservation");
		$notice_tbl = TableRegistry::get("gymNotice");

		$schedule_tbl = TableRegistry::get("classSchedule");

		$members = $mem_table->find("all")->where(["role_name"=>"member"]);
		$members = $members->count();

		$notices = $notice_tbl->find("all")->where(['end_date >' => 'NOW()'])->count();

		$reservations_array = $res_tbl->find("all")->contain(["ReservationList"])->toArray();
		foreach ($reservations_array as $keyevent => $event) {
			$return = true;
			foreach ($event['reservation_list'] as $keyItem => $item) {
				if(strtotime($item['date']) < time()){
					unset($reservations_array[$keyevent]['reservation_list'][$keyItem]);
				}
			}
			if(count($reservations_array[$keyevent]['reservation_list']) === 0){
				unset($reservations_array[$keyevent]);
			}
		}
		$reservations = count($reservations_array);

		$courses = $schedule_tbl->find("all")->count();

		$staff_members = $mem_table->find("all")->where(["role_name"=>"staff_member"]);
		$staff_members = $staff_members->count();

		$curr_id = $uid;
		$messages = $message_tbl->find("all")->where(["receiver"=>$curr_id]);
		$messages = $messages->count();

		$groups = $grp_tbl->find("all");
		$groups = $groups->count();

		$membership = $membership_tbl->find("all")->limit(5)->select(["membership_label","gmgt_membershipimage"])->hydrate(false)->toArray();
		$groups_data = $grp_tbl->find("all")->limit(5)->select(["name","image"])->hydrate(false)->toArray();

		$cal_array = $this->getCalendarData();

		$cal_lang = $this->GYMFunction->getSettings("calendar_lang");
		//debug($cal_lang); die();
		$this->set("cal_lang",$cal_lang);
		$this->set("cal_array",$cal_array);
		$this->set("members",$members);
		$this->set("staff_members",$staff_members);
		$this->set("messages",$messages);
		$this->set("groups",$groups);
		$this->set("membership",$membership);
		$this->set("groups_data",$groups_data);

		$this->set("notices",$notices);
		$this->set("reservations",$reservations);
		$this->set("courses",$courses);

		$gymCalendar = isset($this->request->data['gymCalendar']) ? $this->request->data['gymCalendar'] : $this->getGymNameByDb($this->request->session()->read('database'));
		$this->set("gymCalendar", $gymCalendar);
		$gyms = unserialize(GYMS1);
		$this->set("gyms", $gyms);

		$weight_data["data"] = $this->GYMFunction->generate_chart("Weight",$uid);
		$weight_data["option"] = $this->GYMFunction->report_option("Weight");
		$this->set("weight_data",$weight_data);

		$height_data["data"] = $this->GYMFunction->generate_chart("Height",$uid);
		$height_data["option"] = $this->GYMFunction->report_option("Height");
		$this->set("height_data",$height_data);

		$thigh_data["data"] = $this->GYMFunction->generate_chart("Thigh",$uid);
		$thigh_data["option"] = $this->GYMFunction->report_option("Thigh");
		$this->set("thigh_data",$thigh_data);

		$chest_data["data"] = $this->GYMFunction->generate_chart("Chest",$uid);
		$chest_data["option"] = $this->GYMFunction->report_option("Chest");
		$this->set("chest_data",$chest_data);

		$waist_data["data"] = $this->GYMFunction->generate_chart("Waist",$uid);
		$waist_data["option"] = $this->GYMFunction->report_option("Waist");
		$this->set("waist_data",$waist_data);

		$arms_data["data"] = $this->GYMFunction->generate_chart("Arms",$uid);
		$arms_data["option"] = $this->GYMFunction->report_option("Arms");
		$this->set("arms_data",$arms_data);

		$fat_data["data"] = $this->GYMFunction->generate_chart("Fat",$uid);
		$fat_data["option"] = $this->GYMFunction->report_option("Fat");
		$this->set("fat_data",$fat_data);

		$user = $this->user;
		$this->set("user",$user);

	}

	public function staffAccDashboard()
	{
		$session = $this->request->session()->read("User");
		$uid = intval($session["id"]);
		$conn = ConnectionManager::get('default');
		$mem_table = TableRegistry::get("GymMember");
		$grp_tbl = TableRegistry::get("GymGroup");
		$message_tbl = TableRegistry::get("GymMessage");
		$membership_tbl = TableRegistry::get("Membership");
		$res_tbl = TableRegistry::get("gymReservation");
		$notice_tbl = TableRegistry::get("gymNotice");

		$members = $mem_table->find("all")->where(["role_name"=>"member"]);
		$members = $members->count();

		$staff_members = $mem_table->find("all")->where(["role_name"=>"staff_member"]);
		$staff_members = $staff_members->count();

		$curr_id = $uid;
		$messages = $message_tbl->find("all")->where(["receiver"=>$curr_id]);
		$messages = $messages->count();

		$groups = $grp_tbl->find("all");
		$groups = $groups->count();

		$membership = $membership_tbl->find("all")->limit(5)->select(["membership_label","gmgt_membershipimage"])->hydrate(false)->toArray();
		$groups_data = $grp_tbl->find("all")->limit(5)->select(["name","image"])->hydrate(false)->toArray();

		$cal_array = $this->getCalendarData();

		$cal_lang = $this->GYMFunction->getSettings("calendar_lang");

		$this->set("cal_lang",$cal_lang);
		$this->set("cal_array",$cal_array);
		$this->set("members",$members);
		$this->set("staff_members",$staff_members);
		$this->set("messages",$messages);
		$this->set("groups",$groups);
		$this->set("membership",$membership);
		$this->set("groups_data",$groups_data);

		$gymCalendar = isset($this->request->data['gymCalendar']) ? $this->request->data['gymCalendar'] : $this->getGymNameByDb($this->request->session()->read('database'));
		$this->set("gymCalendar", $gymCalendar);
		$gyms = unserialize(GYMS1);
		$this->set("gyms", $gyms);
	}

	public function getCalendarData()
	{
		$session = $this->request->session()->read("User");
		$res_tbl = TableRegistry::get("gymReservation");
		$mem_table = TableRegistry::get("GymMember");
		$grp_tbl = TableRegistry::get("GymGroup");
		$message_tbl = TableRegistry::get("GymMessage");
		$membership_tbl = TableRegistry::get("Membership");
		$notice_tbl = TableRegistry::get("gymNotice");

		$weekagoTimeStamp = strtotime('-7 days');
		$monthAheadTimeStamp = strtotime('90 days');

		//debug($this->request->params);die();
		$cal_array = array();
		if(count($this->request->params['pass'])> 0) {
			$club = $this->request->params['pass'][0];
		}
		$session = $this->request->session()->read("User");

		$cal_arrayClass = $this->allClassScheduleFromDatabases(
																	$weekagoTimeStamp,
																	$monthAheadTimeStamp,
																	$session["role_name"],
																	isset($club) ? $club : null);
		$cal_array = array_merge($cal_array, $cal_arrayClass);

		$reservationdata = $res_tbl->find("all")->contain(['ReservationList'])->hydrate(false)->toArray();

		if(!empty($reservationdata))
		{
			foreach ($reservationdata as $retrieved_data){


				$res_mem_table = TableRegistry::get("ReservationMember");
				$row = $res_mem_table->find()->where(["reservation_id"=>$retrieved_data['id'],"member_id"=>$session['id']]);

				$color = $retrieved_data['color'];
				$state = 'enabled';
				if($row->count() > 0){
					$state = 'disabled';
				}

				if($retrieved_data['max_members'] == $retrieved_data['number_participants']){
					$state = 'disabled';
				}
				if(empty($retrieved_data['reservation_list'])){
					continue;
				}
				foreach ($retrieved_data['reservation_list'] as $key => $reservation_item) {
					if((strtotime($reservation_item["date"]) > $weekagoTimeStamp)&&(strtotime($reservation_item["date"]) < $monthAheadTimeStamp)){

						//$reservation_item["start_time"] = str_ireplace([":AM",":PM"],["",""],$reservation_item["start_time"]);
						$start_time = $reservation_item["start_time"];
						//$reservation_item["end_time"] = str_ireplace([":AM",":PM"],["",""],$reservation_item["end_time"]);
						$end_time = $reservation_item["end_time"];
						$start_time = date("H:i:s", strtotime($start_time));
						$end_time = date("H:i:s", strtotime($end_time));
						//debug(strtotime($reservation_item["date"]));die();
						$reservation_item["date"] = Time::createFromTimestamp(strtotime($reservation_item["date"]));

						$cal_array [] = array (
								'title' => $retrieved_data["event_name"],
								'start' => $reservation_item["date"]->format("Y-m-d")."T{$start_time}",
								'end' => $reservation_item["date"]->format("Y-m-d")."T{$end_time}",
								'color' => $color,
								'url' => Router::url(['controller' => 'GymReservation', 'action' => 'editReservation', $retrieved_data['id']]),
								'max_members' => $retrieved_data['max_members'],
								'number_participants' => $retrieved_data['number_participants'],
								'state' => $state,
						);
					}
				}
				//debug($cal_arrayt);die();
			}
		}

		$birthday_boys=$mem_table->find("all")->where(["role_name"=>"member"])->group("id")->hydrate(false)->toArray();
		$boys_list="";

		if (! empty ( $birthday_boys )) {
			foreach ( $birthday_boys as $boys ) {
				//$boys_list.=$boys->display_name." ";
				if(isset($boys["birth_date"])){
					$startdate = $boys["birth_date"]->format("Y");
					$enddate = $startdate + 10;
					$years = range($startdate,$enddate,1);
					foreach($years as $year)
					{
						 /*$cal_array [] = array (
								'title' => $boys["first_name"]."'s Birthday",
								'start' =>$boys["birth_date"]->format("Y-m-d"),
								'end' => $boys["birth_date"]->format("Y-m-d"),
								'backgroundColor' => '#F25656' */
							if((strtotime($boys["birth_date"]) > $weekagoTimeStamp)&&(strtotime($boys["birth_date"]) < $monthAheadTimeStamp)){
								 $cal_array [] = array (
									'title' => $boys["first_name"]."'s Birthday",
									'start' =>$year.'-'.$boys["birth_date"]->format("m-d"),
									'end' => $year.'-'.$boys["birth_date"]->format("m-d"),
									'backgroundColor' => '#F25656',
									'description' => 'BD',
									'url' => Router::url(['controller' => 'GymMember', 'action' => 'editMember', $boys['id']])
								);
						}
					}
				}

			}
		}
		##################################
		$all_notice = "";
		if($session["role_name"] == "administrator")
		{
			$all_notice = $notice_tbl->find("all")->hydrate(false)->toArray();
		}
		else{
			$all_notice = $notice_tbl->find("all")->where(["OR"=>[["notice_for"=>"all"],["notice_for"=>$session["role_name"]]]])->hydrate(false)->toArray();
		}
		//debug($all_notice);die();
		if (! empty ( $all_notice )) {
			foreach ( $all_notice as $notice ) {
				//$i=1;
				//$test = $notice["start_date"];
				//debug($notice["start_date"]->format('Y-m-d'));die();
				if((strtotime($notice["start_date"]->format('Y-m-d')) > $weekagoTimeStamp)&&(strtotime($notice["start_date"]->format('Y-m-d')) < $monthAheadTimeStamp)){

					//$notice["start_time"] = str_ireplace([":AM",":PM"],["",""],$notice["start_time"]);
					//$notice["end_time"] = str_ireplace([":AM",":PM"],["",""],$notice["end_time"]);
					$start_time = $notice["start_time"];
					$end_time = $notice["end_time"];

					$start_time = date("H:i:s", strtotime($start_time));
					$end_time = date("H:i:s", strtotime($end_time));

					//'start' => $reservation_item["date"]->format("Y-m-d")."T{$start_time}",
					//'end' => $reservation_item["date"]->format("Y-m-d")."T{$end_time}",

					//$row['start_date'] = $row['start_date']->format('d-m-Y');
					//$row['end_date'] = $row['end_date']->format('d-m-Y');
					//debug($end_time);die();

					$enddate = $notice["end_date"]->format("Y-m-d");
					//debug($enddate);die();
					$enddate = (string) $enddate;
					//var_dump($end_time);die();
					$enddate = $enddate."T{$end_time}";
					$cal_array[] = array (
							'title' => $notice["notice_title"],
							'start' => $notice["start_date"]->format("Y-m-d")."T{$start_time}",
							'end' => $enddate,
							'color' => '#F25656',
							'url' => Router::url(['controller' => 'GymNotice', 'action' => 'editNotice', $notice['id']])
					);
				}
			}
		}
		return $cal_array;
	}

	function getGymNameByDb($db){
		$gymsData = unserialize(GYMS1);
		foreach ($gymsData as $gymName => $data) {
			if($data['db'] == $db){
				return $gymName;
			}
		}
	}
	function allClassScheduleFromDatabases($weekagoTimeStamp, $monthAheadTimeStamp, $type,$club = null){
		$gymsData = unserialize(GYMS1);

		$gymClub = isset($this->request->data['gymCalendar']) ? $this->request->data['gymCalendar'] : $this->getGymNameByDb($this->request->session()->read('database'));

		$gyms[$gymClub] = $gymsData[$gymClub];
		$initialDb = $this->request->session()->read('database');
		$cal_array = [];
		//debug($gyms);die;
		foreach ($gyms as $key => $gym) {
			$database = $gym['db'];
			//debug($gym);die;
			ConnectionManager::alias($database, 'default');
			if($type == 'member') {
				$cal_arrayClass = $this->getClassScheduleMember($key, $weekagoTimeStamp, $monthAheadTimeStamp, $club = null);
			}else{
				$cal_arrayClass = $this->getClassSchedule($initialDb == $database, $key, $weekagoTimeStamp, $monthAheadTimeStamp, $club = null);
			}
			$cal_array = array_merge($cal_array, $cal_arrayClass);
		}
		ConnectionManager::alias($initialDb, 'default');
		return $cal_array;
	}
	function getClassSchedule($isDefaultDb, $gym, $weekagoTimeStamp, $monthAheadTimeStamp, $club = null){
		$cal_array = [];
		$class_schedule_tbl = TableRegistry::get("classSchedule");
		if(isset($club)) $tClassSchedule = $class_schedule_tbl->find("all")->where(["location"=>$club])->hydrate(false)->toArray();
		else $tClassSchedule = $class_schedule_tbl->find("all")->hydrate(false)->toArray();
		if(is_array($tClassSchedule)) {
			foreach($tClassSchedule as $classe) {
				$c = 	$class_schedule_tbl->ClassScheduleList->find("all")->where(["class_id"=>$classe['id']])->hydrate(false)->toArray();
				if(is_array($c)) {
					foreach($c as $hc) {
						$getClassInfo = $class_schedule_tbl->find("all")->where(["id"=>$hc['class_id']])->hydrate(false)->first();
						$hc['class_name'] = $getClassInfo['class_name'];

						$start_time = $hc["start_time"];
						$end_time = $hc["end_time"];
						$start_time = date("H:i:s", strtotime($start_time));
						$end_time = date("H:i:s", strtotime($end_time));

						if((strtotime($start_time) > $weekagoTimeStamp)&&(strtotime($start_time) < $monthAheadTimeStamp)){
							$cal_array [] = array (
								'title' => $hc['class_name'] .' || '. $gym,
								'start' => $hc['days']."T{$start_time}",
								'end' => $hc['days']."T{$end_time}",
								'url' => $isDefaultDb ? Router::url(['controller' => 'ClassSchedule', 'action' => 'editClass', $getClassInfo['id']]) : '#',
								'backgroundColor' => isset($classe['color']) ? $classe['color'] : '#4169E1'
							);
						}
					}
				}
			}
		}
		return $cal_array;
	}

	public function getClassScheduleMember($gym, $weekagoTimeStamp, $monthAheadTimeStamp, $club = null){
		$cal_array = [];
		$session = $this->request->session()->read("User");
		$class_schedule_tbl = TableRegistry::get("classSchedule");
		$assign_class = array();

		//$classes_list = $class_schedule_tbl->GymMemberClass->find()->where(["member_id"=>$session["id"]])->hydrate(false)->toArray();
		$classes_list = $class_schedule_tbl->find()->hydrate(false)->toArray();
		if(is_array($classes_list)) {
			foreach($classes_list as $class)
			{
				if(isset($club)) {
					 //list($class_schedule) = $class_schedule_tbl->find("all")->where(["id"=>$class["assign_class"]])->hydrate(false)->toArray();
					 list($class_schedule) = $class;
					//if($class_schedule['location']==$club) $assign_class[] = $class["assign_class"];
					if($class_schedule['location']==$club) $assign_class[] = $class["id"];
				}
				else $assign_class[] = $class["id"];
			}

			if(count($assign_class)>0) {
				$c = $class_schedule_tbl->ClassScheduleList->find("all")->where(["class_id IN"=>$assign_class])->hydrate(false)->toArray();
				if((is_array($c))&&(count($c) > 0)) {
					foreach($c as $hc) {
						$getClassInfo = $class_schedule_tbl->find("all")->where(["id"=>$hc['class_id']])->hydrate(false)->first();
						$hc['class_name'] = $getClassInfo['class_name'];

						$start_time = $hc["start_time"];
						$end_time = $hc["end_time"];
						$start_time = date("H:i:s", strtotime($start_time));
						$end_time = date("H:i:s", strtotime($end_time));

						if((strtotime($start_time) > $weekagoTimeStamp)&&(strtotime($start_time) < $monthAheadTimeStamp)){
							$cal_array [] = array (
								'title' => $hc['class_name'] .' || '. $gym,
								'start' => $hc['days']."T{$start_time}",
								'end' => $hc['days']."T{$end_time}",
								'backgroundColor' => isset($getClassInfo['color']) ? $getClassInfo['color'] : '#4169E1'
							);
						}
					}
				}
			}
		}
		return $cal_array;
	}
	public function isAuthorized($user)
	{
		$role_name = $user["role_name"];
		$curr_action = $this->request->action;
		$admin_actions = ["index","adminDashboard","memberDashboard","staffAccDashboard", "map"];
		$members_actions = ["index","memberDashboard", "map"];
		$staff_acc_actions = ["index","staffAccDashboard", "map"];
		switch($role_name)
		{
			CASE "administrator":
				if(in_array($curr_action,$admin_actions))
				{return true;}else{return false;}
			break;

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

	public function map()
	{
		$gym1 = unserialize(GYMS1);
		$maps = [];
		foreach ($gym1 as $gym => $data) {
		  $maps[] = $data['map'];
		}
		$maps = json_encode($maps);
		$this->set("maps", $maps);
	}
	/* public function updatedb(){
		$gymMemberTable = TableRegistry::get("GymMember");
		$classScheduleTable = TableRegistry::get("ClassSchedule");
		$gymMemberClassTable = TableRegistry::get("GymMemberClass");

		$members = $gymMemberTable->find()->select(['id'])->where(['member_type' => 'Member'])->limit(200)->page(10);
		$classSchedule = $classScheduleTable->find()->select(['id']);
		$i = 0;
		foreach ($members as $member) {
			foreach($classSchedule as $class) {
				$data[$i]["member_id"] = $member->id;
				$data[$i]["assign_class"] = $class->id;
				$i++;
			}
		}
		$entities = $gymMemberClassTable->newEntities($data);
		$result = $gymMemberClassTable->saveMany($entities);
		die('test');
	} */
}
