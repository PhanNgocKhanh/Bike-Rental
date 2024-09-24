<?php
session_start();

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

// Get variables from the session
$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$error = '';

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

// Initialize variables
$bike_id = '';
$rent_location = '';
$description = '';
$hourly_cost = '';
$error = [];
$formSubmitted = false;

// Fetch available bike IDs
$availableBikes = [];
$sql = "SELECT bike.bike_id FROM $dbTable1 AS bike
        LEFT JOIN $dbTable2 AS transaction ON transaction.bike_id = bike.bike_id 
        AND transaction.rent_end_time IS NULL 
        WHERE transaction.bike_id IS NULL";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availableBikes[] = $row['bike_id'];
    }
}

// Handle form submission for editing the bike details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_edit'])) {
        // Check and assign variables
        if (empty($_POST['rent_location'])) {
            $error['rent_location'] = "Please fill in rent location!";
        } else {
            $rent_location = $_POST['rent_location'];
        }

        if (empty($_POST['description'])) {
            $error['description'] = "Please fill in description!";
        } else {
            $description = $_POST['description'];
        }

        if (empty($_POST['hourly_cost'])) {
            $error['hourly_cost'] = "Please fill in cost per hour!";
        } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $_POST['hourly_cost'])) {
            $error['hourly_cost'] = "Hourly cost must be a number with up to 2 decimal places.";
        } else {
            $hourly_cost = $_POST['hourly_cost'];
        }

        // Check if bike_id is selected
        if (empty($_POST['bike_id'])) {
            $error['bike_id'] = "Please select a bike ID!";
        } else {
            $bike_id = $_POST['bike_id'];
        }

        if (empty($error)) {
            // Update bike details
            $sql = "UPDATE $dbTable1 SET rent_location = '$rent_location', description = '$description', hourly_cost = $hourly_cost WHERE bike_id = '$bike_id'";
            if ($conn->query($sql) === TRUE) {
                $formSubmitted = true;
            } else {
                echo "Error updating bike details: " . $conn->error;
            }
        }
    }
}

// Handle bike ID selection
if (isset($_POST['select_bike'])) {
    $bike_id = $_POST['bike_id'];

    // Fetch bike details
    $sql = "SELECT * FROM $dbTable1 WHERE bike_id = '$bike_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $bike = $result->fetch_assoc();
        $rent_location = $bike['rent_location'];
        $description = $bike['description'];
        $hourly_cost = $bike['hourly_cost'];
    }
}

$conn->close();
?>

<html>
<head>
    <title>Edit Bike</title>
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

        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .button-container, .inline-buttons {
            display: flex;
            justify-content: center;
            gap: 20px; /* Space between buttons */
        }

        button {
            background-color: #007bff; /* Button color */
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100px;
        }

        button:hover {
            background-color: #0056b3; 
        }

        .success-message {
            text-align: left;
            margin: 20px auto;
            padding: 10px;
            border-radius: 4px;
            line-height: 1.9;
        }
    </style>
</head>
<body>

<?php if (!$formSubmitted): ?>
    <div class="container">
        <h1>Edit Bike</h1>
        <form method="POST">
            <label for="bike_id">Bike ID</label>
            <select id="bike_id" name="bike_id">
                <option value="">Select An Available Bike ID</option>
                <?php foreach ($availableBikes as $id): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>" <?php if ($id == $bike_id) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($id); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($error['bike_id'])): ?>
                <div class="error"><?php echo $error['bike_id']; ?></div>
            <?php endif; ?>

            <label for="rent_location">Rent Location</label>
            <input type="text" id="rent_location" name="rent_location" value="<?php echo htmlspecialchars($rent_location); ?>">
            <?php if (isset($error['rent_location'])): ?>
                <div class="error"><?php echo $error['rent_location']; ?></div>
            <?php endif; ?>

            <label for="description">Description</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($description); ?>">
            <?php if (isset($error['description'])): ?>
                <div class="error"><?php echo $error['description']; ?></div>
            <?php endif; ?>

            <label for="hourly_cost">Cost per hour</label>
            <input type="text" id="hourly_cost" name="hourly_cost" value="<?php echo htmlspecialchars($hourly_cost); ?>">
            <?php if (isset($error['hourly_cost'])): ?>
                <div class="error"><?php echo $error['hourly_cost']; ?></div>
            <?php endif; ?>

            <div class="button-container">
                <button type="submit" name="submit_edit">Submit</button>
            </div>
        </form>

        <div class="button-container">
            <form action="admin.php" method="POST" style="display:inline;">
                <button type="submit" name="back">Back</button>
            </form>
        </div>
    </div>

<?php else: ?>
    <div class="container">
        <h1>Success Update</h1>
        <div class="success-message">
            <p>Bike ID: <?php echo htmlspecialchars($bike_id); ?><br>
               Location: <?php echo htmlspecialchars($rent_location); ?><br>
               Description: <?php echo htmlspecialchars($description); ?><br>
               Cost per hour: $<?php echo number_format($hourly_cost, 2); ?><br></p>
        </div>

        <div class="inline-buttons">
            <form action="admin_edit bike.php" method="POST">
                <button type="submit" name="back">Back</button>
            </form>
            <form action="admin.php" method="POST">
                <button type="submit" name="home">Home</button>
            </form>
        </div>
    </div>
<?php endif; ?>

</body>
</html>












