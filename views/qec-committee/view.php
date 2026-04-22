<?php

use app\models\QecCommittee;
use yii\helpers\Html;

/** @var app\models\QecCommittee $model */

$this->title = 'QEC Committee Member Details';
$this->params['breadcrumbs'][] = ['label' => 'QEC Committee', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qec-committee-view card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th style="width: 200px;">Name</th>
                <td><?= Html::encode($model->user->name) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= Html::encode($model->user->email) ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td>
                    <?php if ($model->is_teacher): ?>
                        <span class="badge bg-success">Teacher (QEC)</span>
                    <?php else: ?>
                        <span class="badge bg-info">Clerk (QEC)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Appointed Date</th>
                <td><?= $model->appointed_date ? Yii::$app->formatter->asDate($model->appointed_date) : '-' ?></td>
            </tr>
            
            <tr>
                <th>Created At</th>
                <td><?= Yii::$app->formatter->asDatetime($model->created_at) ?></td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></td>
            </tr>
        </table>
    </div>
</div>