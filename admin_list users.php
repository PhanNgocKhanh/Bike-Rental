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
	$dbTable = "user";

	$conn = new mysqli($serverName, $userName, $password, $dbName);
	if($conn->connect_error){
		die("Fail to connect server. Error: " . $conn->connect_error . "<br>");
	}

	$sql = "SELECT * FROM $dbTable WHERE type = 'renter'";
             
	$result = $conn->query($sql);
?>

<html>
<head>
    <title>All Renters</title>
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
	    <h1>Renter List</h1>

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
			    </tr>";

	            while ($r = $result->fetch_assoc()) {
		            echo "<tr>
				        <td>" . $r['user_id'] . "</td>
				        <td>" . $r['name'] . "</td>
				        <td>" . $r['surname'] . "</td>
				        <td>" . $r['phone'] . "</td>
						<td>" . $r['email'] . "</td>
			        </tr>";
	            }

	            echo "</table>";
            } else {
	            echo "No renter <br>";
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