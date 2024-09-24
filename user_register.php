<?php
session_start();

// Initialize user registration information
$user_id = "";
$user_name = "";
$user_surname = "";
$user_phone = "";
$user_email = "";
$user_type = "";
$error = [];
$formSubmitted = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate User Name
    if (empty($_POST["user_name"])) {
        $error['user_name'] = "Please enter your name!";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $_POST["user_name"])) {
        $error['user_name'] = "Name must contain only alphabetic characters!";
    } else {
        $user_name = $_POST["user_name"];
    }

    // Validate User Surname
    if (empty($_POST["user_surname"])) {
        $error['user_surname'] = "Please enter your surname!";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST["user_surname"])) {
        $error['user_surname'] = "Surname must contain only alphabetic characters!";
    } else {
        $user_surname = $_POST["user_surname"];
    }

    // Validate User Phone
    if (empty($_POST["user_phone"])) {
        $error['user_phone'] = "Please enter your phone number!";
    } elseif (!preg_match("/^[0-9]+$/", $_POST["user_phone"])) {
        $error['user_phone'] = "Phone number must contain only digits!";
    } else {
        $user_phone = $_POST["user_phone"];
    }

    // Validate User Email
    if (empty($_POST["user_email"])) {
        $error['user_email'] = "Please enter your email!";
    } elseif (!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
        $error['user_email'] = "Invalid email format!";
    } else {
        $user_email = $_POST["user_email"];
    }

    // Validate User Type
    if (empty($_POST["user_type"])) {
        $error['user_type'] = "Please select the user type!";
    } elseif (!in_array($_POST["user_type"], ['admin', 'renter'])) {
        $error['user_type'] = "Invalid user type selected!";
    } else {
        $user_type = $_POST["user_type"];
    }

    // Show error messages if any
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
        $user_name = $conn->real_escape_string($user_name);
        $user_surname = $conn->real_escape_string($user_surname);
        $user_phone = $conn->real_escape_string($user_phone);
        $user_email = $conn->real_escape_string($user_email);
        $user_type = $conn->real_escape_string($user_type);

        // Check for duplicate email
        $sql_check_email = "SELECT * FROM $dbTable WHERE email='$user_email'";
        $result_email = $conn->query($sql_check_email);

        if ($result_email->num_rows > 0) {
            $error['user_email'] = "Email already exists!";
        }

        // Check for duplicate phone
        $sql_check_phone = "SELECT * FROM $dbTable WHERE phone='$user_phone'";
        $result_phone = $conn->query($sql_check_phone);

        if ($result_phone->num_rows > 0) {
            $error['user_phone'] = "Phone number already exists!";
        }

        // Show error messages if any
        if (empty($error)) {
            // Construct SQL query
            $sql = "INSERT INTO $dbTable (name, surname, phone, email, type)
                    VALUES ('$user_name', '$user_surname', '$user_phone', '$user_email', '$user_type')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                $formSubmitted = true;
                $user_id = $conn->insert_id;

                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_surname'] = $user_surname;
                $_SESSION['user_phone'] = $user_phone;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_type'] = $user_type;

                // Check the user type and redirect
                if ($user_type == "admin") {
                    header("Location: admin.php");
                    exit;
                } elseif ($user_type == "renter") {
                    header("Location: renter.php");
                    exit;
                }
            } else {
                $error['database'] = "Error inserting record: " . $conn->error;
            }
        }

        // Close the connection
        $conn->close();
    }
}
?>

<html>
<head>
    <title>User Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 80%;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #A7C7E7; /* Pastel blue border */
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #A7C7E7; /* Pastel blue heading */
            text-align: center;
            margin: 0 0 20px 0;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
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
            margin: 0 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .success-message {
            color: #ffffff;
            background-color: #007bff;
            text-align: center;
            padding: 10px;
            border-radius: 4px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<div class="form-container">
    <?php if (!$formSubmitted): ?>
        <h1>Registration Form</h1>
        <form action="user_register.php" method="POST">
            <label for="user_name">User Name</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user_name); ?>">
            <?php if (isset($error['user_name'])): ?>
                <div class="error"><?php echo $error['user_name']; ?></div>
            <?php endif; ?>

            <label for="user_surname">User Surname</label>
            <input type="text" id="user_surname" name="user_surname" value="<?php echo htmlspecialchars($user_surname); ?>">
            <?php if (isset($error['user_surname'])): ?>
                <div class="error"><?php echo $error['user_surname']; ?></div>
            <?php endif; ?>

            <label for="user_phone">User Phone</label>
            <input type="text" id="user_phone" name="user_phone" value="<?php echo htmlspecialchars($user_phone); ?>">
            <?php if (isset($error['user_phone'])): ?>
                <div class="error"><?php echo $error['user_phone']; ?></div>
            <?php endif; ?>

            <label for="user_email">User Email</label>
            <input type="text" id="user_email" name="user_email" value="<?php echo htmlspecialchars($user_email); ?>">
            <?php if (isset($error['user_email'])): ?>
                <div class="error"><?php echo $error['user_email']; ?></div>
            <?php endif; ?>

            <label for="user_type">User Type</label>
            <select id="user_type" name="user_type">
                <option value="">--Select--</option>
                <option value="renter" <?php echo $user_type == 'renter' ? 'selected' : ''; ?>>Renter</option>
                <option value="admin" <?php echo $user_type == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            <?php if (isset($error['user_type'])): ?>
                <div class="error"><?php echo $error['user_type']; ?></div>
            <?php endif; ?>

            <div class="button-container">
                <button type="submit" name="submit">Register</button>
            </div>
        </form>
    <?php else: ?>
        <div class="success-message">
            <p>Registration successful!<br>
               User ID: <?php echo htmlspecialchars($user_id); ?><br>
               Name: <?php echo htmlspecialchars($user_name) . ' ' . htmlspecialchars($user_surname); ?><br>
               Phone: <?php echo htmlspecialchars($user_phone); ?><br>
               Email: <?php echo htmlspecialchars($user_email); ?><br>
               User Type: <?php echo htmlspecialchars($user_type); ?><br>
            </p>
        </div>

        <div class="button-container">
            <form action="login.php" method="POST" style="display:inline;">
                <button type="submit" name="login">Login</button>
            </form>
            <form action="index.php" method="POST" style="display:inline;">
                <button type="submit" name="home">Home</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
