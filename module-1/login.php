<?php
session_start();
include '../db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $real_password);

    if ($stmt->fetch()) {
        $stmt->close();

        if ($password === $real_password) {
            $_SESSION['user_id'] = $user_id;

            $stmt = $conn->prepare("SELECT staff_id, name, position FROM staff WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($staff_id, $name, $position);
            if ($stmt->fetch()) {
                $_SESSION['staff_id'] = $staff_id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $position;

                if ($position == 'Advisor') {
                    header("Location: ../module-2/advisor_dashboard.php");
                } elseif ($position == 'Coordinator') {
                    header("Location: ../module-2/coordinator_dashboard.php");
                } else {
                    $error = "❌ Unknown staff position.";
                }
                exit;
            }
            $stmt->close();

            $stmt = $conn->prepare("SELECT student_id, name FROM student WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($student_id, $name);
            if ($stmt->fetch()) {
                $_SESSION['student_id'] = $student_id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = "Student";
                header("Location: ../module-3/student_dashboard.php");
                exit;
            }
            $stmt->close();

            $stmt = $conn->prepare("SELECT admin_id, name FROM admin WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($admin_id, $name);
            if ($stmt->fetch()) {
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['name'] = $name;
                $_SESSION['role'] = "Petakom Administrator";
                header("Location: ../module-1/admin_dashboard.php");
                exit;
            }

            $error = "❌ Role not assigned.";
        } else {
            $error = "❌ Wrong password.";
        }
    } else {
        $error = "❌ User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - MyPetakom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-image: url('../images/bg-img.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    }
    </style>

</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <img src="../images/petakomlogo.png" alt="Petakom Logo" class="d-block mx-auto mb-3"
                style="width: 300px; height: 300px;">


            <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>

</html>