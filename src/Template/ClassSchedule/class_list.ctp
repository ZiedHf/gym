<?php $session = $this->request->session()->read("User");?>

<?php
$column_5 = 'false';
if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member")
{
	$column_5 = 'true';
	?>
<script>

$(document).ready(function(){
	//var table = $(".mydataTable").DataTable();
	//table.column(5).visible( true );
});
</script>
<?php } ?>

<script>
$(document).ready(function(){
	$(".mydataTable").DataTable({
		"responsive": true,
		"scrollX": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": false,"visible":<?=$column_5?>}],
	"language" : {<?php echo $this->Gym->data_table_lang();?>}


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
				<?php echo __("Class List");?>
				<small><?php echo __("Class Schedule");?></small>
			  </h1>
			  <ol class="breadcrumb">
				<?php
				//if($session["role_name"] == "administrator" || $session["role_name"] == "member" || $session["role_name"] == "staff_member")
				if(1 == 2)
				{ ?>
				<a href="<?php echo $this->Gym->createurl("ClassSchedule","viewSchedule");?>" class="btn btn-flat btn-custom"><i class="fa fa-calendar"></i> <?php echo __("Class Schedules");?></a>
		   <?php }
				if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member")
				{ ?>
				&nbsp;
				<a href="<?php echo $this->Gym->createurl("ClassSchedule","addClass");?>" class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Class Schedule");?></a>
				<?php } ?>
			  </ol>
			</section>
		</div>
		<hr>
		<div class="box-body">
			<table class="mydataTable table table-striped">
			<thead>
				<tr>
					<th><?php echo __("Class Name");?></th>
					<th><?php echo __("Staff Name");?></th>
					<th><?php echo __("Time");?></th>
					<th><?php echo __("Location");?></th>
					<th><?php echo __("Action");?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach($data as $row)
			{
				$content = "";
				foreach($row['class_schedule_list'] as $key => $class_time){
					//$class_time['days'] = json_decode($class_time['days']);
					$class_time['days'] = date('d-m-Y', strtotime($class_time['days']));

					$content .= "<li class='dropdown-item' type='button'>".__($class_time['days']). ' - ' . "{$class_time['start_time']}" . ' - ' . "{$class_time['end_time']}</li>";
				}

				$html = '<div class="dropdown">
								  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    '.__('More').'
								  </button>
								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="box-shadow: 10px 10px 5px #888888; padding:5px 2px;">
								    '.$content.'
								  </ul>
								</div>';

				$html = '
					<button
						type="button"
						class="btn btn-primary"
						data-toggle="modal"
						data-target="#exampleModalCenter"
						data-title="'.$row['class_name'].'"
						data-content="'.$content.'"
						>
						'.__('More').'
					</button>
				';

				echo "<tr>
						<td>{$row['class_name']}</td>
						<td>{$row['gym_member']['first_name']} {$row['gym_member']['last_name']}</td>
						<td>{$html}</td>
						<td>{$row['location']}</td>
						<td>
							<a href='{$this->request->base}/ClassSchedule/editClass/{$row['id']}' title='Edit' class='btn btn-flat btn-primary'><i class='fa fa-edit'></i></a>
							<a href='{$this->request->base}/ClassSchedule/deleteClass/{$row['id']}' title='Delete' class='btn btn-flat btn-danger' onClick=\"return confirm('Are you sure,You want to delete this record?');\"><i class='fa fa-trash-o'></i></a>
						</td>
					</tr>";
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php echo __("Class Name");?></th>
					<th><?php echo __("Staff Name");?></th>
					<th><?php echo __("Time");?></th>
					<th><?php echo __("Location");?></th>
					<th><?php echo __("Action");?></th>
				</tr>
			</tfoot>
		</table>
		</div>
		<div class="overlay gym-overlay">
		  <i class="fa fa-refresh fa-spin"></i>
		</div>
	</div>
</section>

<!-- Modal -->
<div class="modal fade bd-example-modal-sm" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="exampleModalLongTitle">Class</h5>
      </div>
      <div class="modal-body" style="overflow: hidden;">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
	$('#exampleModalCenter').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var content = button.data('content');
			var title = button.data('title');
			var modal = $(this).modal('handleUpdate');
			modal.find('.modal-title')[0].innerHTML = '<h4>'+title+'</h4>';
			var body = modal.find('.modal-body')[0].innerHTML = "<ul>"+content+"</ul>";
		})
</script>
