<?php
session_start();

// Set the timezone to Singapore
date_default_timezone_set('Asia/Singapore');

// Initialize user login information and errors
$user_id = "";
$user_name = "";
$user_type = "";
$error = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST["new_user"])) {
        // Redirect to the registration page if "Register" button is clicked
        header("Location: user_register.php");
        exit;
    }

    // Check for empty fields and set error messages
    if (empty($_POST["user_id"])) {
        $error['user_id'] = "Please enter your ID!";
    } else {
        $user_id = $_POST["user_id"];
    }
    
    if (empty($_POST["user_name"])) {
        $error['user_name'] = "Please enter your name!";
    } else {
        $user_name = $_POST["user_name"];
    }
    
    if (empty($_POST["user_type"])) {
        $error['user_type'] = "Please select the user type!";
    } else {
        $user_type = $_POST["user_type"];
    }

    // Proceed if no errors
    if (empty($error)) {
        // Database connection
        $serverName = "localhost:3307";
        $userName = "root";
        $password = "";
        $dbName = "a2_db";
        $dbTable = "user";

        // Create a new MySQLi connection
        $conn = new mysqli($serverName, $userName, $password, $dbName);
        if ($conn->connect_error) {
            die("Failed to connect to server. Error: " . $conn->connect_error);
        }

        // Escape user inputs for security
        $user_id = $conn->real_escape_string($user_id);
        $user_name = $conn->real_escape_string($user_name);
        $user_type = $conn->real_escape_string($user_type);

        // Query the database
        $sql = "SELECT * FROM $dbTable WHERE user_id = '$user_id' AND name = '$user_name' AND type = '$user_type'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_type'] = $user_type;

            if (isset($_POST["login"])) {
                if ($user_type == "admin") {
                    header("Location: admin.php");
                } elseif ($user_type == "renter") {
                    header("Location: renter.php");
                }
                exit;
            }
        } else {
            $error['login'] = "Invalid ID/Name/Type!";
        }

        // Close the connection
        $conn->close();
    }
} else {
    // Clear session data to ensure the start menu is shown
    session_unset();
    session_destroy();
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #A7C7E7; /* Pastel blue color */
            text-align: center;
            margin: 20px 0;
        }
        .form-container {
            border: 2px solid #A7C7E7; /* Pastel blue color */
            padding: 20px;
            width: 300px;
            margin: 20px auto;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
            margin-top: -5px;
            margin-bottom: 10px;
        }
        button {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .register-container {
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Welcome to GoBike!</h1>

    <div class="form-container">
        <form action="" method="POST">
            <label for="user_id">User ID</label>
            <input type="text" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
            <?php if (isset($error['user_id'])): ?>
                <div class="error-message"><?php echo $error['user_id']; ?></div>
            <?php endif; ?>

            <label for="user_name">User Name</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
            <?php if (isset($error['user_name'])): ?>
                <div class="error-message"><?php echo $error['user_name']; ?></div>
            <?php endif; ?>

            <label for="user_type">User Type</label>
            <select id="user_type" name="user_type">
                <option value="">--Select--</option>
                <option value="renter" <?php echo $user_type == 'renter' ? 'selected' : ''; ?>>Renter</option>
                <option value="admin" <?php echo $user_type == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <?php if (isset($error['user_type'])): ?>
                <div class="error-message"><?php echo $error['user_type']; ?></div>
            <?php endif; ?>

            <?php if (isset($error['login'])): ?>
                <div class="error-message"><?php echo $error['login']; ?></div>
            <?php endif; ?>

            <button type="submit" name="login">Login</button>
        </form>

        <div class="register-container">
            <form method="POST">
                <p>Don't have an account? Register here</p>
                <button type="submit" name="new_user">New User Register</button>
            </form>
        </div>
    </div>

</body>
</html>



