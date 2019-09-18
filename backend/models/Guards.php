<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "timetable".
 *
 * @property int $id
 * @property string $date_ini
 * @property string $date_end
 * @property int $person_id
 * @property int $festive
 *
 * @property User $person
 */
class Guards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_ini', 'date_end', 'person_id', 'type', ], 'required'],
            [['date_ini', 'date_end'], 'safe'],
            [['person_id'], 'integer'],
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
            'date_ini' => Yii::t('app', 'date_ini'),
            'date_end' => Yii::t('app', 'date_ini'),
            'person_id' => Yii::t('app', 'Person ID'),
            'type' => Yii::t('app', 'Type'),
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
