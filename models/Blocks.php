<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blocks".
 *
 * @property int $block_id
 * @property string $block_name
 *
 * @property Floors[] $floors
 */
class Blocks extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blocks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['block_name'], 'required'],
            [['block_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'block_id' => 'Block ID',
            'block_name' => 'Block Name',
        ];
    }

    /**
     * Gets query for [[Floors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFloors()
    {
        return $this->hasMany(Floors::class, ['block_id' => 'block_id']);
    }

}
