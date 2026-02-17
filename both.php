<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <style>
        body {
            background: #e9f2f7;
        }
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="container mt-5 d-flex justify-content-center">
    <div class="card col-md-6 shadow">
        <div class="card-body">
            <h3 class="text-center mb-4">Register</h3>

            <?= $msg ?>

            <form method="POST" id="registerForm">

                <!-- First Name -->
                <div class="mb-3">
                    <label>First Name</label>
                    <input type="text" name="firstName" class="form-control" value="<?= $firstName ?>">
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label>Last Name</label>
                    <input type="text" name="lastName" class="form-control" value="<?= $lastName ?>">
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="<?= $email ?>">
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control">
                        <span class="input-group-text toggle-password">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="cnfpassword" class="form-control">
                        <span class="input-group-text toggle-password">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>

            </form>

        </div>
    </div>
</div>

<script>
$(document).ready(function(){

    // Strong password rule
    $.validator.addMethod("strongPassword", function(value, element) {
        return this.optional(element) 
            || /[a-z]/.test(value)
            && /[A-Z]/.test(value)
            && /[0-9]/.test(value);
    }, "Password must contain uppercase, lowercase and number.");

    $("#registerForm").validate({

        rules: {
            firstName: {
                required: true,
                minlength: 3,
                lettersonly: true
            },
            lastName: {
                required: true,
                minlength: 3,
                lettersonly: true
            },
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 8,
                strongPassword: true
            },
            cnfpassword: {
                required: true,
                equalTo: "#password"
            }
        },

        messages: {
            firstName: {
                required: "First name is required",
                minlength: "Minimum 3 characters required"
            },
            lastName: {
                required: "Last name is required",
                minlength: "Minimum 3 characters required"
            },
            email: {
                required: "Email is required",
                email: "Enter valid email"
            },
            password: {
                required: "Password is required",
                minlength: "Minimum 8 characters required"
            },
            cnfpassword: {
                required: "Confirm password required",
                equalTo: "Passwords do not match"
            }
        },

        errorElement: "div",
        errorClass: "invalid-feedback",

        highlight: function(element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },

        unhighlight: function(element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },

        errorPlacement: function(error, element) {
            if (element.parent(".input-group").length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    // Password toggle
    $(".toggle-password").click(function(){
        let input = $(this).siblings("input");
        let icon = $(this).find("i");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            icon.removeClass("bi-eye").addClass("bi-eye-slash");
        } else {
            input.attr("type", "password");
            icon.removeClass("bi-eye-slash").addClass("bi-eye");
        }
    });

});
</script>

</body>
<<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include './db.php';

function clean($data) {
    return htmlspecialchars(trim($data));
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
    } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST['firstName'])) {
        $firstNameError = "Only alphabets allowed";
        $valid = false;
    } else {
        $firstName = clean($_POST['firstName']);
    }

    // Last Name
    if (empty($_POST['lastName'])) {
        $lastNameError = "Last name is required";
        $valid = false;
    } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST['lastName'])) {
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