<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get user id from the session
	$user_id = $_SESSION['user_id'];

	// Initialize variables to store form input
    $bike_id = '';
    $rent_location = '';
    $description = '';
    $hourly_cost = '';
    $search_result = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
		// Get the variables from the form
		$bike_id = $_POST['bike_id'] ?? '';
		$rent_location = $_POST['rent_location'] ?? '';
		$description = $_POST['description'] ?? '';
		$hourly_cost = $_POST['hourly_cost'] ?? '';
	
		// Database connection
		$serverName = "localhost:3307";
		$userName = "root";
		$password = "";
		$dbName = "a2_db";
		$dbTable = "bike";
	
		$conn = new mysqli($serverName, $userName, $password, $dbName);
		if ($conn->connect_error) {
			die("Failed to connect to the server. Error: " . $conn->connect_error . "<br>");
		}
	
		// SQL query based on the input
		$sql = "SELECT * FROM $dbTable WHERE 1=1";
	
		if (!empty($bike_id)) {
			$sql .= " AND bike_id LIKE '%$bike_id%'";
		}
		if (!empty($rent_location)) {
			$sql .= " AND rent_location LIKE '%$rent_location%'";
		}
		if (!empty($description)) {
			$sql .= " AND description LIKE '%$description%'";
		}
		if (!empty($hourly_cost)) {
			$sql .= " AND hourly_cost <= '$hourly_cost'";
		}
	
		$result = $conn->query($sql);
	
		if ($result === false) {
			$search_result = "Error executing the query: " . $conn->error;
		} elseif ($result->num_rows > 0) {
			// Fetch results and store them in a variable to display later
			$search_result = "<table border='1'>
								<tr>
									<th>Bike ID</th>
									<th>Location</th>
									<th>Description</th>
									<th>Cost per Hour</th>
								</tr>";
	
			while ($row = $result->fetch_assoc()) {
				$search_result .= "<tr>
									<td>" . $row['bike_id'] . "</td>
									<td>" . $row['rent_location'] . "</td>
									<td>" . $row['description'] . "</td>
									<td>" . $row['hourly_cost'] . "</td>
								   </tr>";
			}
	
			$search_result .= "</table>";
		} else {
			$search_result = "No bikes found matching the criteria.";
		}
	
		$conn->close();
	}
?>

<html>
<head>
    <title>Search Bike</title>
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
        }

        h1 {
            color: #A7C7E7;
            text-align: center;
            margin: 0 0 20px 0;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px 0;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd; 
            border-radius: 4px;
        }

        input[type="submit"], button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #0056b3; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .message {
            background-color: #A7C7E7; /* Pastel blue background */
            color: #ffffff;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bike Search</h1>
        <form method="POST">
            <label for="bike_id">Bike ID:</label>
            <input type="text" id="bike_id" name="bike_id" value="<?php echo htmlspecialchars($bike_id); ?>">

            <label for="rent_location">Location:</label>
            <input type="text" id="rent_location" name="rent_location" value="<?php echo htmlspecialchars($rent_location); ?>">

            <label for="description">Description:</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>">

            <label for="hourly_cost">Cost per Hour:</label>
            <input type="text" id="hourly_cost" name="hourly_cost" value="<?php echo htmlspecialchars($hourly_cost); ?>">

            <input type="submit" name="submit" value="Search">
        </form>

        <form method="POST" action="renter.php">
            <button type="submit" value="back">Back</button>
        </form>

        <?php
            // Display search results if any
            if (!empty($search_result)) {
                echo "<div class='message'>$search_result</div>";
            }
        ?>
    </div>
</body>
</html>