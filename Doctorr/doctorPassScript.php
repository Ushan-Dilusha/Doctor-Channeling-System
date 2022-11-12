<?php
/*
    Changes password of a user of a doctor account
*/

//Checks if user is logged in. Otherwise redirects user to sign-in. Otherwise retireves the users id.
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

    //gets input values from the account security form
    $uPassOld = filter_input(INPUT_POST, 'uPassOld');
    $uPassNew = filter_input(INPUT_POST, 'uPassword');
    $uPassRep = filter_input(INPUT_POST, 'uPassRepeat');

    //initializes outputMessage ans isValid.
    $outputMessage = "";
    $isValid = TRUE;

    //Performs server side validation on results. If one is invalid, isValid is set to false and an error message is generated.
    if (empty($uPassOld)){
        $outputMessage .= "Please enter your current password.<br>";
        $isValid = FALSE;
    }
    if (empty($uPassNew)){
        $outputMessage .= "Please enter a new password.<br>";
        $isValid = FALSE;
    }
    if(strlen($uPassNew) < 8){
        $outputMessage .= "Password must be at least 8 characters long.<br>";
        $isValid = FALSE;
    }
    if (empty($uPassRep)){
        $outputMessage .= "Please re-enter your new password.<br>";
        $isValid = FALSE;
    }
    
    if ($uPassRep !== $uPassNew) {
        $outputMessage .= "The new passwords do not match. Please re-enter the correct new password.<br>";
        $isValid = FALSE;
    }
    //If the input values pass the validity check, then the script checks if the current password entered is correct.
    if($isValid === True){
        require('./database.php');
        //retrieves password of the current user from the doctors table
        $query_pass = 'select doctor_password from doctors where doctor_id = :d_id;';
        $statement_pass = $database->prepare($query_pass);
        $statement_pass->bindValue(':d_id', $doctor_id);
        $statement_pass->execute();
        $pass_result = $statement_pass->fetch();
        $statement_pass->closeCursor();

        //checks if current password is same as retireved password. if not isvalid is set to false, generating error message. 
        if($uPassOld !== $pass_result['doctor_password']){
            $outputMessage .= "The password you entered does not match your current password. Please enter the correct password.<br>";
            $isValid = FALSE;
        }
    }
    //if invalid, then user is redirected to profile settings with the constructed error message.
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    //if isValid is still true then the doctor table is updated with new password
    else{
        require('./database.php');

        //updates password of user with new password
        $query = 'update doctors set doctor_password = :d_pass where doctor_id = :d_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':d_pass', $uPassNew);
        $statement->bindValue(':d_id', $doctor_id);
        $statement->execute();
        $statement->closeCursor();

        //redirects user to profile settings with message of success
        $successMessage = "Your password was successfully changed.. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
