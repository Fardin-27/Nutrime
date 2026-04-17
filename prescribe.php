<?php
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "customdiet";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get appointment ID from URL
$appointmentId = $_GET['appointmentid'] ?? null;
if (!$appointmentId) {
    echo "<h2>Error: No appointment ID provided.</h2>";
    exit;
}

// Fetch appointment details
$appointmentSql = "SELECT * FROM appointment WHERE appointmentid = '$appointmentId'";
$appointmentResult = $conn->query($appointmentSql);
$appointmentData = $appointmentResult ? $appointmentResult->fetch_assoc() : null;

if (!$appointmentData) {
    echo "<h2>Error: Appointment information not found.</h2>";
    exit;
}

// Fetch patient details based on appointment
$patientId = $appointmentData['patientid'];
$patientSql = "SELECT * FROM patient WHERE patientid = '$patientId'";
$patientResult = $conn->query($patientSql);
$patientInfo = $patientResult ? $patientResult->fetch_assoc() : null;

if (!$patientInfo) {
    echo "<h2>Error: Patient information not found.</h2>";
    exit;
}

// Fetch symptoms with their names
$symptomSql = "SELECT s.symptom AS symptom_name FROM patientsymptoms ps JOIN symptoms s ON ps.symptom_id = s.symptom_id WHERE ps.patientid = '$patientId'";
$symptomResult = $conn->query($symptomSql);
$symptoms = [];
while ($row = $symptomResult->fetch_assoc()) {
    $symptoms[] = $row['symptom_name'];
}

// Fetch food items
$foodSql = "SELECT * FROM fooditems";
$foodResult = $conn->query($foodSql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedFoods = $_POST['foods'] ?? [];

    if (!empty($selectedFoods)) {
        // Generate a unique dietid
        $dietId = uniqid(); // Generate a unique dietid using PHP's uniqid() function

        // Insert into dietchart
        $dietChartSql = "INSERT INTO dietchart (dietid, licensenumber) VALUES ('$dietId', '" . $appointmentData['licensenumber'] . "')";
        if ($conn->query($dietChartSql) === FALSE) {
            die("Error inserting into dietchart: " . $conn->error);
        }

        // Insert selected foods into includes
        foreach ($selectedFoods as $foodId) {
            $conn->query("INSERT INTO includes (dietid, foodid) VALUES ('$dietId', '$foodId')");
        }

        // Update appointment table
        $conn->query("UPDATE appointment SET dietid = '$dietId', status = 'Confirmed' WHERE appointmentid = '$appointmentId'");

        // Redirect to success or dashboard page
        header("Location: doctor_dashboard.php");
        exit;
    } else {
        $errorMessage = "Please select at least one food item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Diet Chart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .symptoms {
            padding: 10px;
            background: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            transition: transform 0.2s ease;
            position: relative;
        }
        .card img {
            width: 50px;
            height: 50px;
            margin-bottom: 5px;
        }
        .add-button {
            background-color: #32673f;
            color: white;
            border: none;
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .add-button:hover {
            background-color: #244e2c;
        }
        .submit-button {
            background-color: #32673f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Create Diet Chart</h1>
    </div>

    <div class="container">
        <?php if (isset($errorMessage)) { echo "<p style='color:red;'>$errorMessage</p>"; } ?>

        <h2>Patient Information</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($patientInfo['Patient_name'] ?? 'N/A'); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($patientInfo['age'] ?? 'N/A'); ?> years</p>
        <p><strong>Height:</strong> <?php echo htmlspecialchars($patientInfo['height_m'] ?? 'N/A'); ?> m</p>
        <p><strong>Weight:</strong> <?php echo htmlspecialchars($patientInfo['weight_kg'] ?? 'N/A'); ?> kg</p>
        <div class="symptoms">
            <strong>Symptoms:</strong> <?php echo implode(', ', $symptoms); ?>
        </div>

        <h2>Select Food Items</h2>
        <form method="POST">
            <div class="card-container">
                <?php while ($food = $foodResult->fetch_assoc()) { ?>
                    <div class="card">
                        <img src="images/food_pics/<?php echo strtolower($food['name']); ?>.png" alt="<?php echo htmlspecialchars($food['name']); ?>">
                        <p><?php echo htmlspecialchars($food['name']); ?></p>
                        <p><?php echo htmlspecialchars($food['calories']); ?> calories</p>
                        <button type="button" class="add-button" onclick="toggleFood(this, '<?php echo $food['foodid']; ?>')">Add</button>
                        <input type="checkbox" name="foods[]" value="<?php echo $food['foodid']; ?>" hidden>
                    </div>
                <?php } ?>
            </div>
            <button type="submit" class="submit-button">Submit Diet Chart</button>
        </form>
    </div>

    <script>
        function toggleFood(button, foodId) {
            const card = button.closest('.card');
            const checkbox = card.querySelector('input[type="checkbox"]');

            if (card.classList.contains('selected')) {
                card.classList.remove('selected');
                checkbox.checked = false;
                button.textContent = 'Add';
            } else {
                card.classList.add('selected');
                checkbox.checked = true;
                button.textContent = 'Remove';
            }
        }
    </script>
</body>
</html>
