<?php
/*
    deletes a record from worktime table
*/

//checks if user is logged in. If not user is redirected to sign-in.
    if(!isset($_SESSION['doctor_id'])){
        session_start();
    }
    if(!isset($_SESSION['doctor_id'])){
        header('Location: ./signInDoctors.php');
        exit();
    }

    //recieves work time id passed through hyperlink
    $worktimeId = filter_input(INPUT_GET,'worktimeId',FILTER_VALIDATE_INT);

    //initialises $outputMessage and $isValid
    $outputMessage = "";
    $isValid = TRUE;

    //checks if $worktimeId is a valid integer
    //if not $isValid is set to false and error message is generated
    if(empty($worktimeId) || $worktimeId == FALSE){
        $outputMessage .= "Invalid delete query. Please try again.<br>";
        $isValid = FALSE;
    }

    //if $isValid is false, user is redirected to doctor profile settings
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }

    //if $isValid is true, worktime record is deleted
    else{
        require('./database.php');

        //delete specific worktime from workplace table
        $query = 'delete from worktime where worktime_id = :wt_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':wt_id', $worktimeId);
        $statement->execute();
        $statement->closeCursor();

        ////geenrates success message and redirects user to profile settings
        $successMessage = "Worktime record was successfully deleted. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
