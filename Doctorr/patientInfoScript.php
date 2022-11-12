<?php
//checks if the user is logged in. If not redirects user to sign in page.
    if(!isset($_SESSION['patient_id'])){
        session_start();
    }
    if(!isset($_SESSION['patient_id'])){
        header('Location: ./signInPatients.php');
        exit();
    }
    //otherwise user id is assigned to $patient_id from $_SESSION
    else{
        $patient_id = $_SESSION['patient_id'];
    }

    //input values from personal information form are obtained
    $uName = filter_input(INPUT_POST, 'uName');
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $uAddress1 = filter_input(INPUT_POST, 'uAddress1');
    $uAddress2 = filter_input(INPUT_POST, 'uAddress2');
    $uCity = filter_input(INPUT_POST, 'uCity',FILTER_VALIDATE_INT);

    //$outputMessage and isValid are initialised initialised
    $outputMessage = "";
    $isValid = TRUE;

    // performs server side validation
    //if invalid isValid is set to false and error message is generated
    if (empty($uName)){
        $outputMessage .= "Please enter your name.<br>";
        $isValid = FALSE;
    }
    if($uEmail === FALSE) {
        $outputMessage .= "Please enter a valid e-mail address.<br>";
        $isValid = FALSE;
    }
    if(empty($uAddress1)){
        $outputMessage .= "Please enter your first address-line.<br>";
        $isValid = FALSE;
    }
    if(empty($uCity) || $uCity == FALSE){
        $outputMessage .= "Please select a city from the given list.<br>";
        $isValid = FALSE;
    }

    //if validity is false user is redirected to profile settings
    if($isValid === FALSE){
        header("Location: ./patientProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }

    //if valid the user's record is updated with the input values 
    else{
        require('./database.php');

        //update query is prepared and binded to values
        $query = 'update patients set patient_name = :p_name, patient_email = :p_mail, patient_address1 = :p_address1, patient_address2 = :p_address2, patient_city_id = :p_city_id where patient_id = :p_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':p_name',$uName);
        $statement->bindValue(':p_mail',$uEmail);
        $statement->bindValue(':p_address1',$uAddress1);
        $statement->bindValue(':p_address2',$uAddress2);
        $statement->bindValue(':p_city_id',$uCity);
        $statement->bindValue(':p_id',$patient_id);

        /*
            executes the statement. if update fail an exception will be raised. 
            This would happen if the e-mail is changed to a one that already exists.
            If an exception is throw, then an error message will be generated and user is redirected to profile settings page
        */
        try{
            $statement->execute();
        }catch(PDOException $e){
            $outputMessage .= "The e-mail address you entered already exists. Changes were rolled back.<br>";
            header("Location: ./patientProfileSettings.php?outputMessage1=$outputMessage");
            exit();
        }
        $statement->closeCursor();

        //Generates success message and user is redirected to profile settings.
        $successMessage = "Your personal information has been successfully updated. All information is up-to-date.<br>";
        header("Location: ./patientProfileSettings.php?successMessage=$successMessage");
    }
?>
