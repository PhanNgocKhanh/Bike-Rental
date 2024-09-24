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
	$dbTable = "transaction";

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	if($conn->connect_error){
		die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
	}

	// sql query to list the past renting
	$sql = "SELECT transaction.transaction_id, transaction.bike_id, transaction.rent_start_time, transaction.rent_end_time, transaction.total_cost
            FROM $dbTable
			WHERE transaction.user_id = '$user_id' AND transaction.rent_end_time IS NOT NULL";
	$result = $conn->query($sql);		
?>

<html>
<head>
    <title>Past Renting</title>
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
            margin: 20px auto; /* Center the button and add space */
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Past Renting Bikes</h1>

        <?php
            // Display result
            if ($result->num_rows > 0) {
                echo "<table>
                    <tr>
                        <th>Transaction ID</th>    
                        <th>Bike ID</th>
                        <th>Rent Start Time</th>
                        <th>Rent End Time</th>
                        <th>Total Cost</th>
                    </tr>";

                while ($r = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($r['transaction_id']) . "</td>
                        <td>" . htmlspecialchars($r['bike_id']) . "</td>
                        <td>" . htmlspecialchars($r['rent_start_time']) . "</td>
                        <td>" . htmlspecialchars($r['rent_end_time']) . "</td>
                        <td>" . htmlspecialchars($r['total_cost']) . "</td>
                    </tr>";
                }

                echo "</table>";
            } else {
                echo "<div class='message'>No past renting</div>";
            }

            $conn->close();
        ?>

        <button onclick="window.location.href='renter.php'">Back</button>
    </div>
</body>
</html>