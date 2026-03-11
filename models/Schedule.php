<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "schedule".
 *
 * @property int $schedule_id
 * @property int $teacher_id
 * @property int $room_id
 * @property string $day_of_week
 * @property string $subject
 * @property string $start_time
 * @property string $end_time
 *
 * @property Rooms $room
 * @property Users $teacher
 */
class Schedule extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const DAY_OF_WEEK_MONDAY = 'Monday';
    const DAY_OF_WEEK_TUESDAY = 'Tuesday';
    const DAY_OF_WEEK_WEDNESDAY = 'Wednesday';
    const DAY_OF_WEEK_THURSDAY = 'Thursday';
    const DAY_OF_WEEK_FRIDAY = 'Friday';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['teacher_id', 'room_id', 'day_of_week', 'start_time', 'end_time'], 'required'],
            [['teacher_id', 'room_id'], 'integer'],
            [['day_of_week'], 'string'],
            [['start_time', 'end_time'], 'safe'],
            ['day_of_week', 'in', 'range' => array_keys(self::optsDayOfWeek())],
            [['teacher_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['teacher_id' => 'user_id']],
            [['room_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rooms::class, 'targetAttribute' => ['room_id' => 'room_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'teacher_id' => 'Teacher ID',
            'room_id' => 'Room ID',
            'day_of_week' => 'Day Of Week',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
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
     * Gets query for [[Teacher]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        return $this->hasOne(Users::class, ['user_id' => 'teacher_id']);
    }


    /**
     * column day_of_week ENUM value labels
     * @return string[]
     */
    public static function optsDayOfWeek()
    {
        return [
            self::DAY_OF_WEEK_MONDAY => 'Monday',
            self::DAY_OF_WEEK_TUESDAY => 'Tuesday',
            self::DAY_OF_WEEK_WEDNESDAY => 'Wednesday',
            self::DAY_OF_WEEK_THURSDAY => 'Thursday',
            self::DAY_OF_WEEK_FRIDAY => 'Friday',
        ];
    }

    /**
     * @return string
     */
    public function displayDayOfWeek()
    {
        return self::optsDayOfWeek()[$this->day_of_week];
    }

    /**
     * @return bool
     */
    public function isDayOfWeekMonday()
    {
        return $this->day_of_week === self::DAY_OF_WEEK_MONDAY;
    }

    public function setDayOfWeekToMonday()
    {
        $this->day_of_week = self::DAY_OF_WEEK_MONDAY;
    }

    /**
     * @return bool
     */
    public function isDayOfWeekTuesday()
    {
        return $this->day_of_week === self::DAY_OF_WEEK_TUESDAY;
    }

    public function setDayOfWeekToTuesday()
    {
        $this->day_of_week = self::DAY_OF_WEEK_TUESDAY;
    }

    /**
     * @return bool
     */
    public function isDayOfWeekWednesday()
    {
        return $this->day_of_week === self::DAY_OF_WEEK_WEDNESDAY;
    }

    public function setDayOfWeekToWednesday()
    {
        $this->day_of_week = self::DAY_OF_WEEK_WEDNESDAY;
    }

    /**
     * @return bool
     */
    public function isDayOfWeekThursday()
    {
        return $this->day_of_week === self::DAY_OF_WEEK_THURSDAY;
    }

    public function setDayOfWeekToThursday()
    {
        $this->day_of_week = self::DAY_OF_WEEK_THURSDAY;
    }

    /**
     * @return bool
     */
    public function isDayOfWeekFriday()
    {
        return $this->day_of_week === self::DAY_OF_WEEK_FRIDAY;
    }

    public function setDayOfWeekToFriday()
    {
        $this->day_of_week = self::DAY_OF_WEEK_FRIDAY;
    }

    public function getAttendance()
{
    return $this->hasMany(Attendance::class, ['schedule_id'=>'schedule_id']);
}
}
