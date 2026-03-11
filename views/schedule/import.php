<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>">
    <input type="file" name="file" required>
    <button type="submit" class="btn btn-primary">Import Schedule</button>
</form>