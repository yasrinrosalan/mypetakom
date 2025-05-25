<?php
//untuk memastikan session bermula
// dan user telah login sebelum access ke page lain
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//untuk confirmkan user telah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../module-1/login.php");
    exit();
}