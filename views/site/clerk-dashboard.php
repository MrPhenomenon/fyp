<?php

use app\models\Schedule;
use app\models\Attendance;
use app\models\Blocks;

$user = Yii::$app->user->identity;
$isQecTeacher = isset($isQecTeacher) && $isQecTeacher;

$this->title = $isQecTeacher ? 'QEC Committee Dashboard' : 'Clerk Dashboard';
?>

<div class="container">
    <h1>Welcome, <?= $user->name ?><?= ucfirst($user->role) ?></h1>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-md-flex gap-3 align-items-end">
                <div class="mb-3">
                    <label class="form-label">Select Block</label>
                    <select class="form-select" name="block_id" id="block_select">
                        <option value="">Select Block</option>
                        <?php foreach ($blocks as $block): ?>
                            <option value="<?= $block->block_id ?>" <?= $selectedBlockId == $block->block_id ? 'selected' : '' ?>>
                                <?= $block->block_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Select Floor</label>
                    <select class="form-select" name="floor_id" id="floor_select">
                        <option value="">Select Floor</option>
                        <?php foreach ($floors as $floor): ?>
                            <option value="<?= $floor->floor_id ?>" <?= $selectedFloorId == $floor->floor_id ? 'selected' : '' ?>>
                                Floor <?= $floor->floor_number ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($data)): ?>
        <div class="alert alert-warning">Please select Block and Floor to view the dashboard.</div>
    <?php else: ?>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total Classes Today</h5>
                        <h2 class="text-primary"><?= $data['attendanceToday']['total'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Marked</h5>
                        <h2 class="text-success"><?= $data['attendanceToday']['marked'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h2 class="text-warning"><?= $data['attendanceToday']['remaining'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4">

            <!-- Live Classes -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Live Classes <span class="badge bg-danger ms-1" style="font-size:10px;vertical-align:middle;">LIVE</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($data['liveClasses'])): ?>
                            <p class="text-muted p-4 mb-0">No live classes at the moment.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table mb-0 att-table">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Teacher</th>
                                            <th>Subject</th>
                                            <th>Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['liveClasses'] as $class): ?>
                                            <tr>
                                                <td data-label="Room"><strong><?= $class['room_number'] ?></strong></td>
                                                <td data-label="Teacher"><?= $class['teacher'] ?></td>
                                                <td data-label="Subject"><?= $class['subject'] ?></td>
                                                <td class="att-cell">
                                                    <div class="att-group"
                                                        data-schedule="<?= $class['schedule_id'] ?>"
                                                        data-room="<?= $class['room_id'] ?>"
                                                        data-teacher="<?= $class['teacher_id'] ?>"
                                                        data-subject="<?= $class['subject'] ?>">
                                                        <button type="button"
                                                            class="att-btn att-present <?= $class['status'] === 'Yes' ? 'selected' : '' ?>"
                                                            data-value="Yes">
                                                            <i class="bi bi-check2-circle"></i><span>Present</span>
                                                        </button>
                                                        <button type="button"
                                                            class="att-btn att-absent <?= $class['status'] === 'No' ? 'selected' : '' ?>"
                                                            data-value="No">
                                                            <i class="bi bi-x-circle"></i><span>Absent</span>
                                                        </button>
                                                        <button type="button"
                                                            class="att-btn att-noclass <?= $class['status'] === 'Class Absent' ? 'selected' : '' ?>"
                                                            data-value="Class Absent">
                                                            <i class="bi bi-slash-circle"></i><span>Class Absent</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Missing Attendance -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Missing Attendance</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($data['missingAttendance'])): ?>
                            <p class="text-muted p-4 mb-0">No missing attendance.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table mb-0 att-table">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Teacher</th>
                                            <th>Subject</th>
                                            <th>Time</th>
                                            <th>Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['missingAttendance'] as $missing): ?>
                                            <tr>
                                                <td data-label="Room"><strong><?= $missing['room_number'] ?></strong></td>
                                                <td data-label="Teacher"><?= $missing['teacher'] ?></td>
                                                <td data-label="Subject"><?= $missing['subject'] ?></td>
                                                <td data-label="Time" class="text-muted" style="font-size:12px;">
                                                    <?= date('g:i A', strtotime($missing['start_time'])) ?> –
                                                    <?= date('g:i A', strtotime($missing['end_time'])) ?>
                                                </td>
                                                <td class="att-cell">
                                                    <div class="att-group"
                                                        data-schedule="<?= $missing['schedule_id'] ?>"
                                                        data-room="<?= $missing['room_id'] ?>"
                                                        data-teacher="<?= $missing['teacher_id'] ?>"
                                                        data-subject="<?= $missing['subject'] ?>">
                                                        <button type="button"
                                                            class="att-btn att-present <?= ($missing['status'] ?? '') === 'Yes' ? 'selected' : '' ?>"
                                                            data-value="Yes">
                                                            <i class="bi bi-check2-circle"></i><span>Present</span>
                                                        </button>
                                                        <button type="button"
                                                            class="att-btn att-absent <?= ($missing['status'] ?? '') === 'No' ? 'selected' : '' ?>"
                                                            data-value="No">
                                                            <i class="bi bi-x-circle"></i><span>Absent</span>
                                                        </button>
                                                        <button type="button"
                                                            class="att-btn att-noclass <?= ($missing['status'] ?? '') === 'Class Absent' ? 'selected' : '' ?>"
                                                            data-value="Class Absent">
                                                            <i class="bi bi-slash-circle"></i><span>Class Absent</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Room Usage -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Room Usage Today</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($data['roomUsage'])): ?>
                            <p class="text-muted p-4 mb-0">No room usage data.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($data['roomUsage'] as $usage): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Room <?= $usage['room_number'] ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $usage['classes'] ?> classes</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php
$js = <<<JS
    $("#block_select").change(function(){
        var block_id = $(this).val();
        $.ajax({
            url: '/attendance/get-floors',
            type: 'POST',
            data: {block_id: block_id},
            dataType: 'json',
            success: function(floors){
                var options = '<option value="">Select Floor</option>';
                $.each(floors, function(i, floor){
                    options += '<option value="' + floor.floor_id + '">Floor ' + floor.floor_number + '</option>';
                });
                $("#floor_select").html(options);
                setClerkFilter(block_id, '');
            }
        });
    });

    $("#floor_select").change(function(){
        setClerkFilter($("#block_select").val(), $(this).val());
    });

    function setClerkFilter(block_id, floor_id){
        $.ajax({
            url: '/attendance/set-clerk-filter',
            type: 'POST',
            dataType: 'json',
            data: { block_id: block_id, floor_id: floor_id },
            success: function(response){
                if(response.success) location.reload();
            }
        });
    }

    // Attendance button group handler
    $(document).on("click", ".att-btn", function(){
        var \$btn   = $(this);
        var \$group = \$btn.closest(".att-group");
        var status  = \$btn.data("value");

        // Deselect if already selected (toggle off)
        if (\$btn.hasClass("selected")) return;

        \$group.find(".att-btn").removeClass("selected");
        \$btn.addClass("selected");
        \$group.addClass("saving");

        $.ajax({
            url: '/attendance/mark-attendance',
            type: 'POST',
            dataType: 'json',
            data: {
                schedule_id: \$group.data("schedule"),
                teacher_id:  \$group.data("teacher"),
                room_id:     \$group.data("room"),
                subject:     \$group.data("subject"),
                status:      status
            },
            success: function(res){
                \$group.removeClass("saving");
                if (!res.success) {
                    \$group.find(".att-btn").removeClass("selected");
                    showToast("Failed to save attendance.", "danger");
                }
            },
            error: function(){
                \$group.removeClass("saving");
                \$group.find(".att-btn").removeClass("selected");
                showToast("Server error.", "danger");
            }
        });
    });
JS;
$this->registerJs($js);
?>
