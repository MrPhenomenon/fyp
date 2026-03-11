<?php
$this->title = 'Attendance Report';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1><?= $this->title ?></h1>
        </div>
    </div>

    <!-- Error Alert for Clerks without block/floor selection -->
    <?php if ($isClerkWithoutFilter): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Please select a block and floor!</strong> You need to select a block and floor from the dashboard before viewing the attendance report.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Filters Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filter Report</h5>
        </div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">Filter Report</button>
                    <a href="<?= \yii\helpers\Url::to(['report/export', 'start_date' => $startDate, 'end_date' => $endDate]) ?>" class="btn btn-success no-loader">Export CSV</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$isClerkWithoutFilter): ?>
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Total Records</h5>
                    <h2 class="text-primary"><?= $summary['total'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Present</h5>
                    <h2 class="text-success"><?= $summary['present'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Absent</h5>
                    <h2 class="text-danger"><?= $summary['absent'] ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Students Not Present</h5>
                    <h2 class="text-warning"><?= $summary['students_not_present'] ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Attendance Records -->
    <div class="card">
        <div class="card-header">
            <h5>Detailed Attendance Records</h5>
        </div>
        <div class="card-body">
            <?php if (empty($attendanceRecords)): ?>
                <div class="alert alert-info">No attendance records found for the selected date range.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Teacher</th>
                                <th>Room</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Day</th>
                                <th>Marked By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendanceRecords as $record): ?>
                                <tr>
                                    <td><?= date('M d, Y H:i', strtotime($record['timestamp'])) ?></td>
                                    <td><?= htmlspecialchars($record['teacher_name']) ?></td>
                                    <td><?= htmlspecialchars($record['room_number']) ?></td>
                                    <td><?= htmlspecialchars($record['subject']) ?></td>
                                    <td>
                                        <?php
                                        $statusBadge = 'secondary';
                                        if ($record['status'] === 'Yes') {
                                            $statusBadge = 'success';
                                        } elseif ($record['status'] === 'No') {
                                            $statusBadge = 'danger';
                                        } elseif ($record['status'] === 'Students Not Present') {
                                            $statusBadge = 'warning';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $statusBadge ?>"><?= htmlspecialchars($record['status']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($record['day_of_week']) ?></td>
                                    <td><?= htmlspecialchars($record['marked_by']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
