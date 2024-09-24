<?php
session_start();
date_default_timezone_set('Asia/Singapore');

// Database connection
$serverName = "localhost:3307";
$userName = "root";
$password = "";
$dbName = "a2_db";
$dbTable1 = "user";
$dbTable2 = "bike";
$dbTable3 = "transaction";

$conn = new mysqli($serverName, $userName, $password, $dbName);
if ($conn->connect_error) {
    die("Failed to connect to the server. Error: " . $conn->connect_error . "<br>");
}

// Step 1: Display all renters with "Select" button
if (!isset($_POST['select_renter']) && !isset($_POST['select_bike']) && !isset($_POST['submit_rent'])) {
    $sql = "SELECT * FROM $dbTable1 WHERE type = 'renter'";
    $result = $conn->query($sql);
    ?>

    <html>
    <head>
        <title>All Renters</title>
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

            button {
                background-color: #007bff;
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
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Renter List</h1>
        <?php
        if ($result->num_rows > 0) {
            echo "<table>
                <tr>
                    <th>Renter ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>";

            while ($r = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$r['user_id']}</td>
                    <td>{$r['name']}</td>
                    <td>{$r['surname']}</td>
                    <td>{$r['phone']}</td>
                    <td>{$r['email']}</td>
                    <td>
                        <form method='POST'>
                            <input type='hidden' name='renter_id' value='{$r['user_id']}'>
                            <button type='submit' name='select_renter'>Select</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No renters found!</p>";
        }
        ?>

        <div class="button-container">
            <form method="POST" action="admin.php">
                <button type="submit">Back</button>
            </form>
        </div>
    </div>
    </body>
    </html>

    <?php
}

// Step 2: Display available bikes for the selected renter
if (isset($_POST['select_renter'])) {
    $renter_id = $_POST['renter_id'];
    $_SESSION['renter_id'] = $renter_id; // Store renter_id in session

    $sql = "SELECT * FROM $dbTable2 WHERE bike_id NOT IN (SELECT bike_id FROM $dbTable3 WHERE rent_end_time IS NULL)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        ?>
        <html>
        <head>
            <title>Select Bike</title>
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

                h1, h2 {
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

                button {
                    background-color: #007bff;
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
            </style>
        </head>
        <body>
        <div class="container">
            <h2>Select Bike for Renter ID: <?php echo htmlspecialchars($renter_id); ?></h2>
            <?php
            if ($result->num_rows > 0) {
                echo "<table>
                    <tr>
                        <th>Bike ID</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Hourly Cost</th>
                        <th>Action</th>
                    </tr>";

                while ($r = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$r['bike_id']}</td>
                        <td>{$r['description']}</td>
                        <td>{$r['rent_location']}</td>
                        <td>{$r['hourly_cost']}</td>
                        <td>
                            <form method='POST'>
                                <input type='hidden' name='bike_id' value='{$r['bike_id']}'>
                                <input type='hidden' name='hourly_cost' value='{$r['hourly_cost']}'>
                                <button type='submit' name='select_bike'>Rent</button>
                            </form>
                        </td>
                    </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No bikes available for rent!</p>";
            }
            ?>

            <div class="button-container">
                <form method="POST" action="admin_rent bike.php">
                    <button type="submit">Back</button>
                </form>
            </div>
        </div>
        </body>
        </html>
        <?php
    }
}

// Step 3: Rent form submission
if (isset($_POST['select_bike'])) {
    $renter_id = $_SESSION['renter_id'];
    $bike_id = $_POST['bike_id'];
    $hourly_cost = $_POST['hourly_cost'];
    ?>

    <html>
    <head>
        <title>Rent Form</title>
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

            h2 {
                color: #A7C7E7;
                text-align: center;
                margin: 0 0 20px 0;
            }

            form {
                text-align: center;
            }

            .button-container {
                text-align: center;
                margin-top: 20px;
            }

            button {
                background-color: #007bff;
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
        </style>
    </head>
    <body>
    <div class="container">
        <h2>Rent Form</h2>
        <form method="POST">
            <p>Renter ID: <?php echo htmlspecialchars($renter_id); ?></p>
            <p>Bike ID: <?php echo htmlspecialchars($bike_id); ?></p>
            <p>Hourly Cost: <?php echo htmlspecialchars($hourly_cost); ?></p>
            <label for="rent_start_time">Rent Start Time:</label>
            <input type="datetime-local" name="rent_start_time" required>
            <input type="hidden" name="renter_id" value="<?php echo htmlspecialchars($renter_id); ?>">
            <input type="hidden" name="bike_id" value="<?php echo htmlspecialchars($bike_id); ?>">
            <input type="hidden" name="hourly_cost" value="<?php echo htmlspecialchars($hourly_cost); ?>">
            <br><br>
            <button type="submit" name="submit_rent">Submit</button>
        </form>

        <div class="button-container">
            <form method="POST" action="admin_rent bike.php">
                <button type="submit">Cancel</button>
            </form>
        </div>
    </div>
    </body>
    </html>

    <?php
}

// Step 4: Handle rent submission
if (isset($_POST['submit_rent'])) {
    $renter_id = $_POST['renter_id'];
    $bike_id = $_POST['bike_id'];
    $rent_start_time = $_POST['rent_start_time'];
    $hourly_cost = $_POST['hourly_cost'];

    // Define SQL query
    $sql = "INSERT INTO $dbTable3 (bike_id, user_id, rent_start_time)
            VALUES ('$bike_id', '$renter_id', '$rent_start_time')";

    if ($conn->query($sql) === TRUE) {
        $transaction_id = $conn->insert_id;
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title>Rent Submission Result</title>
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

                h2 {
                    color: #A7C7E7;
                    text-align: center;
                    margin: 0 0 20px 0;
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

                .error-message {
                    color: #ffffff;
                    background-color: #dc3545;
                    text-align: center;
                    padding: 10px;
                    border-radius: 4px;
                    margin: 20px auto;
                    width: fit-content;
                    max-width: 80%;
                }
            </style>
        </head>
        <body>
        <div class="container">
            <h2>Rent Transaction Status</h2>
            <p class="success-message" style="text-align: center;">Rent transaction successful!</p>
            <p>Transaction ID: <?php echo htmlspecialchars($transaction_id); ?></p>
            <p>Renter ID: <?php echo htmlspecialchars($renter_id); ?></p>
            <p>Bike ID: <?php echo htmlspecialchars($bike_id); ?></p>
            <p>Cost per hour: $<?php echo number_format($hourly_cost, 2); ?></p>
            <p>Rent Start Time: <?php echo htmlspecialchars($rent_start_time); ?></p>
            <div class="button-container">
                <form method='POST' action='admin.php'><button type='submit'>Home</button></form>
                <form method='POST' action='admin_rent bike.php'><button type='submit'>Back</button></form>
            </div>
        </div>
        </body>
        </html>

        <?php
    } else {
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <title>Rent Submission Error</title>
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

                h2 {
                    color: #A7C7E7;
                    text-align: center;
                    margin: 0 0 20px 0;
                }

                .button-container {
                    text-align: center;
                    margin-top: 20px;
                }

                button {
                    background-color: #007bff;
                    color: white;
                    padding: 10px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    width: 100px;
                    margin-top: 10px;
                }

                button:hover {
                    background-color: #0056b3;
                }

                .error-message {
                    color: #ffffff;
                    background-color: #dc3545;
                    text-align: center;
                    padding: 10px;
                    border-radius: 4px;
                    margin: 20px auto;
                    width: fit-content;
                    max-width: 80%;
                }
            </style>
        </head>
        <body>
        <div class="container">
            <h2>Rent Transaction Error</h2>
            <p class="error-message">Error: <?php echo htmlspecialchars($conn->error); ?></p>
            <div class="button-container">
                <form method='POST' action='admin.php'><button type='submit'>Home</button></form>
                <br><br>
                <form method='POST' action='admin_rent bike.php'><button type='submit'>Back</button></form>
            </div>
        </div>
        </body>
        </html>

        <?php
    }
}

$conn->close();
?>


