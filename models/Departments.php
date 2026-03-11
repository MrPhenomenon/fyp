<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "departments".
 *
 * @property int $department_id
 * @property string $department_name
 *
 * @property DepartmentFloors[] $departmentFloors
 * @property Users[] $users
 */
class Departments extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['department_name'], 'required'],
            [['department_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'department_id' => 'Department ID',
            'department_name' => 'Department Name',
        ];
    }

    /**
     * Gets query for [[DepartmentFloors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentFloors()
    {
        return $this->hasMany(DepartmentFloors::class, ['department_id' => 'department_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::class, ['department_id' => 'department_id']);
    }

}
