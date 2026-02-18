 <script>
$(document).ready(function () {

    // Custom Email Regex
    $.validator.addMethod(
        "IsValidEmail",
        function (value, element) {
            return /^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i.test(value);
        },
        "Invalid email format"
    );

    $("#formregisterid").validate({

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
                email: true,
                IsValidEmail: true
            },
            password: {
                required: true,
                minlength: 8
            },
            cnfpassword: {
                required: true,
                equalTo: "#password-field"
            },
            role: {
                required: true
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
                required: "Email is required"
            },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 8 characters"
            },
            cnfpassword: {
                required: "Confirm password is required",
                equalTo: "Passwords do not match"
            },
            role: {
                required: "Please select role"
            }
        },

        errorElement: "small",
        errorClass: "text-danger",

        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },

        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
        },

        submitHandler: function (form) {
            form.submit();
        }

    });

    // Password toggle
    $(".toggle-password").click(function () {
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