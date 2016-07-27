<head>
    <meta charset="UTF-8">
    <title>supermarktonline.at - Produkteditor</title>
    <link rel="stylesheet" href="<?php echo VIEWPATH; ?>bootstrap-3.3.4-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo VIEWPATH; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo VIEWPATH; ?>css/custom.css">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="<?php echo VIEWPATH; ?>js/jquery-2.1.3.min.js"></script>
    <script src="<?php echo VIEWPATH; ?>js/md5.min.js"></script>
    <script src="<?php echo VIEWPATH; ?>bootstrap-3.3.4-dist/js/bootstrap.min.js"></script>
    <script src="<?php echo VIEWPATH; ?>js/ui-functions.js"></script>
    
    <?php if(isset($_GET['edit'])) { ?>
        <script src="<?php echo VIEWPATH; ?>js/init.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/mixed.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/fs-resize.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/product-open.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/product-save.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/ingredient.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/category.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/tag.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/image.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/tags-admin.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/reservation.js"></script>
        <script src="<?php echo VIEWPATH; ?>js/autosave.js"></script>
    <?php } ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
</head>