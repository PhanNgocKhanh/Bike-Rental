<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get variables from the session
	$user_id = $_SESSION['user_id'];
	
	// Retrieve bike_id from session
    if (isset($_SESSION['bike_id'])) {
        $bike_id = $_SESSION['bike_id'];
    } else {
        die("Error: Bike ID is unidentified!");
    }

    $formSubmitted = false;

	// Database connection
    $serverName = "localhost:3307";
    $userName = "root";
    $password = "";
    $dbName = "a2_db";
    $dbTable1 = "bike";
    $dbTable2 = "transaction";

    $conn = new mysqli($serverName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
    }

	// Retrieve bike details
    $sql_bike_details = "SELECT * FROM $dbTable1 WHERE bike_id = '$bike_id'";
    $result_bike_details = $conn->query($sql_bike_details);

    if ($result_bike_details) {
        $bike_details = $result_bike_details->fetch_assoc();

        if (!$bike_details) {
            die("Error: No bike details found for the given Bike ID.");
        }
    } else {
        die("Error: Could not retrieve bike details. " . $conn->error);
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rent_start_time'])) {
        $rent_start_time = $_POST['rent_start_time'];
        $hourly_cost = $bike_details['hourly_cost'];

        // Check if a rental already exists for this bike and user
        $sql_check_existing = "SELECT * FROM $dbTable2 WHERE bike_id = '$bike_id' AND user_id = '$user_id' AND rent_end_time IS NULL";
        $result_check_existing = $conn->query($sql_check_existing);

        if ($result_check_existing->num_rows > 0) {
            $message = "Error: You already have an active rental for this bike.";
        } else {
            // Insert new transaction
            $sql_insert = "INSERT INTO $dbTable2 (bike_id, user_id, rent_start_time) VALUES ('$bike_id', '$user_id', '$rent_start_time')";

            if ($conn->query($sql_insert) === TRUE) {
                $formSubmitted = true;
                $message = "Bike rented successfully!<br>";
                $message .= "Start Time: " . $rent_start_time . "<br>";
                $message .= "Cost per Hour: $" . number_format($hourly_cost, 2) . "<br>";
            } else {
                $message = "Error during renting process: " . $conn->error;
            }
        }
    }

	$conn->close();
?>

<html>
<head>
    <title>Rent Bike</title>
    <style>
        body {
            background-color: #ffffff; /* White background */
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            border: 2px solid #A7C7E7; /* Pastel blue border */
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9; /* Light grey background */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        h1 {
            color: #A7C7E7; /* Pastel blue heading */
            text-align: center;
            margin: 0 0 20px 0;
        }

        h2 {
            color: #007bff; /* Slightly darker blue for bike ID */
            text-align: center;
            margin: 0 0 20px 0;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="datetime-local"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff; /* Button color */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        button:hover {
            background-color: #0056b3; 
        }

        .button-container {
            text-align: center;
        }

        .button-container form {
            display: inline-block;
            margin: 0 10px;
        }

        .message {
            text-align: left;
            margin: 20px auto;
            padding: 10px;
            border-radius: 4px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rent Bike</h1>
        <h2>Bike ID: <?php echo htmlspecialchars($bike_id); ?></h2>

        <?php if (!$formSubmitted): ?>
        <form method="post" action="renter_rent bike.php?bike_id=<?php echo urlencode($bike_id); ?>">
            <label for="rent_start_time">Start Time:</label>
            <input type="datetime-local" id="rent_start_time" name="rent_start_time" required>
            <button type="submit" name="rent_bike">Rent</button>
        </form>
        <?php endif; ?>

        <?php
            if (isset($message)) {
                echo "<div class='message'>$message</div>";
            }
        ?>

        <div class="button-container">
            <form method="POST" action="renter_list avail bikes.php">
                <button type="submit" value="back">Back</button>
            </form>

            <form method="POST" action="renter.php">
                <button type="submit" value="back_home">Home</button>
            </form>
        </div>
    </div>
</body>
</html>
