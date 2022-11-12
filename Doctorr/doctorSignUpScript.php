<?php
/*
    PHP script which will accept values from the doctor sign-up form.
    Performs server-side validation on the input values.
    After validation, the values are inserted into the database.
*/

    //Accepts values from doctor sign-up form. 
    $uName = filter_input(INPUT_POST, 'uName');
    $uGender = filter_input(INPUT_POST, 'uGender');
    $uEmail = filter_input(INPUT_POST, 'uEmail', FILTER_VALIDATE_EMAIL);
    $uPass = filter_input(INPUT_POST, 'uPassword');
    $uPassRep = filter_input(INPUT_POST, 'uPassRepeat');
    $uSpecialization = filter_input(INPUT_POST, 'doctorSpec',FILTER_VALIDATE_INT);

    //intializes the output message as an empty string and isValid as true.
    $outputMessage = "";
    $isValid = TRUE;

    /*
        Checks validity for each and every input value.
        If invalid, an output message is generated and isValid is set to false.
    */
    //Checks whether doctor name is empty.
    if (empty($uName)){
        $outputMessage .= "Please enter your name.<br>";
        $isValid = FALSE;
    }
    //Checks whether doctor gender is empty.
    if(empty($uGender)){
        $outputMessage .= "Please enter your gender.<br>";
        $isValid = FALSE;
    }
    //Checks whether doctor email is invalid or empty.
    if($uEmail === FALSE) {
        $outputMessage .= "Please enter a valid e-mail address.<br>";
        $isValid = FALSE;
    }
    //Checks whether the password is empty.
    if (empty($uPass)){
        $outputMessage .= "Please enter a password.<br>";
        $isValid = FALSE;
    }
    //Checks the minimum length requirement of the password.
    if(strlen($uPass) < 8){
        $outputMessage .= "Password must be at least 8 characters long.<br>";
        $isValid = FALSE;
    }
    //Checks whether password confirmation is empty.
    if (empty($uPassRep)){
        $outputMessage .= "Please re-enter your password.<br>";
        $isValid = FALSE;
    }
    //Checks whether the password is same as confirmation.
    if ($uPassRep !== $uPass) {
        $outputMessage .= "Passwords do not match. Please re-enter password.<br>";
        $isValid = FALSE;
    }
    //Checks whether a specialization is selected.
    if(empty($uSpecialization) || $uSpecialization == FALSE){
        $outputMessage .= "Please select your specializaion from the given list.<br>";
        $isValid = FALSE;
    }

    //if isValid is false, then user is redirected back to doctor sign-up page along with error message.
    if($isValid === FALSE){
        header("Location: ./signUpDoctors.php?outputMessage=$outputMessage");
        exit();
    }

    //if isValid is true, the values are inserted into the database.
    else{
        require('./database.php');
        $query = 'insert into doctors(doctor_name, doctor_gender, doctor_email, doctor_password, doctor_specialization_id) values (:d_name, :d_gender, :d_mail, :d_pass, :d_specialization_id);';
        $statement = $database->prepare($query);    //prepares query as a PDOStatement and passes to database server.

        //bind values to statement.
        $statement->bindValue(':d_name',$uName);
        $statement->bindValue(':d_gender',$uGender);
        $statement->bindValue(':d_mail',$uEmail);
        $statement->bindValue(':d_pass',$uPass);
        $statement->bindValue(':d_specialization_id',$uSpecialization);
        
        //executes statement. If the e-mail already exists, raises exception which will generate an error message redirects user to sign-up page.
        try{
            $statement->execute();
        }catch(PDOException $e){
            $outputMessage .= "The e-mail address you entered already exists. Try another one.<br>";
            header("Location: ./signUpDoctors.php?outputMessage=$outputMessage");
            exit();
        }
        //Terminates the connection to the database server.
        $statement->closeCursor();
        //Redirects user to sign-in page.
        header('Location: ./signInDoctors.php');
    }
?>
