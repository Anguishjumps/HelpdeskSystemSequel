<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- CSS only -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" type="image/x-icon" href="./img/favicon-16x16.png" />
    <!-- JS -->
    <script src="https://kit.fontawesome.com/1930237c80.js" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
    <?php
    require_once './get-functions.php';

    session_start();

    if (!isset($_SESSION['showOnlyAssigned'])) {
        $_SESSION['showOnlyAssigned'] = "";
    };
    $_SESSION['notLoggedIn'] = false;

    if (!isset($_SESSION['username'])) {
        $_SESSION['notLoggedIn'] = true;
        header("Location: login.php");
    }
    ?>