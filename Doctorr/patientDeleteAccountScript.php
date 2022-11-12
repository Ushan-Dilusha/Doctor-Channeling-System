<?php
/*
    deletes a record from the patients table
*/

//checks if user is logged in, otherwise redirects user to sign-in
    if(!isset($_SESSION['patient_id'])){
        session_start();
    }
    if(!isset($_SESSION['patient_id'])){
        header('Location: ./signInPatients.php');
        exit();
    }
    else{
        $patient_id = $_SESSION['patient_id'];
    }

//gets input value from delete account form of patient profile settings
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
        $query_pass = 'select patient_password from patients where patient_id = :p_id;';
        $statement_pass = $database->prepare($query_pass);
        $statement_pass->bindValue(':p_id', $patient_id);
        $statement_pass->execute();
        $pass_result = $statement_pass->fetch();
        $statement_pass->closeCursor();

        //checks whether entered password is same as actual password
        //if not error message is generated and $isValid is set to false
        if($uPassword !== $pass_result['patient_password']){
            $outputMessage .= "The password you entered is incorrect. Your account was not deleted.<br>";
            $isValid = FALSE;
        }
    }
    //if $isValid is false the user is redirected to profile settings with the error message
    if($isValid === FALSE){
        header("Location: ./patientProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    else{
        require('./database.php');

        //deletes all records related to the patient from the database.
        $query = 'delete from patients where patient_id = :p_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':p_id', $patient_id);
        $statement->execute();
        $statement->closeCursor();

        //redirects user to patientSignOut while will sign out the user of the deleted patient account.
        header("Location: ./patientSignOut.php");
    }
?>
