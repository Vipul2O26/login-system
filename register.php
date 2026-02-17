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
    }
      label.error {
        color: red !important;
    }
    input.error{
        border-color: green;
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
                    <form id="formregisterid" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" >
                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">  
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>                               
                                        </span>   
                                        <input type="text" name="firstName" id="firstName" class="form-control border-info <?php echo $firstNameError ? 'border-danger' : '' ?>" value="<?php echo $firstName; ?>" placeholder="First name">
                                    </div>
                                        <label id="firstName-error" class="error" for="firstName"></label>
                                        <small class="text-danger error"><?php echo $firstNameError; ?></small>
                                        <small class="text-danger invalid-feedback" id="checkFirstNameCharacter">name is required</small>              
                            </div>

                            <div class="col-md-6">
                                <label for="lastname" class="form-label">Last Name</label>
                                <span class="text-danger">*</span>
                                <div class="input-group">  
                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>                               
                                    </span>  
                                    <input type="text" name="lastName" class="form-control border-info <?php echo $lastNameError ? 'border-danger' : '' ?>" value="<?php echo $lastName; ?>"  id="lastName" placeholder="Last name">
                                </div>
                                <label id="lastName-error" class="error" for="lastName"></label>
                                <small class="text-danger"><?php echo $lastNameError; ?></small>
                                <small class="text-danger invalid-feedback" id="checkLastName">last name is required</small>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label">Email:</label>
                                <span class="text-danger">*</span>
                                <div class="input-group">     
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>                                    
                                    </span>                                          
                                    <input type="email" name="email" id="email" class="form-control border-info email <?php echo $emailError ? 'border-danger' : '' ?>" value="<?php echo $email; ?>" placeholder="Email">   
                                </div>
                                    <label id="email-error" class="error" for="email"></label>
                                    <small class="form-text text-danger invalid-feedback">Email is required</small>
                                    <small class="text-danger"> <?php echo $emailError  ?></small>    
                            </div>                          

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Password:</label>
                                <span class="text-danger">*</span>
                                <div class="input-group">                                                               
                                    <input type="password" name="password" id="password-field" class="form-control border-info password <?php echo $passwordError ? 'border-danger' : '' ?>"  value="<?php echo $password; ?>" placeholder="enter password">
                                    <span class="input-group-text toggle-password" toggle="#password-field">
                                        <i class="bi bi-eye"></i>      
                                    </span>    
                                </div>         
                                <label id="password-field-error" class="error" for="password-field">Password should be more than 8 character</label>
                                <small class="text-danger"> <?php echo $passwordError  ?></small>
                                <small class="form-text text-danger invalid-feedback">Password is required</small>     
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="cnfpassword" class="form-label">Confirm Password</label>
                                <span class="text-danger">*</span>
                                <div class="input-group">                              
                                    <input type="password" name="cnfpassword" class="form-control border-info <?php echo $cnfpasswordError? 'border-danger' : '' ?>" id="cnfpassword" value="<?php echo $cnfpassword; ?>" placeholder="Confirm Password">
                                    <span class="input-group-text toggle-password" toggle="#password-field">
                                        <i class="bi bi-eye"></i>      
                                    </span> 
                                </div>  
                                <label id="cnfpassword-error" class="error" for="cnfpassword"></label>
                                <small class="text-danger"><?php echo $cnfpasswordError; ?></small>
                                <small class="form-text text-danger invalid-feedback" id="cnfpasswordvalid">confirm password is required</small>           
                            </div>

                            <div class="col-12">
                                <label for="gender" class="form-label">Role</label>
                                <span class="text-danger">*</span><br>
                                <input type="radio" name="role" class="form-check-input border-info" value="user" checked> User   
                                <input type="radio" name="role" class="form-check-input border-info" value="admin" > Admin                 
                            </div>
                            <div class="col-12 text-center">
                                <button type="submit" id="submitloginform" class="btn btn-info text-light w-100 py-2">Register</button>
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

       console.log(password);

        if ( password === "" ) {
            passwordInput.addClass("border-danger").removeClass("border-info");
            $("#passwordvalid").show();
            $("#passwordvalid").text("password is required");
            return false;
        } else if ( password.length <= 8 ) {
            passwordInput.addClass("border-danger").removeClass("border-info");
             $("#passwordvalid").text("password is required");
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



       if ( password !== cnfpassword ) {
            cnfpasswordInput.addClass("border-danger").removeClass("border-info");
            $("#cnfpasswordvalid").html("confirm password must match password");
            return false;
        } else {
            cnfpasswordInput.removeClass("border-danger border-info border-success").addClass("border-success");
            return true;
        }        
    }    

 $(document).ready(function () {

    // $("#submitloginform").click(function (event) {
    //       event.preventDefault();
    //     });

        $.validator.addMethod(
            "IsValidEmail",
            function (value, element) {
                let emailRegEx = new RegExp(
                    /^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i,
                );
                return emailRegEx.test(value);
            },
          "invalid email",
        );

        $("#formregisterid").validate({
          onkeydown: function (element) {
            let validator = this;
            setTimeout(function () {
              validator.element(element);
            }, 1000);
          },
          rules: {
            firstName: {
                required: true,
                minlength: 3,
            },
            lastName: {
                required: true,
                minlength: 3,
            },
            email: {
                required: true,
                email: true,
                IsValidEmail: true,
            },
            password: {
                required: true,
                minlength: 8,
            },
            cnfpassword: {
                required: true,
                equalTo: "#password",
            },
          },
          messages: {
            firstName: {
                required: "Name is required",
                minlength: "Name must contain 3 alphabhets",
            },
            lastName: {
                required: "LastName is required",
                minlength: "LastName must contain 3 alphabhets",
            },
            email: {
              required: "Email is required",
            },
            password: {
              required: "Password is required",
              minlength: "Password should be more than 7 character",
            },
            cnfpassword: {
                required: "confirm password is requried",
                equalTo: "",
            },

            
          },

          highlight: function(error,element,validClass){
            $(element).removeClass("border-info").addClass("border-success");
          },

          submitHandler: function (form) {
            debugger 
            form.submit();
          },
        });

            $("#submitloginform").click(function (event) {
                        
                const fnstatus = validateFirstName();
                const lnstatus = validateLastName();
                const emailstatus = validateEmail();
                const passwordstatus = validatePassword();
                const cnfpasswordstatus = validateConfirmPassword();
                
                console.log(event);

            if(fnstatus && lnstatus && emailstatus && passwordstatus && cnfpasswordstatus ) {
                debugger
                console.log("form ready to submit");
                // event.target.submit();
                document.getElementById('submitloginform').requestSubmit();

                
            } else {
                event.preventDefault();   
                console.log("form not submit");           
            }
        });

    
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