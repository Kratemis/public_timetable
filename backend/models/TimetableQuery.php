<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[Timetable]].
 *
 * @see Timetable
 */
class TimetableQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Timetable[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Timetable|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
