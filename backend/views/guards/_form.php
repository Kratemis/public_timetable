<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;


/* @var $this yii\web\View */
/* @var $model backend\models\Guards */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="guards-form">
    
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <div class="col-md-3">
            <?= //echo $form->field($model, 'date')->textInput()
            $form->field($model, 'date_ini')->widget(DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
            ])->label('Start Date');
            
            ?>
        </div>
        <div class="col-md-3">
            <?= //echo $form->field($model, 'date')->textInput()
            $form->field($model, 'date_end')->widget(DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
                'language' => 'es',

            ])->label('End Date');
            
            ?>
        </div>
        <div class="col-md-3">
            <?php
            $dropdownPeople = [];
            foreach ($people as $person) {
                $dropdownPeople[$person->id] = $person->name . " " . $person->surname;
            }
            
            echo $form->field($model, 'person_id')->dropDownList($dropdownPeople, ['prompt' => ''])->label("Person") ?>
        </div>
        <div class="col-md-3">
            
            <?= $form->field($model, 'type')->dropDownList(['MASTER' => 'MASTER', 'BACKUP1' => 'BACKUP1', 'BACKUP2' => 'BACKUP2', 'SERVICEMANAGER' => 'SERVICEMANAGER', 'PO' => 'PO',], ['prompt' => '']) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
