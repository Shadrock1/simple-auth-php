<?php

require_once "config.php";

$login = $email = $password = $fullName = "";
$login_err = $email_err = $password_err = $fullName_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["login"]))) {
        $login_err = "Please enter a login.";
    } else {
        $sql = "SELECT id FROM users WHERE login = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_login);

            $param_login = trim($_POST["login"]);

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $login_err = "This username is already taken.";
                } else {
                    $login = trim($_POST["login"]);
                }
            } else {
                echo "Something went wrong.";
            }

            $stmt->close();
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter a email.";
    } elseif (!preg_match("/^[\w]{1}[\w-\.]*@[\w-]+\.[a-z]{2,4}$/i", (trim($_POST["email"])))) {
        $email_err = "Incorrect email .";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["fullName"]))) {
        $fullName_err = "Please check Full Name.";
    } else {
        $fullName = trim($_POST["fullName"]);
    }

    if (empty($login_err) && empty($password_err) && empty($fullName_err) && empty($email_err)) {
        $sql = "INSERT INTO users (login, email, password, fullName) VALUES (?, ?, ?, ?)";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssss", $param_login, $param_email, $param_password, $param_fullName);

            $param_login = $login;
            $param_email = $email;
            $param_fullName = $fullName;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if ($stmt->execute()) {
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }

    $mysqli->close();
}
require_once('templates/register.phtml');
