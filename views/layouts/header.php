<?php 
use yii\helpers\Html;
?>

<header id="mainHeader" class="main-header">
  <div class="header-left">
    <button id="sidebarToggle" class="toggle-btn">
      <i class="bi bi-list"></i>
    </button>
  </div>

  <div class="header-right">
    <div class="dropdown">
      <a href="#" class="text-decoration-none dropdown-toggle fw-bold text-dark" id="userDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-person-circle fs-4 me-2"></i>
        <?= Yii::$app->user->identity->name ?>
      </a>
      <div class="dropdown-menu dropdown-menu-end" data-popper-placement="bottom-end">
        <div class="dropdown-header">
          <div class="d-flex mb-1">
            <div class="flex-shrink-0">
              <i class="bi bi-person-circle fs-3"></i>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="mb-1"><?= Yii::$app->user->identity->name ?></h6>
              <span><?= Yii::$app->user->identity->role ?></span>
            </div>
          </div>
        </div>
        <a href="#!" class="dropdown-item">
          <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'dropdown-item p-0 m-0']) ?>
          <button type="submit" class="btn btn-link dropdown-item text-start">
            <i class="bi bi-power"></i> Logout
          </button>
          <?= Html::endForm() ?>
        </a>
      </div>
    </div>
  </div>
</header>