<?php
// Start session
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customdiet";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve admin ID from session
$adminid = $_SESSION['id'] ?? null;

if (!$adminid) {
    // Redirect to login page if no admin is logged in
    header("Location: login.php");
    exit;
}

// Fetch admin information
$query_admin = "SELECT * FROM admin WHERE adminid = '$adminid'";
$result_admin = mysqli_query($conn, $query_admin);
if (!$result_admin || mysqli_num_rows($result_admin) == 0) {
    die("Invalid admin session. Please log in again.");
}
$admin_info = mysqli_fetch_assoc($result_admin);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        .button-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
            flex-wrap: wrap;
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
    <div class="header">
        <div><img src="images/logo.png" alt="" height='50px'></div>
        <h1>Welcome To NutriMe</h1>
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </div>
    <div class="container">
        <br><br><br><br>
        <div class="card">
            <h2>Welcome, Admin ID: <?php echo htmlspecialchars($admin_info['adminid']); ?></h2>
        </div>
        <br><br><br><br>
        <div class="button-row">
            <a href="manage_doctors.php" class="dashboard-button">
                <img height="60" src="images/doctor.png" alt=""><br>Manage Doctors
            </a>
            <a href="manage_patients.php" class="dashboard-button">
                <img height="60" src="images/patient.png" alt=""><br>Manage Patients
            </a>
            
            
            <a href="view_reviews.php" class="dashboard-button">
                <img height="60" src="images/reviews.png" alt=""><br>View Reviews
            </a>
        </div>
    </div>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?>
