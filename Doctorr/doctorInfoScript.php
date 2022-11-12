<?php
//checks if the user is logged in. If not redirects user to sign in page.
    if(!isset($_SESSION['doctor_id'])){
        session_start();
    }
    if(!isset($_SESSION['doctor_id'])){
        header('Location: ./signInDoctors.php');
        exit();
    }
    //otherwise user id is assigned to $doctor_id from $_SESSION
    else{
        $doctor_id = $_SESSION['doctor_id'];
    }

    //input values from personal information form are obtained
    $uName = filter_input(INPUT_POST, 'uName');
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $doctorSpec = filter_input(INPUT_POST, 'doctorSpec',FILTER_VALIDATE_INT);
    $doctorSmallDesc = filter_input(INPUT_POST, 'doctorSmallDesc');
    $doctorLongDesc = filter_input(INPUT_POST, 'doctorLongDesc');

    //$outputMessage and isValid are initialised initialised
    $outputMessage = "";
    $isValid = TRUE;

    // performs server side validity
    //if invalid isValid is set to false and error message is generated
    if (empty($uName)){
        $outputMessage .= "Please enter your name.<br>";
        $isValid = FALSE;
    }
    if($uEmail === FALSE) {
        $outputMessage .= "Please enter a valid e-mail address.<br>";
        $isValid = FALSE;
    }
    if(empty($doctorSpec) || $doctorSpec == FALSE){
        $outputMessage .= "Please select your specialization from the given list.<br>";
        $isValid = FALSE;
    }
    //checks if small description is within the 300 word limit
    if(strlen($doctorSmallDesc) > 300){
        $outputMessage .= "Too many characters in the search card description.<br>";
        $isValid = FALSE;
    }
    //checks if long description is within the 1000 word limit
    if(strlen($doctorLongDesc) > 1000){
        $outputMessage .= "Too many characters in the Doctorr profile description.<br>";
        $isValid = FALSE;
    }
    //if validity is false user is redirected to profile settings
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    //if valid the user's record is updated with the input values 
    else{
        require('./database.php');

        //update query is prepared and binded to values
        $query = 'update doctors set doctor_name = :d_name, doctor_email = :d_mail, doctor_specialization_id = :d_spec, doctor_short_description = :d_sm_desc, doctor_long_description = :d_lg_desc where doctor_id = :d_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':d_name',$uName);
        $statement->bindValue(':d_mail',$uEmail);
        $statement->bindValue(':d_spec',$doctorSpec);
        $statement->bindValue(':d_sm_desc',$doctorSmallDesc);
        $statement->bindValue(':d_lg_desc',$doctorLongDesc);
        $statement->bindValue(':d_id',$doctor_id);

        /*
            executes the statement. if update fail an exception will be raised. 
            This would happen if the e-mail is changed to a one that already exists.
            If an exception is throw, then an error messgae will be generated and user is redirected to profile settings page
        */
        try{
            $statement->execute();
        }catch(PDOException $e){
            $outputMessage .= "The e-mail address you entered already exists. Changes were rolled back.<br>";
            header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
            exit();
        }
        $statement->closeCursor();

        //Generates success message and user is redirected to profile settings.
        $successMessage = "Your personal information has been successfully updated. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
