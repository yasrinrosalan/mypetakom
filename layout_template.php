<?php
session_start();
include 'db_config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MyPetakom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/app.css">
</head>

<body class="bg-light">

    <?php include 'layout/header.php'; ?>

    <div class="container-fluid" style="padding-top: 80px;">
        <div class="row">
            <?php include 'layout/sidebar.php'; ?>

            <!-- MAIN CONTENT LETAK SINI -->
            <main class="col-md-10 p-4">
                <h2 class="fw-bold mb-3">Page Title</h2>
                <p class="text-muted">This is a layout template for MyPetakom system pages.</p>
            </main>
        </div>
    </div>

    <?php include 'layout/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>