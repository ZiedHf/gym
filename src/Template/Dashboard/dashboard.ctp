<?php
echo $this->Html->script('moment.min');
echo $this->Html->css('fullcalendar');
echo $this->Html->script('fullcalendar.min');
echo $this->Html->script('lang-all');
//debug($cal_array);die();
?>
<style>
.content-wrapper, .right-side {
    background-color: #F1F4F9 !important;
}
.panel-heading{
	height: 52px;
	background-color: #4E5E6A;
	border-bottom : 3px solid #d71b0d;
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
<?php $numerodias = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')); ?>

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
            $("#startTime").html(moment(event.start).format('D/M/Y H:mm'));
            $("#endTime").html(moment(event.end).format('D/M/Y H:mm'));
            if(event.url === '#') {
              $("#eventLink").hide();
            }else{
              $("#eventLink").show().attr('href', event.url);
            }

            if(event.max_members != undefined || event.max_members != null){
                $("#detailEvent").append("<li class='list-group-item'><strong><?=__('Max member')?>: </strong>" + event.max_members + "</li>");
            }

            if(event.number_participants != undefined || event.number_participants != null){
                $("#detailEvent").append("<li class='list-group-item'><strong><?=__('Participants')?>: </strong>" + event.number_participants + "</li>");
            }

            /*if(event.number_participants != undefined || event.number_participants != null){
                $("div.modal-footer a.reservation").remove("");
                $(".modal-footer").prepend("<a href='<//////?php echo $this->Gym->createurl('GymReservation', 'reservationList');?>' type='button' class='reservation btn btn-primary'></////?=__('Reservation')?></a>");
            }*/
            //$("#eventContent").dialog({ modal: true, title: event.title, width:350});
            $("#eventContent").modal('show');
        });
    },
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
			//eventLimit: true, // allow "more" link when too many events
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
          <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong><?=__('Start')?>: </strong> <span id="startTime"></span><br></li>
            <li class="list-group-item"><strong><?=__('End')?>: </strong><span id="endTime"></span><br></li>
          </ul>
        </div>

        <p id="eventInfo"></p>
      </div>
      <div class="modal-footer">
        <a href="" id="eventLink" type="button" class="btn btn-primary"><?=__('Consult')?></a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=__('Close')?></button>
      </div>
    </div>
  </div>
</div>

<!--div id="eventContent" title="Event Details" style="display:none;">
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
    <p id="eventInfo"></p>
    <p><strong><a id="eventLink" href="" target="_blank">Read More</a></strong></p>
</div-->

<section class="content">
<div id="main-wrapper">
		<div class="row"><!-- Start Row2 -->
		<div class="row left_section col-md-8 col-sm-8">
			<div class="col-lg-6 col-md-6 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-6">
			<a href="<?php echo $this->request->base ."/GymMember/memberList";?>">
				<div class="panel info-box panel-white">
					<div class="panel-body member">
						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/member.png" class="dashboard_background">
						<div class="info-box-stats">
							<p class="counter"><?php echo $members;?> <span class="info-box-title"><?php echo __("Member");?></span></p>
						</div>
					</div>
				</div>
			</a>
			</div>
			<div class="col-lg-6 col-md-6 col-xs-5 col-sm-6">
			<a href="<?php echo $this->request->base ."/staff-members/staff-list";?>">
				<div class="panel info-box panel-white">
					<div class="panel-body staff-member">
						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/staff-member.png" class="dashboard_background">
            <div class="info-box-stats">
							<p class="counter"><?php echo $staff_members;?><span class="info-box-title"><?php echo __("Staff Member");?></span></p>
						</div>
					</div>
				</div>
				</a>
			</div>

			<div class="col-lg-6 col-md-6 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-6">
			<a href="<?php echo $this->request->base ."/gym-group/group-list";?>">
				<div class="panel info-box panel-white">
					<div class="panel-body group">
						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/group.png" class="dashboard_background">
						<div class="info-box-stats groups-label">
							<p class="counter"><?php echo $groups;?><span class="info-box-title"><?php echo __("Group");?></span></p>
						</div>

					</div>
				</div>
				</a>
			</div>
			<div class="col-lg-6 col-md-6 col-xs-5 col-sm-6">
			<a href="<?php echo $this->request->base ."/gym-message/inbox";?>">
				<div class="panel info-box panel-white">
					<div class="panel-body message no-padding">
						<img src="<?php echo $this->request->base;?>/webroot/img/dashboard/message.png" class="dashboard_background_message">
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
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __("Group List");?></h3>
					</div>
					<div class="panel-body">
						<?php
						foreach($groups_data as $gd)
						{?>
							<p>
								<img src="<?php echo $this->request->base ."/webroot/upload/" .$gd["image"]; ?>" height="40px" width="40px" class="img-circle">
								<?php echo $gd["name"];?>
							</p>
						<?php
						} ?>
					</div>
				</div>
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
			echo '<select id="club" name="club" name="location" class="form-control" onChange="afficherparclub(\''.$this->Gym->createurl("Dashboard","admin-dashboard").'\');">';
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

      <!--div class="col-md-6 col-sm-6 col-xs-12">
        </?php
        $options = Array(
          'title' => __('Payment by month'),
          'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
          'legend' =>Array('position' => 'right',
          'textStyle'=> Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),


          //'bar'  => Array('groupWidth' => '70%'),
          //'lagend' => Array('position' => 'none'),
          'hAxis' => Array(
            'title' => __('Month'),
            'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
            'textStyle' => Array('color' => '#66707e','fontSize' => 11),
            'maxAlternation' => 2

            //'annotations' =>Array('textStyle'=>Array('fontSize'=>5))
          ),
          'vAxis' => Array(
            'title' => __('Payment'),
            'minValue' => 0,
            'maxValue' => 5,
            'format' => '#',
            'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
            'textStyle' => Array('color' => '#66707e','fontSize' => 12)
          ),
          'colors' => array('#22BAA0')
        );

        $GoogleCharts = new GoogleCharts;
        $chart = $GoogleCharts->load( 'column' , 'chart_div1' )->get( $chart_array_pay , $options );
        ?>
        <div class="panel panel-white">
          <div class="panel-heading">
            <h3 class="panel-title"></?php echo __('Payment');?></h3>
          </div>
          <div class="panel-body">
            <div id="chart_div1" style="width: 100%; height: 500px;">
              </?php
              if(empty($result_pay))
              echo __('There is not enough data to generate report');?>
            </div>
            <script type="text/javascript" src="https://www.google.com/jsapi"></script>
            <script type="text/javascript">
            </?php
            if(!empty($result_pay))
            echo $chart;?>
            </script>
          </div>
        </div>
      </div-->

			<div class="col-md-6 col-sm-6 col-xs-12">
			<?php
			$options = Array(
			'title' => __('Member Attendance Report'),
			'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
			'legend' =>Array('position' => 'right',
						'textStyle'=> Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),

						'hAxis' => Array(
								'title' =>  __('Class'),
										'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
								'textStyle' => Array('color' => '#66707e','fontSize' => 10),
								'maxAlternation' => 2


								),
								'vAxis' => Array(
										'title' =>  __('No of Member'),
					'minValue' => 0,
					'maxValue' => 5,
					'format' => '#',
					'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
					'textStyle' => Array('color' => '#66707e','fontSize' => 12)
								),
										'colors' => array('#22BAA0','#f25656')
										);



			$GoogleCharts = new GoogleCharts;
			$chart = $GoogleCharts->load( 'column' , 'attendance_report' )->get( $chart_array_at , $options );
			?>
			<div class="panel panel-white">
				<div class="panel-heading">
					<h3 class="panel-title"><?php echo __('Member Attendance Report');?></h3>
				</div>
				<div class="panel-body">
					<div id="attendance_report" style="width: 100%; height: 500px;">
						<?php

						if(empty($report_member))
							echo __('There is not enough data to generate report');?>
					</div>
					  <!-- Javascript -->
					  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
					  <script type="text/javascript">
						<?php
						if(!empty($report_member))
							echo $chart;?>
						</script>
				</div>
			</div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-12">
			<?php
			$options12 = Array(
			'title' => __('Staff Attendance Report'),
			'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
			'legend' =>Array('position' => 'right',
						'textStyle'=> Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),

						'hAxis' => Array(
								'title' =>  __('Staff Member'),
										'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
								'textStyle' => Array('color' => '#66707e','fontSize' => 10),
								'maxAlternation' => 2


								),
								'vAxis' => Array(
										'title' =>  __('Number of Staff Members'),
					'minValue' => 0,
					'maxValue' => 5,
					'format' => '#',
					'titleTextStyle' => Array('color' => '#66707e','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
					'textStyle' => Array('color' => '#66707e','fontSize' => 12)
								),
										'colors' => array('#22BAA0','#f25656')
										);
			$GoogleCharts = new GoogleCharts;
			$chart_staff = $GoogleCharts->load( 'column' , 'staff_att_report' )->get( $chart_array_staff , $options12 );
			// var_dump($chart_staff);die;
			?>
				<div class="panel panel-white">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __('Staff Attendance');?></h3>
					</div>
					<div class="panel-body">
						<div id="staff_att_report" style="width: 100%; height: 500px;">
						<?php
						if(empty($report_sataff))
						echo __('There is not enough data to generate report');?>
						</div>

			  <!-- Javascript -->
			  <script type="text/javascript" src="https://www.google.com/jsapi"></script>
			  <script type="text/javascript">
						<?php
						if(!empty($report_sataff))
						{echo $chart_staff;}?>
			</script>
								</div>
							</div>
						</div>
			</div><!-- End Row3 -->


	</div>
 </div>
