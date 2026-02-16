<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);

    include './db.php';
    include './link.php';

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";


function process_data($data) {
    return htmlspecialchars(trim($data));
}

function checkAlphabet($string) {
    return ctype_alpha($string);
}

function checkPassword($password) {
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



$firstNameError = $lastNameError = $emailError = $passwordError = $cnfpasswordError = "";
$firstNameFlag = $lastNameFlag = $emailFlag = $passwordFlag = $cnfpasswordFlag = $roleFlag = true;

$firstName = $lastName = $email = $password = $cnfpassword = $role = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

   // firstname
    if (empty($_POST['firstName'])) {
        $firstNameError = "First name is required";
        $firstNameFlag = false;
    } elseif (!checkAlphabet($_POST['firstName'])) {
        $firstNameError = "Only alphabets allowed";
        $firstNameFlag = false;
    } else {
        $firstName = process_data($_POST['firstName']);
    }

    // lastname
    if (empty($_POST['lastName'])) {
        $lastNameError = "Last name is required";
        $lastNameFlag = false;
    } elseif (!checkAlphabet($_POST['lastName'])) {
        $lastNameError = "Only alphabets allowed";
        $lastNameFlag = false;
    } else {
        $lastName = process_data($_POST['lastName']);
    }

    // email
    if (empty($_POST['email'])) {
        $emailError = "Email is required";
        $emailFlag = false;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
        $emailFlag = false;
    } else {
        $email = process_data($_POST['email']);
    }

    // password 
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
        }
    }

    // confirm password
    if (empty($_POST['cnfpassword'])) {
        $cnfpasswordError = "Confirm password required";
        $cnfpasswordFlag = false;
    } elseif ($_POST['cnfpassword'] !== $password) {
        $cnfpasswordError = "Passwords do not match";
        $cnfpasswordFlag = false;
    } else {
        $cnfPassword = $_POST['cnfpassword'];
    }


    if( empty( $_POST['role'] ) ) {
        $roleFlag = false;  
    } else {
        $role = $_POST['role'];
    }

    if ($firstNameFlag && $lastNameFlag && $emailFlag && $passwordFlag && $cnfpasswordFlag && $roleFlag ) {
        
        try {
            $notification = null;
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $sqlInsert = "INSERT INTO `users`(`firstname`, `lastname`, `email`, `password`, `role`) VALUES ('$firstName','$lastName','$email','$hashPassword','$role')";
            $connect->exec($sqlInsert);
            
            $notification = " <div class='alert alert-success mt-5' role='alert' id='register'>user register successfully</div>";
            header("Location: login.php");
            exit;

        } catch (PDOException $e) {

            $e->getMessage();
            $sqlStateCode = $e->getCode();

            $msg = null;
           // echo $sqlStateCode;
            if ($sqlStateCode == "23000") {
                $msg = " <div class='alert alert-danger mt-5' role='alert' id='emailmsg'>Email already registered</div>";
            }
        }
    }
}
?>

<style>
    body {
        background-color: rgba(49, 167, 203);
    }
      .toggle-password {
        float: right;
        cursor: pointer;
        margin-right: 10px;
        margin-top: -30px;
        height: 25px;
    }
</style>

<body>

<div class="container">
    <?php  if(!empty($notification)) { echo $notification; } ?>
    <?php  if(!empty($msg)) { echo $msg; } ?>
    
</div>
    

    <div class="container mt-5 d-flex justify-content-center">       
        <div class="card col-md-8">
            <div class="card-body">
                <h3 class="text-center text-secondary mb-4">Sign Up</h3>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" id="formregisterid">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstname" class="form-label">First Name</label>
                                <span class="text-danger">*</span>
                                
                                <input type="text" name="firstName" id="firstName" class="form-control border-info <?php echo $firstNameError ? 'border-danger' : '' ?>" value="<?php echo $firstName; ?>" placeholder="First name">
                                <small class="text-danger error"><?php echo $firstNameError; ?></small>
                                <small class="text-danger invalid-feedback" id="checkFirstNameCharacter">name is required</small>
                                
                            </div>

                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name</label>
                                <span class="text-danger">*</span>
                                <input type="text" name="lastName" class="form-control border-info <?php echo $lastNameError ? 'border-danger' : '' ?>" value="<?php echo $lastName; ?>"  id="lastName" placeholder="Last name">
                                <small class="text-danger"><?php echo $lastNameError; ?></small>
                                <small class="text-danger invalid-feedback" id="checkLastName">last name is required</small>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email</label>
                                <span class="text-danger">*</span>
                                <input type="email" name="email" class="form-control border-info <?php echo $emailError ? 'border-danger' : '' ?>" value="<?php echo $email; ?>" id="email" placeholder="Email">
                                <small class="text-danger"><?php echo $emailError; ?></small>
                                <small class="text-danger invalid-feedback" id="checEmailName">email is required</small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Password:</label>
                                <span class="text-danger">*</span>
                                <input type="password" name="password" id="password" class="form-control border-info password <?php echo $lastNameError ? 'border-danger' : '' ?>" value="<?php echo $password; ?>" placeholder="Password">
                                <span class="input-group-text toggle-password" toggle="#password-field" id="password">
                                    <i class="bi bi-eye"></i>
                                </span>                
                                <small class="text-danger"> <?php echo $passwordError  ?></small>
                                <small class="form-text text-danger invalid-feedback" id="passwordvalid">password is required</small>      
                            </div>

                       


                            <div class="col-12 col-md-6">
                                <label for="cnfpassword" class="form-label">Confirm Password</label>
                                <span class="text-danger">*</span>
                                <input type="password" name="cnfpassword" class="form-control border-info <?php echo $lastNameError ? 'border-danger' : '' ?>" id="cnfpassword" value="<?php echo $cnfpassword; ?>" placeholder="Confirm Password">
                                    <span class="input-group-text toggle-password" toggle="#password-field" id="cnfpassword">
                                        <i class="bi bi-eye"></i>
                                    </span>                      
                                <small class="text-danger"><?php echo $cnfpasswordError; ?></small>
                                <small class="form-text text-danger invalid-feedback" id="cnfpasswordvalid">confirm password is required</small>           
                            </div>

                            

                            <div class="col-12">
                                <label for="gender" class="form-label">Role</label>
                                <span class="text-danger">*</span><br>
                                <input type="radio" name="role" class="form-check-input border-info" value="admin" checked> Admin
                                <input type="radio" name="role" class="form-check-input border-info" value="user"> User      
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" id="submitform" class="btn btn-info text-light w-100 py-2">Register</button>
                            </div>

                            <div class="col-12 text-center mt-2">
                                <p>Already have an account? <a class="text-decoration-none" href="login.php">Sign in</a></p>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</body>    


 <script>

    function validateFirstName() {
        let nameInput = $("#firstName");
        let name = nameInput.val().trim();

        let nameCheck = /^([a-zA-z])/;

        if(name.length == "") {
            nameInput.addClass("is-invalid border-danger").removeClass("border-success border-info");
            $("#checkFirstNameCharacter").text("name is required");
            return false;
        }
       
        if(!nameCheck.test(name)) {
            nameInput.addClass("is-invalid border-danger").removeClass("border-success border-info");
            $("#checkFirstNameCharacter").html("only alphabhet allowed");
            return false;
        }

        if (name.length < 5 ) {
            nameInput.addClass("is-invalid").removeClass("border-succes");
            $("#checkFirstNameCharacter").text("name must be upto 5 character");
            return false;
        }

        nameInput.removeClass("is-invalid border-danger border-info").addClass("border-success");
        return true;
    }

    function validateLastName() {
        let LastameInput = $("#lastName");
        let LastName = LastameInput.val().trim();

        let lastNameCheck = /^([a-zA-z])/;

        if( LastName == "" ) {
            LastameInput.addClass("is-invalid border-danger").removeClass("border-info");
            $("#checkLastName").text("lastname is required");
            return false;
        }
        if(!lastNameCheck.test(LastName)) {
            LastameInput.addClass("is-invalid border-danger").removeClass("border-success");
            $("#checkLastName").html("only alphabhet allowed")
            return false;
        }

        if (LastName.length < 5 ) {
            LastameInput.addClass("is-invalid").removeClass("border-succes");
            $("#checkLastName").text("last name must be upto 5 character");
            return false;
        }

        LastameInput.removeClass("is-invalid border-danger border-info").addClass("border-success");
        return true;
    }



    function validateEmail() {
        let emailInput = $("#email");
        let email = emailInput.val().trim();
        let regex =
          /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]{2,5})$/;

        if ( email === "" ) {
            emailInput.addClass("is-invalid border-danger").removeClass("border-info");
            $("#checEmailName").show();
            $("#checEmailName").html("email is required");
            return false;
        } else if (!regex.test(email)) {
            emailInput.addClass("is-invalid border-danger").removeClass("border-success");
            $("#checEmailName").html("email must be valid");
            return false;
        }

        emailInput
          .removeClass("is-invalid border-danger border-info")
          .addClass("border-success");
        return true;
    }

     function validatePassword(){
        let passwordInput = $(".password");
        let password = passwordInput.val().trim();

       // console.log(password);

        if ( password === "" ) {
            passwordInput.addClass("border-danger").removeClass("border-info");
            $("#passwordvalid").show();
            $("#passwordvalid").text("password is required");
            return false;
        } else if ( password.length < 8 ) {
            passwordInput.addClass("border-danger").removeClass("border-info");
            $("#passwordvalid").text("password must be more than 8 character");
            return false;
        } else {
            passwordInput.removeClass("border-danger border-info border-success").addClass("border-success");
            return true;
        }
        
    }

     function validateConfirmPassword(){

        let passwordInput = $(".password");
        let password = passwordInput.val().trim();

        
        let cnfpasswordInput = $("#cnfpassword");
        let cnfpassword = cnfpasswordInput.val();

        if ( cnfpassword === "" ) {
            cnfpasswordInput.addClass("border-danger").removeClass("border-info");
            $("#cnfpasswordvalid").text("confirm password is required");
            $("#cnfpasswordvalid").show();
            return false;
        } else if ( password !== cnfpassword ) {
            cnfpasswordInput.addClass("border-danger").removeClass("border-info");
            $("#cnfpasswordvalid").html("confirm password must match password");
            return false;
        } else {
            cnfpasswordInput.removeClass("border-danger border-info border-success").addClass("border-success");
            return true;
        }        
    }    

 $(document).ready(function () {

    $("#submitform").click(function(event){ 
        const fnstatus = validateFirstName();
        const lnstatus = validateLastName();
        const emailstatus = validateEmail();
        const password = validatePassword();
        const cnfpassword = validateConfirmPassword();

        if ( fnstatus && lnstatus && emailstatus && password && cnfpassowrd ) {
            console.log("form submit");
        } else {
            console.log("form not submit");
            event.preventDefault();
        }


    })
        
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
        //firstname
        $("#firstName").on("keyup blur",validateFirstName);

        // lastname
        $("#lastName").on("keyup blur",validateLastName);

        // email validation
        $("#email").on("keyup blur", validateEmail);
        
        //password validation
        $(".password").on("keyup blur", validatePassword);

        // confirm password
        $("#cnfpassword").on("keyup blur",validateConfirmPassword);
        

        
        setTimeout(() => {
            $("#emailmsg").fadeOut("slow");
        }, 500);

        setTimeout(() => {
            $("#register").fadeOut("slow");
        }, 500);
        
        
      });
</script>