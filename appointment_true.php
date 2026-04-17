<?php
session_start();
$patientId = $_SESSION['id'] ?? null; // Retrieve patient ID from session

if (!$patientId) {
    echo "<h2>Error: No patient logged in.</h2>";
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customdiet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle appointment cancellation before rendering HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    // Delete from appointment table
    $deleteAppointmentSql = "DELETE FROM appointment WHERE patientid = '$patientId'";
    $conn->query($deleteAppointmentSql);

    // Delete from patientsymptoms table
    $deleteSymptomSql = "DELETE FROM patientsymptoms WHERE patientid = '$patientId'";
    $conn->query($deleteSymptomSql);

    // Redirect to dashboard
    header("Location: patient_dashboard.php");
    exit; // Ensure no further code is executed after the redirect
}

// Initialize variables
$appointmentData = null;
$doctorData = null;

// Fetch appointment details
$appointmentSql = "SELECT * FROM appointment WHERE patientid = '$patientId'";
$appointmentResult = $conn->query($appointmentSql);

if ($appointmentResult && $appointmentResult->num_rows > 0) {
    $appointmentData = $appointmentResult->fetch_assoc();
} else {
    echo "<h2>Error: Appointment data not found.</h2>";
    exit;
}

// Fetch symptoms with their names
$symptomSql = "SELECT s.symptom AS symptom_name FROM patientsymptoms ps JOIN symptoms s ON ps.symptom_id = s.symptom_id WHERE ps.patientid = '$patientId'";
$symptomResult = $conn->query($symptomSql);

// Fetch doctor information
if ($appointmentData && $appointmentData['licensenumber']) {
    $doctorId = $appointmentData['licensenumber'];
    $doctorSql = "SELECT * FROM doctor WHERE licensenumber = '$doctorId'";
    $doctorResult = $conn->query($doctorSql);

    if ($doctorResult && $doctorResult->num_rows > 0) {
        $doctorData = $doctorResult->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }
        .header {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            width: 100px;
            height: 50px;
            background-color: white;
            color: #32573b;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border-radius: 5px;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        .card h2 {
            margin: 0;
            padding: 15px;
            background: #32573b;
            color: white;
            font-size: 1.5rem;
            text-align: center;
        }
        .card table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .card th, .card td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .card th {
            background: #f8f8f8;
            color: #333;
        }
        .cancel-button {
            background-color: red;
            color: white;
            padding: 10px 15px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: inline-block;
        }
        .cancel-button:hover {
            background-color: darkred;
        }
        .button-container {
            text-align: center;
        }
        .symptoms {
            padding: 10px;
            font-size: 16px;
            color: #333;
            background: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">LOGO</div>
        <h1>Appointment Details</h1>
    </div>

    <div class="container">
        <?php if ($appointmentData): ?>
            <div class="card">
                <h2>Appointment Information</h2>
                <table>
                    <tr><th>Appointment ID</th><td><?php echo $appointmentData['appointmentid']; ?></td></tr>
                    <tr><th>Status</th><td><?php echo $appointmentData['status']; ?></td></tr>
                    <tr><th>Fees</th><td>$<?php echo $appointmentData['fees']; ?></td></tr>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($doctorData): ?>
            <div class="card">
                <h2>Doctor Information</h2>
                <table>
                    <tr><th>Doctor ID</th><td><?php echo $doctorData['licensenumber']; ?></td></tr>
                    <tr><th>Name</th><td><?php echo $doctorData['name']; ?></td></tr>
                    <tr><th>Specialization</th><td><?php echo $doctorData['specialization']; ?></td></tr>
                    <tr><th>Phone Number</th><td><?php echo $doctorData['phone_no']; ?></td></tr>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($symptomResult && $symptomResult->num_rows > 0): ?>
            <div class="card">
                <h2>Symptoms</h2>
                <div class="symptoms">
                    <?php
                    $symptoms = [];
                    while ($row = $symptomResult->fetch_assoc()) {
                        $symptoms[] = $row['symptom_name'];
                    }
                    echo implode(', ', $symptoms);
                    ?>
                </div>
            </div>
        <?php else: ?>
            <h2>No symptoms recorded for this patient.</h2>
        <?php endif; ?>

        <div class="button-container">
            <form method="POST">
                <button type="submit" name="cancel" class="cancel-button">Cancel Appointment</button>
            </form>
        </div>
    </div>
</body>
</html>
