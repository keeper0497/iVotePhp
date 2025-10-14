<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CATSU iVote - Admin Panel</title>
    <!-- Dynamic base path for CSS -->
    <?php
    // Get the base URL dynamically
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = str_replace('admin.php', '', $_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . "://" . $host . $scriptName;
    ?>
    <link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/style.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>views/assets/css/admin.css">
</head>
<body>