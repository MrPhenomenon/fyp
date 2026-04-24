<?php
$this->title = 'Schedule Management';
?>

<div class="container-fluid px-4 py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><?= $this->title ?></h4>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="refreshBtn">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
            <button class="btn btn-primary btn-sm" id="addScheduleBtn">
                <i class="bi bi-plus-circle me-1"></i>Add Schedule
            </button>
        </div>
    </div>

    <div id="alertContainer" class="mb-2"></div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="scheduleTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Day</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="scheduleTableBody">
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal" class="modal fade" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="scheduleForm">
                <input type="hidden" id="scheduleId" name="schedule_id" value="">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teacher</label>
                        <select id="teacherId" name="teacher_id" class="form-select" required>
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['user_id'] ?>">
                                    <?= htmlspecialchars($teacher['name']) ?>
                                    <?= $teacher['department_name'] ? ' (' . htmlspecialchars($teacher['department_name']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Room</label>
                        <select id="roomId" name="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['room_id'] ?>">Room <?= $room['room_number'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Day of Week</label>
                        <select id="dayOfWeek" name="day_of_week" class="form-select" required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Start Time</label>
                            <input type="time" id="startTime" name="start_time" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">End Time</label>
                            <input type="time" id="endTime" name="end_time" class="form-control" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
var scheduleData = {};
var scheduleModal;

document.addEventListener('DOMContentLoaded', function () {
    scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));

    loadSchedules();

    document.getElementById('addScheduleBtn').addEventListener('click', showAddModal);
    document.getElementById('refreshBtn').addEventListener('click', loadSchedules);
    document.getElementById('scheduleForm').addEventListener('submit', handleSaveSchedule);

    document.getElementById('scheduleModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('scheduleForm').reset();
        document.getElementById('scheduleId').value = '';
    });
});

function loadSchedules() {
    fetch('/schedule-update/get-schedules', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            scheduleData = {};
            data.data.forEach(s => { scheduleData[s.schedule_id] = s; });
            renderScheduleTable(data.data);
        } else {
            showAlert('Failed to load schedules', 'danger');
        }
    })
    .catch(() => showAlert('Error loading schedules', 'danger'));
}

function renderScheduleTable(schedules) {
    const tbody = document.getElementById('scheduleTableBody');

    if (!schedules.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5">No schedules found</td></tr>';
        return;
    }

    tbody.innerHTML = schedules.map(s => `
        <tr>
            <td>#${s.schedule_id}</td>
            <td>${s.teacher_name}</td>
            <td>Room ${s.room_number}</td>
            <td>${s.day_of_week}</td>
            <td>${s.start_time.substring(0, 5)}</td>
            <td>${s.end_time.substring(0, 5)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary no-loader"
                        onclick="editSchedule(${s.schedule_id})">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </td>
        </tr>
    `).join('');
}

function showAddModal() {
    document.getElementById('scheduleForm').reset();
    document.getElementById('scheduleId').value = '';
    document.getElementById('scheduleModalLabel').textContent = 'Add New Schedule';
    scheduleModal.show();
}

function editSchedule(scheduleId) {
    const s = scheduleData[scheduleId];
    if (!s) return;

    document.getElementById('scheduleId').value   = s.schedule_id;
    document.getElementById('teacherId').value    = s.teacher_id;
    document.getElementById('roomId').value       = s.room_id;
    document.getElementById('dayOfWeek').value    = s.day_of_week;
    // DB stores HH:MM:SS — time inputs only accept HH:MM
    document.getElementById('startTime').value    = s.start_time.substring(0, 5);
    document.getElementById('endTime').value      = s.end_time.substring(0, 5);
    document.getElementById('scheduleModalLabel').textContent = 'Edit Schedule';

    scheduleModal.show();
}

function handleSaveSchedule(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('scheduleForm'));

    fetch('/schedule-update/save', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            scheduleModal.hide();
            loadSchedules();
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message || 'Failed to save schedule', 'danger');
        }
    })
    .catch(() => showAlert('Error saving schedule', 'danger'));
}

function showAlert(message, type) {
    const container = document.getElementById('alertContainer');
    const div = document.createElement('div');
    div.className = `alert alert-${type} alert-dismissible fade show`;
    div.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    container.innerHTML = '';
    container.appendChild(div);
    setTimeout(() => div.classList.remove('show'), 4000);
}
</script>
