<?php
session_start();
$patientId = $_SESSION['id'] ?? null; // Retrieve patient ID from session

if (!$patientId) {
    $error = "Error: No patient logged in.";
} else {
    // Database connection settings
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "customdiet";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch diet ID and license number from the appointment table
    $appointmentSql = "SELECT dietid, licensenumber FROM appointment WHERE patientid = '$patientId' AND status = 'Confirmed' AND dietid IS NOT NULL";
    $appointmentResult = $conn->query($appointmentSql);
    $appointmentData = $appointmentResult->fetch_assoc();

    if (!$appointmentData) {
        $error = "No diet chart suggested for this patient.";
    } else {
        // Fetch doctor name
        $doctorId = $appointmentData['licensenumber'];
        $doctorSql = "SELECT name FROM doctor WHERE licensenumber = '$doctorId'";
        $doctorResult = $conn->query($doctorSql);
        $doctorData = $doctorResult->fetch_assoc();

        // Fetch diet details
        $dietId = $appointmentData['dietid'];
        $dietSql = "SELECT f.foodid, f.name, f.calories FROM includes i JOIN fooditems f ON i.foodid = f.foodid WHERE i.dietid = '$dietId'";
        $dietResult = $conn->query($dietSql);

        // Handle review submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
            $comments = $conn->real_escape_string($_POST['comments']);
            $rating = (int)$_POST['rating'];

            if ($rating >= 1 && $rating <= 5 && !empty($comments)) {
                // Check if the review already exists
                $existingReviewSql = "SELECT * FROM reviews WHERE patientid = '$patientId' AND licensenumber = '$doctorId'";
                $existingReviewResult = $conn->query($existingReviewSql);

                if ($existingReviewResult && $existingReviewResult->num_rows > 0) {
                    // Update the existing review
                    $updateReviewSql = "UPDATE reviews SET comments = '$comments', rating = '$rating' WHERE patientid = '$patientId' AND licensenumber = '$doctorId'";
                    $conn->query($updateReviewSql);
                    $successMessage = "Your review has been updated!";
                } else {
                    // Insert a new review
                    $reviewSql = "INSERT INTO reviews (patientid, licensenumber, comments, rating) VALUES ('$patientId', '$doctorId', '$comments', '$rating')";
                    $conn->query($reviewSql);
                    $successMessage = "Thank you for your review!";
                }
            } else {
                $errorMessage = "Please provide a valid rating between 1 and 5 and a comment.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Diet Chart</title>
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
        .container {
            text-align: center;
            margin: 20px auto;
        }
        .error, .success {
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            font-size: 1.2rem;
        }
        .error {
            background: #d9534f;
            color: white;
        }
        .success {
            background: #5cb85c;
            color: white;
        }
        .card {
            background: linear-gradient(352deg, rgba(82,127,93,1) 0%, rgba(118,168,125,1) 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
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
            background: white;
            color: black;
        }
        .card th, .card td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        .card th {
            background-color: #32573b;
            color: white;
        }
        .review-form {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .review-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .review-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .review-form button {
            background-color: #32673f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .review-form button:hover {
            background-color: #244e2c;
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
        <?php if (isset($error)) { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } else { ?>
            <div class="card">
                <h2>Doctor Information</h2>
                <table>
                    <tr><th>Doctor Name</th><td><?php echo $doctorData['name'] ?? 'N/A'; ?></td></tr>
                </table>
            </div>

            <div class="card">
                <h2>Diet Chart</h2>
                <table>
                    <tr>
                        <th>Food ID</th>
                        <th>Food Name</th>
                        <th>Calories</th>
                    </tr>
                    <?php while ($row = $dietResult->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['foodid']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['calories']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>

            <div class="review-form">
                <h2>Leave a Review</h2>
                <?php if (isset($successMessage)) { ?>
                    <div class="success"><?php echo $successMessage; ?></div>
                <?php } elseif (isset($errorMessage)) { ?>
                    <div class="error"><?php echo $errorMessage; ?></div>
                <?php } ?>
                <form method="POST">
                    <textarea name="comments" placeholder="Write your review here..." rows="4" required></textarea>
                    <select name="rating" required>
                        <option value="">Rate the doctor</option>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                    <button type="submit" name="review">Submit Review</button>
                </form>
            </div>
        <?php } ?>
    </div>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
