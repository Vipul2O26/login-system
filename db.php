<?php
 
    $host = "mysql:host=localhost;dbname=authentication";
    $username = "root";
    $password = "Root@123";

    
    try {
        $connect = new PDO($host,$username,$password);
        $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       // echo "connection successfully";
    } catch(PDOException $e) {
        echo "connection failed";

        $sqlStateCode = $e->getCode();
        echo "$sqlStateCode";

        if($sqlStateCode == "1045"){
            echo "<br>username or password may be wrong";
        }

         if($sqlStateCode == "1049"){
            echo "<br>datasbe may not exist";
        }

        if($sqlStateCode == "2002"){
            echo "<br> check your host name";
        }
    }

?>