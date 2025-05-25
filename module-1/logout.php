<?php
session_start();
session_unset();
session_destroy();

// untuk hilangkan cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// untuk redirect ke login page
header("Location: login.php");
exit;