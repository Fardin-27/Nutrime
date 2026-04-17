<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
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
            text-align: center;
            margin: 20px auto;
        }
        .card {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card h2 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .card table {
            width: 100%;
            border-collapse: collapse;
        }
        .card th, .card td {
            text-align: left;
            padding: 10px;
            font-size: 1.2rem;
        }
        .card th {
            font-weight: bold;
        }
        .button-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
        }
        .dashboard-button {
            width: 200px;
            height: 180px;
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, background-color 0.3s ease;
        }
        .dashboard-button:hover {
            background-color: #76a87d;
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php
        session_start();
        $patientId = $_SESSION['id'] ?? null; // Retrieve patient ID from session
    ?>
    <div class="header">
        <div><img src="images/logo.png" alt="" height='50px'></div>
        <h1>Welcome to NutriMe</h1>
        <div style="display: flex; align-items: center; gap: 10px;">
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>
    <br><br><br><br>
    <div class="container">
        <div class="card">
            <h2>Patient Information</h2>
            <table>
                <?php
                if (!$patientId) {
                    echo "<tr><td colspan='2'>Error: No patient logged in.</td></tr>";
                    exit; // Stop further execution if no patient ID
                }

                // Database connection settings
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "customdiet";

                // Connect to the database
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Fetch patient data
                $sql = "SELECT * FROM patient WHERE patientid = '$patientId'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<tr><th>ID:</th><td>" . $row['patientid'] . "</td></tr>";
                    echo "<tr><th>Name:</th><td>" . $row['Patient_name'] . "</td></tr>";
                    echo "<tr><th>Age:</th><td>" . $row['age'] . "</td></tr>";
                    echo "<tr><th>Height:</th><td>" . $row['height_m'] . " m</td></tr>";
                    echo "<tr><th>Weight:</th><td>" . $row['weight_kg'] . " kg</td></tr>";

                    // Fetch appointment status
                    $appointmentSql = "SELECT status FROM appointment WHERE patientid = '$patientId'";
                    $appointmentResult = $conn->query($appointmentSql);

                    if ($appointmentResult->num_rows > 0) {
                        $appointment = $appointmentResult->fetch_assoc();
                        echo "<tr><th>Appointment Status:</th><td>" . $appointment['status'] . "</td></tr>";
                    } else {
                        echo "<tr><th>Appointment Status:</th><td>No Appointment</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No patient information found.</td></tr>";
                }

                $conn->close();
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div class="button-row">
            <?php
                if ($patientId) {
                    echo '<a href="doctor_list.php?patient_id=' . $patientId . '" class="dashboard-button"><img height="60" src="images/doctor.png" alt=""><br>DOCTOR LIST</a>';

                    $conn = new mysqli($servername, $username, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM appointment WHERE patientid = '$patientId'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo '<a href="appointment_true.php?patient_id=' . $patientId . '" class="dashboard-button"><img height="60" src="images/calendar.png" alt=""><br>CHECK APPOINTMENT</a>';
                    } else {
                        echo '<a href="appointment.php?patient_id=' . $patientId . '" class="dashboard-button"><img height="60" src="images/calendar.png" alt=""><br>TAKE APPOINTMENT</a>';
                    }

                    $conn->close();

                    echo '<a href="food_items.php?patient_id=' . $patientId . '" class="dashboard-button"><img height="60" src="images/vegetable.png" alt=""><br>FOOD ITEM</a>';
                    echo '<a href="my_diet_chart.php?patient_id=' . $patientId . '" class="dashboard-button"><img height="60" src="images/salad.png" alt=""><br>MY DIET CHART</a>';
                }
            ?>
        </div>
    </div>
</body>
</html>