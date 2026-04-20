<?php

$user = Yii::$app->user->identity;
$today = date('l');

$this->title = 'Teacher Dashboard';
?>

<div class="container mt-4">
    <h1>Welcome, <?= ucfirst($user->name) ?> (Teacher)</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Today's Schedule (<?= $today ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['todaySchedule'])): ?>
                        <p>No classes scheduled for today.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Block</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['todaySchedule'] as $schedule): ?>
                                        <tr>
                                            <td>
                                                <?= date('g:i A', strtotime($schedule->start_time)) ?> - 
                                                <?= date('g:i A', strtotime($schedule->end_time)) ?>
                                            </td>
                                            <td><?= $schedule->subject ?></td>
                                            <td>
                                                <?= $schedule->room->departmentFloor->floor->block->block_name ?>
                                            </td>
                                            <td>
                                                <?= $schedule->room->room_number ?>
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

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Next Class</h5>
                </div>
                <div class="card-body">
                    <?php if ($data['nextClass']): ?>
                        <p><strong>Subject:</strong> <?= $data['nextClass']->subject ?></p>
                        <p><strong>Time:</strong> <?= date('g:i A', strtotime($data['nextClass']->start_time)) ?> - <?= date('g:i A', strtotime($data['nextClass']->end_time)) ?></p>
                        <p><strong>Room:</strong> <?= $data['nextClass']->room->room_number ?></p>
                    <?php else: ?>
                        <p>No upcoming classes today.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Monthly Attendance Summary</h5>
                </div>
                <div class="card-body">
                    <div id="attendanceChart"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$chartData = $data['last30DaysAttendance'];
$labels = json_encode(array_keys($chartData));
$values = json_encode(array_values($chartData));

$js = <<<JS
    var options = {
        series: $values,
        chart: {
            type: 'pie',
            height: 350
        },
        labels: $labels,
        colors: ['#4CAF50', '#F44336', '#FF9800'],
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        return Math.round(val) + '%';
                    }
                }
            }
        },
        legend: {
            position: 'bottom'
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#attendanceChart"), options);
    chart.render();
JS;
$this->registerJs($js);
?>
