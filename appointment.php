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

// Fetch patient ID from session or request
session_start();
$patientid = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : (isset($_GET['patient_id']) ? $_GET['patient_id'] : '');

if (empty($patientid)) {
    die("Error: No patient ID found. Please log in again.");
}

// Fetch patient information
$query_patient = "SELECT * FROM patient WHERE patientid = '$patientid'";
$result_patient = mysqli_query($conn, $query_patient);

if (!$result_patient || mysqli_num_rows($result_patient) == 0) {
    die("Error: No patient data found.");
}

$patient_info = mysqli_fetch_assoc($result_patient);

// Fetch all symptoms
$query_symptoms = "SELECT * FROM symptoms";
$result_symptoms = mysqli_query($conn, $query_symptoms);

if (!$result_symptoms) {
    die("Error: Failed to fetch symptoms data. " . mysqli_error($conn));
}

$symptoms_available = mysqli_num_rows($result_symptoms) > 0;

// Fetch all doctors
$query_doctors = "SELECT * FROM doctor";
$result_doctors = mysqli_query($conn, $query_doctors);

if (!$result_doctors) {
    die("Error: Failed to fetch doctor data.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $symptoms = isset($_POST['symptoms']) ? $_POST['symptoms'] : [];
    $doctor = isset($_POST['doctor']) ? $_POST['doctor'] : '';

    if (!empty($doctor) && !empty($symptoms)) {
        // Determine patient fee based on doctor's seniority
        $query_doctor_seniority = "SELECT seniorkey FROM doctor WHERE licensenumber = '$doctor'";
        $result_doctor_seniority = mysqli_query($conn, $query_doctor_seniority);
        $doctor_info = mysqli_fetch_assoc($result_doctor_seniority);
        $fees = ($doctor_info['seniorkey'] == 1) ? 2000.00 : 1000.00;

        // Generate appointment ID based on the latest appointment
        $latest_appointment_query = "SELECT appointmentid FROM appointment ORDER BY appointmentid DESC LIMIT 1";
        $latest_appointment_result = mysqli_query($conn, $latest_appointment_query);
        $latest_appointment_id = "APT000"; // Default if no appointments exist

        if ($latest_appointment_result && mysqli_num_rows($latest_appointment_result) > 0) {
            $latest_row = mysqli_fetch_assoc($latest_appointment_result);
            $latest_appointment_id = $latest_row['appointmentid'];
        }

        // Increment appointment ID
        $numeric_part = (int)substr($latest_appointment_id, 3) + 1;
        $appointment_id = "APT" . str_pad($numeric_part, 3, "0", STR_PAD_LEFT);

        // Create a new appointment
        $insert_appointment = "INSERT INTO appointment (appointmentid, patientid, licensenumber, status, fees) VALUES ('$appointment_id', '$patientid', '$doctor', 'Pending', '$fees')";

        if (!mysqli_query($conn, $insert_appointment)) {
            die("Error: Failed to create appointment. " . mysqli_error($conn));
        }

        // Insert into patientsymptoms table
        foreach ($symptoms as $symptom_id) {
            $insert_symptom = "INSERT INTO patientsymptoms (patientid, symptom_id) VALUES ('$patientid', '$symptom_id')";
            mysqli_query($conn, $insert_symptom);
        }

        // Redirect back to dashboard
        header('Location: patient_dashboard.php');
        exit;
    } else {
        echo "<p style='color:red;text-align:center;'>Please select at least one symptom and a doctor.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Appointment</title>
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
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
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
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .section-box {
            background: white;
            border: 2px solid #32673f;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
            text-align: center;
        }
        .section-box h2 {
            background-color: #32673f;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .symptoms-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .symptoms-container label {
            flex: 1 1 calc(20% - 10px);
            text-align: left;
        }
        .symptoms-container input[type="checkbox"] {
            transform: scale(1.2);
            accent-color: #32673f;
        }
        .radio-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }
        .radio-container input[type="radio"] {
            transform: scale(1.2);
            accent-color: #32673f;
        }
        button[type="submit"] {
            background: #32673f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
        }
        button[type="submit"]:hover {
            background: #2a5233;
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
        <h2>Welcome, <?php echo htmlspecialchars($patient_info['Patient_name'] ?? 'N/A'); ?> (Patient ID: <?php echo htmlspecialchars($patientid ?? 'N/A'); ?>)</h2>

        <div class="section-box">
            <h2>Patient Information</h2>
            <p>Age: <?php echo htmlspecialchars($patient_info['age'] ?? 'N/A'); ?> years</p>
            <p>Height: <?php echo htmlspecialchars($patient_info['height_m'] ?? 'N/A'); ?> m</p>
            <p>Weight: <?php echo htmlspecialchars($patient_info['weight_kg'] ?? 'N/A'); ?> kg</p>
        </div>

        <form action="" method="POST">
            <div class="section-box">
                <h2>Select Symptoms</h2>
                <div class="symptoms-container">
                    <?php if ($symptoms_available) { ?>
                        <?php while ($symptom = mysqli_fetch_assoc($result_symptoms)) { ?>
                            <label>
                                <input type="checkbox" name="symptoms[]" value="<?php echo $symptom['symptom_id']; ?>"> <?php echo htmlspecialchars($symptom['symptom']); ?>
                            </label>
                        <?php } ?>
                    <?php } else { ?>
                        <p>No symptoms available.</p>
                    <?php } ?>
                </div>
            </div>

            <div class="section-box">
                <h2>Choose a Doctor</h2>
                <div class="radio-container">
                    <?php if (mysqli_num_rows($result_doctors) > 0) {
                        while ($row = mysqli_fetch_assoc($result_doctors)) { ?>
                            <label>
                                <input type="radio" name="doctor" value="<?php echo htmlspecialchars($row['licensenumber']); ?>"> 
                                <?php echo htmlspecialchars($row['name']); ?> (<?php echo htmlspecialchars($row['specialization']); ?>)
                            </label>
                        <?php }
                    } else { ?>
                        <p>No doctors available.</p>
                    <?php } ?>
                </div>
            </div>

            <button type="submit">Take Appointment</button>
        </form>
    </div>
</body>
</html>
<?php
// Close database connection
mysqli_close($conn);
?>
