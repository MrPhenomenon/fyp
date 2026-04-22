<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attendance".
 *
 * @property int $attendance_id
 * @property int $teacher_id
 * @property string $marked_by
 * @property int $room_id
 * @property string|null $subject
 * @property string $status
 * @property int $schedule_id
 * @property string $timestamp
 *
 * @property Rooms $room
 * @property Schedule $schedule
 * @property Users $teacher
 */
class Attendance extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_YES = 'Yes';
    const STATUS_NO = 'No';
    const STATUS_CLASS_ABSENT = 'Class Absent';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subject'], 'default', 'value' => null],
            [['teacher_id', 'marked_by', 'room_id', 'status', 'schedule_id'], 'required'],
            [['teacher_id', 'room_id', 'schedule_id'], 'integer'],
            [['status'], 'string'],
            [['timestamp'], 'safe'],
            [['marked_by', 'subject'], 'string', 'max' => 50],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['teacher_id' => 'user_id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rooms::class, 'targetAttribute' => ['room_id' => 'room_id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Schedule::class, 'targetAttribute' => ['schedule_id' => 'schedule_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attendance_id' => 'Attendance ID',
            'teacher_id' => 'Teacher ID',
            'marked_by' => 'Marked By',
            'room_id' => 'Room ID',
            'subject' => 'Subject',
            'status' => 'Status',
            'schedule_id' => 'Schedule ID',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * Gets query for [[Room]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(Rooms::class, ['room_id' => 'room_id']);
    }

    /**
     * Gets query for [[Schedule]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchedule()
    {
        return $this->hasOne(Schedule::class, ['schedule_id' => 'schedule_id']);
    }

    /**
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Users::class, ['user_id' => 'teacher_id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_YES => 'Yes',
            self::STATUS_NO => 'No',
            self::STATUS_CLASS_ABSENT => 'Class Absent',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusYes()
    {
        return $this->status === self::STATUS_YES;
    }

    public function setStatusToYes()
    {
        $this->status = self::STATUS_YES;
    }

    /**
     * @return bool
     */
    public function isStatusNo()
    {
        return $this->status === self::STATUS_NO;
    }

    public function setStatusToNo()
    {
        $this->status = self::STATUS_NO;
    }

    /**
     * @return bool
     */
    public function isStatusStudentsNotPresent()
    {
        return $this->status === self::STATUS_STUDENTS_NOT_PRESENT;
    }

    public function setStatusToStudentsNotPresent()
    {
        $this->status = self::STATUS_STUDENTS_NOT_PRESENT;
    }
}
