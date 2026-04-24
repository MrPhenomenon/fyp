<?php

namespace app\controllers;

use app\models\Blocks;
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

class ScheduleController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['login', 'error', 'captcha'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
        $teacherId = Yii::$app->user->id;

        $schedules = Schedule::find()
            ->with(['room.departmentFloor.floor.block'])
            ->where(['teacher_id' => $teacherId])
            ->orderBy(['day_of_week' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        return $this->render('index', [
            'schedules' => $schedules
        ]);
    }

    public function actionImport()
    {
        if (Yii::$app->request->isPost) {

            $file = UploadedFile::getInstanceByName('file');

            if (!$file) {
                Yii::$app->session->setFlash('error', 'No file uploaded');
                return $this->redirect(['index']);
            }

            $path = Yii::getAlias('@runtime') . '/' . $file->name;
            $file->saveAs($path);

            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            unset($rows[0]);

            Yii::$app->db->createCommand('DELETE FROM schedule')->execute();

            foreach ($rows as $row) {

                list(
                    $teacherName,
                    $email,
                    $subject,
                    $blockName,
                    $roomNumber,
                    $day,
                    $start,
                    $end
                ) = $row;

                /*
                ==========================
                Resolve Teacher
                ==========================
                */

                $teacher = Users::find()
                    ->where(['email' => $email])
                    ->one();

                if (!$teacher) {
                    continue; // skip if teacher not found
                }

                /*
                ==========================
                Resolve Block
                ==========================
                */

                $block = Blocks::find()
                    ->where(['block_name' => $blockName])
                    ->one();

                if (!$block) {
                    continue;
                }

                /*
                ==========================
                Resolve Floor → Room
                ==========================
                */

                $room = Rooms::find()
                    ->alias('r')
                    ->joinWith(['departmentFloor df'])
                    ->joinWith(['departmentFloor.floor f'])
                    ->where([
                        'r.room_number' => $roomNumber,
                        'f.block_id' => $block->block_id
                    ])
                    ->one();

                if (!$room) {
                    continue;
                }

                /*
                ==========================
                Insert Schedule
                ==========================
                */

                $schedule = new Schedule();
                $schedule->teacher_id = $teacher->user_id;
                $schedule->room_id = $room->room_id;
                $schedule->day_of_week = $day;
                $schedule->subject = $subject;
                $schedule->start_time = date("H:i:s", strtotime($start));
                $schedule->end_time = date("H:i:s", strtotime($end));

                if (!$schedule->save()) {
                    Yii::error($schedule->errors);
                }
            }

            unlink($path);

            Yii::$app->session->setFlash('success', 'Schedule imported successfully');
            return $this->redirect(['index']);
        }

        return $this->render('import');
    }

}
