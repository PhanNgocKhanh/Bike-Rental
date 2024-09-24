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
			if (isset($_POST['list_all_bike'])) {
				header("Location: admin_list all bikes.php");
				exit;
			} elseif (isset($_POST['list_avail_bike'])) {
				header("Location: admin_list avail bikes.php");
				exit;
			} elseif (isset($_POST['list_rented_bike'])) {
				header("Location: admin_list rented bikes.php");
				exit;
			} elseif (isset($_POST['insert_bike'])) {
				header("Location: admin_insert bike.php");
				exit;
			} elseif (isset($_POST['edit_bike'])) {
				header("Location: admin_edit bike.php");
				exit;
			} elseif (isset($_POST['search_bike'])) {
				header("Location: admin_search bike.php");
				exit;
			} elseif (isset($_POST['list_all_users'])) {
				header("Location: admin_list users.php");
				exit;
			} elseif (isset($_POST['list_user_renting'])) {
				header("Location: admin_list users renting.php");
				exit;
			} elseif (isset($_POST['search_user'])) {
				header("Location: admin_search user.php");
				exit;
			} elseif (isset($_POST['rent_bike'])) {
				header("Location: admin_rent bike.php");
				exit;
			} elseif (isset($_POST['return_bike'])) {
				header("Location: admin_return bike.php");
				exit;
			}
		}
	}
?>


<html>
<head>
    <title>Admin Home Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        h1, h3 {
			margin-top: 20px;
            color: #A7C7E7; /* Pastel blue color */
            text-align: center;
        }
        .form-container {
            width: 80%;
            max-width: 900px;
            margin: 20px auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }
        .form-container form {
            width: 45%;
            padding: 20px;
            border: 2px solid #A7C7E7; /* Pastel blue color */
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container form h2 {
            color: #007bff;
            text-align: center;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            display: block;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .exit-button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto; /* Auto height to fit content */
            margin-top: 30px; /* Distance from the content above */
            margin-bottom: 30px; /* Distance from the content below */
        }
		.exit-button-container form {
			margin: 0;
        }
        .exit-button-container button {
            background-color: #007bff;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;    
        }
        .exit-button-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1><?php echo "Welcome $user_name! Admin Homepage" . "<br>" ?></h1>
    <h3><?php echo "User ID: $user_id" . "<br>" ?></h3>

    <div class="form-container">
        <form method="post">
            <h2>Bike</h2>
            <button type="submit" name="list_all_bike">All Bikes</button>
            <button type="submit" name="list_avail_bike">Available Bikes</button>
            <button type="submit" name="list_rented_bike">Rented Bikes</button>
            <button type="submit" name="insert_bike">Create New Bike</button>
            <button type="submit" name="edit_bike">Edit Bike</button>
            <button type="submit" name="search_bike">Search Bike</button>
        </form>

        <form method="post">
            <h2>Renter</h2>
            <button type="submit" name="list_all_users">All Renters</button>
            <button type="submit" name="list_user_renting">Currently Renting Renters</button>
            <button type="submit" name="search_user">Search Renter</button>
            <button type="submit" name="rent_bike">Rent Bike For Renter</button>
            <button type="submit" name="return_bike">Return Bike For Renter</button>
        </form>
    </div>

    <div class="exit-button-container">
        <form method="post">
            <button type="submit" name="exit">Exit</button>
        </form>
    </div>
</body>
</html>