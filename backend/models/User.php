<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $confirmed_at
 * @property string $unconfirmed_email
 * @property int $blocked_at
 * @property string $registration_ip
 * @property int $created_at
 * @property int $updated_at
 * @property int $flags
 * @property int $last_login_at
 * @property string $name
 * @property string $surname
 * @property string $send_email
 * @property string $timesSix
 * @property string $timesReduced
 * @property string $timesFridays
 * @property string $veteran
 * @property int $status
 * @property int $is_admin
 *
 * @property Profile $profile
 * @property SocialAccount[] $socialAccounts
 * @property Timetable[] $timetables
 * @property Token[] $tokens
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password_hash', 'auth_key', 'created_at', 'updated_at'], 'required'],
            [['confirmed_at', 'blocked_at', 'created_at', 'updated_at', 'flags', 'last_login_at', 'status', 'is_admin', 'send_email', 'timesSix', 'timesReduced', 'timesFridays', 'veteran', 'is_glued_user'], 'integer'],
            [['username', 'email', 'unconfirmed_email'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
            [['auth_key'], 'string', 'max' => 32],
            [['registration_ip', 'name', 'surname'], 'string', 'max' => 45],
            [['username'], 'unique'],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'email' => Yii::t('app', 'Email'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'confirmed_at' => Yii::t('app', 'Confirmed At'),
            'unconfirmed_email' => Yii::t('app', 'Unconfirmed Email'),
            'blocked_at' => Yii::t('app', 'Blocked At'),
            'registration_ip' => Yii::t('app', 'Registration Ip'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'flags' => Yii::t('app', 'Flags'),
            'last_login_at' => Yii::t('app', 'Last Login At'),
            'name' => Yii::t('app', 'Name'),
            'surname' => Yii::t('app', 'Surname'),
            'send_email' => Yii::t('app', 'Send Email'),
            'timesSix' => Yii::t('app', 'Times Six'),
            'timesReduced' => Yii::t('app', 'Times Reduced'),
            'timesFridays' => Yii::t('app', 'Times Fridays'),
            'veteran' => Yii::t('app', 'Veteran'),
            'status' => Yii::t('app', 'Status'),
            'is_admin' => Yii::t('app', 'Is Admin?'),
            'is_glued_user' => Yii::t('app', 'Is Glued User?'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSocialAccounts()
    {
        return $this->hasMany(SocialAccount::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimetables()
    {
        return $this->hasMany(Timetable::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Get people for timetable
     *
     * @param $type
     * @return Person[]|array
     */
    public static function getPersonOrderByTimes($type)
    {
        switch ($type) {
            case 'REDUCED':
                return self::find()->andWhere('veteran = 1')->andWhere('is_doing_reduced = 1')->orderBy('timesReduced ASC')->all();
                break;
            case 'SIX':
                return self::find()->andWhere('veteran = 1')->andWhere('is_doing_six = 1')->orderBy('timesSix ASC')->all();
                break;
            case 'FRIDAY':
                return self::find()->andWhere('veteran = 1')->andWhere('is_doing_fridays = 1')->orderBy('timesFridays ASC')->all();
                break;
        }
    }
}
