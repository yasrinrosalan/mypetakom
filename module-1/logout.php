<?php
session_start();
// session_unset(); 
session_destroy();    // Destroy the session


header("Location: ../module-1/login.php");
exit;
