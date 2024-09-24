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
	$dbTable1 = "user";
	$dbTable2 = "transaction";

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	if($conn->connect_error){
		die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
	}

	$sql = "SELECT user.user_id, user.name, user.surname, user.phone, user.email,
                   GROUP_CONCAT(transaction.transaction_id ORDER BY transaction.transaction_id ASC SEPARATOR ', ') AS transaction_ids,
                   GROUP_CONCAT(transaction.bike_id ORDER BY transaction.bike_id ASC SEPARATOR ', ') AS bike_ids,
                   GROUP_CONCAT(transaction.rent_start_time ORDER BY transaction.rent_start_time ASC SEPARATOR ', ') AS rent_start_times
            FROM $dbTable1 AS user
            INNER JOIN $dbTable2 AS transaction ON user.user_id = transaction.user_id
            WHERE transaction.rent_end_time IS NULL
            GROUP BY user.user_id, user.name, user.surname, user.phone, user.email";
			
	$result = $conn->query($sql);
?>

<html>
    <head>
	    <title> Current Renting Renters </title>
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
	    <h1>Current Renting Renter List</h1>

        <?php
            // Display result
            if ($result->num_rows > 0) {
	            echo "<table border='1'>
			    <tr>
				    <th>Renter ID</th>
				    <th>Name</th>
					<th>Surname</th>
				    <th>Phone</th>
				    <th>Email</th>
					<th>Transaction ID</th>
					<th>Bike ID</th>
					<th>Rent Start Time</th>
			    </tr>";

	            while ($r = $result->fetch_assoc()) {
		            echo "<tr>
				        <td>" . $r['user_id'] . "</td>
				        <td>" . $r['name'] . "</td>
				        <td>" . $r['surname'] . "</td>
				        <td>" . $r['phone'] . "</td>
						<td>" . $r['email'] . "</td>
						<td>" . $r['transaction_ids'] . "</td>
						<td>" . $r['bike_ids'] . "</td>
						<td>" . $r['rent_start_times'] . "</td>
			        </tr>";
	            }

	            echo "</table>";
            } else {
	            echo '<p style="text-align: center;">No currently renting renter! <br></p>';
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