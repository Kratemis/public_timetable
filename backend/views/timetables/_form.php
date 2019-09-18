<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\Timetable */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="timetable-form">
    
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= //echo $form->field($model, 'date')->textInput()
            $form->field($model, 'date')->widget(DatePicker::classname(), [
                'dateFormat' => 'yyyy-MM-dd',
                'language' => 'es',
            ]);
            
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
            <?= $form->field($model, 'festive')->dropDownList([0 => 'No', 1 => 'Yes']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type')->dropDownList(['SIX' => 'SIX', 'REDUCED' => 'REDUCED', 'FRIDAY' => 'FRIDAY', 'HOLIDAY' => 'HOLIDAY',], ['prompt' => '']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'week')->hiddenInput(['value' => 1])->label(false) ?>
        </div>
        
        
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
