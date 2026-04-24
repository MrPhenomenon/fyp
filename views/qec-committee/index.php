<?php

use app\models\QecCommittee;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'QEC Committee Members';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qec-committee-index card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<i class="bi bi-plus-circle"></i> Add Member', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'user_id',
                    'label' => 'Name',
                    'value' => 'user.name',
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'Email',
                    'value' => 'user.email',
                ],
                [
                    'attribute' => 'is_teacher',
                    'label' => 'Role',
                    'value' => function ($model) {
                        return $model->is_teacher ? 'Teacher (QEC)' : 'Clerk (QEC)';
                    },
                    'filter' => [0 => 'Clerk', 1 => 'Teacher'],
                ],
                [
                    'attribute' => 'Departmemt',
                    'label' => 'Department',
                    'value' => 'user.department.department_name',
                ],
                [
                    'attribute' => 'appointed_date',
                    'value' => function ($model) {
                        return $model->appointed_date ? Yii::$app->formatter->asDate($model->appointed_date) : '-';
                    },
                ],
               
                [
                    'class' => ActionColumn::class,
                    'template' => '{view} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="bi bi-eye"></i>', $url, ['class' => 'btn btn-sm btn-info', 'title' => 'View']);
                        },
                       
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="bi bi-trash"></i>', $url, [
                                'class' => 'btn btn-sm btn-danger',
                                'title' => 'Delete',
                                'data' => [
                                    'confirm' => 'Are you sure you want to remove this member from QEC committee?',
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