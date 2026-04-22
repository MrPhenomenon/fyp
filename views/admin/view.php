<?php

use app\models\Users;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Users $model */

$this->title = 'User Details';
$this->params['breadcrumbs'][] = ['label' => 'User Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'user_id',
                    'name',
                    'email',
                    [
                        'attribute' => 'role',
                        'value' => function ($model) {
                            return ucfirst($model->role);
                        },
                    ],
                    [
                        'attribute' => 'department_id',
                        'label' => 'Department',
                        'value' => function ($model) {
                            return $model->department ? $model->department->department_name : 'N/A';
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>