<?php

use app\models\Schedule;
use app\models\Attendance;
use app\models\Blocks;

$user = Yii::$app->user->identity;

$this->title = 'Clerk Dashboard';
?>

<div class="container mt-4">
    <h1>Welcome, <?= $user->name ?> (Clerk)</h1>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-md-flex gap-3 align-items-end">
                <div class="mb-3">
                    <label>Select Block</label>
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
                    <label class="">Select Floor</label>
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
        <div class="alert bg-warning">Please Select Block and Floor to View Dashboard</div>
    <?php else: ?>
        <div>
            <div class="row">
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

            <div class="row mt-3 gy-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Live Classes (Ongoing)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($data['liveClasses'])): ?>
                                <p>No live classes at the moment.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
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
                                                    <td><?= $class['room_number'] ?></td>
                                                    <td><?= $class['teacher'] ?></td>
                                                    <td><?= $class['subject'] ?></td>
                                                    <td>
                                                        <?php if ($class['status']): ?>
                                                            <span class="badge bg-success">Marked</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Pending</span>
                                                        <?php endif; ?>
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

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Missing Attendance</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($data['missingAttendance'])): ?>
                                <p>No missing attendance at the moment.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Room</th>
                                                <th>Teacher</th>
                                                <th>Subject</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['missingAttendance'] as $missing): ?>
                                                <tr>
                                                    <td><?= $missing['room_number'] ?></td>
                                                    <td><?= $missing['teacher'] ?></td>
                                                    <td><?= $missing['subject'] ?></td>
                                                    <td>
                                                        <?= date('g:i A', strtotime($missing['start_time'])) ?> -
                                                        <?= date('g:i A', strtotime($missing['end_time'])) ?>
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

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Room Usage Today</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($data['roomUsage'])): ?>
                                <p>No room usage data.</p>
                            <?php else: ?>
                                <ul class="list-group">
                                    <?php foreach ($data['roomUsage'] as $usage): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Room <?= $usage['room_number'] ?>
                                            <span class="badge bg-primary rounded-pill"><?= $usage['classes'] ?> classes</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
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
                
                // Save filter and reload dashboard
                setClerkFilter(block_id, '');
            }
        });
    });

    $("#floor_select").change(function(){
        var block_id = $("#block_select").val();
        var floor_id = $(this).val();
        
        // Save filter and reload dashboard
        setClerkFilter(block_id, floor_id);
    });

    function setClerkFilter(block_id, floor_id){
        $.ajax({
            url: '/attendance/set-clerk-filter',
            type: 'POST',
            dataType: 'json',
            data: {
                block_id: block_id,
                floor_id: floor_id
            },
            success: function(response){
                if(response.success){
                    location.reload();
                }
            }
        });
    }
JS;
$this->registerJs($js);
?>