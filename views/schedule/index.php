<?php

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

$grid = [];
$timeSlots = [];

foreach ($schedules as $s) {

    $timeKey = $s->start_time . '-' . $s->end_time;

    $timeSlots[$timeKey] = [
        'start' => $s->start_time,
        'end' => $s->end_time
    ];

    $grid[$timeKey][$s->day_of_week] = $s;
}

ksort($timeSlots);

?>

<h3>My Weekly Schedule</h3>

<table class="table table-bordered text-center">

    <thead>
        <tr>
            <th>Time</th>
            <?php foreach ($days as $day): ?>
                <th><?= $day ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>

    <tbody>

        <?php foreach ($timeSlots as $key => $slot): ?>

            <tr>

                <td>
                    <?= date('g:i A', strtotime($slot['start'])) ?>
                    -
                    <?= date('g:i A', strtotime($slot['end'])) ?>
                </td>

                <?php foreach ($days as $day): ?>

                    <td>

                        <?php if (isset($grid[$key][$day])):

                            $row = $grid[$key][$day];
                        ?>

                            <strong><?= $row->subject ?></strong><br>

                            Room <?= $row->room->room_number ?><br>

                            <?= $row->room->departmentFloor->floor->block->block_name ?>

                        <?php endif; ?>

                    </td>

                <?php endforeach; ?>

            </tr>

        <?php endforeach; ?>

    </tbody>

</table>