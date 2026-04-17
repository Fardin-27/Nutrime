<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customdiet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch doctor ID from session or request
session_start();
$doctor_license = isset($_SESSION['doctor_licensenumber']) ? $_SESSION['doctor_licensenumber'] : (isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '');

if (empty($doctor_license)) {
    die("Error: No doctor ID provided. Please log in again.");
}

// Fetch pending appointments for the logged-in doctor
$sql = "SELECT a.appointmentid, a.patientid, p.Patient_name, p.age, p.height_m, p.weight_kg, a.status 
        FROM appointment a
        JOIN patient p ON a.patientid = p.patientid
        WHERE a.licensenumber = ? AND a.status = 'Pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $doctor_license);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Appointments</title>
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
            padding: 30px 30px;
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
        }
        .header .logout-button {
            background-color: white;
            color: #32573b;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .header .logout-button:hover {
            background-color: #76a87d;
            color: white;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
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
            color: #ffffff;
            font-weight: bold;
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
        .button {
            text-decoration: none;
            color: #ffffff;
            background-color: #32673f;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #244e2c;
        }
    </style>
</head>
<body>
    <div class="header">
    <div><img src="images/logo.png" alt="Logo" height='50px'></div>
        <div><h1 style="color:white;">Welcome to NutriMe</h1></div>
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
    <div class="container">
        <h1>My Appointments</h1>
        <?php if ($result->num_rows > 0) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient ID</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Height (m)</th>
                        <th>Weight (kg)</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['appointmentid']); ?></td>
                            <td><?php echo htmlspecialchars($row['patientid']); ?></td>
                            <td><?php echo htmlspecialchars($row['Patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['height_m']); ?></td>
                            <td><?php echo htmlspecialchars($row['weight_kg']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <a href="prescribe.php?appointmentid=<?php echo urlencode($row['appointmentid']); ?>" class="button">Prescribe</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="no-data">No pending appointments available.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
