<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor List</title>
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
        .header button {
            background-color: white;
            color: #32573b;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .header button:hover {
            background-color: #32573b;
            color: white;
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
    </style>
</head>
<body>
    <div class="header">
        <div><img src="images/logo.png" alt="Logo" height='50px'></div>
        <div><h1>Welcome to NutriMe</h1></div>
        <div></div>  
    </div>

    <div class="container">
        <h2>Available Doctors</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Contact</th>
                    <th>Seniority</th>
                </tr>
            </thead>
            <tbody>
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

                $sql = "SELECT * FROM doctor";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['licensenumber'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['specialization'] . "</td>";
                        echo "<td>" . $row['phone_no'] . "</td>";
                        echo "<td>" . ($row['seniorkey'] == 1 ? "Senior" : "Junior") . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No doctors available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
