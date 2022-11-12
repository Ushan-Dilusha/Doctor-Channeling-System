<?php
/*
    PHP script which will accept values from the patient sign-up form.
    Performs server-side validation on the input values.
    After validation, the values are inserted into the database.
*/
    
    //Accepts values from patient sign-up form. 
    $uName = filter_input(INPUT_POST, 'uName');
    $uGender = filter_input(INPUT_POST, 'uGender');
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $uPass = filter_input(INPUT_POST, 'uPassword');
    $uPassRep = filter_input(INPUT_POST, 'uPassRepeat');
    $uAddress1 = filter_input(INPUT_POST, 'uAddress1');
    $uAddress2 = filter_input(INPUT_POST, 'uAddress2');
    $uCity = filter_input(INPUT_POST, 'uCity',FILTER_VALIDATE_INT);

    //intializes the output message as an empty string and isValid as true.
    $outputMessage = "";
    $isValid = TRUE;

    /*
        Checks validity for each and every input value.
        If invalid, an output message is generated and isValid is set to false.
    */
    if (empty($uName)){
        $outputMessage .= "Please enter your name.<br>";
        $isValid = FALSE;
    }
    if(empty($uGender)){
        $outputMessage .= "Please enter your gender.<br>";
        $isValid = FALSE;
    }
    if($uEmail === FALSE) {
        $outputMessage .= "Please enter a valid e-mail address.<br>";
        $isValid = FALSE;
    }
    if (empty($uPass)){
        $outputMessage .= "Please enter a password.<br>";
        $isValid = FALSE;
    }
    if(strlen($uPass) < 8){
        $outputMessage .= "Password must be at least 8 characters long.<br>";
        $isValid = FALSE;
    }
    if (empty($uPassRep)){
        $outputMessage .= "Please re-enter your password.<br>";
        $isValid = FALSE;
    }
    if ($uPassRep !== $uPass) {
        $outputMessage .= "Passwords do not match. Please re-enter password.<br>";
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

    //if isValid is false, then user is redirected back to patient sign-up page along with error message.
    if($isValid === FALSE){
        header("Location: ./signUpPatients.php?outputMessage=$outputMessage");
        exit();
    }

    //if isValid is true, the values are inserted into the database.
    else{
        require('./database.php');

        //Insert all values to patients table
        $query = 'insert into patients(patient_name, patient_gender, patient_email, patient_address1, patient_address2, patient_city_id, patient_password) values (:p_name, :p_gender, :p_mail, :p_address1, :p_address2, :p_city_id, :p_pass);';
        $statement = $database->prepare($query);
        $statement->bindValue(':p_name',$uName);
        $statement->bindValue(':p_gender',$uGender);
        $statement->bindValue(':p_mail',$uEmail);
        $statement->bindValue(':p_address1',$uAddress1);
        $statement->bindValue(':p_address2',$uAddress2);
        $statement->bindValue(':p_city_id',$uCity);
        $statement->bindValue(':p_pass',$uPass);

        /*
            the statement when executed will throw an exception, if the e-mail already exists.
            This is because patient-email attribute has a unique contraint.
            if an exception is thrown, a message is genterated, and patient is redirected to sign-up page
        */
        try{
            $statement->execute();
        }catch(PDOException $e){
            $outputMessage .= "The e-mail address you entered already exists. Try another one.<br>";
            header("Location: ./signUpPatients.php?outputMessage=$outputMessage");
            exit();
        }
        $statement->closeCursor();

        //redirects user to sign-in page
        header('Location: ./signInPatients.php');
    }
?>
