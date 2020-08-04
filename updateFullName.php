<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$fullName = "";
$fullName_err = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $param_id = $_SESSION["id"];
    $sql = "SELECT fullName FROM users  WHERE id = $param_id";
    $data = $mysqli->query($sql);
    $oldFullName = $data->fetch_array(MYSQLI_NUM);
    $mysqli->close();
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate new password
    if (empty(trim($_POST["fullName"]))) {
        $fullName = "Please enter the new Full Name.";
    } else {
        $fullName = trim($_POST["fullName"]);
    }

    // Check input errors before updating the database
    if (empty($fullName_err)) {
        // Prepare an update statement
        $sql = "UPDATE users SET fullName = ? WHERE id = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_fullName, $param_id);

            // Set parameters
            $param_fullName = $fullName;
            $param_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Password updated successfully. Destroy the session, and redirect to login page
                header("location: welcome.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}

require_once('templates/updateFullName.phtml');
