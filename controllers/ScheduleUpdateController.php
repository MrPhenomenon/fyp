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
    /**
     * {@inheritdoc}
     */
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
                            if ($identity->isRoleTeacher()) return true;
                            $qecMember = QecCommittee::find()->where(['user_id' => $identity->user_id])->one();
                            if ($qecMember) return true;
                            return false;
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

    /**
     * Display schedule management page with all schedules
     */
    public function actionIndex()
    {
        $schedules = Schedule::find()
            ->with(['teacher', 'room'])
            ->orderBy(['day_of_week' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        $teachers = Users::find()
            ->where(['role' => 'Teacher'])
            ->asArray()
            ->all();

        $rooms = Rooms::find()
            ->asArray()
            ->all();

        return $this->render('schedules', [
            'schedules' => $schedules,
            'teachers' => $teachers,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Get schedule data as JSON
     */
    public function actionGetSchedules()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $blockId = Yii::$app->session->get('clerk_block_id', '');
        $floorId = Yii::$app->session->get('clerk_floor_id', '');

        $schedules = Schedule::find()
            ->alias('s')
            ->joinWith([
                'room r',
                'room.departmentFloor df',
                'room.departmentFloor.floor f'
            ])
            ->where([
                'f.floor_id' => $floorId,
                'f.block_id' => $blockId,
            ])
            ->with(['teacher', 'room'])
            ->orderBy(['day_of_week' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($schedules as $schedule) {
            $data[] = [
                'schedule_id' => $schedule->schedule_id,
                'teacher_id' => $schedule->teacher_id,
                'teacher_name' => $schedule->teacher->name,
                'room_id' => $schedule->room_id,
                'room_number' => $schedule->room->room_number,
                'day_of_week' => $schedule->day_of_week,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ];
        }

        return ['success' => true, 'data' => $data];
    }

    /**
     * Save/Create schedule via AJAX
     */
    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scheduleId = Yii::$app->request->post('schedule_id');
        $teacherId = Yii::$app->request->post('teacher_id');
        $roomId = Yii::$app->request->post('room_id');
        $dayOfWeek = Yii::$app->request->post('day_of_week');
        $startTime = Yii::$app->request->post('start_time');
        $endTime = Yii::$app->request->post('end_time');

        if (!$teacherId || !$roomId || !$dayOfWeek || !$startTime || !$endTime) {
            return [
                'success' => false,
                'message' => 'All fields are required'
            ];
        }

        if ($scheduleId) {
            $schedule = Schedule::findOne($scheduleId);
            if (!$schedule) {
                return ['success' => false, 'message' => 'Schedule not found'];
            }
        }

        $schedule->teacher_id = $teacherId;
        $schedule->room_id = $roomId;
        $schedule->day_of_week = $dayOfWeek;
        $schedule->start_time = $startTime;
        $schedule->end_time = $endTime;

        if ($schedule->save()) {
            return [
                'success' => true,
                'message' => 'Schedule saved successfully',
                'schedule' => [
                    'schedule_id' => $schedule->schedule_id,
                    'teacher_id' => $schedule->teacher_id,
                    'teacher_name' => $schedule->teacher->name,
                    'room_id' => $schedule->room_id,
                    'room_number' => $schedule->room->room_number,
                    'day_of_week' => $schedule->day_of_week,
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                ]
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to save schedule',
            'errors' => $schedule->errors
        ];
    }
}
