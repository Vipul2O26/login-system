<?php
    
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors',1);
    error_reporting(E_ALL);
    

    include './db.php';
    include './link.php';

   
    if(!isset($_SESSION['email']) ) {
        header("Location: login.php");
        exit();
    } 
    

    $name = $lastname = $email = $notification = null;
    
    $name = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    $email = $_SESSION['email'];

    $notification = $_SESSION['notification'];

?>




<nav class="navbar navbar-light bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold fs-4 text-light">Dashboard</a>
        <div class="d-flex">
            <span class="text-light px-3 py-3 fs-5">Hello , <?php echo $_SESSION['firstname']; ?></span>
            <div class="btn-group dropstart">
                <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <img src="./assest/profile.jpeg" height="65px" alt="profile photo" class="rounded-circle px-2 py-2"/>            
                </button>
                
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                <hr class="text-danger">
                    <li>
                        <a href="./logout.php" class="dropdown-item">Logout</a>
                    </li>
                </ul>
          </div>
        </div>
    </div>
</nav>


<div class="container mt-5">
    <?php if(!empty($notification)) { echo $notification; } ?>


   


</div>



<script>
    $(document).ready(function(){
        
        setTimeout(() => {
            $("#login").fadeOut("slow");
        }, 2000);
        
    })
</script>