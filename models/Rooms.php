<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rooms".
 *
 * @property int $room_id
 * @property int $department_floor
 * @property int $room_number
 *
 * @property Attendance[] $attendances
 * @property DepartmentFloors $departmentFloor
 * @property Schedule[] $schedules
 */
class Rooms extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_floor', 'room_number'], 'required'],
            [['department_floor', 'room_number'], 'integer'],
            [['department_floor'], 'exist', 'skipOnError' => true, 'targetClass' => DepartmentFloors::class, 'targetAttribute' => ['department_floor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'room_id' => 'Room ID',
            'department_floor' => 'Department Floor',
            'room_number' => 'Room Number',
        ];
    }

    /**
     * Gets query for [[Attendances]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(Attendance::class, ['room_id' => 'room_id']);
    }

    /**
     * Gets query for [[DepartmentFloor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentFloor()
    {
        return $this->hasOne(DepartmentFloors::class, ['id' => 'department_floor']);
    }

    /**
     * Gets query for [[Schedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedules()
    {
        return $this->hasMany(Schedule::class, ['room_id' => 'room_id']);
    }

}
