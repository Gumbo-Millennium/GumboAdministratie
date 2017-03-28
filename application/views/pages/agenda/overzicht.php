<div class="wrapper wrapper-content">
    <div class="row animated fadeInDown">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Agenda</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

	var resizeCalendar = function() {
		var calendarOffset = $('#calendar').offset();
		var calendarTop = calendarOffset.top;
		var windowHeight = $(window).height();
		var surroundingPadding = 60;
		
		var availableHeight = windowHeight - calendarTop - surroundingPadding;
		
		$("#calendar").fullCalendar('option', 'height', availableHeight);
	};
	
    $(document).ready(function () {
		
        $('#calendar').fullCalendar({
            height: 'auto',
            weekNumbers: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            eventStartEditable: true,
            editable: true,
            eventRender: function (event, element) {
                element.attr('href', event.url);
            },
            events: <?= $items ?>
        })
		
		resizeCalendar();
		$(window).on('resize', resizeCalendar);
    });
</script>