<?php
/* 
    adds a record to worktime table.
*/

//Checks if user is logged in. If not, user is redirected to sign-in page.
    if(!isset($_SESSION['doctor_id'])){
        session_start();
    }
    if(!isset($_SESSION['doctor_id'])){
        header('Location: ./signInDoctors.php');
        exit();
    }
    else{
        //$doctor_id is assigned with users ID in the $_SESSION superglobal variable.
        $doctor_id = $_SESSION['doctor_id'];
    }

    //gets input values from work schedule form
    $workplaceId = filter_input(INPUT_POST, 'timePlaceName',FILTER_VALIDATE_INT);
    $timeStart = filter_input(INPUT_POST, 'timeStart');
    $timeEnd = filter_input(INPUT_POST, 'timeEnd');
    $workDayId = filter_input(INPUT_POST, 'workDay',FILTER_VALIDATE_INT);

    //initializes $outputMessage and $isValid
    $outputMessage = "";
    $isValid = TRUE;

    //performs server side validation
    if(empty($workplaceId) || $workplaceId == FALSE){
        $outputMessage .= "Please select a workplace from the given list.<br>";
        $isValid = FALSE;
    }
    if(empty($timeStart)){
        $outputMessage .= "Please enter the time at which you start working.<br>";
        $isValid = FALSE;
    }
    if(empty($timeEnd)){
        $outputMessage .= "Please enter the time at which you end working.<br>";
        $isValid = FALSE;
    }
    if(empty($workDayId) || $workDayId == FALSE){
        $outputMessage .= "Please select the day of work from the given list.<br>";
        $isValid = FALSE;
    }
    //if $isValid is false, user is redirected to profile settings
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    //otherwise, values are inserted to database
    else{
        require('./database.php');

        //values are inserted to worktime table of database
        $query = 'insert into worktime(day_id, worktime_start, worktime_end, workplace_id) values (:day_id, :wt_start, :wt_end, :wp_id);';
        $statement = $database->prepare($query);
        $statement->bindValue(':day_id',$workDayId);
        $statement->bindValue(':wt_start',$timeStart);
        $statement->bindValue(':wt_end',$timeEnd);
        $statement->bindValue(':wp_id',$workplaceId);
        $statement->execute();
        $statement->closeCursor();

        //success message is generated and user is redirected to profile settings
        $successMessage = "Your work schedule information was successfully added. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
