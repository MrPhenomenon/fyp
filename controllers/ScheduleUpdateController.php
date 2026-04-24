<?php

namespace app\controllers;

use app\models\QecCommittee;
use app\models\Schedule;
use app\models\Users;
use app\models\Rooms;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class ScheduleUpdateController extends Controller
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            if ($identity->isRoleClerk()) return true;
                            if ($identity->isRoleAdmin()) return true;
                            if ($identity->isRoleTeacher()) return true;
                            return QecCommittee::find()->where(['user_id' => $identity->user_id])->exists();
                        }
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\ForbiddenHttpException('You do not have access to this page.');
                }
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'save' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // Join departments so we can show dept name in the teacher dropdown
        $teachers = Users::find()
            ->select(['users.user_id', 'users.name', 'departments.department_name'])
            ->leftJoin('departments', 'departments.department_id = users.department_id')
            ->where(['users.role' => Users::ROLE_TEACHER])
            ->orderBy('users.name')
            ->asArray()
            ->all();

        $rooms = Rooms::find()->asArray()->all();

        return $this->render('schedules', [
            'teachers' => $teachers,
            'rooms'    => $rooms,
        ]);
    }

    public function actionGetSchedules()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Schedule::find()
            ->with(['teacher.department', 'room'])
            ->orderBy(['day_of_week' => SORT_ASC, 'start_time' => SORT_ASC]);

        // Only apply block/floor filter when both are set (clerk context)
        $blockId = Yii::$app->session->get('clerk_block_id');
        $floorId = Yii::$app->session->get('clerk_floor_id');

        if ($blockId && $floorId) {
            $query->alias('s')
                ->joinWith([
                    'room r',
                    'room.departmentFloor df',
                    'room.departmentFloor.floor f',
                ])
                ->andWhere(['f.floor_id' => $floorId, 'f.block_id' => $blockId]);
        }

        $schedules = $query->all();

        $data = [];
        foreach ($schedules as $schedule) {
            $dept        = $schedule->teacher->department;
            $teacherName = $schedule->teacher->name . ($dept ? ' (' . $dept->department_name . ')' : '');

            $data[] = [
                'schedule_id'  => $schedule->schedule_id,
                'teacher_id'   => $schedule->teacher_id,
                'teacher_name' => $teacherName,
                'room_id'      => $schedule->room_id,
                'room_number'  => $schedule->room->room_number,
                'day_of_week'  => $schedule->day_of_week,
                'start_time'   => $schedule->start_time,
                'end_time'     => $schedule->end_time,
            ];
        }

        return ['success' => true, 'data' => $data];
    }

    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scheduleId = Yii::$app->request->post('schedule_id');
        $teacherId  = Yii::$app->request->post('teacher_id');
        $roomId     = Yii::$app->request->post('room_id');
        $dayOfWeek  = Yii::$app->request->post('day_of_week');
        $startTime  = Yii::$app->request->post('start_time');
        $endTime    = Yii::$app->request->post('end_time');

        if (!$teacherId || !$roomId || !$dayOfWeek || !$startTime || !$endTime) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if ($scheduleId) {
            $schedule = Schedule::findOne($scheduleId);
            if (!$schedule) {
                return ['success' => false, 'message' => 'Schedule not found'];
            }
        } else {
            $schedule = new Schedule();
        }

        $schedule->teacher_id  = $teacherId;
        $schedule->room_id     = $roomId;
        $schedule->day_of_week = $dayOfWeek;
        $schedule->start_time  = $startTime;
        $schedule->end_time    = $endTime;

        if ($schedule->save()) {
            return ['success' => true, 'message' => 'Schedule saved successfully'];
        }

        return [
            'success' => false,
            'message' => 'Failed to save schedule',
            'errors'  => $schedule->errors,
        ];
    }
}
