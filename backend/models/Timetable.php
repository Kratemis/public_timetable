<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "timetable".
 *
 * @property int $id
 * @property string $date
 * @property int $person_id
 * @property int $festive
 * @property string $type
 * @property int $week
 *
 * @property User $person
 */
class Timetable extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'person_id', 'type', ], 'required'],
            [['date'], 'safe'],
            [['person_id', 'festive', 'week'], 'integer'],
            [['type'], 'string'],
            [['person_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['person_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'date' => Yii::t('app', 'Date'),
            'person_id' => Yii::t('app', 'Person ID'),
            'festive' => Yii::t('app', 'Festive'),
            'type' => Yii::t('app', 'Type'),
            'week' => Yii::t('app', 'Week'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(User::className(), ['id' => 'person_id']);
    }

    /**
     * {@inheritdoc}
     * @return TimetableQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TimetableQuery(get_called_class());
    }
}
