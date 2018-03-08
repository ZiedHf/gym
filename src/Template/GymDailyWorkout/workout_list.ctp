<?php $session = $this->request->session()->read("User");?>

<?php
$column_4 = 'false';
if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member" || $session["role_name"] == "member")
{
	$column_4 = 'true'; 
} 
?>

<script>
$(document).ready(function(){
	$(".mydataTable.table1").DataTable({
		"responsive": true,
		"scrollX": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": false,"visible":<?=$column_4?>}],
	"language" : {<?php echo $this->Gym->data_table_lang();?>}
	});

	<?php if(!empty($measurments)) { ?>
	$("#level_list.mydataTable.table2").DataTable({
		"sScrollX": "99.2%", // Minimize the width of table to hide the scrollbar on desktop
		"responsive": true,
		"scrollX": false,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
									{"bSortable": false},
									{"bSortable": true},
									{"bSortable": true},
									{"bSortable": true},
									{"bSortable": false}],
		"language" : {<?php echo $this->Gym->data_table_lang();?>}
	});
	<?php } ?>
});
</script>

<section class="content">
	<br>
	<div class="col-md-12 box box-default">
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-bars"></i>
				<?php echo __("Workouts") . ' & ' . __("Measurements"); ?>
				<small><?php echo __("Workout Daily");?></small>
			  </h1>
			  <?php
				if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member" || $session["role_name"] == "member")
				{ ?>
			  <ol class="breadcrumb">
					<a href="<?php echo $this->Gym->createurl("GymDailyWorkout","addWorkout");?>" class="btn btn-sm btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Workout");?></a>
					&nbsp;
					<a href="<?php echo $this->Gym->createurl("GymDailyWorkout","addMeasurment");?>" class="btn btn-sm btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Measurement");?></a>
					&nbsp;
					<a 
						href="<?php echo $this->request->base . 
															'/GymDailyWorkout/viewWorkout/' . 
															$session['id']; 
															?>" 
						class='btn btn-sm btn-custom'>
							<i class='fa fa-eye'></i> 
							<?php echo __("View");?>
					</a>
			  </ol>
			<?php } ?>
			</section>
		</div>
		<hr>
		<div class="box-body">
			<?php if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member"){ ?>
				<table class="mydataTable table1 table table-striped">
					<thead>
						<tr>
							<th><?php echo __("Photo");?></th>
							<th><?php echo __("Member Name");?></th>
							<th><?php echo __("Mobile");?></th>
							<th><?php echo __("Email");?></th>
							<th><?php echo __("Action");?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach($data as $row)
					{
						echo "<tr>
						<td><image src='".$this->request->base ."/webroot/upload/{$row['image']}' class='membership-img img-circle'></td>
						<td>{$row["first_name"]} {$row["last_name"]}</td>
						<td>{$row["mobile"]}</td>
						<td>{$row["email"]}</td>
						<td class='action'>
							<a href='".$this->request->base ."/GymDailyWorkout/viewWorkout/{$row['id']}' class='btn btn-flat btn-primary'><i class='fa fa-eye'></i> ".__("View")."</a>
							<a href='javascript:void(0)' data-url='{$this->request->base}/GymAjax/GymViewMeasurment' class='view_measurment btn btn-flat btn-default view-measurement-popup' data-val='{$row['id']}'>". __("View Measurement")."</a>
						</td>
						</tr>";
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th><?php echo __("Photo");?></th>
							<th><?php echo __("Member Name");?></th>
							<th><?php echo __("Mobile");?></th>
							<th><?php echo __("Email");?></th>
							<th><?php echo __("Action");?></th>
						</tr>
					</tfoot>
				</table>
			<?php } ?>
			<?php if($session["role_name"] == "member"){ ?>
				<!-- From the function GymViewMeasurment in the GymAjaxController -->
				<div class="row">
					<div class="col-sm-12 table table-striped">
						<table class="mydataTable table table2 table-striped" id="level_list">
							<thead>
								<tr>
									<th><?php echo __("Image");?></th>
									<th><?php echo __("Measurement");?></th>
									<th><?php echo __("Result");?></th>
									<th><?php echo __("Record Date");?></th>
									<th><?php echo __("Action");?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							if(!empty($measurments))
							{
								foreach($measurments as $row)
								{
							?>
									<tr id='<?="row_".$row['id']?>'>
										<td><img src='<?=$this->request->webroot?>webroot/upload/<?=$row['image']?>' class='membership-img img-circle'></td>
										<td><?=__($row['result_measurment'])?></td>
										<td><?=$row['result']?></td>
										<td><?=$row['result_date']?></td>
										<td>
											<a href='<?=$this->request->base?>/GymDailyWorkout/editMeasurment/<?=$row['id']?>' class='btn btn-flat btn-primary' title='Edit'><i class='fa fa-edit'></i></a>
											<a href='javascript:void(0)' data-url='<?=$this->request->base?>/GymAjax/deleteMeasurment/<?=$row['id']?>' class='delete_measurment btn btn-flat btn-danger view-measurement-popup' did='<?=$row['id']?>' title='Delete'><i class='fa fa-trash'></i></a>
										</td>
									</tr>
							<?php
								}
							}else{
								echo "<tr><td>".__("No Data Found.")."</td></tr>";
							}
							?>
							</tbody>
							<tfoot>
								<tr>
									<th><?php echo __("Image");?></th>
									<th><?php echo __("Measurement");?></th>
									<th><?php echo __("Result");?></th>
									<th><?php echo __("Record Date");?></th>
									<th><?php echo __("Action");?></th>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>

			<?php } ?>
		<!-- END -->
		</div>
		<div class='overlay gym-overlay'>
			<i class='fa fa-refresh fa-spin'></i>
		</div>
	</div>
</section>
