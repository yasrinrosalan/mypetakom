<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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
                <center><h2 class="fw-bold mb-3">Student Dashboard</h2> 
				
				<style>
					.center
				{
					text-align:center;
				}
                    table 
                {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                    th, td 
                {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                    th 
                {
                    background-color: #3498db;
                    color: white;
                }
                    tr:hover
                {
                     background-color: #f5f5f5;
                }

				</style>
				<br>
				<br>
				<br>


            </tbody>
                
                <h3>Student Membership Application</h3>
        <form action="process_membership.php" method="post">
            <table>
                <tr>
                    <th colspan="2">Student Information</th>
                </tr>
                <tr>
                    <td width="30%"><label for="student_name">Full Name</label></td>
                    <td><input type="text" id="student_name" name="student_name" required></td>
                </tr>
                <tr>
                    <td><label for="student_email">Email</label></td>
                    <td><input type="email" id="student_email" name="student_email" required></td>
                </tr>
                
                <tr>
                    <th colspan="2">Student ID Card Upload</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="file-upload">
                            <label class="file-upload-label" for="id_card">
                                Click to upload Student ID Card (JPG/PNG/PDF)
                                <input type="file" id="id_card" name="id_card" class="file-upload-input" 
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                            </label>
                           
                        </div>
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">
                            Maximum file size: 2MB. Accepted formats: JPG, PNG, PDF.
                        </p>
                    </td>
                </tr>

                <tr>
                    <th colspan="2">Membership Selection</th>
                </tr>
                <tr>
                    <td>Apply for Membership?</td>
                    <td>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="membership_yes" name="apply_membership" value="yes">
                                <label for="membership_yes">Yes</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="membership_no" name="apply_membership" value="no" checked>
                                <label for="membership_no">No</label>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            <button type="submit">Submit Application</button>
        </form>
    </div>
            </tbody>

           
				<br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <p class="text-muted">This is a layout template for MyPetakom system pages.</p>
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



</body>

</html>