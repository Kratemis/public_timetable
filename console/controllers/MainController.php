<?php

namespace console\controllers;

use backend\models\Constants;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Yii;
use backend\models\User;
use backend\models\Timetable;

define("REDUCED", "REDUCED");
define("SIX", "SIX");
define("FRIDAY", "FRIDAY");
define("HOLIDAY", "HOLIDAY");


class MainController extends \yii\console\Controller
{

    private $week;

    private $maxPeopleReduced = 0 ;

    private $maxPeopleSix = 0;

    private $maxPeopleFriday = 0;

    /**
     * Main method of the class. Called from console.
     */

    public function actionGenerateNextWeek() {
        $now = date("Y-m-d H:i:s");
        $week = date('W', strtotime(date ( 'Y-m-d H:i:s' , strtotime ( '+2 week' , strtotime ( $now )))));
        $this->actionIndex($week);
    }
    
    private static function getMaxPeopleReduced() {
        return Constants::find()->where('name = "REDUCED_PERSONS"')->one()->value;
    }
    
    private static function getMaxPeopleFriday() {
        return Constants::find()->where('name = "FRIDAY_PERSONS"')->one()->value;
    }
    
    private static function getMaxPeopleSix() {
        return Constants::find()->where('name = "SIX_PERSONS"')->one()->value;
    }
    
    private  function initValues() {
        $this->maxPeopleReduced = self::getMaxPeopleReduced();
        $this->maxPeopleSix = self::getMaxPeopleSix();
        $this->maxPeopleFriday = self::getMaxPeopleFriday();
    }

    public function actionIndex($week)
    {
        
        $this->initValues();
        
        $this->week = $week - 1;

        $reduced = <<< TEXT
  _____    ______   _____    _    _    _____   ______   _____  
 |  __ \  |  ____| |  __ \  | |  | |  / ____| |  ____| |  __ \ 
 | |__) | | |__    | |  | | | |  | | | |      | |__    | |  | |
 |  _  /  |  __|   | |  | | | |  | | | |      |  __|   | |  | |
 | | \ \  | |____  | |__| | | |__| | | |____  | |____  | |__| |
 |_|  \_\ |______| |_____/   \____/   \_____| |______| |_____/ 
                                                               
                                                               
TEXT;




       echo $reduced . "\n";

        $this->setWeek(REDUCED);

        $five = <<< TEXT
  ______   _____  __      __  ______   
 |  ____| |_   _| \ \    / / |  ____|  
 | |__      | |    \ \  / /  | |__     
 |  __|     | |     \ \/ /   |  __|    
 | |       _| |_     \  /    | |____   
 |_|      |_____|     \/     |______|  
                                       
                                                                                             
TEXT;
        echo $five . "\n";


        $this->setWeek(SIX);
        $this->setWeek(FRIDAY);
    }

    private function setWeek($type)
    {
        if ($type != SIX) {
            $users = User::getPersonOrderByTimes($type);
        }

        for ($i = 0; $i < 7; $i++) {
            $day = date('Y-m-d', strtotime('01/06 +' . ($this->week - 1) . ' weeks first day ' . $i . ' day'));
            $this->restartCounters();

            if ($type == SIX) {
                $this->checkGluedPersons($day);
                $users = User::getPersonOrderByTimes($type);
            }

            foreach ($users as $user) {
                if ($user->veteran) {
                    if (!$this->isWeekend($day)) {
                        if (!$this->isFriday($day)) {
                            if (!$this->isFestive($day)) {
                                if (!$this->isHoliday($day, $user)) {
                                    if (!$this->isDoingAnotherTimetable($day, $user)) {
                                        if ($type == REDUCED && $this->isGluedUser($user->id)) {
                                            if (!$this->isAnotherGluedPersonDoingReduced($day)) {
                                                echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " CAN DO. SETTING DAY...\n";
                                                if ($this->checkCounters($day, $type, $user)) {
                                                    $this->setDay($day, $type, $user);
                                                } else {
                                                    echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " TIMETABLE COMPLETE\n";
                                                }
                                            } else {
                                                echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " THERE IS ANOTHER GLUED PERSON\n";
                                            }
                                        } elseif(!$this->isGluedUser($user->id)) {
                                            echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " CAN DO. SETTING DAY...\n";

                                            if ($this->checkCounters($day, $type, $user)) {
                                                $this->setDay($day, $type, $user);
                                            } else {
                                                echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " TIMETABLE COMPLETE\n";
                                            }
                                        }
                                    } else {
                                        echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " IS DOING ANOTHER TIMETABLE\n";
                                    }
                                } else {
                                    echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " HAS HOLIDAY\n";
                                }
                            } else {
                                echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " IS FESTIVE\n";
                                continue;
                            }
                        } else {
                            echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " IS FRIDAY\n";
                        }
                    } else {
                        echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " IS WEEKEND\n";
                    }
                } else {
                    echo str_pad("[$day][$type][$user->name $user->surname]", 70) . " IS NOOB\n";
                    continue;
                }
            }
        }
    }

    /**
     * @param $userId
     * @return bool
     */
    private function isGluedUser($userId)
    {
        if ($gluedUser = User::find()->where('id = ' . $userId)->andWhere('is_glued_user = 1')->one()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $day
     * @return bool
     */
    private function isAnotherGluedPersonDoingReduced($day)
    {
        $gluedPersons = User::find()->where('is_glued_user = 1')->all();
        foreach ($gluedPersons as $gluedPerson) {
            if ($timetable = Timetable::find()
                ->where('person_id = ' . $gluedPerson->id)
                ->andWhere('date = "' . $day . '"')
                ->one()) {
                return true;
            }
        }
        return false;
    }

    private function checkGluedPersons($day)
    {
        $gluedPersons = User::find()
            ->where('is_glued_user = 1')
            ->all();

        $gluedPersonsDoingOtherTimetable = 0;

        foreach ($gluedPersons as $gluedPerson) {
            if ($timetable = Timetable::find()
                ->where('person_id = ' . $gluedPerson->id)
                ->andWhere('date = "' . $day . '"')
                ->andWhere('festive = 0')
                ->one()) {
                $gluedPersonsDoingOtherTimetable++;
            }
        }
        // limite - (glued person - personas encontradas)
        var_dump("LIMITE: " . $this->getMaxPeopleReduced());
        var_dump("GLUED PERSONS: " . count($gluedPersons));
        var_dump("PERSONAS ENCONTRADAS: " . $gluedPersonsDoingOtherTimetable);

        $this->maxPeopleSix = $this->getMaxPeopleReduced() - count($gluedPersons) + $gluedPersonsDoingOtherTimetable;
    }

    private function restartCounters()
    {
        $this->maxPeopleReduced = $this->getMaxPeopleReduced();

        $this->maxPeopleSix = 0;

        $this->maxPeopleFriday = 0;
    }

    private function checkCounters($day, $type, $user)
    {
        switch ($type) {
            case SIX:
                if ($this->maxPeopleSix > 0) {
                    $this->maxPeopleSix--;
                    return true;
                }
                break;
            case REDUCED:
                if ($this->maxPeopleReduced > 0) {
                    $this->maxPeopleReduced--;
                    return true;
                }
                break;
            case FRIDAY:
                if ($this->maxPeopleFriday > 0) {
                    $this->maxPeopleFriday--;
                    return true;
                }
                break;
        }
        return false;
    }

    private
    function isDoingAnotherTimetable($day, $user)
    {
        if ($personTimetable = Timetable::find()
            ->where('person_id = ' . $user->id)
            ->andWhere('date = "' . $day . '"')
            ->andWhere('festive = 0')
            ->one()) {

            echo str_pad("[$day][$user->name $user->surname]", 70) . " isDoingAnotherTimetable: YES\n";
            return true;
        } else {
            echo str_pad("[$day][$user->name $user->surname]", 70) . " isDoingAnotherTimetable: NO\n";
            return false;
        }


    }

    private
    function isFriday($day)
    {
        if (date("D", strtotime($day)) != "Fri") {
            echo str_pad("[$day]", 70) . " isFriday: NO\n";
            return false;
        } else {
            echo str_pad("[$day]", 70) . " isFriday: YES\n";
            return true;
        }
    }


    /**
     * @param $day
     * @return int
     */
    private
    function isFestive($day)
    {
        if (!$timetable = Timetable::find()->where('date = "' . $day . '"')->andWhere('festive = 1')->one()) {
            echo str_pad("[$day]", 70) . " isFestive: NO\n";
            return false;
        } else {
            echo str_pad("[$day]", 70) . " isFestive: " . $timetable->festive . "\n";
            return $timetable->festive;
        }
    }

    /**
     * @param $day
     * @param $user
     * @return bool
     */
    private
    function isHoliday($day, $user)
    {
        if ($holiday = Timetable::find()
            ->where('person_id = ' . $user->id)
            ->andWhere('date = "' . $day . '"')
            ->andWhere('type = "' . HOLIDAY . '"')
            ->one()) {

            echo str_pad("[$day][$user->name $user->surname]", 70) . " isHoliday: YES\n";
            return true;
        }

        echo str_pad("[$day][$user->name $user->surname]", 70) . " isHoliday: NO\n";
        return false;
    }

    /**
     * @param $day
     * @return bool
     */
    private
    function isWeekend($day)
    {
        if (date("D", strtotime($day)) == 'Sat') {
            echo str_pad("[$day]", 70) . " isWeekend: YES\n";
            return true;
        } elseif (date("D", strtotime($day)) == 'Sun') {
            echo str_pad("[$day]", 70) . " isWeekend: YES\n";
            return true;
        } else {
            echo str_pad("[$day]", 70) . " isWeekend: NO\n";
            return false;
        }
    }


    /**
     * Try to set person to a day
     *
     * @param $type
     * @param $day
     * @param Person $person
     */
    private
    function setDay($day, $type, User $user)
    {
        $timetable = new Timetable();
        $timetable->date = $day;
        $timetable->person_id = $user->id;
        $timetable->type = $type;
        $timetable->week = $this->week;
        $timetable->save(false);
    }

    /**
     * Called from a crontab every 5 min
     */
    public function actionSetNumberOfWeek()
    {
        $timetables = Timetable::find()->all();
        foreach ($timetables as $timetable) {
            $timetable->week = date("W", strtotime($timetable->date));
            $timetable->save(false);
        }
    }

}
