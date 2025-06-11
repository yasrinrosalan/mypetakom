<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Attendance QR Code</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>
    <h2>Scan the QR Code to Register Attendance</h2>
    <div id="reader" style="width:300px;"></div>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Handle the scanned code as you like, for example:
            window.location.href = 'register_attendance.php?code=' + encodeURIComponent(decodedText);
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>