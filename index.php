<?php
    require_once 'config.php';
    require_once DIR_PHP_FUNCTIONS.'session_manager.php';
    require_once DIR_PHP_FUNCTIONS.'db_manager.php';
    require_once DIR_PHP_FUNCTIONS.'lib.php';

    start_session();
?>

<!DOCTYPE html>
<html>
<head>
    <title>PoliBid</title>
    <?php require_once('./php/fragments/head_tags.php'); ?>
</head>
<body>
    <?php require_once('./php/fragments/header.php'); ?>
    <main>
        <?php require_once('./php/fragments/sidebar.php'); ?>
        <?php require_once('./php/fragments/content_index.php'); ?>
    </main>
</body>
</html>