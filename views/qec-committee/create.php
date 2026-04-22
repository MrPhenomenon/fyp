<?php

use app\models\QecCommittee;
use app\models\Users;
use app\models\Departments;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var app\models\QecCommittee $model */
/** @var app\models\Users $userModel */
/** @var array $availableUsers */

$this->title = 'Add QEC Committee Member';
$this->params['breadcrumbs'][] = ['label' => 'QEC Committee', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="qec-committee-create card">
    <div class="card-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card-body">
        <?php $form = ActiveForm::begin(); ?>

        <div class="mb-4">
            <label class="form-label fw-bold">Add Member Type</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="user_selection_type" id="selectExisting" value="existing" checked>
                <label class="form-check-label" for="selectExisting">
                    Select Existing User
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="user_selection_type" id="createNew" value="new">
                <label class="form-check-label" for="createNew">
                    Create New User
                </label>
            </div>
        </div>

        <div id="existingUserSection">
            <?= $form->field($model, 'user_id')->dropdownList(
                ArrayHelper::map($availableUsers, 'user_id', 'name'),
                ['prompt' => 'Select a user...', 'class' => 'form-select']
            )->label('Select User') ?>
        </div>

        <div id="newUserSection" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="Users[name]" class="form-control" maxlength="true">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="Users[email]" class="form-control" maxlength="true">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="Users[password]" class="form-control" maxlength="true">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select name="Users[department_id]" class="form-select">
                            <option value="">Select Department...</option>
                            <?php foreach (Departments::find()->all() as $dept): ?>
                                <option value="<?= $dept->department_id ?>"><?= Html::encode($dept->department_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <h5 class="mb-3">QEC Member Details</h5>

        <?= $form->field($model, 'appointed_date')->input('date', ['value' => date('Y-m-d')])->label('Appointed Date') ?>

        <input type="hidden" name="create_new_user" id="createNewUser" value="0">

        <div class="form-group mt-4">
            <?= Html::submitButton('<i class="bi bi-save"></i> Save', ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$script = <<<JS
$(document).ready(function() {
    $('input[name="user_selection_type"]').on('change', function() {
        if ($(this).val() === 'existing') {
            $('#existingUserSection').show();
            $('#newUserSection').hide();
            $('#qeccommittee-user_id').attr('required', 'required');
            $('#createNewUser').val('0');
        } else {
            $('#existingUserSection').hide();
            $('#newUserSection').show();
            $('#qeccommittee-user_id').removeAttr('required');
            $('#createNewUser').val('1');
        }
    });
});
JS;
$this->registerJs($script);
?>