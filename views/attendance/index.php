<?php
$this->title = 'Attendance';
?>
<h3>
    <?= $this->title ?>
</h3>

<div class="row mt-3">

    <?php if (empty($schedules)): ?>

        <div class="col-12">
            <div class="alert alert-info">
                No classes scheduled for the selected block and floor.
            </div>
        </div>

    <?php else: ?>

        <?php foreach ($schedules as $row):

            $start = substr($row->start_time, 0, 5);
            $end = substr($row->end_time, 0, 5);

            $status = '';
            if (!empty($row->attendance)) {
                $status = $row->attendance[0]->status;
            }

            ?>

            <div class="col-md-4 col-lg-3 mb-3">

                <div class="card shadow-sm h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            Room
                            <?= $row->room->room_number ?>
                        </h5>

                        <p class="mb-1">
                            <strong>Teacher:</strong>
                            <?= $row->teacher->name ?>
                        </p>

                        <p class="mb-1">
                            <strong>Subject:</strong>
                            <?= $row->subject ?>
                        </p>

                        <p class="mb-3">
                            <strong>Time:</strong>
                            <?= $start ?> -
                            <?= $end ?>
                        </p>
                    
                        <select class="form-select attendance_status <?= $status ? 'is-valid' : '' ?>"
                            data-schedule="<?= $row->schedule_id ?>" 
                            data-room="<?= $row->room_id ?>"
                            data-teacher="<?= $row->teacher_id ?>" 
                            data-subject="<?= $row->subject ?>" >
                            

                            <option value="">Select Attendance</option>

                            <option value="Yes" <?= $status === 'Yes' ? 'selected' : '' ?>>
                                Present
                            </option>

                            <option value="No" <?= $status === 'No' ? 'selected' : '' ?>>
                                Absent
                            </option>

                            <option value="Class Absent" <?= $status === 'Class Absent' ? 'selected' : '' ?>>
                                Class Absent
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>


<?php

$js = <<<JS
    $(document).on("change",".attendance_status",function(){
        var select = $(this);
        var status = select.val();

        if(status === '') return;

        var teacher_id = select.data("teacher");
        var room_id = select.data("room");
        var subject = select.data("subject");
        var scheduleid = select.data("schedule");

        $.ajax({
            url:'/attendance/mark-attendance',
            type:'POST',
            dataType:'json',
            data:{
                schedule_id:scheduleid,
                teacher_id:teacher_id,
                room_id:room_id,
                subject:subject,
                status:status
            },
            success:function(res){
                console.log(res);
                if (res.success){
                    select.removeClass("is-invalid").addClass("is-valid");
                } else {
                    select.removeClass("is-valid").addClass("is-invalid");
                }
            },
            error:function(){
                select.removeClass("is-valid").addClass("is-invalid");
            }
        });
    });
JS;
$this->registerJs($js);
?>