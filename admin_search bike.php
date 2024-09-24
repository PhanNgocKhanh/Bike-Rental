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
			$search_result = "<div class='result-container'>
			<div class='result-header'>
				<div class='result-cell'><strong>Bike ID</strong></div>
				<div class='result-cell'><strong>Location</strong></div>
				<div class='result-cell'><strong>Description</strong></div>
				<div class='result-cell'><strong>Cost per Hour</strong></div>
			</div>";

            while ($row = $result->fetch_assoc()) {
                $search_result .= "<div class='result-row'>
				<div class='result-cell'>" . htmlspecialchars($row['bike_id']) . "</div>
				<div class='result-cell'>" . htmlspecialchars($row['rent_location']) . "</div>
				<div class='result-cell'>" . htmlspecialchars($row['description']) . "</div>
				<div class='result-cell'>" . htmlspecialchars($row['hourly_cost']) . "</div>
			   </div>";
            }

            $search_result .= "</div>";
		} else {
			$search_result = "<div class='result-container'>No bikes found matching the criteria.</div>";
		}
	
		$conn->close();
	}
?>

<html>
<head>
    <title>Search Bike</title>
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-container, .inline-buttons {
            display: flex;
            justify-content: center;
            gap: 20px; /* Space between buttons */
        }

        button, input[type="submit"] {
            background-color: #007bff; /* Button color */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100px;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #0056b3; 
        }

        .result-container {
            background-color: #A7C7E7; /* Pastel blue background */
            color: #ffffff;
            text-align: left;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .result-header, .result-row {
            display: flex;
            padding: 10px;
            border-bottom: 1px solid #ffffff;
        }

        .result-header {
            font-weight: bold;
            background-color: #007bff;
        }

        .result-cell {
            flex: 1;
            padding: 5px;
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

            <div class="button-container">
                <input type="submit" name="submit" value="Search">
            </div>
        </form>

        <div class="button-container">
            <form method="POST" action="admin.php" style="display:inline;">
                <button type="submit">Back</button>
            </form>
        </div>

        <?php if (!empty($search_result)): ?>
        <div class="result-container">
            <?php echo $search_result; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
