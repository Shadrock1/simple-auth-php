<?php

session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

require_once "config.php";

$login = $password = "";
$login_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["login"]))) {
        $login_err = "Please enter login.";
    } else {
        $login = trim($_POST["login"]);
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($login_err) && empty($password_err)) {
        $sql = "SELECT id, login, password FROM users WHERE login = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("s", $param_login);

            $param_login = $login;

            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $login, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            session_start();

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["login"] = $login;

                            header("location: welcome.php");
                        } else {
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    $login_err = "No account found with that login.";
                }
            } else {
                echo "Something went wrong.";
            }

            $stmt->close();
        }
    }

    $mysqli->close();
}
require_once('templates/login.phtml');
