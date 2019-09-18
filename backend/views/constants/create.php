<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Constants */

$this->title = Yii::t('app', 'Create Constants');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Constants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="constants-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
