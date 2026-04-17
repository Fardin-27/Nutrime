<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .header {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 30px 30px;
            text-align: center;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }
        .header h1 {
            margin: 0;
        }
        .signup-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            margin-top: 50px;
        }
        .signup-container h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .signup-container label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            text-align: left;
        }
        .signup-container input {
            width: calc(100% - 20px);
            padding: 12px 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .signup-container button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .signup-container button:hover {
            background-color: #76a87d;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
            color: red;
        }
    </style>
</head>
<body>
<div class="header">
        <div><img src="images/logo.png" alt="Logo" height='50px'></div>
        <div><h1>NutriMe - Patient Sign Up</h1></div>
        <div></div>  
    </div>
    
    <div class="signup-container" style='margin-top:100px;'>
        <h2>Create New Patient Account</h2>
        <form method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>

            <label for="age">Age</label>
            <input type="number" id="age" name="age" required>

            <label for="height">Height (meters)</label>
            <input type="number" step="0.01" id="height" name="height" required>

            <label for="weight">Weight (kg)</label>
            <input type="number" step="0.1" id="weight" name="weight" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Sign Up</button>
        </form>
        <div class="message">
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

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = $_POST['name'];
                $age = $_POST['age'];
                $height = $_POST['height'];
                $weight = $_POST['weight'];
                $password = $_POST['password'];

                // Fetch the latest patient ID and increment it
                $sql = "SELECT patientid FROM patient ORDER BY patientid DESC LIMIT 1";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $lastId = $row['patientid'];
                    $number = (int)substr($lastId, 1) + 1;
                    $newId = "P" . str_pad($number, 3, "0", STR_PAD_LEFT);
                } else {
                    $newId = "P001"; // Default ID if no patients exist
                }

                // Insert new patient record
                $stmt = $conn->prepare("INSERT INTO patient (patientid, Patient_name, age, height_m, weight_kg, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiiis", $newId, $name, $age, $height, $weight, $password);

                if ($stmt->execute()) {
                    echo "<span style='color: green;'>Account created successfully! Redirecting to login page...</span>";
                    header("Refresh: 3; url=login.php");
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
