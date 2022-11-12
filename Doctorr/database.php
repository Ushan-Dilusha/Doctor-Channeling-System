<?php
/*
    Connects to the database by creating a PDO object
*/

    try{
        //creates $database as a database object of the PDO class
        $database = new PDO('mysql:host=localhost;dbname=doctorrdb','root','');
    }catch (PDOException $except) { //Handles exceptions thrown by try block
        $error_msg = $except->getMessage(); //Retrieves the error message of the expetion raised
        header("Location: ./error404.php?errorMsg=$error_msg"); //Calls for Error404 page to handle error
    }
?>
