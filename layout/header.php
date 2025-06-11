<?php
$dashboardUrl = "#";
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'Advisor':
            $dashboardUrl = '../module-2/advisor_dashboard.php';
            break;
        case 'Admin':
            $dashboardUrl = '../module-1/admin_dashboard.php';
            break;
        case 'Student':
            $dashboardUrl = '../module-4/student_dashboard.php';
            break;
        case 'Coordinator':
            $dashboardUrl = '../module-2/coordinator_dashboard.php';
            break;
    }
}
?>

<nav class="navbar navbar-light bg-white border-bottom shadow-sm fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center py-2 px-4">
        <a href="<?= $dashboardUrl ?>" class="d-flex align-items-center text-decoration-none">
            <img src="../images/logo.png" alt="UMP Logo" style="height: 50px;" class="me-2">
        </a>

        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="../images/cb23098.png" alt="User Avatar" width="32" height="32" class="rounded-circle me-2">
                <span class="fw-semibold">
                    <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-danger" href="../module-1/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>