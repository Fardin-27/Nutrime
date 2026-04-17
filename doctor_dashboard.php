<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
        $doctorId = $_SESSION['id'] ?? null; // Retrieve doctor ID from session

        // Initialize doctorName
        $doctorName = "";

        if ($doctorId) {
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

            // Fetch doctor name
            $sql = "SELECT name FROM doctor WHERE licensenumber = '$doctorId'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $doctorName = $row['name'];
            }

            $conn->close();
        }
    ?>
    <div class="header">
        <div class="logo">LOGO</div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?php
                if ($doctorName) {
                    echo '<h1>Welcome ' . htmlspecialchars($doctorName) . '</h1>';
                } else {
                    echo '<h1>Welcome Guest</h1>';
                }
            ?>
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>
    </div>
    <br><br><br><br>
    <div class="container">
        <div class="card">
            <h2>Doctor Information</h2>
            <table>
                <?php
                if (!$doctorId) {
                    echo "<tr><td colspan='2'>Error: No doctor logged in.</td></tr>";
                    exit; // Stop further execution if no doctor ID
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

                // Fetch doctor data
                $sql = "SELECT * FROM doctor WHERE licensenumber = '$doctorId'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo "<tr><th>License Number:</th><td>" . $row['licensenumber'] . "</td></tr>";
                    echo "<tr><th>Name:</th><td>" . $row['name'] . "</td></tr>";
                    echo "<tr><th>Specialization:</th><td>" . $row['specialization'] . "</td></tr>";
                    echo "<tr><th>Phone:</th><td>" . $row['phone_no'] . "</td></tr>";
                } else {
                    echo "<tr><td colspan='2'>No doctor information found.</td></tr>";
                }

                $conn->close();
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div class="button-row">
            <?php
                if ($doctorId) {
                    echo '<a href="doctor_list.php?doctor_id=' . $doctorId . '" class="dashboard-button"><img height="60" src="images/doctor.png" alt=""><br>DOCTOR LIST</a>';
                    echo '<a href="appointment_list.php?doctor_id=' . $doctorId . '" class="dashboard-button"><img height="60" src="images/calendar.png" alt=""><br>APPOINTMENTS</a>';
                    echo '<a href="patient_list.php?doctor_id=' . $doctorId . '" class="dashboard-button"><img height="60" src="images/patient.png" alt=""><br>MY PATIENTS</a>';
                    echo '<a href="food_items.php?doctor_id=' . $doctorId . '" class="dashboard-button"><img height="60" src="images/vegetable.png" alt=""><br>FOOD ITEM</a>';
                }
            ?>
        </div>
    </div>
</body>
</html>
