<?php $session = $this->request->session()->read("User");?>
<script>
$( function() {
    $( document ).tooltip();
  } );
  </script>
<script>
$(document).ready(function(){

  	$(".mydataTable").DataTable({
      "responsive": true,
      "scrollX": true,
      "iDisplayLength" : 50,
  		"order": [[ 1, "asc" ]],
  		"aoColumns":[
  	                  {"bSortable": true},
  	                  {"bSortable": true},
  	                  {"bSortable": true},
                      {"bSortable": true},
                      {"bSortable": false},
                      <?php if($session["role_name"] == "member"){ ?>{"bSortable": true},<?php }
                      if($session["role_name"] == "member"){ ?>{"bSortable": false,"visible":false}<?php }else{ ?>{"bSortable": false}<?php } ?>
  	                  ],
  	"language" : {<?php echo $this->Gym->data_table_lang();?>}
  	});

});

</script>
<?php
if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member" || $session["role_name"] == "accountant")
{ ?>
<script>

$(document).ready(function(){
	//var table = $(".mydataTable").DataTable({"responsive": true});
	//table.column(5).visible( true );
});
</script>
<?php } ?>
<section class="content">
	<br>
	<div class="col-md-12 box box-default">
		<div class="box-header">
			<section class="content-header">
			  <h1>
				<i class="fa fa-bars"></i>
				<?php echo __("Reservation List");?>
				<small><?php echo __("Reservation");?></small>
			  </h1>

				<?php if($session["role_name"] == "member") { ?>
					<h4><?=__('Your coming reservations')?> : <?=$memberReservationNumber?></h4>
				<?php } ?>
			   <?php
			if($session["role_name"] == "administrator" || $session["role_name"] == "staff_member" || $session["role_name"] == "accountant")
			{ ?>
			  <ol class="breadcrumb">
				<a href="<?php echo $this->Gym->createurl("GymReservation","addReservation");?>" class="btn btn-flat btn-custom"><i class="fa fa-plus"></i> <?php echo __("Add Reservation");?></a>
			  </ol>
			<?php } ?>
			</section>
		</div>
		<hr>
		<div class="box-body">
			<table class="mydataTable table table-striped">
			<thead>
				<tr>
					<th><?php echo __("Event Name");?></th>
					<th><?php echo __("Start Time");?></th>
					<th><?php echo __("End Time");?></th>
          <th><?php echo __("Available Places");?></th>
          <!--th></?php echo __("Max members");?></th-->
          <!--th></?php echo __("Participants");?></th-->
          <th><?php echo __("Date");?></th>
          <?php if($session["role_name"] == "member") { ?><th><?php echo __("Your state");?></th> <?php } ?>
					<th><?php echo __("Action");?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th><?php echo __("Event Name");?></th>
					<th><?php echo __("Start Time");?></th>
					<th><?php echo __("End Time");?></th>
          <th><?php echo __("Available Places");?></th>
          <!--th></?php echo __("Max members");?></th>
          <th></?php echo __("Participants");?></th-->
          <th><?php echo __("Date");?></th>
          <?php if($session["role_name"] == "member") { ?><th><?php echo __("Your state");?></th> <?php } ?>
					<th><?php echo __("Action");?></th>
				</tr>
			</tfoot>
			<tbody>
			<?php
			foreach($data as $row)
			{
        //Get reservationList of this reservation
        $content = "";
				foreach($row['reservation_list'] as $key => $reservation_item){
					//$content .= "<li class='dropdown-item' type='button'>".__($reservation_item['date'])."</li>";
					$content .= __($reservation_item['date'])."<br />";
				}
        if($content == ""){
          $content = __("No data available.");
				}
				// button dropdown
				// replaced bu content directly
				/* $html = '<div class="dropdown">
								  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    '.__('More').'
								  </button>
								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="box-shadow: 10px 10px 5px #888888; padding:5px 2px;">
								    '.$content.'
								  </ul>
								</div>'; */

        $content_time = "";
        if(empty($reservation_item['start_time']) && empty($reservation_item['end_time'])){
          $content_time = __("No data available.");
        }else{
          $startTime = ' - ';
          $endTime = ' - ';
          if((!empty($reservation_item["start_time"])) && (!empty($reservation_item["end_time"]))){
            $reservation_item["start_time"] = str_ireplace([":AM",":PM"],[" am"," pm"],$reservation_item["start_time"]);
            $startTime = date('H:i', strtotime($reservation_item['start_time']));
          }
          if(!empty($reservation_item["start_time"])){
            $reservation_item["end_time"] = str_ireplace([":AM",":PM"],[" am"," pm"],$reservation_item["end_time"]);
            $endTime = date('H:i', strtotime($reservation_item['end_time']));
          }

          $content_time_start = $startTime;
					$content_time_end = $endTime;
        }

				// button dropdown
				// replaced bu content directly
				/* $html_time = '<div class="dropdown">'.
                  $row['event_name']
								  .'<button class="btn btn-secondary dropdown-toggle pull-right" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								    '.__('Time').'
								  </button>
								  <ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu3" style="box-shadow: 10px 10px 5px #888888; padding:5px 2px;">
								    '.$content_time.'
								  </ul>
								</div>'; */


        $full = false;
        $class = "bg-success";
        if($row['number_participants'] == $row['max_members']){
          $full = true;
          $class = "bg-danger";
        }
        $availablePlaces = $row['max_members'] - $row['number_participants'];
				echo "<tr>
					<td>{$row['event_name']}</td>";
				echo "<td>{$content_time_start}</td>";
				echo "<td>{$content_time_end}</td>";
        echo "<td style='text-align: center;'><p class='$class'><strong>{$availablePlaces}</strong></p></td>";
        /*echo "<td style='text-align: center;'><p class='$class'><strong>{$row['max_members']}</strong></p></td>
          <td style='text-align: center;'><p class='$class'><strong>{$row['number_participants']}</strong></p></td>";*/
        echo"<td>$content</td>";
          if($session["role_name"] == "member") {
          echo "<td>";

              if($this->gym->isMemberInThisReservation($row['id'], $session['id'])){
                //echo "<a href='javascript: void(0)' data-url='".$this->request->base ."/GymReservation/memberToReservationToggle/{$row['id']}/{$session['id']}' onclick=\"return confirm('Are you sure, you want to unsubscribe from this reservation ?');\" class='btn btn-info btn-flat' id={$row['id']}><i class='fa fa-minus-square' aria-hidden='true'></i></a>";
                echo $this->Html->link("<i class='fa fa-minus-square' aria-hidden='true'></i>", ["controller" => 'GymReservation',"action" => 'memberToReservationToggle', $row['id'], $session['id']], ['class' => 'btn btn-danger btn-flat', 'confirm' => 'Êtes-vous certain de vouloir annuler votre réservation ?', 'escape' => false]);
                echo " <span>".__("Subscribed")."</span>";
              }elseif($row['max_members'] == $row['number_participants']){
                //echo $this->Html->link("<i class='fa fa-plus-square' aria-hidden='true'></i>", ["#"], ['class' => 'btn btn-info btn-flat disabled', 'escape' => false]);
                echo __("Vous pouvez vous réabonner à cet événement plus tard");
              }else{
                //echo "<a href='javascript: void(0)' data-url='".$this->request->base ."/GymReservation/memberToReservationToggle/{$row['id']}/{$session['id']}' onclick=\"return confirm('Are you sure, you want to subscribe to this events ?');\" class='btn btn-info btn-flat' id={$row['id']}><i class='fa fa-plus-square' aria-hidden='true'></i></a>";
                echo $this->Html->link("<i class='fa fa-plus-square' aria-hidden='true'></i>", ["controller" => 'GymReservation',"action" => 'memberToReservationToggle', $row['id'], $session['id']], ['class' => 'btn btn-success btn-flat', 'confirm' => 'Etes vous certain de vouloir participer à cet événement  ?', 'escape' => false]);
                echo " <span>".__("Unsubscribed")."</span>";
              }
          echo "</td>";
          }
          echo "<td><a href='".$this->request->base ."/GymReservation/editReservation/{$row['id']}' class='btn btn-primary btn-flat' title='Edit'><i class='fa fa-edit'></i> </a>";
            echo " <a href='".$this->request->base ."/GymReservation/deleteReservation/{$row['id']}' class='btn btn-danger btn-flat' title='Delete' onclick=\"return confirm('Etes vous certain de vouloir supprimer cet événement ?')\"><i class='fa fa-trash'></i></a>
					</td>
				</tr>";
			}
			?>
			</tbody>
			</table>
			<!-- END -->
		</div>
		<div class='overlay gym-overlay'>
			<i class='fa fa-refresh fa-spin'></i>
		</div>
	</div>
</section>
