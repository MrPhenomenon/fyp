<?php
$this->title = 'Schedule Management';
$this->registerCss('
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal-dialog {
        z-index: 1060 !important;
    }
');
?>

<div class="schedule-management">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= $this->title ?></h2>
       
    </div>

    <div id="alertContainer"></div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="scheduleTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Teacher</th>
                        <th>Room</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="scheduleTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="scheduleModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="scheduleForm">
                <input type="hidden" id="scheduleId" name="schedule_id" value="">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Teacher</label>
                        <select id="teacherId" name="teacher_id" class="form-control" required>
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['user_id'] ?>"><?= $teacher['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Room</label>
                        <select id="roomId" name="room_id" class="form-control" required>
                            <option value="">Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['room_id'] ?>">Room <?= $room['room_number'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Day of Week</label>
                        <select id="dayOfWeek" name="day_of_week" class="form-control" required>
                            <option value="">Select Day</option>
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" id="startTime" name="start_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" id="endTime" name="end_time" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        initializeScheduleManager();
    });

    function initializeScheduleManager() {
        loadSchedules();

        document.getElementById('addScheduleBtn').addEventListener('click', showAddModal);
        document.getElementById('refreshBtn').addEventListener('click', loadSchedules);
        document.getElementById('scheduleForm').addEventListener('submit', handleSaveSchedule);

        // Bootstrap modal events
        $('#scheduleModal').on('hidden.bs.modal', function () {
            document.getElementById('scheduleForm').reset();
            document.getElementById('scheduleId').value = '';
        });
    }

    function loadSchedules() {
        fetch('/schedule-update/get-schedules', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderScheduleTable(data.data);
                }
            })
            .catch(error => {
                showAlert('Error loading schedules', 'error');
                console.error('Error:', error);
            });
    }

    function renderScheduleTable(schedules) {
        const tbody = document.getElementById('scheduleTableBody');

        if (schedules.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;"><div class="empty-state"><div class="empty-state-icon">📚</div><p>No schedules found</p></div></td></tr>';
            return;
        }

        tbody.innerHTML = schedules.map(schedule => `
        <tr data-schedule-id="${schedule.schedule_id}">
            <td>#${schedule.schedule_id}</td>
            <td>${schedule.teacher_name}</td>
            <td>Room ${schedule.room_number}</td>
            <td>${schedule.day_of_week}</td>
            <td>${schedule.start_time}</td>
            <td>${schedule.end_time}</td>
            <td>
                <button class="action-btn btn btn-info no-loader" onclick="editSchedule(${schedule.schedule_id})">Edit</button>
            </td>
        </tr>
    `).join('');
    }



    function showAddModal() {
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleForm').reset();
        document.getElementById('scheduleModalLabel').textContent = 'Add New Schedule';
        $('#scheduleModal').modal('show');
    }

    function editSchedule(scheduleId) {
        document.getElementById('scheduleId').value = scheduleId;
        document.getElementById('scheduleModalLabel').textContent = 'Edit Schedule';

        const row = document.querySelector(`tr[data-schedule-id="${scheduleId}"]`);
        if (row) {
            document.getElementById('teacherId').value = row.dataset.teacherId || '';
            document.getElementById('roomId').value = row.dataset.roomId || '';
            document.getElementById('dayOfWeek').value = row.querySelectorAll('td')[3].textContent || '';
            document.getElementById('startTime').value = row.querySelectorAll('td')[4].textContent || '';
            document.getElementById('endTime').value = row.querySelectorAll('td')[5].textContent || '';
        }

        $('#scheduleModal').modal('show');
    }

    function closeModal() {
        $('#scheduleModal').modal('hide');
    }

    function handleSaveSchedule(e) {
        e.preventDefault();

        const formData = new FormData(document.getElementById('scheduleForm'));

        fetch('/schedule-update/save', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideloader();
                    closeModal();
                    loadSchedules();
                    showAlert(data.message, 'success');
                } else {
                    hideloader();
                    showAlert(data.message || 'Failed to save', 'error');
                }
            })
            .catch(error => {
                hideloader();
                showAlert('Error saving schedule', 'error');
                console.error('Error:', error);
            });
    }



    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alertContainer');
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;

        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);

        setTimeout(() => {
            alert.style.animation = 'slideUp 0.3s forwards';
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }
</script>