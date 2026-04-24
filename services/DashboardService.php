<?php

namespace app\services;

use app\models\Schedule;
use app\models\Attendance;
use yii\db\Query;

class DashboardService
{

    public static function getTeacherDashboard($teacherId)
    {
        return [
            'todaySchedule' => self::getTodaySchedule($teacherId),
            'nextClass' => self::getNextClass($teacherId),
            'attendanceSummary' => self::getAttendanceSummary($teacherId),
            'last30DaysAttendance' => self::getLast30DaysAttendance($teacherId),
            'roomsThisWeek' => self::getTeacherRooms($teacherId),
        ];
    }


    const FACULTY_CHART_LIMIT = 15;

    public static function getAdminDashboard($department_id = null, $faculty_id = null, $date_from = null, $date_to = null)
    {
        $date_from = $date_from ?: date('Y-m-01');
        $date_to   = $date_to   ?: date('Y-m-d');

        return [
            'kpi'              => self::getAdminKpi($department_id, $faculty_id, $date_from, $date_to),
            'byDepartment'     => self::getAttendanceByDepartment($department_id, $faculty_id, $date_from, $date_to),
            'byFaculty'        => self::getAttendanceByFaculty($department_id, $faculty_id, $date_from, $date_to),
            'facultyChartLimit' => self::FACULTY_CHART_LIMIT,
        ];
    }

    private static function getAdminKpi($department_id, $faculty_id, $date_from, $date_to)
    {
        $dateStart = $date_from . ' 00:00:00';
        $dateEnd   = $date_to   . ' 23:59:59';

        $base = (new Query())
            ->from('attendance a')
            ->innerJoin('users u', 'u.user_id = a.teacher_id')
            ->andWhere(['>=', 'a.timestamp', $dateStart])
            ->andWhere(['<=', 'a.timestamp', $dateEnd]);

        if ($department_id) {
            $base->andWhere(['u.department_id' => $department_id]);
        }
        if ($faculty_id) {
            $base->andWhere(['a.teacher_id' => $faculty_id]);
        }

        $total       = (clone $base)->count();
        $present     = (clone $base)->andWhere(['a.status' => 'Yes'])->count();
        $absent      = (clone $base)->andWhere(['a.status' => 'No'])->count();
        $classAbsent = (clone $base)->andWhere(['a.status' => 'Class Absent'])->count();

        return compact('total', 'present', 'absent', 'classAbsent');
    }

    private static function getAttendanceByDepartment($department_id, $faculty_id, $date_from, $date_to)
    {
        $dateStart = $date_from . ' 00:00:00';
        $dateEnd   = $date_to   . ' 23:59:59';

        $query = (new Query())
            ->select(['d.department_name', 'a.status', 'COUNT(*) as cnt'])
            ->from('attendance a')
            ->innerJoin('users u', 'u.user_id = a.teacher_id')
            ->innerJoin('departments d', 'd.department_id = u.department_id')
            ->andWhere(['>=', 'a.timestamp', $dateStart])
            ->andWhere(['<=', 'a.timestamp', $dateEnd]);

        if ($department_id) {
            $query->andWhere(['u.department_id' => $department_id]);
        }
        if ($faculty_id) {
            $query->andWhere(['a.teacher_id' => $faculty_id]);
        }

        $rows = $query->groupBy(['d.department_id', 'a.status'])->all();

        $result = [];
        foreach ($rows as $row) {
            $dept = $row['department_name'];
            if (!isset($result[$dept])) {
                $result[$dept] = ['Yes' => 0, 'No' => 0, 'Class Absent' => 0];
            }
            $result[$dept][$row['status']] = (int)$row['cnt'];
        }

        return $result;
    }

    private static function getAttendanceByFaculty($department_id, $faculty_id, $date_from, $date_to)
    {
        $dateStart = $date_from . ' 00:00:00';
        $dateEnd   = $date_to   . ' 23:59:59';
        $limit     = self::FACULTY_CHART_LIMIT;

        // Find top-N teachers by absent count first (DB-level limit)
        $topTeacherIds = (new Query())
            ->select(['a.teacher_id'])
            ->from('attendance a')
            ->innerJoin('users u', 'u.user_id = a.teacher_id')
            ->andWhere(['>=', 'a.timestamp', $dateStart])
            ->andWhere(['<=', 'a.timestamp', $dateEnd])
            ->andWhere(['a.status' => 'No']);

        if ($department_id) {
            $topTeacherIds->andWhere(['u.department_id' => $department_id]);
        }
        if ($faculty_id) {
            $topTeacherIds->andWhere(['a.teacher_id' => $faculty_id]);
        }

        $topTeacherIds = $topTeacherIds
            ->groupBy('a.teacher_id')
            ->orderBy(['COUNT(*)' => SORT_DESC])
            ->limit($limit)
            ->column();

        if (empty($topTeacherIds)) {
            return [];
        }

        // Now fetch full breakdown (all statuses) for those teachers only
        $rows = (new Query())
            ->select(['u.name as faculty_name', 'a.status', 'COUNT(*) as cnt'])
            ->from('attendance a')
            ->innerJoin('users u', 'u.user_id = a.teacher_id')
            ->andWhere(['>=', 'a.timestamp', $dateStart])
            ->andWhere(['<=', 'a.timestamp', $dateEnd])
            ->andWhere(['a.teacher_id' => $topTeacherIds])
            ->groupBy(['a.teacher_id', 'a.status'])
            ->all();

        $result = [];
        foreach ($rows as $row) {
            $name = $row['faculty_name'];
            if (!isset($result[$name])) {
                $result[$name] = ['Yes' => 0, 'No' => 0, 'Class Absent' => 0];
            }
            $result[$name][$row['status']] = (int)$row['cnt'];
        }

        uasort($result, fn($a, $b) => $b['No'] - $a['No']);

        return $result;
    }

    public static function getClerkDashboard($block_id = null, $floor_id = null)
    {
        if(!$block_id || !$floor_id) return [];
        return [
            'liveClasses' => self::getLiveClasses($block_id, $floor_id),
            'attendanceToday' => self::getAttendanceTodayStats($block_id, $floor_id),
            'missingAttendance' => self::getMissingAttendance($block_id, $floor_id),
            'roomUsage' => self::getRoomUsageToday($block_id, $floor_id),
        ];
    }


    private static function getTodaySchedule($teacherId)
    {
        $today = date('l');

        return Schedule::find()
            ->alias('s')
            ->joinWith(['room r'])
            ->where([
                's.teacher_id' => $teacherId,
                's.day_of_week' => $today
            ])
            ->orderBy(['s.start_time' => SORT_ASC])
            ->all();
    }


    private static function getNextClass($teacherId)
    {
        $today = date('l');
        $now = date('H:i:s');

        return Schedule::find()
            ->where([
                'teacher_id' => $teacherId,
                'day_of_week' => $today
            ])
            ->andWhere(['>', 'start_time', $now])
            ->orderBy(['start_time' => SORT_ASC])
            ->one();
    }


    private static function getAttendanceSummary($teacherId)
    {
        $monthStart = date('Y-m-01');

        $query = (new Query())
            ->from('attendance')
            ->where(['teacher_id' => $teacherId])
            ->andWhere(['>=', 'timestamp', $monthStart]);

        $total = $query->count();

        $present = (clone $query)
            ->andWhere(['status' => 'present'])
            ->count();

        $absent = (clone $query)
            ->andWhere(['status' => 'absent'])
            ->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'rate' => $total ? round(($present / $total) * 100) : 0
        ];
    }

    private static function getLast30DaysAttendance($teacherId)
    {
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));

        $query = (new Query())
            ->from('attendance')
            ->where(['teacher_id' => $teacherId])
            ->andWhere(['>=', 'timestamp', $thirtyDaysAgo]);

        $statusCounts = (clone $query)
            ->select(['status', 'COUNT(*) as count'])
            ->groupBy('status')
            ->all();

        $chartData = [
            'Yes' => 0,
            'No' => 0,
            'Students Not Present' => 0
        ];

        foreach ($statusCounts as $row) {
            if (isset($chartData[$row['status']])) {
                $chartData[$row['status']] = $row['count'];
            }
        }

        return $chartData;
    }


    private static function getTeacherRooms($teacherId)
    {
        return (new Query())
            ->select(['r.room_number'])
            ->distinct()
            ->from('schedule s')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->where(['s.teacher_id' => $teacherId])
            ->all();
    }


    private static function getLiveClasses($block_id = null, $floor_id = null)
    {
        $today = date('l');
        $now = date('H:i:s');
        $dateStart = date('Y-m-d 00:00:00');
        $dateEnd = date('Y-m-d 23:59:59');

        $query = (new Query())
            ->select([
                's.schedule_id',
                'r.room_number',
                'r.room_id',
                'u.name as teacher',
                'u.user_id as teacher_id',
                's.subject',
                'a.status'
            ])
            ->from('schedule s')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->innerJoin('users u', 'u.user_id = s.teacher_id')
            ->innerJoin('department_floors df', 'df.id = r.department_floor')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->leftJoin('attendance a', 'a.schedule_id = s.schedule_id AND a.timestamp >= :dateStart AND a.timestamp <= :dateEnd', [':dateStart' => $dateStart, ':dateEnd' => $dateEnd])
            ->where(['s.day_of_week' => $today])
            ->andWhere(['<=', 's.start_time', $now])
            ->andWhere(['>=', 's.end_time', $now]);

        if ($block_id) {
            $query->andWhere(['f.block_id' => $block_id]);
        }

        if ($floor_id) {
            $query->andWhere(['f.floor_id' => $floor_id]);
        }

        return $query->all();
    }


    private static function getAttendanceTodayStats($block_id = null, $floor_id = null)
    {
        $today = date('Y-m-d');

        $totalQuery = (new Query())
            ->from('schedule s')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->innerJoin('department_floors df', 'df.id = r.department_floor')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->where(['s.day_of_week' => date('l')]);

        if ($block_id) {
            $totalQuery->andWhere(['f.block_id' => $block_id]);
        }

        if ($floor_id) {
            $totalQuery->andWhere(['f.floor_id' => $floor_id]);
        }

        $total = $totalQuery->count();

        $markedQuery = (new Query())
            ->from('attendance a')
            ->innerJoin('schedule s', 's.schedule_id = a.schedule_id')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->innerJoin('department_floors df', 'df.id = r.department_floor')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->where(['like', 'a.timestamp', $today]);

        if ($block_id) {
            $markedQuery->andWhere(['f.block_id' => $block_id]);
        }

        if ($floor_id) {
            $markedQuery->andWhere(['f.floor_id' => $floor_id]);
        }

        $marked = $markedQuery->count();

        return [
            'total' => $total,
            'marked' => $marked,
            'remaining' => $total - $marked
        ];
    }


    private static function getMissingAttendance($block_id = null, $floor_id = null)
    {
        $today = date('l');
        $now = date('H:i:s');
        $dateStart = date('Y-m-d 00:00:00');
        $dateEnd = date('Y-m-d 23:59:59');

        $query = (new Query())
            ->select([
                's.schedule_id',
                'r.room_number',
                'r.room_id',
                'u.name as teacher',
                'u.user_id as teacher_id',
                's.subject',
                's.start_time',
                's.end_time',
                'a.status'
            ])
            ->from('schedule s')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->innerJoin('users u', 'u.user_id = s.teacher_id')
            ->innerJoin('department_floors df', 'df.id = r.department_floor')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->leftJoin('attendance a', 'a.schedule_id = s.schedule_id AND a.timestamp >= :dateStart AND a.timestamp <= :dateEnd', [':dateStart' => $dateStart, ':dateEnd' => $dateEnd])
            ->where(['s.day_of_week' => $today])
            ->andWhere(['<', 's.end_time', $now])
            ->andWhere(['or', ['a.schedule_id' => null], ['a.status' => null]]);

        if ($block_id) {
            $query->andWhere(['f.block_id' => $block_id]);
        }

        if ($floor_id) {
            $query->andWhere(['f.floor_id' => $floor_id]);
        }

        return $query->all();
    }


    private static function getRoomUsageToday($block_id = null, $floor_id = null)
    {
        $today = date('l');

        $query = (new Query())
            ->select([
                'r.room_number',
                'COUNT(*) as classes'
            ])
            ->from('schedule s')
            ->innerJoin('rooms r', 'r.room_id = s.room_id')
            ->innerJoin('department_floors df', 'df.id = r.department_floor')
            ->innerJoin('floors f', 'f.floor_id = df.floor_id')
            ->where(['s.day_of_week' => $today]);

        if ($block_id) {
            $query->andWhere(['f.block_id' => $block_id]);
        }

        if ($floor_id) {
            $query->andWhere(['f.floor_id' => $floor_id]);
        }

        return $query->groupBy('r.room_number')
            ->orderBy(['classes' => SORT_DESC])
            ->all();
    }

}