<?php

namespace console\controllers;

use backend\models\Guards;
use Yii;
use backend\models\User;
use backend\models\Timetable;


class MailController extends \yii\console\Controller
{
    private $date;

    public function actionIndex()
    {
        echo "Inicio index\n";
        $today = date('Y-m-d');

        $tomorrow = strtotime('+1 day', strtotime($today));
        $this->date = date('Y-m-d', $tomorrow);

        $people = User::find()->all();
        echo "Inicio despues people\n";

        foreach ($people as $person) {
            echo "dentro del for\n";

            $timetable = Timetable::find()
                ->where('date = "' . $this->date . '"')
                ->andWhere('person_id = ' . $person->id)
                ->andWhere('festive != 1')
                ->one();

            $festive = Timetable::find()
                ->where('date = "' . $this->date . '"')
                ->andWhere('festive = 1')
                ->one();


            $this->sendMail($person, $timetable, $festive);
        }
    }

    /**
     * Send mail with the timetable of tomorrow
     *
     * @param Person $person
     * @param Timetable $day
     */
    private function sendMail(User $person, $timetable, $festive)
    {
        if (date('D') != 'Fri' && date('D') != 'Sat' && $festive == null) {
            echo "START sendMail\n";

            if (isset($person->send_email) && !is_null($person->send_email) && $person->send_email == 1) {
                echo "SENDING EMAIL TO: $person->name $person->surname \n";

                if (is_null($timetable)) {
                    echo "TIMETABLE IS NULL. SENDING EMAIL TO: $person->name $person->surname \n";

                    Yii::$app->mailer
                        ->compose()
                        ->setFrom('no-reply@sergioestebanadan.es')
                        ->setTo($person->email)
                        ->setSubject("Tomorrow ($this->date), NORMAL")
                        ->setHtmlBody("You have NORMAL timetable tomorrow.")
                        ->send();
                } else {
                    echo "TIMETABLE IS NOT NULL. SENDING EMAIL TO: $person->name $person->surname \n";
                    if(strtoupper($timetable->type) == "SIX") {
                        $type = "FIVE";
                    }else {
                        $type = $timetable->type;
                    }
                    Yii::$app->mailer
                        ->compose()
                        ->setFrom('no-reply@sergioestebanadan.es')
                        ->setTo($person->email)
                        ->setSubject("Tomorrow ($timetable->date), $timetable->type")
                        ->setHtmlBody("You have " . $type . " timetable tomorrow.")
                        ->send();
                }
                echo "END sendMail\n";
            }
        }
    }


    public function actionHolidayMail()
    {

        $now = date("Y-m-d H:i:s");

        $week = date('W', strtotime(date('Y-m-d H:i:s', strtotime('+2 week', strtotime($now)))));

        $subject = "Week " . $week . " will be generated tomorrow";

        $users = User::find()->where('send_email != 0')->all();

        foreach ($users as $user) {
            $body = "Hi " . $user->name . ",<br><br> 
            Week " . $week . " will be generated tomorrow. <br>
            Please, fill your vacations before tomorrow at 3:00 p.m.<br>
            Regards.";

            Yii::$app->mailer
                ->compose()
                ->setFrom('no-reply@sergioestebanadan.es')
                ->setTo($user->email)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();
        }
    }

    /**
     * Mail for advise for guards weeks. Called from crontab all Mondays (10:00)
     */
    public function actionGuardsMail()
    {
        echo "START GUARDS MAIL\n";
        $now = date("Y-m-d");
        $guards = Guards::find()
            ->where('date_ini = "' . $now . '"')
            ->all();

        foreach ($guards as $guard) {
            $user = User::find()
                ->where('id = ' . $guard->person_id)
                ->andWhere('send_email != 0')
                ->one();
            echo "SENDING TO $user->name $user->surname";

            $subject = $body = 'Today, a ' . $guard->type . ' guard starts for you. Do the redirection.';

            Yii::$app->mailer
                ->compose()
                ->setFrom('no-reply@sergioestebanadan.es')
                ->setTo($user->email)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();

        }
        echo "END GUARDS MAIL\n";
    }
}