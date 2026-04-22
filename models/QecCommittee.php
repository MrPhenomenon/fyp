<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "qec_committee".
 *
 * @property int $id
 * @property int $user_id
 * @property bool $is_teacher
 * @property string|null $appointed_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Users $user
 */
class QecCommittee extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qec_committee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['is_teacher'], 'boolean'],
            [['appointed_date'], 'safe'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'is_teacher' => 'Is Teacher',
            'appointed_date' => 'Appointed Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }


    /**
     * Check if a user is a QEC teacher (has both teacher role and QEC membership)
     *
     * @param int $userId
     * @return bool
     */
    public static function isQecTeacher($userId)
    {
        $member = self::find()
            ->where(['user_id' => $userId, 'is_teacher' => true])
            ->one();
        
        return $member !== null;
    }

    /**
     * Get all available users (not already QEC members)
     *
     * @return array
     */
    public static function getAvailableUsers()
    {
        $existingMemberIds = self::find()->select('user_id')->column();
        
        return Users::find()
            ->where(['NOT IN', 'user_id', $existingMemberIds])
            ->andWhere(['!=', 'role', Users::ROLE_ADMIN])
            ->orderBy('name')
            ->all();
    }

    /**
     * Get all users (including existing QEC members for editing)
     *
     * @return array
     */
    public static function getAllUsersForSelection()
    {
        return Users::find()
            ->orderBy('name')
            ->all();
    }
}