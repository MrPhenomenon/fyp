<!-- Sidebar -->
<nav id="sidebar" class="sidebar pc-sidebar">
  <div class="sidebar-header">
    <a href="<?= Yii::$app->homeUrl ?>" class="text-white fw-bold fs-2 d-flex align-items-center text-decoration-none flex-grow-1">
      <img src="<?= Yii::getAlias('@web') ?>/logo.png" class="img-fluid me-2" alt="logo" width="80">
    </a>
    <button id="sidebarClose" class="sidebar-close-btn" aria-label="Close sidebar">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
  <div class="sidebar-content">
    <?php include('menu-list.php'); ?>
  </div>
</nav>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>