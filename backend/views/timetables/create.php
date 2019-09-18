<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Timetable */

$this->title = Yii::t('app', 'Create Timetable');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Timetables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="timetable-create">
<?php if(!is_null(Yii::$app->user->identity) && Yii::$app->user->identity->is_admin) { ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people
    ]) ?> <?php

} else {
    ?>
    <div class="jumbotron">
      <h1 class="display-4">Forbidden</h1>
  	</p>
	</div> <?php } ?>
</div>
