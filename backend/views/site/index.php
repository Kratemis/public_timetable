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
            <div class="site-index col-md-9 col-xs-12">

                <?= \yii2fullcalendar\yii2fullcalendar::widget([
                    'events' => $events,
                    'clientOptions' => [
                        'selectable' => $selectable,
                        //'selectHelper' => true,
                        'clickable' => true,
                        'droppable' => $droppable,
                        'editable' => $editable,
                        'eventDrop' => new JsExpression($JSDropEvent),
                        'select' => new JsExpression($JSCode),
                        'eventClick' => $eventClick,
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
            <div class="col-md-3 col-xs-12">
                <?php
                /*echo ButtonDropdown::widget([
                    'label' => 'Tools',
                    'dropdown' => [
                        'items' => [
                            ['label' => 'Propose Change', 'items' =>
                                [
                                    ['label' => 'Day', 'url' => '#'],
                                    ['label' => 'Week', 'url' => '#'],
                                ]
                            ],
                            ['label' => 'Holidays', 'items' =>
                                [
                                    ['label' => 'Add', 'url' => 'site/add-holiday'],
                                    ['label' => 'Delete', 'url' => '#']
                                ],
                            ]
                        ]
                    ],
                    'buttonOptions' => ['class' => 'btn-secondary disabled']

                ]);*/
                ?>
                <div class="row">
                    <br>
                    <div class="col-md-12 col-sm-4 col-xs-3">
                        <span class="badge badge-primary"
                              style="background-color: #1de000;">REDUCED</span></div>
                    <div class="col-md-12 col-sm-4 col-xs-3">
                        <span class="badge badge-secondary" style="background-color: orange;">UNTIL 18</span>
                    </div>
                    <div class="col-md-12 col-sm-4 col-xs-3">
                        <span class="badge badge-danger" style="background-color: #f20000;">HOLIDAY OR FESTIVE</span>
                    </div>
                </div>
                <br><br>
                <table class="table table-striped table-condensed">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>18:00 Times</th>
                        <th>Reduced Times</th>
                        <th>Friday Times</th>
                        <th>Holidays</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($people as $person) {
                        if($person->name == 'FESTIVE' || $person->id == 14 || $person->id == 15) {
                            continue;
                        }
                     ?>
                        <tr>
                            <td><?= $person->name ?> <?= $person->surname ?></td>
                            <td><?= $person->timesSix ?> </td>
                            <td><?= $person->timesReduced ?> </td>
                            <td><?= $person->timesFridays ?> </td>
                            <td><?= $holidays[$person->id] ?> </td>
                            <td><a href="site/punish/?id=<?= $person->id ?>" type="button" class="btn btn-danger" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Punish</button></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br>
        <div class="col-12">
            <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Licencia de Creative Commons" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/88x31.png" /></a> Este obra está bajo una <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/">licencia de Creative Commons Reconocimiento-NoComercial-SinObraDerivada 4.0 Internacional</a>.
        </div>
    </div>
</div>