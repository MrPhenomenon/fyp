<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "department_floors".
 *
 * @property int $id
 * @property int $department_id
 * @property int $floor_id
 *
 * @property Departments $department
 * @property Floors $floor
 * @property Rooms[] $rooms
 */
class DepartmentFloors extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_floors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_id', 'floor_id'], 'required'],
            [['department_id', 'floor_id'], 'integer'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Departments::class, 'targetAttribute' => ['department_id' => 'department_id']],
            [['floor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Floors::class, 'targetAttribute' => ['floor_id' => 'floor_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_id' => 'Department ID',
            'floor_id' => 'Floor ID',
        ];
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Departments::class, ['department_id' => 'department_id']);
    }

    /**
     * Gets query for [[Floor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFloor()
    {
        return $this->hasOne(Floors::class, ['floor_id' => 'floor_id']);
    }

    /**
     * Gets query for [[Rooms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRooms()
    {
        return $this->hasMany(Rooms::class, ['department_floor' => 'id']);
    }

}
