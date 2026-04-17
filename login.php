<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        .login-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            margin-top: 100px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .login-container label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
            text-align: left;
        }
        .login-container input {
            width: calc(100% - 20px);
            padding: 12px 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .login-container button {
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
        .login-container button:hover {
            background-color: #76a87d;
        }
        .signup-container {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .signup-container a {
            text-decoration: none;
            color: #76a87d;
            font-weight: bold;
        }
        .signup-container a:hover {
            color: #32573b;
        }
    </style>
</head>
<body>
<div class="header">
        <div><img src="images/logo.png" alt="Logo" height='100px'></div>
        <div><h1>NutriMe</h1></div>
        <div></div>  
    </div>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST">
            <label for="id">ID</label>
            <input type="text" id="id" name="id" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <div class="signup-container">
            <p>New user? <a href="signup.php">Sign up here</a></p>
        </div>
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

            // Fetch login inputs
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = $_POST['id'];
                $password = $_POST['password'];

                // Determine ID type and appropriate query
                if (preg_match('/^BD\d+$/', $id)) { // Doctor ID
                    $stmt = $conn->prepare("SELECT * FROM doctor WHERE licensenumber = ? AND password = ?");
                    $stmt->bind_param("ss", $id, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $_SESSION['id'] = $id;
                        header("Location: doctor_dashboard.php");
                        exit;
                    } else {
                        echo "Invalid doctor ID or password.";
                    }

                } elseif (preg_match('/^\d{2}$/', $id)) { // Admin ID
                    $stmt = $conn->prepare("SELECT * FROM admin WHERE adminid = ? AND password = ?");
                    $stmt->bind_param("is", $id, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $_SESSION['id'] = $id;
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        echo "Invalid admin ID or password.";
                    }

                } elseif (preg_match('/^P\d+$/', $id)) { // Patient ID
                    $stmt = $conn->prepare("SELECT * FROM patient WHERE patientid = ? AND password = ?");
                    $stmt->bind_param("ss", $id, $password);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $_SESSION['id'] = $id;
                        header("Location: patient_dashboard.php");
                        exit;
                    } else {
                        echo "Invalid patient ID or password.";
                    }
                } else {
                    echo "Invalid ID format.";
                }

                $stmt->close();
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
