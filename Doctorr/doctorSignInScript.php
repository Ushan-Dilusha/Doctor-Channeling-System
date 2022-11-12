<?php
/*
    Script which will accept the data entered to doctor sign in form.
    It will check whether the entered email and passoword exists. If it does its will redirect user to doctor profile page.
    Also it will create a session with the user.
    Else it will redirect the user back to sign in page with an error message.
*/

//Accepts input calues from doctor sign in page
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $uPass = filter_input(INPUT_POST, 'uPassword');

//initializes outputmessage to an empty string and isValid to true. 
    $outputMessage = "";
    $isValid = TRUE;

    //Checks whether either the email or password is empty. If so isValid is set to false.
    if($uEmail == FALSE) {
        $outputMessage .= "Please enter your e-mail address.<br>";
        $isValid = FALSE;
    }
    if (empty($uPass)){
        $outputMessage .= "Please enter your password.<br>";
        $isValid = FALSE;
    }

    //if isValid is false, then redirects the user to signin page with the error message.
    if($isValid === FALSE){
        header("Location: ./signInDoctors.php?outputMessage=$outputMessage");
        exit();
    }

    //otherwise interpreter moves to sign-in process.
    else{
        //retieves the doctor_id who's email and passwords matches the input values.
        require('./database.php');
        $query = 'select doctor_id from doctors where doctor_email=:d_mail and doctor_password=:d_pass';
        $statement = $database->prepare($query);
        $statement->bindValue(':d_mail',$uEmail);
        $statement->bindValue(':d_pass',$uPass);
        $statement->execute();
        $doctor_id_result = $statement->fetch();    //if array is empty, the method will return false, this is due to incorrect e-mail or password.
        $statement->closeCursor();

        //if the array is empty, generates an error message and redirects the user back to sign-in page.
        if($doctor_id_result === FALSE){
            $outputMessage = "The account was not found. Please try a different e-mail or password.<br>";
            header("Location: ./signInDoctors.php?outputMessage=$outputMessage");
            exit();
        }
        
        //If the array was successfully retireved, then the retrived doctor id is assigned to the $SESSION super global variable. 
        session_start();   //Initiates a session with the user's browser or resumes a previous session
        $_SESSION['doctor_id'] = $doctor_id_result['doctor_id'];

        //redirects the user to the doctor profile settings page
        header('Location: ./doctorProfileSettings.php');
    }
?>
