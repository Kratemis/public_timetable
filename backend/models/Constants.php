<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "constants".
 *
 * @property int $id
 * @property string $name
 * @property string $value
 */
class Constants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'constants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value'], 'required'],
            [['name', 'value'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'value' => Yii::t('app', 'Value'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return ConstantsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ConstantsQuery(get_called_class());
    }
}
