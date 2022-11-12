<?php
/*
    Script which will accept the data entered to patient sign in form.
    It will check whether the entered email and passoword exists. If it does its will redirect user to search page.
    Also it will create a session with the user.
    Else it will redirect the user back to sign in page with an error message.
*/
    
    //Accepts input values from doctor sign in page
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $uPass = filter_input(INPUT_POST, 'uPassword');

    //initializes outputmessage to an empty string and isValid to true. 
    $outputMessage = "";
    $isValid = TRUE;

    //Checks whether either the email or password is empty. If so isValid is set to false.
    if($uEmail == false) {
        $outputMessage .= "Please enter your e-mail address.<br>";
        $isValid = FALSE;
    }
    if (empty($uPass)){
        $outputMessage .= "Please enter your password.<br>";
        $isValid = FALSE;
    }

    //if isValid is false, then redirects the user to signin page with the error message.
    if($isValid === FALSE){
        header("Location: ./signInPatients.php?outputMessage=$outputMessage");
        exit();
    }

    //otherwise interpreter moves to sign-in process.
    else{
        //retieves the patient_id who's email and passwords matches the input values.
        require('./database.php');
        $query = 'select patient_id from patients where patient_email=:p_mail and patient_password=:p_pass';
        $statement = $database->prepare($query);
        $statement->bindValue(':p_mail',$uEmail);
        $statement->bindValue(':p_pass',$uPass);
        $statement->execute();
        $patient_id_result = $statement->fetch();    //if array is empty, the method will return false, this is due to incorrect e-mail or password.
        $statement->closeCursor();

        //if the array is empty, generates an error message and redirects the user back to sign-in page.
        if($patient_id_result === FALSE){
            $outputMessage = "The account was not found. Please try a different e-mail or password.<br>";
            header("Location: ./signInPatients.php?outputMessage=$outputMessage");
            exit();
        }
        
        //If the array was successfully retireved, then the retrived patient id is assigned to the $SESSION super global variable. 
        session_start();    //Initiates a session with the user's browser or resumes a previous session
        $_SESSION['patient_id'] = $patient_id_result['patient_id'];

        //redirects the user to the search page
        header('Location: ./search.php');
    }
?>
