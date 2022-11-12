<?php
/*
    deletes a record from the doctors table
*/

//checks if user is logged in, otherwise redirects user to sign-in
    if(!isset($_SESSION['doctor_id'])){
        session_start();
    }
    if(!isset($_SESSION['doctor_id'])){
        header('Location: ./signInDoctors.php');
        exit();
    }
    else{
        $doctor_id = $_SESSION['doctor_id'];
    }
//gets input value from delete account form of doctor profile settings
    $uPassword = filter_input(INPUT_POST, 'uPasswordDel');

    //initializes the $outputMessage and $isValid
    $outputMessage = "";
    $isValid = TRUE;

    //checks if password is empty. If so, sets $isValid to false and generates error message
    if (empty($uPassword)){
        $outputMessage .= "Please enter your password.<br>";
        $isValid = FALSE;
    }
    //if $isValid is true checks if password is correct 
    if($isValid === True){
        require('./database.php');

        //retrieves password of user from database
        $query_pass = 'select doctor_password from doctors where doctor_id = :d_id;';
        $statement_pass = $database->prepare($query_pass);
        $statement_pass->bindValue(':d_id', $doctor_id);
        $statement_pass->execute();
        $pass_result = $statement_pass->fetch();
        $statement_pass->closeCursor();

        //checks whether entered password is same as actual password
        //if not error message is generated and $isValid is set to false
        if($uPassword !== $pass_result['doctor_password']){
            $outputMessage .= "The password you entered is incorrect. Your account was not deleted.<br>";
            $isValid = FALSE;
        }
    }
    //if $isValid is false the user is redirected to profile settings with the error message
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    else{
        require('./database.php');

        //deletes all records related to doctor from the database. (Deletion will cascade to child tables)
        $query = 'delete from doctors where doctor_id = :d_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':d_id', $doctor_id);
        $statement->execute();
        $statement->closeCursor();

        //redirects user to doctorSignOut while will sign out the user of the deleted doctor account.
        header("Location: ./doctorSignOut.php");
    }
?>
