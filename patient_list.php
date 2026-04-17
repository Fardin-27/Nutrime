<?php
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customdiet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor ID from URL
$doctorId = $_GET['doctor_id'] ?? null;
if (!$doctorId) {
    echo "<h2>Error: No doctor ID provided.</h2>";
    exit;
}

// Fetch confirmed appointments for the logged-in doctor
$appointmentSql = "SELECT a.appointmentid, a.patientid, p.Patient_name, p.age, p.height_m, p.weight_kg, r.comments, r.rating 
                   FROM appointment a 
                   JOIN patient p ON a.patientid = p.patientid 
                   LEFT JOIN reviews r ON a.patientid = r.patientid AND a.licensenumber = r.licensenumber
                   WHERE a.licensenumber = '$doctorId' AND a.status = 'Confirmed'";
$appointmentResult = $conn->query($appointmentSql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .header {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1100px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #32673f;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        table tr:hover {
            background-color: #e9ffe9;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmed Appointments</h1>
    </div>
    <div class="container">
        <?php if ($appointmentResult->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Height (m)</th>
                        <th>Weight (kg)</th>
                        <th>Review</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointmentResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['appointmentid']); ?></td>
                            <td><?php echo htmlspecialchars($row['patientid']); ?></td>
                            <td><?php echo htmlspecialchars($row['Patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['height_m']); ?></td>
                            <td><?php echo htmlspecialchars($row['weight_kg']); ?></td>
                            <td><?php echo htmlspecialchars($row['comments'] ?? 'No review yet'); ?></td>
                            <td><?php echo htmlspecialchars($row['rating'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-data">No confirmed appointments found.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
