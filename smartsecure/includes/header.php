<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartSecure</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="icon/icon.png" type="image/png">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
                
    
    <?php if (isset($_SESSION['flash'])) : ?>
        <?php foreach ($_SESSION['flash'] as $type => $message) : ?>
            <div class="alert alert-<?= $type ?>">
                <?= $message ?>
            </div>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']) ?>
    <?php endif; ?>
    
    <h1>SmartSecure</h1>
    <a href="z_login-google.php" class="google">google</a>
    