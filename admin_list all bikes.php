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
	$dbTable = "bike";

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	if($conn->connect_error){
		die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
	}

	$sql = "SELECT * FROM $dbTable";
             
	$result = $conn->query($sql);
?>

<html>
<head>
    <title>All Bikes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #A7C7E7; /* Pastel blue color */
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #A7C7E7; /* Pastel blue color */
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>Bike List</h1>

    <?php
        // Display result
        if ($result->num_rows > 0) {
            echo "<table>
            <tr>
                <th>Bike ID</th>
                <th>Location</th>
                <th>Description</th>
                <th>Hourly Cost</th>
            </tr>";

            while ($r = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $r['bike_id'] . "</td>
                    <td>" . $r['rent_location'] . "</td>
                    <td>" . $r['description'] . "</td>
                    <td>" . $r['hourly_cost'] . "</td>
                </tr>";
            }

            echo "</table>";
        } else {
            echo "<p style='text-align: center;'>No bike listing</p>";
        }

        $conn->close();
    ?>

    <div class="button-container">
        <form method="POST" action="admin.php">
            <button type="submit">Back</button>
        </form>
    </div>
</body>
</html>
