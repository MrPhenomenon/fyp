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

$facLabels    = array_keys($byFaculty);
$facPresent   = array_map(fn($f) => $f['Yes'],          array_values($byFaculty));
$facAbsent    = array_map(fn($f) => $f['No'],           array_values($byFaculty));
$facClassAbs  = array_map(fn($f) => $f['Class Absent'], array_values($byFaculty));

$donutPresent = (int) $kpi['present'];
$donutAbsent = (int) $kpi['absent'];
$donutClassAbsent = (int) $kpi['classAbsent'];
?>

<div class="container-fluid px-4 py-3">

    <div class="text-center mb-4">
        <h4 class="fw-bold text-uppercase" style="letter-spacing:1px;">Faculty Lecture Attendance Analysis Dashboard</h4>
        <small class="text-muted" id="last-updated"></small>
    </div>

    <!-- Filters row -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="get" action="" class="row g-2 align-items-end">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label form-label-sm fw-semibold mb-1">
                        Department
                    </label>
                    <select name="department_id" class="form-select form-select-sm" id="dept-select">
                        <option value="">All Departments</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept->department_id ?>"
                                <?= $selectedDepartmentId == $dept->department_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dept->department_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <label class="form-label form-label-sm fw-semibold mb-1">
                        Faculty
                    </label>
                    <select name="faculty_id" class="form-select form-select-sm" id="faculty-select">
                        <option value="">All Faculty</option>
                        <?php foreach ($faculties as $faculty): ?>
                            <option value="<?= $faculty->user_id ?>"
                                data-dept-id="<?= (int) $faculty->department_id ?>"
                                <?= $selectedFacultyId == $faculty->user_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($faculty->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <label class="form-label form-label-sm fw-semibold mb-1">
                        From
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($dateFrom) ?>">
                </div>
                <div class="col-6 col-sm-4 col-lg-2">
                    <label class="form-label form-label-sm fw-semibold mb-1">
                        To
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                        value="<?= htmlspecialchars($dateTo) ?>">
                </div>
                <div class="col-12 col-sm-4 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="bi bi-funnel me-1"></i>Apply
                    </button>
                    <a href="<?= \yii\helpers\Url::to(['site/admin-dashboard']) ?>"
                        class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats row -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 p-3 bg-secondary bg-opacity-10">
                        <i class="bi bi-journal-check fs-3 text-secondary"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total</div>
                        <div class="fs-2 fw-bold lh-1" id="kpi-total"><?= $kpi['total'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 p-3 bg-secondary bg-opacity-10">
                        <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Present</div>
                        <div class="fs-2 fw-bold lh-1 text-success" id="kpi-present"><?= $kpi['present'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 p-3 bg-secondary bg-opacity-10">
                        <i class="bi bi-x-circle-fill fs-3 text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Absent</div>
                        <div class="fs-2 fw-bold lh-1 text-danger" id="kpi-absent"><?= $kpi['absent'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="rounded-3 p-3 bg-secondary bg-opacity-10">
                        <i class="bi bi-slash-circle-fill fs-3 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Class Absent</div>
                        <div class="fs-2 fw-bold lh-1 text-warning" id="kpi-class-absent"><?= $kpi['classAbsent'] ?></div>
                    </div>
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
                    <div id="no-data-dept" class="text-muted text-center py-5<?= empty($byDepartment) ? '' : ' d-none' ?>">No data for selected filters.</div>
                    <div id="chart-by-dept"<?= empty($byDepartment) ? ' class="d-none"' : '' ?>></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header py-2">
                    <span class="fw-semibold">Overall Distribution</span>
                </div>
                <div class="card-body p-2 d-flex align-items-center justify-content-center">
                    <div id="no-data-donut" class="text-muted text-center py-5<?= $kpi['total'] == 0 ? '' : ' d-none' ?>">No data for selected filters.</div>
                    <div id="chart-donut" style="width:100%"<?= $kpi['total'] == 0 ? ' class="d-none"' : '' ?>></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Attendance by Faculty</span>
                    <?php if (!empty($byFaculty)): ?>
                        <span class="badge bg-secondary"
                            title="Showing top <?= $data['facultyChartLimit'] ?> by absent count">
                            Top <?= count($byFaculty) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-2">
                    <div id="no-data-faculty" class="text-muted text-center py-5<?= empty($byFaculty) ? '' : ' d-none' ?>">No data for selected filters.</div>
                    <div id="chart-by-faculty"<?= empty($byFaculty) ? ' class="d-none"' : '' ?>></div>
                </div>
            </div>
        </div>


    </div>

</div>

<?php
$deptLabelsJson   = json_encode(array_values($deptLabels));
$deptPresentJson  = json_encode($deptPresent);
$deptAbsentJson   = json_encode($deptAbsent);
$deptClassAbsJson = json_encode($deptClassAbs);

$facLabelsJson    = json_encode(array_values($facLabels));
$facPresentJson   = json_encode(array_values($facPresent));
$facAbsentJson    = json_encode(array_values($facAbsent));
$facClassAbsJson  = json_encode(array_values($facClassAbs));

$donutPresentJson     = json_encode($donutPresent);
$donutAbsentJson      = json_encode($donutAbsent);
$donutClassAbsentJson = json_encode($donutClassAbsent);

$deptCount = count($byDepartment);
$facCount  = count($byFaculty);

$dataUrl = \yii\helpers\Url::to(['site/admin-dashboard-data']);

$js = <<<JS

var chartDept    = null;
var chartFaculty = null;
var chartDonut   = null;

function createDeptChart(labels, present, absent, classAbs) {
    var el = document.querySelector('#chart-by-dept');
    if (!el) return;
    var h = Math.max(300, labels.length * 42);
    chartDept = new ApexCharts(el, {
        series: [
            { name: 'PRESENT',      data: present },
            { name: 'ABSENT',       data: absent },
            { name: 'Class Absent', data: classAbs }
        ],
        chart:  { type: 'bar', stacked: true, height: h, toolbar: { show: false } },
        colors: ['#28a745', '#dc3545', '#fd7e14'],
        plotOptions: { bar: { columnWidth: '55%' } },
        dataLabels: {
            enabled: true,
            formatter: function(val) { return val > 0 ? val : ''; },
            style: { fontSize: '11px', colors: ['#fff'] }
        },
        xaxis: { categories: labels, labels: { rotate: -30, trim: true } },
        yaxis: { labels: { formatter: function(v){ return Math.round(v); } } },
        legend: { position: 'top', horizontalAlign: 'center' },
        tooltip: { shared: true, intersect: false }
    });
    chartDept.render();
}

function createFacultyChart(labels, present, absent, classAbs) {
    var el = document.querySelector('#chart-by-faculty');
    if (!el) return;
    var h = Math.max(280, labels.length * 46);
    chartFaculty = new ApexCharts(el, {
        series: [
            { name: 'PRESENT',      data: present },
            { name: 'ABSENT',       data: absent },
            { name: 'Class Absent', data: classAbs }
        ],
        chart:  { type: 'bar', stacked: true, height: h, toolbar: { show: false } },
        colors: ['#28a745', '#dc3545', '#fd7e14'],
        plotOptions: { bar: { horizontal: true, barHeight: '55%', borderRadius: 3 } },
        dataLabels: {
            enabled: true,
            formatter: function(val) { return val > 0 ? val : ''; },
            style: { fontSize: '11px', colors: ['#fff'] }
        },
        xaxis: {
            categories: labels,
            title: { text: '' },
            labels: { formatter: function(val) { return Math.floor(val) === val ? val : ''; } }
        },
        yaxis: { labels: { style: { fontSize: '12px' }, maxWidth: 150 } },
        legend: { position: 'top', horizontalAlign: 'center' },
        tooltip: { shared: true, intersect: false }
    });
    chartFaculty.render();
}

function createDonutChart(present, absent, classAbs) {
    var el = document.querySelector('#chart-donut');
    if (!el) return;
    chartDonut = new ApexCharts(el, {
        series: [present, absent, classAbs],
        chart:  { type: 'donut', height: 320 },
        labels: ['PRESENT', 'ABSENT', 'Class Absent'],
        colors: ['#28a745', '#dc3545', '#fd7e14'],
        dataLabels: { enabled: true, formatter: function(val){ return Math.round(val) + '%'; } },
        plotOptions: { pie: { donut: { size: '70%' } } },
        legend: { position: 'bottom', fontSize: '12px' },
        tooltip: { y: { formatter: function(val){ return val + ' records'; } } }
    });
    chartDonut.render();
}

// Initial render (only when data exists on page load)
if ($deptCount > 0) {
    createDeptChart($deptLabelsJson, $deptPresentJson, $deptAbsentJson, $deptClassAbsJson);
}
if ($facCount > 0) {
    createFacultyChart($facLabelsJson, $facPresentJson, $facAbsentJson, $facClassAbsJson);
}
if ($donutPresent + $donutAbsent + $donutClassAbsent > 0) {
    createDonutChart($donutPresentJson, $donutAbsentJson, $donutClassAbsentJson);
}

// Faculty dropdown: client-side filtering by department
(function(){
    var deptSel    = document.getElementById('dept-select');
    var facultySel = document.getElementById('faculty-select');
    if (!deptSel || !facultySel) return;

    var allOptions = Array.from(facultySel.options).slice(1);

    deptSel.addEventListener('change', function(){
        var deptId     = this.value;
        var currentVal = facultySel.value;

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

// Auto-refresh every 20 seconds
(function(){
    var dataUrl = '$dataUrl';

    function getFilterParams() {
        var params = new URLSearchParams();
        var deptId  = (document.getElementById('dept-select')    || {}).value || '';
        var facId   = (document.getElementById('faculty-select') || {}).value || '';
        var fromVal = (document.querySelector('[name=date_from]') || {}).value || '';
        var toVal   = (document.querySelector('[name=date_to]')   || {}).value || '';
        if (deptId)  params.set('department_id', deptId);
        if (facId)   params.set('faculty_id',    facId);
        if (fromVal) params.set('date_from',      fromVal);
        if (toVal)   params.set('date_to',        toVal);
        return params.toString();
    }

    function refreshDashboard() {
        fetch(dataUrl + '?' + getFilterParams(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                // Update KPI cards
                var el;
                if ((el = document.getElementById('kpi-total')))        el.textContent = data.kpi.total;
                if ((el = document.getElementById('kpi-present')))      el.textContent = data.kpi.present;
                if ((el = document.getElementById('kpi-absent')))       el.textContent = data.kpi.absent;
                if ((el = document.getElementById('kpi-class-absent'))) el.textContent = data.kpi.classAbsent;

                // Update department chart
                var depts = Object.keys(data.byDepartment);
                var noDataDept = document.getElementById('no-data-dept');
                var elDept = document.getElementById('chart-by-dept');
                if (depts.length > 0) {
                    if (noDataDept) noDataDept.classList.add('d-none');
                    if (elDept) elDept.classList.remove('d-none');
                    if (chartDept) {
                        chartDept.updateOptions({
                            xaxis: { categories: depts },
                            series: [
                                { name: 'PRESENT',      data: depts.map(function(d){ return data.byDepartment[d]['Yes']          || 0; }) },
                                { name: 'ABSENT',       data: depts.map(function(d){ return data.byDepartment[d]['No']           || 0; }) },
                                { name: 'Class Absent', data: depts.map(function(d){ return data.byDepartment[d]['Class Absent'] || 0; }) }
                            ]
                        }, false, true);
                    } else {
                        createDeptChart(
                            depts,
                            depts.map(function(d){ return data.byDepartment[d]['Yes']          || 0; }),
                            depts.map(function(d){ return data.byDepartment[d]['No']           || 0; }),
                            depts.map(function(d){ return data.byDepartment[d]['Class Absent'] || 0; })
                        );
                    }
                } else {
                    if (noDataDept) noDataDept.classList.remove('d-none');
                    if (elDept) elDept.classList.add('d-none');
                }

                // Update faculty chart
                var facs = Object.keys(data.byFaculty);
                var noDataFac = document.getElementById('no-data-faculty');
                var elFac = document.getElementById('chart-by-faculty');
                if (facs.length > 0) {
                    if (noDataFac) noDataFac.classList.add('d-none');
                    if (elFac) elFac.classList.remove('d-none');
                    if (chartFaculty) {
                        chartFaculty.updateOptions({
                            xaxis: { categories: facs },
                            series: [
                                { name: 'PRESENT',      data: facs.map(function(f){ return data.byFaculty[f]['Yes']          || 0; }) },
                                { name: 'ABSENT',       data: facs.map(function(f){ return data.byFaculty[f]['No']           || 0; }) },
                                { name: 'Class Absent', data: facs.map(function(f){ return data.byFaculty[f]['Class Absent'] || 0; }) }
                            ]
                        }, false, true);
                    } else {
                        createFacultyChart(
                            facs,
                            facs.map(function(f){ return data.byFaculty[f]['Yes']          || 0; }),
                            facs.map(function(f){ return data.byFaculty[f]['No']           || 0; }),
                            facs.map(function(f){ return data.byFaculty[f]['Class Absent'] || 0; })
                        );
                    }
                } else {
                    if (noDataFac) noDataFac.classList.remove('d-none');
                    if (elFac) elFac.classList.add('d-none');
                }

                // Update donut chart
                var noDataDonut = document.getElementById('no-data-donut');
                var elDonut = document.getElementById('chart-donut');
                if (data.kpi.total > 0) {
                    if (noDataDonut) noDataDonut.classList.add('d-none');
                    if (elDonut) elDonut.classList.remove('d-none');
                    if (chartDonut) {
                        chartDonut.updateSeries([data.kpi.present, data.kpi.absent, data.kpi.classAbsent]);
                    } else {
                        createDonutChart(data.kpi.present, data.kpi.absent, data.kpi.classAbsent);
                    }
                } else {
                    if (noDataDonut) noDataDonut.classList.remove('d-none');
                    if (elDonut) elDonut.classList.add('d-none');
                }

                // Timestamp
                var ts = document.getElementById('last-updated');
                if (ts) {
                    var now = new Date();
                    ts.textContent = 'Last updated: ' + now.toLocaleTimeString();
                }
            })
            .catch(function(){});
    }

    // Set initial timestamp on load
    var ts = document.getElementById('last-updated');
    if (ts) ts.textContent = 'Last updated: ' + new Date().toLocaleTimeString();

    setInterval(refreshDashboard, 20000);
})();

JS;
$this->registerJs($js);
?>