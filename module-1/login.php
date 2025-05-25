<?php
session_start();
include '../db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // check email ada ke tak
    $stmt = $conn->prepare("SELECT user_id, password, role_type, name FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password, $role, $name);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            $stmt->close();

            // case by role
            switch ($role) {
                case 'Advisor':
                case 'Coordinator':
                    $stmt = $conn->prepare("SELECT staff_id FROM event_advisor WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->bind_result($staff_id);
                    $stmt->fetch();
                    $_SESSION['staff_id'] = $staff_id;
                    $stmt->close();

                    if ($role == 'Advisor') {
                        header("Location: ../module-2/advisor_dashboard.php");
                    } else {
                        header("Location: ../module-2/coordinator_dashboard.php");
                    }
                    exit;

                case 'Student':
                    $stmt = $conn->prepare("SELECT student_id FROM student WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->bind_result($student_id);
                    $stmt->fetch();
                    $_SESSION['student_id'] = $student_id;
                    $stmt->close();

                    header("Location: ../module-3/student_dashboard.php");
                    exit;

                case 'Admin':
                case 'Petakom Administrator':
                    $stmt = $conn->prepare("SELECT staff_id FROM admin WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->bind_result($admin_id);
                    $stmt->fetch();
                    $_SESSION['admin_id'] = $admin_id;
                    $stmt->close();

                    header("Location: ../module-1/admin_dashboard.php");
                    exit;

                default:
                    $error = "Unknown role.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    body {
        background-image: url('../images/bg-img.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: 100vh;
    }

    .card {
        background-color: rgba(255, 255, 255, 0.9);
    }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
            <img src="../images/petakomlogo.png" alt="Petakom Logo" class="d-block mx-auto mb-3"
                style="width: 150px; height: 150px;">

            <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleIcon.classList.toggle('bi-eye');
        toggleIcon.classList.toggle('bi-eye-slash');
    });
    </script>

</body>

</html>