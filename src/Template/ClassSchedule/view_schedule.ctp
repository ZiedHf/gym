<?php $session = $this->request->session()->read("User");?>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-calendar"></i>
				<?php echo __("Class Schedules");?>
				<small><?php echo __("Class Schedule");?></small>
			  </h1>
			<br><br>
			<?php
			/*$clubs["FitnessForMe BOBIGNY"]= "FitnessForMe BOBIGNY";
			$clubs["FitnessForMe MONTGERON"]= "FitnessForMe MONTGERON";
			$clubs["FitnessForMe ETAMPES"]= "FitnessForMe ETAMPES";
			$clubs["FitnessForMe SUCY-EN-BRIE"]= "FitnessForMe SUCY-EN-BRIE";
			$clubs["FitnessForMe CERET"]= "FitnessForMe CERET";
			$clubs["FitnessForMe ARRAS"]= "FitnessForMe ARRAS";

			if(count($this->request->params['pass'])> 0) {
				$club_select = $this->request->params['pass'][0];
			}
			echo "<div class='form-group'>";
			echo '<div class="col-md-4">';
			echo '<select id="club" name="club" name="location" class="form-control" onChange="afficherparclub(\''.$this->Gym->createurl("ClassSchedule","viewSchedule").'\');">';
			echo '<option value="">Select Club</option>';
			foreach($clubs as $club) {
				 $selected ="";
                if($club == $club_select) {
                  $selected = "selected='selected'";
                }
				echo '<option value="'.$club.'"'.$selected.'>'.$club.'</option>';
			}
			echo '</select>';
			echo "</div>";
			echo "</div>";*/
			?>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("ClassSchedule","classList");?>" class="btn btn-flat btn-custom"><i class="fa fa-bars"></i> <?php echo __("Class List");?></a>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
		<table class="table table-bordered table-hover">
		<?php
		$days = ["Monday"=>"Monday","Tuesday"=>"Tuesday","Wednesday"=>"Wednesday","Thursday"=>"Thursday","Friday"=>"Friday","Saturday"=>"Saturday","Sunday"=>"Sunday"];

		foreach($days as $day)
		{
			echo "<tr><th width='50' height='50'>". __($day) ."</th><td>";
				foreach($classes as $class)
			{
				//$days = json_decode($class['days']);
				$days = date('l', strtotime($class['days']));
				//if(in_array($day,$days))
				if($day == $days)
				{ ?>
					<div class="btn-group m-b-sm">
						<button class="btn btn-flat btn-primary dropdown-toggle" aria-expanded="false" data-toggle="dropdown"><span class="period_box" id="<?php echo $class['id'];?>"><?php echo $this->Gym->get_class_by_id($class['class_id']);?><span class="time"> <?php echo "({$class['start_time']}-{$class['end_time']})";?> </span></span><span class="caret"></span></button>
						<ul role="menu" class="dropdown-menu">
							<?php if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member")
							{?>
							<li><a href="<?php echo "{$this->request->base}/ClassSchedule/editClass/{$class['class_id']}";?>"><?php echo __("Edit");?></a></li>
						<?php }else{
							echo "<script>$('.caret').hide();</script>";
						}?>
						<!-- <li><a href="<?php echo "{$this->request->base}/ClassSchedule/deleteClass/{$class['id']}";?>" onClick="return confirm('Are you sure you want to delete this record?');"><?php echo __("Delete");?></a></li> -->
						 </ul>
					</div>
		<?php	}
			}
			echo "</td></tr>";
		}
		?>
		</table>
		</div>
	</div>
</section>

<script>
function afficherparclub(url)
{
	var club = document.getElementById("club").value;
	if(club) {
		location.href = url+'/'+club;
	}
	else {
		location.href = url;
	}
}
</script>
