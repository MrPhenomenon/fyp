<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */

/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>
<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?= Yii::getAlias('@web') ?>/logo.png" alt="Logo">
        </div>
        <h3 class="text-center mb-4">Welcome</h3>
        <p class="text-center text-muted mb-4">Sign in to continue</p>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
        ]); ?>

        <?= $form->field($model, 'username')->textInput([
            'autofocus' => true,
            'placeholder' => 'Enter your email',
            'class' => 'form-control'
        ])->label('Email') ?>

        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Enter your password',
            'class' => 'form-control'
        ])->label('Password') ?>

        <div class="form-group mt-4">
            <?= Html::submitButton('Sign In', ['class' => 'btn btn-primary w-100', 'name' => 'login-button']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
