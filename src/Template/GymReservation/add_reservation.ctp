<?php
	echo $this->Html->css('bootstrap-multiselect');
	echo $this->Html->script('bootstrap-multiselect');

	echo $this->Html->css('spectrum');
	echo $this->Html->script('spectrum');
?>
<script type="text/javascript">
$(document).ready(function() {
	// $(".date").datepicker( {format: '<?php echo $this->Gym->getSettings("date_format"); ?>'} );
	//$(".date").datepicker( {format: 'yyyy-mm-dd'} );
	$(".date").datepicker( {format: 'dd-mm-yyyy'} );

	$("#add_time").click(function(){
		var time_list = [];
		var days = $("#event_date").val();
		if(days == null || $(".start_hrs").val() == "" || $(".end_hrs").val() == "")
		{
			alert("Please select days,start time and end time");
			return false;
		}
		$(".time_list").css("display","block");
		var json_days =  JSON.stringify(days);
		var start_time = $(".start_hrs").val() + ":" +  $(".start_min").val();
		var end_time = $(".end_hrs").val() + ":" +  $(".end_min").val();

		//days.replace("/", "-");
		//var mystring = "/"
		days = days.replace(/\//g , "-");

		time_list[0] = days;
		time_list[1] = start_time;
		time_list[2] = end_time;
		var val = JSON.stringify(time_list);

		/* $(".time_list").append("<input type='text' name='time_list[]' class='ssd' value='"+val+"'>"); */

		$(".time_table").append('<tr><td>'+days+'</td><td>'+start_time+'</td><td>'+end_time+'<input type="hidden" name="time_list[]" value='+val+'></td><td>&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-danger class_sch_del_row"><i class="fa fa-times-circle"></i></span></td></tr>');
	});
	//delete time
	$("body").on("click",".class_sch_del_row",function(){
		$(this).parents("tr").remove();
	});

	$("#full").spectrum({
	    color: '<?=$edit ? $data['color'] : '#000000'?>',
	    flat: true,
	    showInput: true,
	    className: "full-spectrum",
	    showInitial: true,
	    showPalette: true,
	    showSelectionPalette: true,
	    maxPaletteSize: 10,
	    preferredFormat: "hex",
	    localStorageKey: "spectrum.demo",
			allowEmpty:false,
			showButtons: false,
	    palette: [
	        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
	        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
	        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
	        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
	        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
	        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
	        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
	        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
	        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
	        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
	        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
	        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
	        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
	        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
	    ]
	});
});
</script>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-bars"></i>
				<?php echo __("Add Reservation");?>
				<small><?php echo __("Schedule");?></small>
			  </h1>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("GymReservation","reservationList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Reservation List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<?php
			echo $this->Form->create("reseravtion_Add",["class"=>"validateForm form-horizontal","role"=>"form"]);
			echo "<div class='form-group'>";
			echo '<label class="control-label col-md-2" for="email">'. __("Event Name").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false,"name"=>"event_name","class"=>"form-control validate[required]","value"=>(($edit)?$data['event_name']:'')]);
			echo "</div>";
			echo "</div>";

			echo "<div class='form-group'>";
			echo '<label class="control-label col-md-2" for="email">'. __("Event Date").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo $this->Form->input("",["label"=>false, "id" => "event_date", "name"=>"event_date","class"=>"form-control date","value"=>(($edit)?$data['event_date']->format("d-m-Y"):'')]);
			echo "</div>";
			echo "</div>";

			echo "<div class='form-group'>";
			echo "<label class='control-label col-md-2'>".__("Max members")."<span class='text-danger'> *</span></label>";
			echo "<div class='col-md-6'>";
			echo $this->Form->input("",["type" => 'number', "min" => 1, "label"=>false,"name"=>"max_members","class"=>"form-control validate[required]","value"=>($edit)?$data['max_members']:""]);
			echo "</div>";
			echo "</div>";

			echo "<div class='form-group'>";
			echo '<label class="control-label col-md-2" for="email">'. __("Event Place").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-6">';
			echo @$this->Form->select("place_id",$event_places,["default"=>$data['place_id'],"empty"=>__("Select Event Places"),"class"=>"form-control events_place_list validate[required]"]);
			echo "</div>";
			echo '<div class="col-md-2">';
			echo "<a href='javascript:void(0)' data-url='{$this->request->base}/GymAjax/EventPlaceList' id='eventplace_list' class='btn btn-flat btn-default'>".__("Add or Remove")."</a>";
			echo "</div>";
			echo "</div>";

			$hrs = ["0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23"];
			$min = ["00"=>"00","15"=>"15","30"=>"30","45"=>"45"];
			//$ampm = ["AM"=>"AM","PM"=>"PM"];

			echo "<div class='form-group'>";
			echo '<label class="control-label col-md-2" for="email">'. __("Start Time").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-2">';
			echo @$this->Form->select("start_hrs",$hrs,["default"=>$data['start_hrs'],"empty"=>__("Select Time"),"class"=>"start_hrs form-control validate[required]"]);
			echo "</div>";
			echo '<div class="col-md-2">';
			echo @$this->Form->select("start_min",$min,["default"=>$data['start_min'],"class"=>"start_min form-control"]);
			echo "</div>";
			/*echo '<div class="col-md-2">';
			echo @$this->Form->select("start_ampm",$ampm,["default"=>$data['start_ampm'],"class"=>"start_ampm form-control"]);
			echo "</div>";*/
			echo "</div>";

			echo "<div class='form-group'>";
			echo '<label class="control-label col-md-2" for="email">'. __("End Time").'<span class="text-danger"> *</span></label>';
			echo '<div class="col-md-2">';
			echo @$this->Form->select("end_hrs",$hrs,["default"=>$data['end_hrs'],"empty"=>__("Select Time"),"class"=>"end_hrs form-control validate[required]"]);
			echo "</div>";
			echo '<div class="col-md-2">';
			echo @$this->Form->select("end_min",$min,["default"=>$data['end_min'],"class"=>"end_min form-control"]);
			echo "</div>";
			/*echo '<div class="col-md-2">';
			echo @$this->Form->select("end_ampm",$ampm,["default"=>$data['end_ampm'],"class"=>"end_ampm form-control"]);
			echo "</div>";*/
			echo "</div>";
			echo "<hr /><br />";
			echo '
				<div class="form-group">
					<div class="col-xs-12 col-md-offset-3 col-md-6">
						<input type="text" name="color" id="full"/>
					</div>
				</div>
			';

			echo $this->Form->button(__("Add Time"),['type'=>'button','id'=>'add_time','class'=>"btn btn-flat btn-success col-md-offset-2","name"=>"add_class"]);
			echo "<br><br>";

			echo "<div class='time_list col-md-10 col-md-offset-2'>";?>
			<table class="table">
				<tr>
					<th><?php echo __("Day");?></th>
					<th><?php echo __("Start Time");?></th>
					<th><?php echo __("End Time");?></th>
					<th><?php echo __("Action");?></th>
				</tr>
				<tbody class="time_table">
					<?php
					if($edit)
					{
						foreach($reservation_list as $reservation)
						{	//$reservation["days"] = date($this->Gym->getSettings("date_format"),strtotime($reservation["date"]));
							$reservation["days"] = date('d-m-Y', strtotime($reservation["date"]));
							?>
							<tr>
								<td><?php echo $reservation["days"];?></td>
								<td><?php echo $reservation["start_time"];?></td>
								<td><?php echo $reservation["end_time"];?>
									<input type="hidden" name="time_list[]" value='[<?php echo "&quot;".$reservation["days"]."&quot;,&quot;".$reservation["start_time"]."&quot;,&quot;".$reservation["end_time"] ."&quot;"; ?>]'>
								</td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;<span class="text-danger class_sch_del_row"><i class="fa fa-times-circle"></i></span></td>
							</tr>
					<?php }
					}?>
				</tbody>
			</table>
			<?php
			echo "</div>";

			echo "<br>";
			echo $this->Form->button(__("Save Class"),['class'=>"btn btn-flat btn-success col-md-offset-2","name"=>"add_class"]);
			echo $this->Form->end();
		?>


		<?php
			if(($edit)&&(isset($data['members']))){
		?>
		<h3><?=__('Subscriptions')?></h3>
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Gender</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data['members'] as $key => $value) { ?>
							<tr>
								<th scope="row"><?=$key+1?></th>
								<td><?=$value['gym_member']['first_name']?></td>
								<td><?=$value['gym_member']['last_name']?></td>
								<td><?=$value['gym_member']['gender']?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
		<?php
			}
		?>
		<!-- END -->
		</div>
		<div class='overlay gym-overlay'>
			<i class='fa fa-refresh fa-spin'></i>
		</div>
	</div>
</section>

<?php
if(!$edit)
{?>
	<script>
		$(".time_list").css("display","none");
	</script>
<?php }
?>
