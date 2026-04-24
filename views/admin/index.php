<?php

use app\models\Users;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Create User', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
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
                        'value' => function ($model) {
                            return $model->department ? $model->department->department_name : 'N/A';
                        },
                    ],
                    [
                        'class' => ActionColumn::class,
                        'template' => '{view} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->user_id], [
                                    'class' => 'btn btn-sm btn-info',
                                    'title' => 'View',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                // Don't show delete for current user
                                if ($model->user_id === Yii::$app->user->identity->user_id) {
                                    return '';
                                }
                                return Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $model->user_id], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'title' => 'Delete',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this user?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            </div>
        </div>
    </div>
</div>