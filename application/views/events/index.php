<?php
load_css(array(
    "assets/js/fullcalendar/fullcalendar.min.css"
));

load_js(array(
    "assets/js/fullcalendar/fullcalendar.min.js",
    "assets/js/fullcalendar/lang-all.js",
	"assets/js/fullcalendar/fc-custom.js",
    "assets/js/bootstrap-confirmation/bootstrap-confirmation.js",
));

$client = "";
if (isset($client_id)) {
    $client = $client_id;
}
?>

<div id="page-content<?php echo $client; ?>" class="p20<?php echo $client; ?> clearfix">
    <div class="panel panel-default">
        <div class="page-title clearfix">
            <?php if ($client) { ?>
                <h4><?php echo lang('events'); ?></h4>
            <?php } else { ?>
                <h1><?php echo lang('event_calendar'); ?></h1>
            <?php } ?>
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("events/modal_form"), "<i class='fa fa-plus-circle'></i> " . lang('add_event'), array("class" => "btn btn-default", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/modal_form"), "", array("class" => "hide", "id" => "add_event_hidden", "title" => lang('add_event'), "data-post-client_id" => $client)); ?>
                <?php echo modal_anchor(get_uri("events/view"), "", array("class" => "hide", "id" => "show_event_hidden", "data-post-client_id" => $client,  "data-post-cycle" => "0", "data-post-editable" => "1", "title" => lang('event_details'))); ?>
            </div>
        </div>
        <div class="panel-body">
            <div id="event-calendar"></div>
        </div>
    </div>
</div>
<style>
.fc-day-number .guest,.fc-day-number .room{
	margin-right:15%;
}
.fc-day-number f{
	color:red;
	font-weight:bold;
}
#select-month, #select-year{
  	display:inline-block !important;
 	width:auto;
}
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $("#event-calendar").fullCalendar({
			lang: AppLanugage.locale,			
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: "<?php echo_uri("events/calendar_events/".$client); ?>",
            dayClick: function (date, jsEvent, view) {
                $("#add_event_hidden").attr("data-post-start_date", date.format("YYYY-MM-DD"));
                var startTime = date.format("HH:mm:ss");
                if (startTime === "00:00:00") {
                    startTime = "";
                }
                $("#add_event_hidden").attr("data-post-start_time", startTime);
                var endDate = date.add(1, 'hours');

                $("#add_event_hidden").attr("data-post-end_date", endDate.format("YYYY-MM-DD"));
                var endTime = "";
                if (startTime != "") {
                    endTime = endDate.format("HH:mm:ss");
                }

                $("#add_event_hidden").attr("data-post-end_time", endTime);
                $("#add_event_hidden").trigger("click");
            },
            eventClick: function (calEvent, jsEvent, view) {
                $("#show_event_hidden").attr("data-post-id", calEvent.encrypted_event_id);
                $("#show_event_hidden").attr("data-post-cycle", calEvent.cycle);
                $("#show_event_hidden").trigger("click");
            },
            eventRender: function (event, element) {
                if (event.icon) {
                    element.find(".fc-title").prepend("<i class='fa " + event.icon + "'></i> ");
                }
            },
          	viewRender: function(view) {
              	var months = ["Styczeń","Luty","Marzec","Kwiecień","Maj","Czerwiec","Lipiec","Sierpień","Wrzesień","Październik","Listopad","Grudzień"];
              	var select_html = '<select id="select-year" class="form-control">';
              	var title = view.title;
              	var ymarray = title.split(" ");
              	for(var year=2010;year<2100;year++){
                  	if(year == ymarray[1])
                      select_html=select_html+'<option value="'+year+'" selected>'+year+'</option>';
                  	else
                      select_html=select_html+'<option value="'+year+'">'+year+'</option>';
                }
              	select_html+='</select><select id="select-month" class="form-control">';
              	for(var i =0;i<12;i++){
                  	if(months[i]==ymarray[0])
                      select_html+='<option value="'+i+'" selected>'+months[i]+'</option>';
                  	else
                      select_html+='<option value="'+i+'">'+months[i]+'</option>';
                }
				select_html+='</select>';
              	var title = $(".fc-center h2").text();
              	if($("#event-calendar .fc-left").has("#select-year")){
                	$("#select-year").remove();
                  	$("#select-month").remove();
               	}              	
              	$("#event-calendar .fc-left").append(select_html);
              	var year, month;
              	year = $("#select-year").val();
              	month = $("#select-month").val();
				$("#select-year").change(function(){
                  	year = $(this).val();
                  	var ricksDate = new Date(year, month, 1);
                  	$("#event-calendar").fullCalendar("gotoDate",ricksDate);
                  	show_total_numbers();
                });
              	$("#select-month").change(function(){
					month=$(this).val();   
                  	var ricksDate = new Date(year, month, 1);
                  	$("#event-calendar").fullCalendar("gotoDate",ricksDate);
                  	show_total_numbers();
				});
            },
            firstDay: AppHelper.settings.firstDayOfWeek
        });
		
        var client = "<?php echo $client; ?>";
        if (client) {
            setTimeout(function () {
                $('#event-calendar').fullCalendar('today');              	
            });          
        }
        
        
        //autoload the event popover
        var encrypted_event_id = "<?php echo isset($encrypted_event_id)? $encrypted_event_id:'';?>";
       
        if(encrypted_event_id){
            $("#show_event_hidden").attr("data-post-id", encrypted_event_id);
            $("#show_event_hidden").trigger("click"); 
        }       
		show_total_numbers();
		$(".fc-next-button").click(function(){
			setTimeout(show_total_numbers(),100);
		});
		$(".fc-prev-button").click(function(){
			setTimeout(show_total_numbers(),100);
		});
		$(".fc-month-button").click(function(){
			setTimeout(show_total_numbers(),100);
		});
		//var html = "<button>OKOK</button>";
		//$(".fc-header").append(html);
    });
	function show_total_numbers(){
		var dates = $(".fc-day-number"),data_date;
		dates.each(function(){
			data_date = $(this).attr("data-date");
			//var obj=$(this);
			//show_number(obj,data_date);			
			show_number(data_date);
		});
	}
	function show_number(data_date){
		var obj=$(".fc-day-number[data-date='"+ data_date +"']");
		var date = data_date.substr(8);
		$.ajax({
				url:"events/get_total_numbers",
				type:"post",
				dataType:"json",
				data:{date:data_date},
				success:function(response){
					var html = '<span class="guest"><i class="fa fa-child"></i> <f>'+response.guests+'</f></span>'+
								'<span class="room"><i class="fa fa-windows"></i> <f>'+response.rooms+'</f></span>'+" "+date;
					obj.html(html);
				},
				async:false,
				error:function(){
					alert("error");
				}
			});
	}
</script>