<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get user id from the session
	$user_id = $_SESSION['user_id'];

	// Initialize variables to store form input
    $renter_id = '';
    $name = '';
    $surname = '';
    $phone = '';
    $email = '';

	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
		// Get the variables from the form
		$renter_id = $_POST['renter_id'] ?? '';
		$name = $_POST['name'] ?? '';
		$surname = $_POST['surname'] ?? '';
		$phone = $_POST['phone'] ?? '';
		$email = $_POST['email'] ?? '';
	
		// Database connection
		$serverName = "localhost:3307";
		$userName = "root";
		$password = "";
		$dbName = "a2_db";
		$dbTable = "user";
	
		$conn = new mysqli($serverName, $userName, $password, $dbName);
		if ($conn->connect_error) {
			die("Failed to connect to the server. Error: " . $conn->connect_error . "<br>");
		}
	
		// SQL query based on the input
		$sql = "SELECT * FROM $dbTable WHERE 1=1 AND type = 'renter'";
	
		if (!empty($renter_id)) {
			$sql .= " AND user_id LIKE '%$renter_id%'";
		}
		if (!empty($name)) {
			$sql .= " AND name LIKE '%$name%'";
		}
		if (!empty($surname)) {
			$sql .= " AND surname LIKE '%$surname%'";
		}
		if (!empty($phone)) {
			$sql .= " AND phone LIKE '%$phone%'";
		}
		if (!empty($email)) {
			$sql .= " AND email LIKE '%$email%'";
		}
	
		$result = $conn->query($sql);
	
		if ($result === false) {
			$search_result = "Error executing the query: " . $conn->error;
		} elseif ($result->num_rows > 0) {
			// Fetch results and store them in a variable to display later
            $search_result = "<div class='result-container'>
            <div class='result-header'>
                <div class='result-cell'><strong>Renter ID</strong></div>
                <div class='result-cell'><strong>Name</strong></div>
                <div class='result-cell'><strong>Surname</strong></div>
                <div class='result-cell'><strong>Phone</strong></div>
                <div class='result-cell'><strong>Email</strong></div>
            </div>";

            while ($r = $result->fetch_assoc()) {
                $search_result .= "<div class='result-row'>
                <div class='result-cell'>" . htmlspecialchars($r['user_id']) . "</div>
                <div class='result-cell'>" . htmlspecialchars($r['name']) . "</div>
                <div class='result-cell'>" . htmlspecialchars($r['surname']) . "</div>
                <div class='result-cell'>" . htmlspecialchars($r['phone']) . "</div>
                <div class='result-cell'>" . htmlspecialchars($r['email']) . "</div>
                </div>";
            }

            $search_result .= "</div>";
		} else {
			$search_result = "<div class='result-container'>No renters found matching the criteria.</div>";
		}
	
		$conn->close();
	}
?>

<html>
<head>
    <title>Search Renter</title>
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

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-container {
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
    overflow-x: auto; /* Add horizontal scroll if needed */
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
    text-align: center; /* Center-align text in columns */
}

.result-cell {
    min-width: 100px; 
}
    </style>
</head>

<body>
    <div class="container">
        <h1>Renter Search</h1>
        <form method="POST">
            <label for="renter_id">Renter ID:</label>
            <input type="text" id="renter_id" name="renter_id" value="<?php echo htmlspecialchars($renter_id); ?>">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">

            <label for="surname">Surname:</label>
            <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>">

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

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