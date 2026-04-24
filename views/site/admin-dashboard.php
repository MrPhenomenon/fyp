<?php
/** @var yii\web\View $this */
/** @var array $data */
/** @var \app\models\Departments[] $departments */
/** @var \app\models\Users[] $faculties */

$this->title = 'Faculty Lecture Attendance Dashboard';

$kpi = $data['kpi'];
$byDepartment = $data['byDepartment'];
$byFaculty = $data['byFaculty'];

$deptLabels = array_keys($byDepartment);
$deptPresent = array_map(fn($d) => $d['Yes'], array_values($byDepartment));
$deptAbsent = array_map(fn($d) => $d['No'], array_values($byDepartment));
$deptClassAbs = array_map(fn($d) => $d['Class Absent'], array_values($byDepartment));

$facLabels = array_keys($byFaculty);
$facAbsent = array_map(fn($f) => $f['No'], array_values($byFaculty));

$donutPresent = (int) $kpi['present'];
$donutAbsent = (int) $kpi['absent'];
$donutClassAbsent = (int) $kpi['classAbsent'];
?>

<div class="container-fluid px-4 py-3">

    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase" style="letter-spacing:1px;">Faculty Lecture Attendance Analysis Dashboard
        </h4>
    </div>

    <div class="row g-3 align-items-stretch mb-4">
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card h-100 text-center border-2">
                <div class="card-body py-3">
                    <div class="text-muted small fw-semibold text-uppercase mb-1">Total</div>
                    <div class="display-6 fw-bold"><?= $kpi['total'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card h-100 text-center border-2 border-success">
                <div class="card-body py-3">
                    <div class="text-success small fw-semibold text-uppercase mb-1">Present</div>
                    <div class="display-6 fw-bold text-success"><?= $kpi['present'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card h-100 text-center border-2 border-danger">
                <div class="card-body py-3">
                    <div class="text-danger small fw-semibold text-uppercase mb-1">Absent</div>
                    <div class="display-6 fw-bold text-danger"><?= $kpi['absent'] ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="card h-100 text-center border-2 border-warning">
                <div class="card-body py-3">
                    <div class="text-warning small fw-semibold text-uppercase mb-1">Class Absent</div>
                    <div class="display-6 fw-bold text-warning"><?= $kpi['classAbsent'] ?></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-body py-3">
                    <form method="get" action="" class="row g-2 align-items-end">
                        <div class="col-6">
                            <label class="form-label form-label-sm fw-semibold mb-1">Department</label>
                            <select name="department_id" class="form-select form-select-sm" id="dept-select">
                                <option value="">All</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?= $dept->department_id ?>"
                                        <?= $selectedDepartmentId == $dept->department_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dept->department_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm fw-semibold mb-1">Faculty</label>
                            <select name="faculty_id" class="form-select form-select-sm" id="faculty-select">
                                <option value="">All</option>
                                <?php foreach ($faculties as $faculty): ?>
                                    <option value="<?= $faculty->user_id ?>"
                                        data-dept-id="<?= (int) $faculty->department_id ?>"
                                        <?= $selectedFacultyId == $faculty->user_id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($faculty->name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm fw-semibold mb-1">From</label>
                            <input type="date" name="date_from" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($dateFrom) ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label form-label-sm fw-semibold mb-1">To</label>
                            <input type="date" name="date_to" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($dateTo) ?>">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                <i class="bi bi-funnel me-1"></i>Apply
                            </button>
                            <a href="<?= \yii\helpers\Url::to(['site/admin-dashboard']) ?>"
                                class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="bi bi-x-circle me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header py-2">
                    <span class="fw-semibold">Attendance by Department</span>
                </div>
                <div class="card-body p-2">
                    <?php if (empty($byDepartment)): ?>
                        <div class="text-muted text-center py-5">No data for selected filters.</div>
                    <?php else: ?>
                        <div id="chart-by-dept"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header py-2">
                    <span class="fw-semibold">Overall Distribution</span>
                </div>
                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                    <?php if ($kpi['total'] == 0): ?>
                        <div class="text-muted text-center py-5">No data for selected filters.</div>
                    <?php else: ?>
                        <div id="chart-donut" style="width:100%"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Absent Count by Faculty</span>
                    <?php if (!empty($byFaculty)): ?>
                        <span class="badge bg-secondary"
                            title="Showing top <?= $data['facultyChartLimit'] ?> by absent count">
                            Top <?= count($byFaculty) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <?php if (empty($byFaculty)): ?>
                        <div class="text-muted text-center py-5">No data for selected filters.</div>
                    <?php else: ?>
                        <div id="chart-by-faculty"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>


    </div>

</div>

<?php
$deptLabelsJson = json_encode(array_values($deptLabels));
$deptPresentJson = json_encode($deptPresent);
$deptAbsentJson = json_encode($deptAbsent);
$deptClassAbsJson = json_encode($deptClassAbs);

$facLabelsJson = json_encode(array_values($facLabels));
$facAbsentJson = json_encode(array_values($facAbsent));

$donutPresentJson = json_encode($donutPresent);
$donutAbsentJson = json_encode($donutAbsent);
$donutClassAbsentJson = json_encode($donutClassAbsent);

$deptCount = count($byDepartment);
$facCount = count($byFaculty);

$js = <<<JS

(function(){
    var el = document.querySelector('#chart-by-dept');
    if (!el) return;
    var h = Math.max(300, $deptCount * 42);
    new ApexCharts(el, {
        series: [
            { name: 'PRESENT',      data: $deptPresentJson },
            { name: 'ABSENT',       data: $deptAbsentJson },
            { name: 'Class Absent', data: $deptClassAbsJson }
        ],
        chart:  { type: 'bar', stacked: true, height: h, toolbar: { show: false } },
        colors: ['#28a745', '#dc3545', '#fd7e14'],
        plotOptions: { bar: { columnWidth: '55%' } },
        dataLabels: {
            enabled: true,
            formatter: function(val) { return val > 0 ? val : ''; },
            style: { fontSize: '11px', colors: ['#fff'] }
        },
        xaxis: { categories: $deptLabelsJson, labels: { rotate: -30, trim: true } },
        yaxis: { labels: { formatter: function(v){ return Math.round(v); } } },
        legend: { position: 'top', horizontalAlign: 'center' },
        tooltip: { shared: true, intersect: false }
    }).render();
})();

(function(){
    var el = document.querySelector('#chart-by-faculty');
    if (!el) return;
    var h = Math.max(280, $facCount * 46);
    new ApexCharts(el, {
        series: [{ name: 'ABSENT', data: $facAbsentJson }],
        chart:  { type: 'bar', height: h, toolbar: { show: false } },
        colors: ['#dc3545'],
        plotOptions: { bar: { horizontal: true, barHeight: '55%', borderRadius: 3 } },
        dataLabels: {
            enabled: true,
            style: { fontSize: '11px', colors: ['#fff'] }
        },
        xaxis: { categories: $facLabelsJson, title: { text: 'ABSENT' } },
        yaxis: { labels: { style: { fontSize: '12px' }, maxWidth: 140 } },
        legend: { show: false },
        tooltip: { theme: 'light' }
    }).render();
})();

(function(){
    var el = document.querySelector('#chart-donut');
    if (!el) return;
    new ApexCharts(el, {
        series: [$donutPresentJson, $donutAbsentJson, $donutClassAbsentJson],
        chart:  { type: 'donut', height: 320 },
        labels: ['PRESENT', 'ABSENT', 'Class Absent'],
        colors: ['#28a745', '#dc3545', '#fd7e14'],
        dataLabels: { enabled: true, formatter: function(val){ return Math.round(val) + '%'; } },
        plotOptions: { pie: { donut: { size: '70%' } } },
        legend: { position: 'bottom', fontSize: '12px' },
        tooltip: {
            y: { formatter: function(val){ return val + ' records'; } }
        }
    }).render();
})();

(function(){
    var deptSel    = document.getElementById('dept-select');
    var facultySel = document.getElementById('faculty-select');
    if (!deptSel || !facultySel) return;

    var allOptions = Array.from(facultySel.options).slice(1); // skip "All"

    deptSel.addEventListener('change', function(){
        var deptId = this.value;
        var currentVal = facultySel.value;

        // Rebuild options
        while (facultySel.options.length > 1) facultySel.remove(1);

        allOptions.forEach(function(opt){
            if (!deptId || opt.dataset.deptId === deptId) {
                facultySel.appendChild(opt.cloneNode(true));
            }
        });

        var stillPresent = Array.from(facultySel.options).some(function(o){ return o.value === currentVal; });
        if (!stillPresent) facultySel.value = '';
    });
})();

JS;
$this->registerJs($js);
?>