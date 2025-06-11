<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<aside class="col-md-2 sidebar border-end min-vh-100 pt-4 d-none d-md-block bg-dark text-white">
    <nav class="nav flex-column px-3">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'Advisor'): ?>
                <a class="nav-link text-white fw-semibold mb-3" href="../module-2/advisor_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>

                <span class="text-uppercase text-white-50 small mb-1">Events</span>
                <a class="nav-link text-white ps-4 mb-1" href="../module-2/list_event.php">
                    <i class="bi bi-list-task me-2"></i>My Events
                </a>
                <a class="nav-link text-white ps-4 mb-1" href="../module-2/register_event.php">
                    <i class="bi bi-plus-circle me-2"></i>Register Event
                </a>
                <a class="nav-link text-white ps-4 mb-3" href="../module-2/generate_event_qr.php">
                    <i class="bi bi-qr-code me-2"></i>Generate QR
                </a>

                <span class="text-uppercase text-white-50 small mb-1">Committees</span>
                <a class="nav-link text-white ps-4 mb-1" href="../module-2/list_committee.php">
                    <i class="bi bi-people me-2"></i>My Committees
                </a>
                <a class="nav-link text-white ps-4 mb-3" href="../module-2/assign_committee.php">
                    <i class="bi bi-person-plus me-2"></i>Assign Committees
                </a>

            <?php elseif ($_SESSION['role'] === 'Coordinator'): ?>
                <a class="nav-link text-white fw-semibold mb-3" href="../module-2/coordinator_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link text-white ps-4 mb-2" href="../module-2/view_all_events.php">
                    <i class="bi bi-calendar-event me-2"></i>All Events
                </a>

            <?php elseif ($_SESSION['role'] === 'Petakom Administrator'): ?>
                <a class="nav-link text-white fw-semibold mb-3" href="../module-1/admin_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                </a>
                <a class="nav-link text-white ps-4 mb-1" href="../module-1/manage_users.php">
                    <i class="bi bi-person-gear me-2"></i>Manage Users
                </a>
                <a class="nav-link text-white ps-4 mb-2" href="../module-1/manage_membership.php">
                    <i class="bi bi-people-fill me-2"></i>Memberships
                </a>

            <?php elseif ($_SESSION['role'] === 'Student'): ?>
                <a class="nav-link text-white fw-semibold mb-3" href="../module-4/student_dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link text-white ps-4 mb-1" href="../module-4/view_events.php">
                    <i class="bi bi-calendar-week me-2"></i>View Events
                </a>
                <a class="nav-link text-white ps-4 mb-1" href="../module-4/my_attendance.php">
                    <i class="bi bi-check-square me-2"></i>My Attendance
                </a>
                <a class="nav-link text-white ps-4 mb-2" href="../module-4/claim_merit.php">
                    <i class="bi bi-award me-2"></i>Merit Claims
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <hr class="border-light my-3">
        <a class="nav-link text-danger fw-semibold mt-auto" href="../module-1/logout.php">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a>
    </nav>
</aside>