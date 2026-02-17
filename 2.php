<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include './db.php';

function clean($data) {
    return htmlspecialchars(trim($data));
}

function checkAlphabet($string) {
    return preg_match("/^[a-zA-Z]+$/", $string);
}

function checkPassword($password) {
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "At least one lowercase letter required";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "At least one uppercase letter required";
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "At least one digit required";
    }

    return $errors;
}

$firstName = $lastName = $email = "";
$firstNameError = $lastNameError = $emailError = $passwordError = $cnfpasswordError = "";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $valid = true;

    // First Name
    if (empty($_POST['firstName'])) {
        $firstNameError = "First name is required";
        $valid = false;
    } elseif (!checkAlphabet($_POST['firstName'])) {
        $firstNameError = "Only alphabets allowed";
        $valid = false;
    } else {
        $firstName = clean($_POST['firstName']);
    }

    // Last Name
    if (empty($_POST['lastName'])) {
        $lastNameError = "Last name is required";
        $valid = false;
    } elseif (!checkAlphabet($_POST['lastName'])) {
        $lastNameError = "Only alphabets allowed";
        $valid = false;
    } else {
        $lastName = clean($_POST['lastName']);
    }

    // Email
    if (empty($_POST['email'])) {
        $emailError = "Email is required";
        $valid = false;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
        $valid = false;
    } else {
        $email = clean($_POST['email']);
    }

    // Password
    if (empty($_POST['password'])) {
        $passwordError = "Password is required";
        $valid = false;
    } else {
        $passwordErrors = checkPassword($_POST['password']);
        if (!empty($passwordErrors)) {
            $passwordError = implode("<br>", $passwordErrors);
            $valid = false;
        }
    }

    // Confirm Password
    if (empty($_POST['cnfpassword'])) {
        $cnfpasswordError = "Confirm password required";
        $valid = false;
    } elseif ($_POST['password'] !== $_POST['cnfpassword']) {
        $cnfpasswordError = "Passwords do not match";
        $valid = false;
    }

    if ($valid) {

        try {
            $hashPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (firstname, lastname, email, password, role)
                    VALUES (:firstname, :lastname, :email, :password, :role)";

            $stmt = $connect->prepare($sql);

            $stmt->execute([
                ':firstname' => $firstName,
                ':lastname'  => $lastName,
                ':email'     => $email,
                ':password'  => $hashPassword,
                ':role'      => 'user'
            ]);

            header("Location: login.php");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $msg = "<div class='alert alert-danger'>Email already registered</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Something went wrong</div>";
            }
        }
    }
}
?>