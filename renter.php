<?php
    session_start();

	// Set the timezone to Singapore
    date_default_timezone_set('Asia/Singapore');

	// Get the variables from session
    $user_name = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];
	$user_type = $_SESSION['user_type'];

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if (isset($_POST['exit'])) {
			// Destroy the session and redirect to the login page
			session_unset();
			session_destroy();
			header("Location: home.php");
			exit;
		} else {
			if (isset($_POST['list_avail_bike'])) {
				header("Location: renter_list avail bikes.php");
				exit;
			} elseif (isset($_POST['list_current_renting'])) {
				header("Location: renter_list current renting.php");
				exit;
			} elseif (isset($_POST['list_renting_history'])) {
				header("Location: renter_list renting history.php");
				exit;
			}
			elseif (isset($_POST['return_bike'])) {
				header("Location: renter_return bike.php");
				exit;
			}elseif (isset($_POST['search_bike'])) {
				header("Location: renter_search bike.php");
				exit;
			}
		}
	}
?>


<html>
<head>
    <title>Renter Home Page</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            text-align: center;
        }
        h1, h3 {
            color: #A7C7E7; /* Pastel blue color */
        }
        .form-container {
            border: 2px solid #A7C7E7; /* Pastel blue color */
            padding: 20px;
            margin: 20px auto;
            width: 300px;
            border-radius: 8px;
        }
        button {
            background-color: #007bff; /* Lighter blue */
            color: white;
            padding: 10px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #A7C7E7; 
        }
    </style>
</head>

<body>
    <h1>Welcome <?php echo htmlspecialchars($user_name); ?></h1>
    <h3>User ID: <?php echo htmlspecialchars($user_id); ?></h3>

    <div class="form-container">
        <form method="post">
            <button type="submit" name="list_avail_bike">Available Bikes</button>
            <button type="submit" name="list_current_renting">Current Renting</button>
            <button type="submit" name="list_renting_history">Past Renting</button>
            <button type="submit" name="return_bike">Return Bike</button>
            <button type="submit" name="search_bike">Search Bike</button>
            <button type="submit" name="exit">Exit</button>
        </form>
    </div>
</body>
</html>