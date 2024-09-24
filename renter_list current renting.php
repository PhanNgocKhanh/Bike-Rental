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

	// sql query to list the current renting
	$sql = "SELECT transaction.bike_id, transaction.rent_start_time, bike.hourly_cost, bike.rent_location, bike.description
            FROM $dbTable2 AS transaction
            LEFT JOIN $dbTable1 AS bike ON bike.bike_id = transaction.bike_id
			WHERE transaction.user_id = '$user_id' AND transaction.rent_end_time IS NULL";
	$result = $conn->query($sql);		
?>

<html>
<head>
    <title>Current Renting</title>
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
            border: 2px solid #A7C7E7;
            border-radius: 8px;
            padding: 20px;
            background-color: #f9f9f9; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            position: relative;
        }

        h1 {
            color: #A7C7E7;
            text-align: center;
            margin: 0 0 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd; 
        }

        th {
            background-color: #007bff; 
            color: #ffffff; 
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
            box-sizing: border-box;
            display: block; /* Ensure block display for centering */
            margin: 20px auto; /* Center the button and add margin */
        }

        button:hover {
            background-color: #0056b3; 
        }

        .button-container {
            text-align: center;
            margin-top: 50px;
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
                        <th>Location</th>
                        <th>Description</th>
                        <th>Rent Start Time</th>
                        <th>Hourly Cost</th>
                    </tr>";

                while ($r = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($r['bike_id']) . "</td>
                        <td>" . htmlspecialchars($r['rent_location']) . "</td>
                        <td>" . htmlspecialchars($r['description']) . "</td>
                        <td>" . htmlspecialchars($r['rent_start_time']) . "</td>
                        <td>" . htmlspecialchars($r['hourly_cost']) . "</td>
                    </tr>";
                }

                echo "</table>";
            } else {
                echo "<div class='message'>No current renting</div>";
            }

            $conn->close();
        ?>

        <div class="button-container">
            <form method="POST" action="renter.php">
                <button type="submit" value="back">Back</button>
            </form>
        </div>
    </div>
</body>
</html>