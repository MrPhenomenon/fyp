<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
  <div class="sidebar-header">
    <a href="<?= Yii::$app->homeUrl ?>" class="text-white fw-bold fs-2 d-flex align-items-center text-decoration-none">
      <img src="<?= Yii::getAlias('@web') ?>/logo.png" class="img-fluid me-2" alt="logo" width="80">
      
    </a>
  </div>
  <div class="sidebar-content">
    <?php include('menu-list.php'); ?>
  </div>
</nav>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="sidebar-overlay"></div>