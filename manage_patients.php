<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Patient List</title>
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
        .header .back-button {
            background-color: #32573b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }
        .header .back-button:hover {
            background-color: #457d4a;
        }
        .container {
            text-align: center;
            margin: 20px auto;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            font-size: 1.2rem;
        }
        table th {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            text-align: center;
        }
        table td {
            background-color: #fff;
            text-align: center;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .form-container {
            margin: 30px auto;
            width: 50%;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .delete-button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .delete-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Admin Panel</div>
        <a href="admin_dashboard.php" class="back-button">Back to Dashboard</a>
    </div>

    <div class="container">
        <h2>Manage Patients</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Height</th>
                    <th>Weight</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "customdiet";

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['delete'])) {
                        $deleteId = $_POST['delete'];

                        // Delete related records from `patientsymptoms` table
                        $sqlDeletePatientSymptoms = "DELETE FROM patientsymptoms WHERE patientid='$deleteId'";
                        $conn->query($sqlDeletePatientSymptoms);

                        // Delete related records from `appointment` table
                        $sqlDeleteAppointments = "DELETE FROM appointment WHERE patientid='$deleteId'";
                        $conn->query($sqlDeleteAppointments);

                        // Delete related records from `reviews` table
                        $sqlDeleteReviews = "DELETE FROM reviews WHERE patientid='$deleteId'";
                        $conn->query($sqlDeleteReviews);

                        // Finally, delete the patient record
                        $sqlDeletePatient = "DELETE FROM patient WHERE patientid='$deleteId'";
                        if ($conn->query($sqlDeletePatient) === TRUE) {
                            echo "<script>alert('Patient deleted successfully');</script>";
                        } else {
                            echo "<script>alert('Error deleting patient: " . $conn->error . "');</script>";
                        }
                    }
                }

                $sql = "SELECT * FROM patient";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['patientid'] . "</td>";
                        echo "<td>" . $row['Patient_name'] . "</td>";
                        echo "<td>" . $row['age'] . "</td>";
                        echo "<td>" . $row['height_m'] . "</td>";
                        echo "<td>" . $row['weight_kg'] . "</td>";
                        echo "<td>
                            <form method='POST' style='display:inline-block;'>
                                <button name='delete' value='" . $row['patientid'] . "' class='delete-button'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No patients available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
