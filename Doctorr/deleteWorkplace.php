<?php
/*
    deletes a record from wokrplace table
*/

//checks if user is logged in. If not user is redirected to sign-in.
    if(!isset($_SESSION['doctor_id'])){
        session_start();
    }
    if(!isset($_SESSION['doctor_id'])){
        header('Location: ./signInDoctors.php');
        exit();
    }

    //recieves workplace id passed through hyperlink
    $workplaceId = filter_input(INPUT_GET,'workplaceId',FILTER_VALIDATE_INT);

    //initialises $outputMessage and $isValid
    $outputMessage = "";
    $isValid = TRUE;

    //checks if $workplaceId is a valid integer
    //if not $isValid is set to false and error message is generated
    if(empty($workplaceId) || $workplaceId == FALSE){
        $outputMessage .= "Invalid delete query. Please try again.<br>";
        $isValid = FALSE;
    }

    //if $isValid is false, user is redirected to doctor profile settings
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }

    //if $isValid is true, workplace record is deleted
    else{
        require('./database.php');

        //delete specific workplace from workplace table
        $query = 'delete from workplace where workplace_id = :wp_id;';
        $statement = $database->prepare($query);
        $statement->bindValue(':wp_id', $workplaceId);
        $statement->execute();
        $statement->closeCursor();

        //geenrates success message and redirects user to profile settings
        $successMessage = "Workplace record was successfully deleted. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
