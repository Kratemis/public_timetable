<?php

namespace backend\controllers;

use backend\models\User;
use Yii;
use backend\models\Timetable;
use backend\models\TimetableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TimetableController implements the CRUD actions for Timetable model.
 */
class TimetablesController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'addholiday' => ['POST'],
                    'changeevent' => ['POST'],
                    'deleteevent' => ['POST']
                ],
            ],
        ];
    }

    /**
     * Lists all Timetable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TimetableSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    private function getVeteranPeople() {
       return User::find()->where('veteran = 1')->all();
    }

    /**
     * Displays a single Timetable model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Timetable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Timetable();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'people' => $this->getVeteranPeople()
        ]);
    }

    /**
     * Updates an existing Timetable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Timetable model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteevent(){
        if(Yii::$app->user->identity->is_admin){
            date_default_timezone_set('Europe/Madrid');

            $postParameters = $_POST;


            $nameAndSurname = (explode(",", str_replace("[", "", str_replace("]", "", preg_replace('/[0-9]|H/', '', $postParameters['title'])))));
            $oldDate = (date("Y-m-d", strtotime($postParameters['oldDate'])));


            if($nameAndSurname[0] == 'ME') {
                $user = User::find()
                    ->where('id = "' . Yii::$app->user->id  . '"')
                    ->one();
            } else {
                $user = User::find()
                    ->where('name = "' . trim($nameAndSurname[1])  . '"')
                    ->andWhere('surname = "' . trim($nameAndSurname[0]) . '"')
                    ->one();
            }


            $timetable = Timetable::find()
                ->where('person_id = ' . $user->id)
                ->andWhere('date = "' . $oldDate . '"')
                ->one();

            $timetable->delete();
        }

    }

    /**
     * Finds the Timetable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Timetable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Timetable::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionAddholiday()
    {
        date_default_timezone_set('Europe/Madrid');

        $postParameters = $_POST;

        $model = new Timetable();
        $model->person_id = $postParameters['user_id'];
        $model->date = date("Y-m-d", $postParameters['date']);
        $model->type = "HOLIDAY";
        $model->festive = 0;
        $model->week = date("W", $postParameters['date']);
        $model->save(false);


    }

    public function actionChangeevent()
    {
        date_default_timezone_set('Europe/Madrid');

        $postParameters = $_POST;

        $nameAndSurname = (explode(",", str_replace("[", "", str_replace("]", "", preg_replace('/[0-9]|H/', '', $postParameters['title'])))));
        var_dump($nameAndSurname);
        $oldDate = (date("Y-m-d", strtotime($postParameters['oldDate'])));
        $newDate = (date("Y-m-d", strtotime($postParameters['newDate'])));


        if($nameAndSurname[0] == 'ME') {
            $user = User::find()
                ->where('id = "' . Yii::$app->user->id  . '"')
                ->one();
        } else {
            $user = User::find()
                ->where('name = "' . trim($nameAndSurname[1])  . '"')
                ->andWhere('surname = "' . trim($nameAndSurname[0]) . '"')
                ->one();
        }


        $timetable = Timetable::find()
            ->where('person_id = ' . $user->id)
            ->andWhere('date = "' . $oldDate . '"')
            ->one();

        $timetable->date = $newDate;
        $timetable->save(false);


        return 'OK';

    }
}
