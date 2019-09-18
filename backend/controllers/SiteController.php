<?php

namespace backend\controllers;

use backend\models\Guards;
use backend\models\Timetable;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use backend\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['logout', 'index', 'get-desgraciao', 'punish'],
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
            'events' => $events,
            'people' => $this->getPersons(),
            'editable' => $editable,
            'droppable' => $droppable,
            'selectable' => $selectable,
            'holidays' => $this->getHolidays()
        ]);
    }

    public function actionPunish() {
        $params = Yii::$app->request->get();

        $user = User::find()->where("id = ".$params['id'])->one();
       

        if (Yii::$app->user->identity->is_admin){
                 Yii::$app->mailer
                    ->compose()
                    ->setFrom('no-reply@sergioestebanadan.es')
                    ->setTo($user->email)
                    ->setSubject("You have a punishment of 2 weeks until 6:00 p.m.")
                    ->setHtmlBody("
                        Hi $user->name,<br><br>

                        You have a punishment of 2 weeks until 6:00 p.m.<br><br>

                        You cannot talk to anyone about this topic or you will receive a worse punishment. <br><br>

                        Regards & Have a good day!")
                    ->send();
        }else {
            echo Yii::$app->user->identity->name.", no te pases de listo";
        }
        return $this->redirect(['/']);
    }

    private function getPersons()
    {
        return User::find()->orderBy('name ASC')->all();
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

    private function getEvents()
    {
        $timetables = Timetable::find()->orderBy('type ASC')->all();
        $events = array();
        $color = '';

        foreach ($timetables as $timetable) {
            $title = '';
            if (!$timetable->festive) {
                switch ($timetable->type) {
                    case "SIX":
                        $color = 'orange';
                        $title = '18';
                        break;
                    case "REDUCED":
                        $color = '#1de000';
                        $title = '15';
                        break;
                    case "HOLIDAY":
                        $color = '#f20000';
                        $title = 'H';
                        break;
                    case "FRIDAY":
                        $color = 'orange';
                        $title = '18';
                        break;
                }

                $name = '';

                if (!Yii::$app->user->isGuest && !is_null (Yii::$app->user) && !is_null(Yii::$app->user->identity) && Yii::$app->user->identity->name === $timetable->person->name && Yii::$app->user->identity->surname === $timetable->person->surname) {
                    $name = 'ME';
                } else {
                    $name = $timetable->person->surname . ", " . $timetable->person->name;
                }

                $event = new \yii2fullcalendar\models\Event();
                $event->id = $timetable->id;
                $event->title = "[" . $name . "][" . $title . "]";
                $event->start = date('Y-m-d', strtotime($timetable->date));
                $event->color = $color;
                $events[] = $event;
            } else {
                $event = new \yii2fullcalendar\models\Event();
                $event->id = $timetable->id;
                $event->title = "FESTIVE";
                $event->start = date('Y-m-d', strtotime($timetable->date));
                $event->color = 'red';
                $events[] = $event;
            }


        }

        $events = array_merge($this->getGuards(), $events);
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

    private function getGuards()
    {
        $guards = Guards::find()->orderBy('id desc')->all();
        $events = array();
        $color = '';

       
        foreach ($guards as $guard) {
            $user = User::find()->where("id = ".$guard->person_id)->one();
             if (Yii::$app->user->identity->name === $user->name && Yii::$app->user->identity->surname === $user->surname) {
                        $name  =  'ME' ;
        }else {
            $name  =  $user->name." ". $user->surname ;
        }

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
}
