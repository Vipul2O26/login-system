<?php

    include './header.php';
    include './check_cookie.php';


    if (!isset($_SESSION['role']) || $_SESSION['role'] !== "user") {
        header("Location: anauthorized.php");
        exit();
    }

    echo "user dashboard";
    $val = (!empty($_COOKIE['user_email'])) ?  $_COOKIE['user_email'] . "is cookie variable"  : "<br>not cookies stored";
    echo $val;

    echo "<pre>";
    print_r($_COOKIE);
    echo "</pre>";



?>