<?php
echo $this->Html->css('fullcalendar');
echo $this->Html->script('moment.min');
echo $this->Html->script('fullcalendar.min');
echo $this->Html->script('lang-all');
?>
<style>
.content-wrapper, .right-side {
    background-color: #F1F4F9 !important;
}
.panel-heading{
	height: 52px;
  background-color: #4E5E6A;
	padding: 0 0 0 21px;
	margin: 0;
}
.panel-heading .panel-title {
    font-size: 16px;
	color :#eee;
    float: left;
    margin: 0;
    padding: 0;
	line-height :3em;
    font-weight: 600;
}

@media only screen and (max-width : 1200px) {
    #calendar {
      max-width: 900px;
      margin: 0 auto;
    }

    //Styles added to show Horizontal Scroll Bar
    .fc-view-container {
      width: auto;
    }

    .fc-view-container .fc-view {
      overflow-x: scroll;
    }

    .fc-view-container .fc-view > table {
      width: 1200px;
    }
  }
</style>
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
	 $(document).ready(function() {
		 $('#calendar').fullCalendar({
       header: {
					left: 'prev,next',
					center: 'title',
					right: ''
				},
        defaultView:'month',
        timeFormat: 'H:mm',
        contentHeight:1000,
        lang: '<?php echo $cal_lang;?>',
				editable: false,
        eventRender: function (event, element) {
          element.attr('href', 'javascript:void(0);');
          element.click(function() {
              $("#eventTitle").html(event.title);
              $("#detailEvent").empty();
              startTime = moment(event.start).format('D/M/Y H:mm');
              if(startTime !== "Invalid date"){
                $("#detailEvent").append("<li class='list-group-item'><strong><?=__('Start')?>: </strong>" + startTime + "</li>");
              }
              endTime = moment(event.end).format('D/M/Y H:mm');
              if(endTime !== "Invalid date"){
                $("#detailEvent").append("<li class='list-group-item'><strong><?=__('End')?>: </strong>" + endTime + "</li>");
              }
              if(event.max_members != undefined || event.max_members != null){
                  $("#detailEvent").append("<li class='list-group-item'><strong><?=__('Max member')?>: </strong>" + event.max_members + "</li>");
              }

              if(event.number_participants != undefined || event.number_participants != null){
                  $("#detailEvent").append("<li class='list-group-item'><strong><?=__('Participants')?>: </strong>" + event.number_participants + "</li>");
              }

              if(event.number_participants != undefined || event.number_participants != null){
                $("div.modal-footer a.reservation").remove("");
                if(event.state == 'enabled'){
                    $(".modal-footer").prepend("<a href='<?php echo $this->Gym->createurl('GymReservation', 'reservationList');?>' type='button' class='reservation btn btn-primary'><?=__('Reservation')?></a>");
                }else{
                  $(".modal-footer").prepend("<a href='<?php echo $this->Gym->createurl('GymReservation', 'reservationList');?>' type='button' class='reservation btn btn-primary disabled'><?=__('Reservation')?></a>");
                }
              }

              //$("#eventContent").dialog({ modal: true, title: event.title, width:350});
              $("#eventContent").modal('show');
          });
      },
			eventLimit: false, // allow "more" link when too many events
      viewRender: function(view,element) {
              var firstDate = new Date();
              var end = new Date();
              //end.setMonth(firstDate.getMonth());
              //end.setDate(</?php echo $numerodias; ?>);
              firstDate.setDate(firstDate.getDate() - 7);
              end.setDate(end.getDate() + 90);

              if ( end < view.end) {
                  $("#calendar .fc-next-button").hide();
                  return false;
                  alert(end);
              }
              else {
                  $("#calendar .fc-next-button").show();
              }

              if ( view.start < firstDate) {
                  $("#calendar .fc-prev-button").hide();
                  return false;
              }
              else {
                  $("#calendar .fc-prev-button").show();
              }
          },
			events: <?php echo json_encode($cal_array);?>

		});

	});
</script>
<?php
	$session = $this->request->session();
	$pull = ($session->read("User.is_rtl") == "1") ? "pull-left" : "pull-right";
?>

<div id="eventContent" title="Event Details" class="modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 id="eventTitle" class="modal-title"></h5>
      </div>
      <div class="modal-body" style="overflow:hidden;">
        <div class="card">
          <ul class="list-group list-group-flush" id="detailEvent">

          </ul>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div>
  </div>
</div>

<section class="content">
<div id="main-wrapper">
		<div class="row"><!-- Start Row2 -->
		<div class="row left_section col-sm-12">
			<div class="col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-3">
  			<a href="<?php echo $this->request->base ."/classSchedule/classList";?>">
  				<div class="panel info-box panel-white">
  					<div class="panel-body member">
  						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/workout.png" class="dashboard_background">
  						<div class="info-box-stats">
  							<p class="counter"><?php echo $courses;?> <span class="info-box-title"><?php echo __("Cours");?></span></p>
  						</div>
  					</div>
  				</div>
  			</a>
			</div>
			<div class="col-xs-5 col-sm-3">
  			<a href="<?php echo $this->request->base ."/GymReservation/ReservationList";?>">
  				<div class="panel info-box panel-white">
  					<div class="panel-body staff-member">
  						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/calendar-icon.png" class="dashboard_background">
                          <div class="info-box-stats">
  							<p class="counter"><?php echo $reservations;?><span class="info-box-title"><?php echo __("Reservations");?></span></p>
  						</div>
  					</div>
  				</div>
  				</a>
			</div>

      <div class="col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-3">
  			<a href="<?php echo $this->request->base ."/dashboard/map";?>">
  				<div class="panel info-box panel-white">
  					<div class="panel-body group">
  						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/map.png" class="dashboard_background">
  						<div class="info-box-stats groups-label">
  							<p class="counter"><span class="info-box-title">Map</span></p>
  						</div>
  					</div>
  				</div>
  				</a>
			</div>

			<!--div class="col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-3">
  			<a href="<?php echo $this->request->base ."/gym-notice/notice-list";?>">
  				<div class="panel info-box panel-white">
  					<div class="panel-body group">
  						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/notices.png" class="dashboard_background">
  						<div class="info-box-stats groups-label">
  							<p class="counter"><?php echo $notices;?><span class="info-box-title"><?php echo __("Notices");?></span></p>
  						</div>

  					</div>
  				</div>
  				</a>
			</div-->
			<div class="col-xs-5 col-sm-3">
  			<a href="<?php echo $this->request->base ."/gym-message/inbox";?>">
  				<div class="panel info-box panel-white">
  					<div class="panel-body message">
  						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/messages-icon.png" class="dashboard_background">
  						<div class="info-box-stats">
  							<p class="counter"><?php echo $messages;?><span class="info-box-title"><?php echo __("Message");?></span></p>
  						</div>
  					</div>
  				</div>
  				</a>
			</div>
			</div>
			<div class="col-md-4 membership-list <?php echo $pull;?> col-sm-4 col-xs-12">
				<!--div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"></?php echo __("Membership");?></h3>
					</div>
					<div class="panel-body">
						</?php
						foreach($membership as $ms)
						{
							$m_img = (!empty($ms["gmgt_membershipimage"])) ? $ms["gmgt_membershipimage"] : "Thumbnail-img2.png";
							?>
							<p>
								<img src="</?php echo $this->request->base ."/webroot/upload/" .$m_img; ?>" height="40px" width="40px" class="img-circle">
								</?php echo $ms["membership_label"];?>
							</p>
						</?php
						} ?>
					</div>
				</div-->
				<!--div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"></?php echo __("Group List");?></h3>
					</div>
					<div class="panel-body">
						</?php
						foreach($groups_data as $gd)
						{?>
							<p>
								<img src="</?php echo $this->request->base ."/webroot/upload/" .$gd["image"]; ?>" height="40px" width="40px" class="img-circle">
								</?php echo $gd["name"];?>
							</p>
						</?php
						} ?>
					</div>
				</div-->
		   </div>
			<div class="col-md-12 col-sm-12 col-xs-12">
<?php
	/*		$clubs["FitnessForMe BOBIGNY"]= "FitnessForMe BOBIGNY";
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
			echo '<select id="club" name="club" name="location" class="form-control" onChange="afficherparclub(\''.$this->Gym->createurl("Dashboard","member-dashboard").'\');">';
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
				<div class="panel panel-white">
					<div class="panel-body">
						<div id="calendar">
						</div>
				</div>
			</div>

		</div>	<!-- End row2 -->
		<div class="row inline"><!-- Start Row3 -->
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Weight Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="weight_report" style="width: 100%; height: 250px;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$weight_chart = $GoogleCharts->load( 'LineChart' , 'weight_report' )->get( $weight_data["data"] , $weight_data["option"] );

							if(empty($weight_data["data"]) || count($weight_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($weight_data["data"]) && count($weight_data["data"]) > 1)
							echo $weight_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Waist  Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="waist_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$waist_chart = $GoogleCharts->load( 'LineChart' , 'waist_report' )->get( $waist_data["data"] , $waist_data["option"] );

							if(empty($waist_data["data"]) || count($waist_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($waist_data["data"]) && count($waist_data["data"]) > 1)
							echo $waist_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">

					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Thigh Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="thing_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$thing_chart = $GoogleCharts->load( 'LineChart' , 'thing_report' )->get( $thigh_data["data"] , $thigh_data["option"] );

							if(empty($thigh_data["data"]) || count($thigh_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($thigh_data["data"]) && count($thigh_data["data"]) > 1)
							echo $thing_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Arms Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="arms_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$arms_chart = $GoogleCharts->load( 'LineChart' , 'arms_report' )->get( $arms_data["data"] , $arms_data["option"] );

							if(empty($arms_data["data"]) || count($arms_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($arms_data["data"]) && count($arms_data["data"]) > 1)
							echo $arms_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Height Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="height_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$height_chart = $GoogleCharts->load( 'LineChart' , 'height_report' )->get( $height_data["data"] , $height_data["option"] );

							if(empty($height_data["data"]) || count($height_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($height_data["data"]) && count($height_data["data"]) > 1)
							echo $height_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Chest Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="chest_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$chest_chart = $GoogleCharts->load( 'LineChart' , 'chest_report' )->get( $chest_data["data"] , $chest_data["option"] );

							if(empty($chest_data["data"]) || count($chest_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($chest_data["data"]) && count($chest_data["data"]) > 1)
							echo $chest_chart;?>
						</script>
					</div>
				</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Fat Progress Report");?></h3>
					</div>
					<div class="panel-body">
						<div id="fat_report" style="width: 100%; height: 250px;float:left;">
							<?php
							$GoogleCharts = new GoogleCharts;
							$fat_chart = $GoogleCharts->load( 'LineChart' , 'fat_report' )->get( $fat_data["data"] , $fat_data["option"] );

							if(empty($fat_data["data"]) || count($fat_data["data"]) == 1)
							echo __('There is not enough data to generate report'); ?>
						</div>
						<script type="text/javascript" src="https://www.google.com/jsapi"></script>
						<script type="text/javascript">
							<?php
							if(!empty($fat_data["data"]) && count($fat_data["data"]) > 1)
							echo $fat_chart;?>
						</script>
					</div>
				</div>
			</div>
		</div><!-- End Row3 -->


	</div>
 </div>
</section>
