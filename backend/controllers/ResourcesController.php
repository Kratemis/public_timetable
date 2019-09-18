<?php

namespace backend\controllers;

use backend\models\Timetable;
use backend\models\Guards;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\User;

/**
 * Site controller
 */
class ResourcesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'get-desgraciao'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'get-desgraciao'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    private function getGuards()
    {
        $guards = Guards::find()->orderBy('id desc')->all();
        $events = array();
        $color = '';

       
        foreach ($guards as $guard) {
            $user = User::find()->where("id = ".$guard->person_id)->one();
             
            $name  =  $user->name." ". $user->surname ;
        

            $title = '';
                switch ($guard->type) {
                    case "MASTER":
                        $color = 'black';
                        $title = $name . " [MASTER]";
                        break;
                    case "BACKUP1":
                        $color = '#0066f6';
                        $title = $name . " [BACKUP1]";
                        break;
                    case "BACKUP2":
                        $color = 'grey';
                        $title = $name . " [BACKUP2]";
                        break;
                    case "SERVICEMANAGER":
                        $color = 'blue';
                        $title = $name . " [SERVICEMANAGER]";
                        break;
                        case "PO":
                        $color = '#8c8b00';
                        $title = $name . " [PO]";
                        break;
                }
              
                $event = new \yii2fullcalendar\models\Event();
                $event->id = $guard->id;
                $event->title = $title;
                $event->start = date('Y-m-d', strtotime($guard->date_ini));
                $event->end = date('Y-m-d', strtotime($guard->date_end));
                $event->color = $color;
                $events[] = $event;
        }
        return $events;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $events = $this->getEvents();
        $editable = false;
        $droppable = false;
        $selectable = false;

        if (Yii::$app->user->identity->is_admin == 1) {
            $editable = true;
            $droppable = true;
            $selectable = true;
        }

        return $this->render('index', [
            'events' => array_merge($events, $this->getGuards()),
            'people' => $this->getPersons(),
            'editable' => $editable,
            'droppable' => $droppable,
            'selectable' => $selectable,
            'holidays' => $this->getHolidays()
        ]);
    }

    private function getHolidays()
    {
        $users = User::find()->orderBy('timesSix DESC')->all();
        $timesHolidays = [];
        foreach ($users as $user) {
            $holidays = Timetable::find()
                ->where('person_id = ' . $user->id)
                ->andWhere('type LIKE "HOLIDAY"')
                ->all();
            $timesHolidays[$user->id] = count($holidays);
        }
        return $timesHolidays;
    }

    private function getPersons()
    {
        return User::find()->orderBy('name ASC')->all();
    }

    private function getEvents()
    {
        $timetables = Timetable::find()->orderBy('type ASC')->all();

        $auxEvents = [];
        foreach($timetables as $timetable){


            switch($timetable->type) {

                case "REDUCED":
                    isset($auxEvents[$timetable->date]['REDUCED']) ? $auxEvents[$timetable->date]['REDUCED']++ : $auxEvents[$timetable->date]['REDUCED']=1;
                    break;
                case "SIX":
                    isset($auxEvents[$timetable->date]['SIX']) ? $auxEvents[$timetable->date]['SIX']++ : $auxEvents[$timetable->date]['SIX'] = 1;
                    break;
                case "FRIDAY":
                    isset($auxEvents[$timetable->date]['FRIDAY']) ? $auxEvents[$timetable->date]['FRIDAY']++ : $auxEvents[$timetable->date]['FRIDAY'] = 1;
                    break;
                case "HOLIDAY":
                    if($timetable->festive == 0){
                        isset($auxEvents[$timetable->date]['HOLIDAY']) ? $auxEvents[$timetable->date]['HOLIDAY']++ : $auxEvents[$timetable->date]['HOLIDAY'] = 1;
                    }else {
                        isset($auxEvents[$timetable->date]['FESTIVE']) ? $auxEvents[$timetable->date]['FESTIVE']++ : $auxEvents[$timetable->date]['FESTIVE'] = 1;
                    }
                    break;
            }
        }




        foreach($auxEvents as $date => $auxEvent){
            $eventNormal = new \yii2fullcalendar\models\Event();
            $eventNormal->id = rand(1,999999);


            $eventNormal->start = date('Y-m-d', strtotime($date));

            $totalInSpecialTimetables = 0;
            foreach($auxEvent as $key => $type) {

                $event = new \yii2fullcalendar\models\Event();
                $event->id = rand(1,999999);


                $event->title = "[".$key."][".$type."]";
                $event->start = date('Y-m-d', strtotime($date));
                $event->url = $type;
                $totalInSpecialTimetables = $totalInSpecialTimetables + $type;

                switch($key) {

                    case "REDUCED":
                        $event->color = '#1de000';
                        break;
                    case "SIX":
                        $event->title = "[SIX][3]";
                        $event->color = 'orange';
                        $event->url = 3;

                        break;
                    case "FRIDAY":
                        $event->title = "[SIX][3]";
                        $event->url = 3;

                        $event->color = 'orange';
                        break;
                    case "HOLIDAY":
                        $event->color = '#f20000';
                        break;
                    case "FESTIVE":
                        $event->title = "FESTIVE";
                        $event->color = '#f20000';
                        break;
                }

                $events[] = $event;

            }

        }



        $aux = [];
        foreach($events as $event) {
            if($event->title != "FESTIVE") {
                isset($aux[$event->start]) ? $aux[$event->start] = $aux[$event->start] + $event->url : $aux[$event->start] = $event->url;

            }
        }

        foreach ($aux as $date => $count){
            $event = new \yii2fullcalendar\models\Event();
            $event->id = rand(1,999999);


            $event->title = "[FIVE][". (12 - $count) ."]";
            $event->start = date('Y-m-d', strtotime($date));
            $events[] = $event;

        }
        return $events;
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionGetDesgraciao()
    {
        echo "DESGRACIAOOOOOOO";
        exit;
    }
}
