<?php

namespace app\controllers;

use app\models\Attendance;
use app\models\Blocks;
use app\models\Floors;
use app\models\QecCommittee;
use app\models\Rooms;
use app\models\Schedule;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

class AttendanceController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'get-floors', 'mark-attendance'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            $identity = Yii::$app->user->identity;
                            if ($identity->isRoleClerk()) return true;
                            $qecMember = $identity->isQec();
                            return $qecMember !== null;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $blockId = Yii::$app->session->get('clerk_block_id', '');
        $floorId = Yii::$app->session->get('clerk_floor_id', '');

        if (empty($blockId) || empty($floorId)) {
            Yii::$app->session->setFlash('error', 'Please select a block and floor from the Clerk Dashboard first.');
            return $this->redirect(['site/index']);
        }

        $today = date('l');
        $dateStart = date('Y-m-d 00:00:00');
        $dateEnd = date('Y-m-d 23:59:59');

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
                's.day_of_week' => $today
            ])
            ->with([
                'teacher',
                'room',
                'attendance' => function ($q) use ($dateStart, $dateEnd) {
                    $q->andWhere(['between', 'timestamp', $dateStart, $dateEnd]);
                }
            ])
            ->all();

        return $this->render('index', [
            'schedules' => $schedules
        ]);
    }

    public function actionGetFloors()
    {
        $block_id = Yii::$app->request->post('block_id');

        $floors = Floors::find()
            ->where(['block_id' => $block_id])
            ->asArray()
            ->all();

        return $this->asJson($floors);
    }

    public function actionMarkAttendance()
    {
        $schedule_id = Yii::$app->request->post('schedule_id');
        $status = Yii::$app->request->post('status');
        $subject = Yii::$app->request->post('subject');
        $room_id = Yii::$app->request->post('room_id');
        $teacher_id = Yii::$app->request->post('teacher_id');


        $today = date('Y-m-d');

        $attendance = Attendance::find()
            ->where(['schedule_id' => $schedule_id])
            ->andWhere(['between', 'timestamp', "$today 00:00:00", "$today 23:59:59"])
            ->one();

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->schedule_id = $schedule_id;
        }


        $attendance->teacher_id = $teacher_id;
        $attendance->room_id = $room_id;
        $attendance->subject = $subject;

        $attendance->status = $status;
        $attendance->marked_by = Yii::$app->user->identity->name;
        $attendance->timestamp = date('Y-m-d H:i:s');

        if ($attendance->save()) {
            return $this->asJson([
                'success' => true,
                'updated' => !$attendance->isNewRecord
            ]);
        }

        return $this->asJson([
            'success' => false,
            'errors' => $attendance->errors
        ]);
    }

    public function actionSetClerkFilter()
    {
        $block_id = Yii::$app->request->post('block_id');
        $floor_id = Yii::$app->request->post('floor_id');

        Yii::$app->session->set('clerk_block_id', $block_id);
        Yii::$app->session->set('clerk_floor_id', $floor_id);

        return $this->asJson([
            'success' => true,
            'message' => 'Filter saved'
        ]);
    }
}
