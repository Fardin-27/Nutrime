<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Doctor List</title>
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
            background-color: white;
            color: #32573b;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .header .back-button:hover {
            background-color: #e0e0e0;
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
        .form-container input, .form-container button, .form-container label {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-container button {
            background-color: #32573b;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-container button:hover {
            background-color: #457d4a;
        }
        .radio-group {
            display: flex;
            justify-content: center;
            gap: 20px; /* Adds space between radio buttons */
        }
        .radio-item {
            display: flex;
            align-items: center;
            gap: 5px; /* Space between radio button and label */
        }
        .radio-item input[type="radio"] {
            transform: scale(1.5); /* Makes the radio buttons larger */
            accent-color: #32573b; /* Keeps the custom theme color */
        }
        .radio-item label {
            font-size: 1rem;
            font-weight: bold;
        }
        /* Add/Delete Button Styles */
        button[name="delete"] {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[name="delete"]:hover {
            background-color: #c9302c;
        }

        button[name="add_doctor"] {
            background-color: #32573b;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[name="add_doctor"]:hover {
            background-color: #457d4a;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Admin Panel</div>
        <a href="admin_dashboard.php" class="back-button">Back</a>
    </div>

    <div class="container">
        <h2>Manage Doctors</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Contact</th>
                    <th>Seniority</th>
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

                        // Delete related records
                        $sqlDeleteIncludes = "DELETE FROM includes WHERE dietid IN (SELECT dietid FROM dietchart WHERE licensenumber='$deleteId')";
                        $conn->query($sqlDeleteIncludes);

                        $sqlDeleteDietChart = "DELETE FROM dietchart WHERE licensenumber='$deleteId'";
                        $conn->query($sqlDeleteDietChart);

                        $sqlDeleteReviews = "DELETE FROM reviews WHERE licensenumber='$deleteId'";
                        $conn->query($sqlDeleteReviews);

                        $sqlDeleteAppointments = "DELETE FROM appointment WHERE licensenumber='$deleteId'";
                        $conn->query($sqlDeleteAppointments);

                        $sqlDeleteDoctor = "DELETE FROM doctor WHERE licensenumber='$deleteId'";
                        if ($conn->query($sqlDeleteDoctor) === TRUE) {
                            echo "<script>alert('Doctor deleted successfully');</script>";
                        } else {
                            echo "<script>alert('Error deleting doctor: " . $conn->error . "');</script>";
                        }
                    } elseif (isset($_POST['add_doctor'])) {
                        $licensenumber = $_POST['licensenumber'];
                        $name = $_POST['name'];
                        $password = $_POST['password'];
                        $specialization = $_POST['specialization'];
                        $phone_no = $_POST['phone_no'];
                        $seniorkey = isset($_POST['seniorkey']) ? $_POST['seniorkey'] : 0;

                        $sqlInsert = "INSERT INTO doctor (licensenumber, name, password, specialization, phone_no, seniorkey) 
                                      VALUES ('$licensenumber', '$name', '$password', '$specialization', '$phone_no', '$seniorkey')";
                        if ($conn->query($sqlInsert) === TRUE) {
                            echo "<script>alert('Doctor added successfully');</script>";
                        } else {
                            echo "<script>alert('Error adding doctor: " . $conn->error . "');</script>";
                        }
                    }
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
                        echo "<td>
                            <form method='POST' style='display:inline-block;'>
                                <button name='delete' value='" . $row['licensenumber'] . "'>Delete</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No doctors available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h3>Add New Doctor</h3>
        <form method="POST">
            <input type="text" name="licensenumber" placeholder="License Number" required>
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="password" placeholder="Password" required>
            <input type="text" name="specialization" placeholder="Specialization" required>
            <input type="text" name="phone_no" placeholder="Phone Number" required>
            <div class="radio-group">
                <div class="radio-item">
                    <input type="radio" name="seniorkey" value="1" id="senior">
                    <label for="senior">Senior</label>
                </div>
                <div class="radio-item">
                    <input type="radio" name="seniorkey" value="0" id="junior" checked>
                    <label for="junior">Junior</label>
                </div>
            </div>
            <button type="submit" name="add_doctor">Add Doctor</button>
        </form>
    </div>
</body>
</html>
