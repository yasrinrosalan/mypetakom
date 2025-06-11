<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="../styles/app.css">
</head>
<body>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                
                <div class="table-responsive">
                    <center>
                        <table id="MembershipTable" class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Student</th>
                                    <th>Status Membership</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            
                        <h1>Welcome, Petakom Coordinator</h1>
                            <h2> Reports Membership </h2>
                                <canvas id="membershipChart" width="400" height="200"></canvas>
                        <script>
                                const ctx = document.getElementById('membershipChart').getContext('2d');
                                new Chart(ctx, {
                                type: 'bar',
                                data: {
                                labels: ['Pending', 'Approved', 'Rejected'],
                                datasets: [{
                                label: '# of Requests',
                                data: [12, 25, 5],
                                backgroundColor: ['lightblue', 'green', 'red']
                            }]
                         }
                    });

                        </script>

                            <tbody>
                                
                                <tr>
                                    <tr>
                                        <td>Li Wei</td>
                                        <td>Approved</td>
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
                                </tr>
                                
                                <tr>
                                    <tr>
                                        <td>Fatimah Yusof</td>
                                        <td>Pending</td>
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
                                </tr>

                                <tr>
                                    <tr>
                                        <td>Toh Goh</td>
                                        <td>Approved</td>
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
                                </tr>

                            </tbody>
                        </table>
                    </center>
             
                <p class="text-muted">No events found.</p>
                
            </main>
        </div>
    </div>

</body>
</html>