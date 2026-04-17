<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Items</title>
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
        .header h1 {
            margin: 0;
        }
        .container {
            text-align: center;
            margin: 20px auto;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            margin: 10px auto;
        }
        .card h3 {
            margin: 10px 0;
            font-size: 1.5rem;
            color: #32573b;
        }
        .card p {
            margin: 5px 0;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php
        session_start();
        $userId = $_SESSION['id'] ?? null; // Retrieve user ID from session
    ?>
    <div class="header">
        <div><img src="images/logo.png" alt="Logo" height='50px'></div>
        <div><h1>Welcome to NutriMe</h1></div>
        <div></div>  
    </div>

    <div class="container">
        <h2>Available Food Items</h2>
        <div class="card-container">
            <?php
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

            // Fetch food items data
            $sql = "SELECT * FROM fooditems";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = "images/food_pics/" . strtolower($row['name']) . ".png";
                    echo "<div class='card'>";
                    echo "<img src='$imagePath' alt='" . $row['name'] . "'>";
                    echo "<h3>" . $row['name'] . "</h3>";
                    echo "<p>Calories: " . $row['calories'] . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No food items found.</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
