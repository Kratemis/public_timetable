<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::encode($this->title) ?></title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php if (!is_null(Yii::$app->user->identity) && !Yii::$app->user->isGuest) { ?>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
        <a class="navbar-brand" href="/"><?= Html::img('./logo-swag.png', [ 'style' => 'max-height: 35px  '])?> </a>
    </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="/">Calendar</a></li>
                <li><a href="/guards">Guards</a></li>
                <li><a href="/people" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>People</a></li>
                <li><a href="/timetables" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Timetable</a></li>
                <li><a href="/timetables/create" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Create Timetable</a></li>
                <li><a href="/resources" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Resources</a></li>
                <li><a href="/constants" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Constants</a></li>
                <li><a href="/guards/create" <?= Yii::$app->user->identity->is_admin ? "" : "disabled" ?>>Create guard</a></li>

            </ul>
         <p class="navbar-text navbar-right"><?php
                        echo Html::beginForm(['/site/logout'], 'post')
                            . Html::submitButton(
                                'Logout (' . Yii::$app->user->identity->name . " " . Yii::$app->user->identity->surname . ')',
                                ['class' => 'btn btn-link logout navbar-text navbar-right']
                            )
                            . Html::endForm();
                        ?>
  </div>
</nav>



    <?php } ?>

    <div class="container col-xs-12">
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
