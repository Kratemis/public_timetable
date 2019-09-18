<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Guards */

$this->title = Yii::t('app', 'Create Guards');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Guards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="guards-create">
    <?php if(!is_null(Yii::$app->user->identity) && Yii::$app->user->identity->is_admin) { ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'people' => $people
    ]) ?>

</div>
<?php
}


?>
