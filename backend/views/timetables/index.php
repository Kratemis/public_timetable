<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TimetableSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Timetables');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timetable-index">
<?php if(!is_null(Yii::$app->user->identity) && Yii::$app->user->identity->is_admin) { ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Timetable'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'date',
            [
                    'attribute' => 'person_id',
                    'value' => function ($model)
                    {
                        return "(".$model->person_id.") ".$model->person->name." ". $model->person->surname;
                    }
            ],
            [
                'attribute' => 'festive',
                'value' => function ($model)
                {
                    return $model->festive == 1 ? 'Yes' : 'No';
                }
            ],
            'type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); 
} else {
    ?>
    <div class="jumbotron">
      <h1 class="display-4">Forbidden</h1>
  </p>
</div>
    <?php
}


?>

</div>
