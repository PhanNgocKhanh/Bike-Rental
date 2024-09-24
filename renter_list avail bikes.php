<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get variables from the session
	$user_name = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];
	$user_type = $_SESSION['user_type'];

	// Database connection
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

	// Check if a bike_id has been submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bike_id'])) {
        $_SESSION['bike_id'] = $_POST['bike_id'];
        header("Location: renter_rent bike.php");
        exit();
    }

	$sql = "SELECT bike.bike_id, bike.rent_location, bike.description, bike.hourly_cost
            FROM $dbTable1 AS bike
            LEFT JOIN $dbTable2 AS transaction ON bike.bike_id = transaction.bike_id
            AND transaction.rent_end_time IS NULL
            WHERE transaction.bike_id IS NULL";
	$result = $conn->query($sql);
?>

<html>
<head>
    <title>Available Bikes</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #A7C7E7; /* Pastel blue color */
            margin: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            border: 2px solid #A7C7E7; /* Pastel blue border */
            border-radius: 8px;
        }
        th, td {
            border: 1px solid #A7C7E7; /* Pastel blue border */
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #007bff; /* Pastel blue header */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Light grey for alternating rows */
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3; 
        }
		td .button-container {
            text-align: center; /* Center the button */
        }
    </style>
</head>

<body>
    <h1>Available Bikes</h1>

    <?php
        // Display result
        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>Bike ID</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Hourly Cost</th>
                    <th>Action</th>
                </tr>";

            while ($r = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $r['bike_id'] . "</td>
                    <td>" . $r['rent_location'] . "</td>
                    <td>" . $r['description'] . "</td>
                    <td>" . $r['hourly_cost'] . "</td>
                    <td>
                        <div class='button-container'>
                            <form method='POST'>
                                <input type='hidden' name='bike_id' value='" . $r['bike_id'] . "'>
                                <button type='submit' name='rent'> Rent </button>
                            </form>
                        </div>
                    </td>
                </tr>";
            }

            echo "</table>";
        } else {
            echo "<p>No available bikes</p>";
        }

        $conn->close();
    ?>

    <form method="POST" action="renter.php">
        <button type="submit">Back</button>
    </form>
</body>
</html>