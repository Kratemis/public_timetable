<?php

use yii\web\JsExpression;

$DragJS = <<<EOF
/* initialize the external events
-----------------------------------------------------------------*/
$('#external-events .fc-event').each(function() {
    // store data so the calendar knows to render an event upon drop
    $(this).data('event', {
        title: $.trim($(this).text()), // use the element's text as the event title
        stick: true // maintain when user navigates (see docs on the renderEvent method)
    });
    // make the event draggable using jQuery UI
    $(this).draggable({
        zIndex: 999,
        revert: true,      // will cause the event to go back to its
        revertDuration: 0  //  original position after the drag
    });
});
EOF;
$this->registerJs($DragJS);
?>
<div class="site-index">
    <div class="body-content">

        <?php
        $JSCode = "
function(start, end) {
    var title = confirm('Holidays?');
    var eventData;
    if (title) {
        eventData = {
            title: '[HOLIDAY][MYSELF]',
            start: start,
            color : '#00ffe9',
            end: end
        };
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', 'timetables/addholiday', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('date='+start/1000+'&user_id=" . Yii::$app->user->id . "');
        $('#w0').fullCalendar('renderEvent', eventData, true);
    }
    $('#w0').fullCalendar('unselect');
}";
        $JSDropEvent = "
function(event) {
    var title = confirm('Do you want to change?');
    console.log(event);
    if (title) 
    {
        var d = new Date(event.start._d);
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', 'timetables/changeevent', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('oldDate='+encodeURI(event.start._i)+'&newDate='+encodeURI(d.toISOString())+'&title='+encodeURI(event.title));
    }
    //$('#w0').fullCalendar('unselect');
}";
        $JSEventClick = "
function(event) {

   var title = confirm('Do you want to delete?');
    console.log(event);
    if (title) 
    {
        var d = new Date(event.start._d);
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', 'timetables/deleteevent', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('oldDate='+encodeURI(event.start._i)+'&title='+encodeURI(event.title));
        $('#w0').fullCalendar('removeEvents',event._id);
    }
}";

        /* @var $this yii\web\View */

        $this->title = 'SWAG';
        $eventClick = false;

        if (Yii::$app->user->identity->is_admin) {
            $eventClick = new JsExpression($JSEventClick);
        }
        ?>

        <div class="row">
            <?php if(!is_null(Yii::$app->user->identity) && Yii::$app->user->identity->is_admin) { ?>

            <div class="site-index col-md-12 col-xs-12">

                <?= \yii2fullcalendar\yii2fullcalendar::widget([
                    'events' => $events,
                    'clientOptions' => [
                        'selectable' => false,
                        //'selectHelper' => true,
                        'clickable' => true,
                        'droppable' => false,
                        'editable' => false,
                        'eventDrop' => new JsExpression($JSDropEvent),
                        'select' => new JsExpression($JSCode),
                        'eventClick' => false,
                        'defaultDate' => date('Y-m-d'),
                        'weekends' => true,
                        'weekNumbers' => true,
                        'weekNumberTitle' => 'Week',
                        'firstDay' => 1,
                        'header' => [
                            'right' => 'month,agendaWeek,basicDay'
                        ]
                    ],
                ]); ?>
            </div>
            
        </div>
        <br><br>
        <div class="col-12">
            You can view the code in <a href="https://github.com/Kratemis/timetable"> Github <i
                        class="fab fa-github fa-2x"></i> </a>
        </div>
    </div>
<?php
} else {
    ?>
    <div class="jumbotron">
      <h1 class="display-4">Forbidden</h1>
    </p>
    </div> <?php } ?>
</div>