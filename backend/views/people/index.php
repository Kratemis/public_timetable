<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
<?php if(!is_null(Yii::$app->user->identity) && Yii::$app->user->identity->is_admin) { ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'username',
            'email:email',
          //  'last_login_at',
            'name',
            'surname',
            'send_email',
            'timesSix',
            'timesReduced',
            'timesFridays',
            'veteran',
            'is_admin',

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
