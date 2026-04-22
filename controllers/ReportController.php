<?php

namespace app\controllers;

use app\models\Attendance;
use app\models\QecCommittee;
use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\db\Query;

class ReportController extends Controller
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
        $user = Yii::$app->user->identity;
        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-d'));

        $blockId = null;
        $floorId = null;
        $isClerkWithoutFilter = false;

        if ($user->isRoleClerk() || $user->isQec()) {
            $blockId = Yii::$app->session->get('clerk_block_id');
            $floorId = Yii::$app->session->get('clerk_floor_id');
            
            if (!$blockId || !$floorId) {
                $isClerkWithoutFilter = true;
            }
        }

        $attendanceRecords = [];
        $summary = $this->calculateSummary([]);

        if ($user->role === Users::ROLE_TEACHER || !$isClerkWithoutFilter) {
            $query = (new Query())
                ->select([
                    'a.attendance_id',
                    'a.schedule_id',
                    'a.teacher_id',
                    'a.room_id',
                    'a.subject',
                    'a.status',
                    'a.marked_by',
                    'a.timestamp',
                    'u.name as teacher_name',
                    'r.room_number',
                    's.day_of_week'
                ])
                ->from('attendance a')
                ->innerJoin('users u', 'u.user_id = a.teacher_id')
                ->innerJoin('rooms r', 'r.room_id = a.room_id')
                ->innerJoin('schedule s', 's.schedule_id = a.schedule_id')
                ->innerJoin('department_floors df', 'df.id = r.department_floor')
                ->innerJoin('floors f', 'f.floor_id = df.floor_id')
                ->where(['>=', 'a.timestamp', $startDate . ' 00:00:00'])
                ->andWhere(['<=', 'a.timestamp', $endDate . ' 23:59:59']);

            if ($user->role === Users::ROLE_TEACHER) {
                $query->andWhere(['a.teacher_id' => $user->user_id]);
            } elseif ($user->role === Users::ROLE_CLERK) {
                // Apply block and floor restrictions for clerks
                if ($blockId) {
                    $query->andWhere(['f.block_id' => $blockId]);
                }
                
                if ($floorId) {
                    $query->andWhere(['f.floor_id' => $floorId]);
                }
            }

            $attendanceRecords = $query->orderBy(['a.timestamp' => SORT_DESC])->all();
            $summary = $this->calculateSummary($attendanceRecords);
        }

        return $this->render('index', [
            'attendanceRecords' => $attendanceRecords,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userRole' => $user->role,
            'isClerkWithoutFilter' => $isClerkWithoutFilter
        ]);
    }

    private function calculateSummary($records)
    {
        $summary = [
            'total' => count($records),
            'present' => 0,
            'absent' => 0,
            'students_not_present' => 0,
            'by_status' => []
        ];

        foreach ($records as $record) {
            switch ($record['status']) {
                case 'Yes':
                    $summary['present']++;
                    break;
                case 'No':
                    $summary['absent']++;
                    break;
                case 'Students Not Present':
                    $summary['students_not_present']++;
                    break;
            }

            // Count by status for chart
            if (!isset($summary['by_status'][$record['status']])) {
                $summary['by_status'][$record['status']] = 0;
            }
            $summary['by_status'][$record['status']]++;
        }

        return $summary;
    }

    public function actionExport()
    {
        $user = Yii::$app->user->identity;

        if ($user->role === Users::ROLE_CLERK) {
            $blockId = Yii::$app->session->get('clerk_block_id');
            $floorId = Yii::$app->session->get('clerk_floor_id');
            
            if (!$blockId || !$floorId) {
                Yii::$app->session->setFlash('error', 'Please select a block and floor from the dashboard before exporting the report.');
                return $this->redirect(['index']);
            }
        }

        $startDate = Yii::$app->request->get('start_date', date('Y-m-01'));
        $endDate = Yii::$app->request->get('end_date', date('Y-m-d'));

        $query = (new Query())
            ->select([
                'a.attendance_id',
                'a.schedule_id',
                'a.teacher_id',
                'a.room_id',
                'a.subject',
                'a.status',
                'a.marked_by',
                'a.timestamp',
                'u.name as teacher_name',
                'r.room_number',
                's.day_of_week'
            ])
            ->from('attendance a')
            ->innerJoin('users u', 'u.user_id = a.teacher_id')
            ->innerJoin('rooms r', 'r.room_id = a.room_id')
            ->innerJoin('schedule s', 's.schedule_id = a.schedule_id')
            ->innerJoin('department_floors df', 'df.department_floor_id = r.department_floor_id')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->where(['>=', 'a.timestamp', $startDate . ' 00:00:00'])
            ->andWhere(['<=', 'a.timestamp', $endDate . ' 23:59:59']);

        if ($user->role === Users::ROLE_TEACHER) {
            $query->andWhere(['a.teacher_id' => $user->user_id]);
        } elseif ($user->role === Users::ROLE_CLERK) {
            $blockId = Yii::$app->session->get('clerk_block_id');
            $floorId = Yii::$app->session->get('clerk_floor_id');
            
            if ($blockId) {
                $query->andWhere(['f.block_id' => $blockId]);
            }
            
            if ($floorId) {
                $query->andWhere(['f.floor_id' => $floorId]);
            }
        }

        $attendanceRecords = $query->orderBy(['a.timestamp' => SORT_DESC])->all();

        $filename = 'attendance_report_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, ['Date', 'Day', 'Teacher', 'Room', 'Subject', 'Status', 'Marked By']);

        foreach ($attendanceRecords as $record) {
            fputcsv($output, [
                $record['timestamp'],
                $record['day_of_week'],
                $record['teacher_name'],
                $record['room_number'],
                $record['subject'],
                $record['status'],
                $record['marked_by']
            ]);
        }

        fclose($output);
        exit;
    }
}
