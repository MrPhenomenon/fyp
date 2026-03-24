<?php
$this->title = 'Schedule Management';
?>

<style>
    .admin-container {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .schedule-controls {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .schedule-table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .schedule-table thead {
        background-color: #2c3e50;
        color: white;
    }

    .schedule-table th {
        padding: 12px 15px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #34495e;
    }

    .schedule-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ecf0f1;
    }

    .schedule-table tbody tr:hover {
        background-color: #f5f6fa;
    }

    .schedule-table tbody tr {
        transition: background-color 0.2s;
    }



    .action-btn {
        padding: 6px 12px;
        margin: 0 4px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .modal-close {
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #7f8c8d;
        line-height: 20px;
    }

    .modal-close:hover {
        color: #2c3e50;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #2c3e50;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #bdc3c7;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
    }

    .modal-footer {
        text-align: right;
        margin-top: 20px;
    }

    .alert {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        animation: slideDown 0.3s;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #7f8c8d;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }
</style>

<div class="admin-container">
    <h1><?= $this->title ?></h1>

    <div id="alertContainer"></div>

    <div class="schedule-controls">
        <button class="btn btn-success" id="addScheduleBtn">
            ➕ Add New Schedule
        </button>
        <button class="btn btn-primary" id="refreshBtn">
            🔄 Refresh
        </button>
    </div>

    <table class="schedule-table" id="scheduleTable">
        <thead>
            <tr>
                <th>Schedule ID</th>
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

<!-- Add/Edit Modal -->
<div id="scheduleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span id="modalTitle">Add New Schedule</span>
            <span class="modal-close" id="closeModal">&times;</span>
        </div>
        <form id="scheduleForm">
            <input type="hidden" id="scheduleId" name="schedule_id" value="">

            <div class="form-group">
                <label>Teacher</label>
                <select id="teacherId" name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= $teacher['user_id'] ?>"><?= $teacher['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Room</label>
                <select id="roomId" name="room_id" required>
                    <option value="">Select Room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['room_id'] ?>">Room <?= $room['room_number'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Day of Week</label>
                <select id="dayOfWeek" name="day_of_week" required>
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
                <input type="time" id="startTime" name="start_time" required>
            </div>

            <div class="form-group">
                <label>End Time</label>
                <input type="time" id="endTime" name="end_time" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Schedule</button>
            </div>
        </form>
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
        document.getElementById('closeModal').addEventListener('click', closeModal);
        document.getElementById('cancelBtn').addEventListener('click', closeModal);
        document.getElementById('scheduleForm').addEventListener('submit', handleSaveSchedule);

        document.getElementById('scheduleModal').addEventListener('click', function (e) {
            if (e.target === this) closeModal();
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
                <button class="action-btn btn btn-info" onclick="editSchedule(${schedule.schedule_id})">Edit</button>
            </td>
        </tr>
    `).join('');
    }



    function showAddModal() {
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleForm').reset();
        document.getElementById('modalTitle').textContent = 'Add New Schedule';
        document.getElementById('scheduleModal').classList.add('active');
    }

    function editSchedule(scheduleId) {
        document.getElementById('scheduleId').value = scheduleId;
        document.getElementById('modalTitle').textContent = 'Edit Schedule';

        const row = document.querySelector(`tr[data-schedule-id="${scheduleId}"]`);
        if (row) {
            document.getElementById('teacherId').value = row.dataset.teacherId || '';
            document.getElementById('roomId').value = row.dataset.roomId || '';
            document.getElementById('dayOfWeek').value = row.querySelectorAll('td')[3].textContent || '';
            document.getElementById('startTime').value = row.querySelectorAll('td')[4].textContent || '';
            document.getElementById('endTime').value = row.querySelectorAll('td')[5].textContent || '';
        }

        document.getElementById('scheduleModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('scheduleForm').reset();
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleModal').classList.remove('active');
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