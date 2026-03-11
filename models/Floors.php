<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "floors".
 *
 * @property int $floor_id
 * @property int $block_id
 * @property int $floor_number
 *
 * @property Blocks $block
 * @property DepartmentFloors[] $departmentFloors
 */
class Floors extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'floors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['block_id', 'floor_number'], 'required'],
            [['block_id', 'floor_number'], 'integer'],
            [['block_id'], 'exist', 'skipOnError' => true, 'targetClass' => Blocks::class, 'targetAttribute' => ['block_id' => 'block_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'floor_id' => 'Floor ID',
            'block_id' => 'Block ID',
            'floor_number' => 'Floor Number',
        ];
    }

    /**
     * Gets query for [[Block]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlock()
    {
        return $this->hasOne(Blocks::class, ['block_id' => 'block_id']);
    }

    /**
     * Gets query for [[DepartmentFloors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentFloors()
    {
        return $this->hasMany(DepartmentFloors::class, ['floor_id' => 'floor_id']);
    }

}
