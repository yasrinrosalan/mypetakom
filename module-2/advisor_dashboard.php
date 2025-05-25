<?php
session_start();
include '../db_config.php';
include '../module-1/auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Advisor') {
    header("Location: ../module-1/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
if (isset($_SESSION['success'])) {
    $success_message = $_SESSION['success'];
    unset($_SESSION['success']);
}

// untuk function count events
function getCount($conn, $user_id, $status = null)
{
    $count = 0;
    if ($status) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event WHERE user_id = ? AND status = ?");
        $stmt->bind_param("is", $user_id, $status);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

// untuk assign value dekat variable
$totalEvents = getCount($conn, $user_id);
$upcomingEvents = getCount($conn, $user_id, 'Upcoming');
$postponedEvents = getCount($conn, $user_id, 'Postponed');
$cancelledEvents = getCount($conn, $user_id, 'Cancelled');

// untuk ambik semua events yang advisor manage
$stmt = $conn->prepare("SELECT event_id, event_name, event_date, status, merit_applied FROM event WHERE user_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Advisor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="../styles/app.css">
</head>

<body class="bg-light">
    <?php include '../layout/header.php'; ?>
    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include '../layout/sidebar.php'; ?>
            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-3">My Events - Advisor Dashboard</h2>

                <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>

                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary rounded-4 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-collection me-1"></i> Total Events</h5>
                                <p class="card-text fs-4"><?= $totalEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success rounded-4 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-calendar-check me-1"></i> Upcoming</h5>
                                <p class="card-text fs-4"><?= $upcomingEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-dark bg-warning rounded-4 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-clock-history me-1"></i> Postponed</h5>
                                <p class="card-text fs-4"><?= $postponedEvents ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger rounded-4 shadow">
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-x-circle me-1"></i> Cancelled</h5>
                                <p class="card-text fs-4"><?= $cancelledEvents ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- carta untuk event status -->
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="card p-3 shadow-sm">
                            <h5 class="text-center">Event Status Distribution</h5>
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-3">
                        <div class="card p-3 shadow-sm">
                            <h5 class="text-center">Event Status Overview</h5>
                            <canvas id="statusBarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ini untuk event table -->
                <?php if ($result->num_rows > 0): ?>
                <div class="table-responsive">
                    <center>
                        <table id="eventsTable" class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Event Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Merit Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                                    <td><?= htmlspecialchars($row['event_date']) ?></td>
                                    <td><?= $row['status'] ?></td>
                                    <td><?= $row['merit_applied'] ? "Yes" : "No" ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-info text-white"
                                            href="event_detail.php?event_id=<?= $row['event_id'] ?>">View</a>
                                        <a class="btn btn-sm btn-warning"
                                            href="update_event.php?event_id=<?= $row['event_id'] ?>">Edit</a>
                                        <a class="btn btn-sm btn-danger"
                                            href="delete_event.php?event_id=<?= $row['event_id'] ?>"
                                            onclick="return confirm('Delete this event?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </center>
                </div>
                <?php else: ?>
                <p class="text-muted">No events found.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <?php include '../layout/footer.php'; ?>

    <!-- semua javascript yang perlu -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
    $('#eventsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copyHtml5',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csvHtml5',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                },
                orientation: 'landscape',
                pageSize: 'A4',
                title: 'Advisor Event Report'
            },
            {
                extend: 'print',
                className: 'btn btn-primary btn-sm',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],

        order: [
            [1, "desc"]
        ],
        columnDefs: [{
            orderable: false,
            targets: [4]
        }]
    });

    const statusData = {
        labels: ['Upcoming', 'Postponed', 'Cancelled'],
        datasets: [{
            label: 'Events',
            data: [<?= $upcomingEvents ?>, <?= $postponedEvents ?>, <?= $cancelledEvents ?>],
            backgroundColor: ['#198754', '#ffc107', '#dc3545'],
            borderWidth: 1
        }]
    };

    new Chart(document.getElementById('statusPieChart'), {
        type: 'pie',
        data: statusData
    });
    new Chart(document.getElementById('statusBarChart'), {
        type: 'bar',
        data: statusData,
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>

</body>

</html>