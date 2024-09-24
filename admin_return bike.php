<?php
session_start();

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

// Get the variables from the session
$user_id = $_SESSION['user_id'];

$formSubmitted = false;

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

// Fetch current renting details
$sql = "SELECT transaction.transaction_id, transaction.bike_id, transaction.user_id, 
               bike.rent_location, bike.description, transaction.rent_start_time, bike.hourly_cost
        FROM $dbTable2 AS transaction
        LEFT JOIN $dbTable1 AS bike ON bike.bike_id = transaction.bike_id
        WHERE transaction.rent_end_time IS NULL";
$result = $conn->query($sql);

// Handle bike return
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bike_id'])) {
    $bike_id = $_POST['bike_id'];
    $renter_id = $_POST['renter_id'];
    $rent_end_time = new DateTime(); // Current time
    $rent_end_time_formatted = $rent_end_time->format('Y-m-d H:i:s');

    // Loop through fetched data to find the right transaction
    while ($r = $result->fetch_assoc()) {
        if ($r['bike_id'] == $bike_id && $r['user_id'] == $renter_id) {
            $rent_start_time = new DateTime($r['rent_start_time']);
            $hourly_cost = $r['hourly_cost'];

            // Calculate the total cost
            $interval = $rent_start_time->diff($rent_end_time);
            $hours = $interval->h + ($interval->days * 24) + ($interval->i / 60);
            $total_cost = $hours * $hourly_cost;

            // Update the transaction with the rent end time and total cost
            $update_sql = "UPDATE $dbTable2 SET rent_end_time = '$rent_end_time_formatted', total_cost = $total_cost 
                           WHERE bike_id = '$bike_id' AND user_id = '$renter_id' AND rent_end_time IS NULL";
            if ($conn->query($update_sql) === TRUE) {
                $formSubmitted = true;
                $message = "Bike returned successfully!<br>";
                $message .= "Renter ID: " . htmlspecialchars($renter_id) . "<br>";
                $message .= "Bike ID: " . htmlspecialchars($bike_id) . "<br>";
                $message .= "End Time: " . htmlspecialchars($rent_end_time_formatted) . "<br>";
                $message .= "Total Cost: $" . number_format($total_cost, 2) . "<br>";
            } else {
                $message = "Error during return process: " . htmlspecialchars($conn->error);
            }
            break; // Exit the loop once the right transaction is found and processed
        }
    }

    if (!$formSubmitted) {
        $message = "Error in handling the bike return";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Current Renting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container form {
            display: inline-block;
            margin: 0 10px;
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
    <div class="container">
        <h1>Current Renting Bikes</h1>
        <?php if(!$formSubmitted): ?>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Bike ID</th>
                        <th>Renter ID</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Rent Start Time</th>
                        <th>Hourly Cost</th>
                        <th>Action</th>
                    </tr>

                    <?php 
                    // Reset the result pointer and loop again to display the data
                    $result->data_seek(0);
                    while ($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['transaction_id']) ?></td>
                            <td><?= htmlspecialchars($r['bike_id']) ?></td>
                            <td><?= htmlspecialchars($r['user_id']) ?></td>
                            <td><?= htmlspecialchars($r['rent_location']) ?></td>
                            <td><?= htmlspecialchars($r['description']) ?></td>
                            <td><?= htmlspecialchars($r['rent_start_time']) ?></td>
                            <td><?= htmlspecialchars($r['hourly_cost']) ?></td>
                            <td>
                                <form method='POST'>
                                    <input type='hidden' name='bike_id' value='<?= htmlspecialchars($r['bike_id']) ?>'>
                                    <input type='hidden' name='renter_id' value='<?= htmlspecialchars($r['user_id']) ?>'>
                                    <button type='submit' name='select_bike'>Return</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No current renting</p>
            <?php endif; ?>

            <div class="button-container">
                <form method="POST" action="admin.php">
                    <button type="submit">Back</button>
                </form>
            </div>
        <?php else: ?>
            <p class="success-message">
                <?php echo $message; ?>
            </p>
            <div class="button-container">
                <form method='POST' action='admin.php'><button type='submit'>Home</button></form>
                <form method='POST' action='admin_return bike.php'><button type='submit'>Back</button></form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
