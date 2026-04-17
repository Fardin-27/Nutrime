<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reviews</title>
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
            text-align: center;
        }
        .header h1 {
            margin: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin - View Reviews</h1>
    </div>
    <div class="container">
        <h2>All Reviews and Ratings</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Comments</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection settings
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "customdiet";

                // Establish connection to the database
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check the connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to get reviews with patient and doctor names
                $sql = "SELECT patient.Patient_name AS patient_name, doctor.name AS doctor_name, reviews.comments, reviews.rating 
                        FROM reviews
                        JOIN patient ON reviews.patientid = patient.patientid
                        JOIN doctor ON reviews.licensenumber = doctor.licensenumber";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['patient_name'] . "</td>";
                        echo "<td>" . $row['doctor_name'] . "</td>";
                        echo "<td>" . $row['comments'] . "</td>";
                        echo "<td>" . $row['rating'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No reviews available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
