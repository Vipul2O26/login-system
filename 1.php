<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include './db.php';
include './link.php';

/* ================= VARIABLES ================= */

$emailError = $passwordError = $alert = null;
$emailFlag = $passwordFlag = true;

$email = $password = null;

/* ================= FUNCTIONS ================= */

function checkPassword($password)
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    if (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one digit";
    }

    return $errors;
}

/* ================= FORM SUBMIT ================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* ===== EMAIL VALIDATION ===== */

    if (empty($_POST['email'])) {
        $emailError = "Email is required";
        $emailFlag = false;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
        $emailFlag = false;
    } else {
        $email = trim($_POST['email']);
    }

    /* ===== PASSWORD VALIDATION ===== */

    if (empty($_POST['password'])) {
        $passwordError = "Password is required";
        $passwordFlag = false;
    } else {

        $passwordErrors = checkPassword($_POST['password']);

        if (!empty($passwordErrors)) {
            $passwordError = implode("<br>", $passwordErrors);
            $passwordFlag = false;
        } else {
            $password = $_POST['password'];
            $passwordFlag = true;
        }
    }

    /* ===== IF VALIDATION PASSED ===== */

    if ($emailFlag && $passwordFlag) {

        try {
            $stmt = $connect->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                /* ===== SESSION SET ===== */
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname']  = $user['lastname'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];

                /* ===== REMEMBER ME ===== */
                if (!empty($_POST['rememberMe'])) {
                    setcookie("user_email", $user['email'], time() + (86400 * 7), "/");
                }

                /* ===== REDIRECT BY ROLE ===== */
                if ($user['role'] === "admin") {
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($user['role'] === "user") {
                    header("Location: user_dashboard.php");
                    exit();
                }

            } else {
                $alert = "<div class='alert alert-danger mt-4'>Invalid email or password</div>";
            }

        } catch (PDOException $e) {
            $alert = "<div class='alert alert-danger mt-4'>Database Error</div>";
        }

    } else {
        $alert = "<div class='alert alert-danger mt-4'>Please fix validation errors</div>";
    }
}
?>