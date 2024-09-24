<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get the variables from the session
	$user_id = $_SESSION['user_id'];

	// database connection
	$serverName = "localhost:3307";
	$userName = "root";
	$password = "";
	$dbName = "a2_db";
	$dbTable1 = "bike";
	$dbTable2 = "transaction";

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	if($conn->connect_error){
		die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
	}

	// Handle bike return
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bike_id'])) {
        $bike_id = $_POST['bike_id'];
        $rent_end_time = new DateTime(); // Current time
        $rent_end_time_formatted = $rent_end_time->format('Y-m-d H:i:s');

        // Fetch the rent start time and hourly cost
        $sql = "SELECT rent_start_time, hourly_cost 
                FROM $dbTable2 AS transaction
                LEFT JOIN $dbTable1 AS bike ON bike.bike_id = transaction.bike_id
                WHERE transaction.bike_id = '$bike_id' AND transaction.user_id = '$user_id' AND transaction.rent_end_time IS NULL";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $r = $result->fetch_assoc();
            $rent_start_time = new DateTime($r['rent_start_time']);
            $hourly_cost = $r['hourly_cost'];

            // Calculate the total cost
            $interval = $rent_start_time->diff($rent_end_time);
            $hours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
            $total_cost = $hours * $hourly_cost;

            // Update the transaction with the rent end time and total cost
            $update_sql = "UPDATE $dbTable2 SET rent_end_time = '$rent_end_time_formatted', total_cost = $total_cost 
                           WHERE bike_id = '$bike_id' AND user_id = '$user_id' AND rent_end_time IS NULL";
            if ($conn->query($update_sql) === TRUE) {
                $message = "Bike returned successfully!<br>";
                $message .= "Bike ID: " . $bike_id . "<br>";
                $message .= "End Time: " . $rent_end_time_formatted . "<br>";
                $message .= "Total Cost: $" . number_format($total_cost, 2) . "<br>";
            } else {
                $message = "Error during return process: " . $conn->error;
            }
        } else {
            $message = "Error in handling the bike return";
        }
    }

	// sql query to list the current renting
	$sql = "SELECT transaction.bike_id, transaction.rent_start_time, bike.hourly_cost
            FROM $dbTable2 AS transaction
            LEFT JOIN $dbTable1 AS bike ON bike.bike_id = transaction.bike_id
			WHERE transaction.user_id = '$user_id' AND transaction.rent_end_time IS NULL";
	$result = $conn->query($sql);		
?>

<html>
<head>
    <title>Return Bike</title>
    <style>
        body {
            background-color: #ffffff; 
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            border: 2px solid #A7C7E7; /* Pastel blue border */
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
        }

        h1 {
            color: #A7C7E7; /* Pastel blue color */
            text-align: center;
            margin: 0 0 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #A7C7E7; /* Pastel blue border */
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff; /* Pastel blue header */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2; /* Light grey for alternating rows */
        }

        td {
            background-color: #ffffff; 
            color: #333; 
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: auto;
            margin: 10px auto; /* Center the button and add space */
            display: block; /* Ensure block display to center with margins */
        }

        button:hover {
            background-color: #0056b3; 
        }

        .message {
            background-color: #007bff; /* Pastel blue background */
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px; /* Space between message and table */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Current Renting Bikes</h1>

        <?php
            // Display result
            if ($result->num_rows > 0) {
                echo "<table>
                    <tr>
                        <th>Bike ID</th>
                        <th>Rent Start Time</th>
                        <th>Hourly Cost</th>
                        <th>Action</th>
                    </tr>";

                while ($r = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($r['bike_id']) . "</td>
                        <td>" . htmlspecialchars($r['rent_start_time']) . "</td>
                        <td>" . htmlspecialchars($r['hourly_cost']) . "</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='bike_id' value='" . htmlspecialchars($r['bike_id']) . "'>
                                <button type='submit' name='return'>Return</button>
                            </form>
                        </td>
                    </tr>";
                }

                echo "</table>";
            } else {
                echo "<div class='message'>No current renting</div>";
            }

            if (isset($message)) {
                echo "<div class='message'>$message</div>";
            }

            $conn->close();
        ?>

        <button onclick="window.location.href='renter.php'">Back</button>
    </div>
</body>
</html>