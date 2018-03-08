<?php
echo $this->Html->css('fullcalendar');
echo $this->Html->script('moment.min');
echo $this->Html->script('fullcalendar.min');
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
      width: 1500px;
    }
  }
</style>
<script>
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
		<div class="row left_section col-md-8 col-sm-8">
			<div class="col-xs-6">
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
			<div class="col-xs-6">
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

			<div class="col-xs-6">
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
			<div class="col-xs-6">
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
				<div class="panel panel-white">
					<div class="panel-body">
						<div id="calendar">
						</div>
				</div>
			</div>

		</div>	<!-- End row2 -->

	</div>
 </div>
</section>
