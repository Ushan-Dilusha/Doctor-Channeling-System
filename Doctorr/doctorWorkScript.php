<?php
/*
    adds a record to workplace table.
*/

//Checks if user is still logged in. Otherwise redirects user to sign-in page, otherwise retrieves users ID.
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

//Gets input values from the workplace form in the doctor profile settings
    $workplaceName = filter_input(INPUT_POST, 'workplaceName');
    $workplaceAddress1 = filter_input(INPUT_POST, 'workplaceAddress1');
    $workplaceAddress2 = filter_input(INPUT_POST, 'workplaceAddress2');
    $workplaceCity = filter_input(INPUT_POST, 'workplaceCity',FILTER_VALIDATE_INT);
    $workplaceType = filter_input(INPUT_POST, 'workplaceType',FILTER_VALIDATE_INT);

//initalizes outputMessage and isValid
    $outputMessage = "";
    $isValid = TRUE;

    //Server side validation, checks where any variable is empty. Sets is valid to false if that is true and generates error message.
    if (empty($workplaceName)){
        $outputMessage .= "Please enter the name of your workplace.<br>";
        $isValid = FALSE;
    }
    if(empty($workplaceCity) || $workplaceCity == FALSE){
        $outputMessage .= "Please select a city from the given list.<br>";
        $isValid = FALSE;
    }
    if(empty($workplaceType) || $workplaceType == FALSE){
        $outputMessage .= "Please select the type of workplace from the given list.<br>";
        $isValid = FALSE;
    }
    //if isValid is false redirects user to profile settings with an error message.
    if($isValid === FALSE){
        header("Location: ./doctorProfileSettings.php?outputMessage=$outputMessage");
        exit();
    }
    //Inserts values to database of isValid is true
    else{
        require('./database.php');

        //insert values to workplace table into the database
        $query = 'insert into workplace (workplace_name, workplace_type_id, workplace_address1, workplace_address2, workplace_city_id, doctor_id) values (:wp_name, :wp_type_id, :wp_add1, :wp_add2, :wp_city_id, :d_id);';
        $statement = $database->prepare($query);
        $statement->bindValue(':wp_name',$workplaceName);
        $statement->bindValue(':wp_type_id',$workplaceType);
        $statement->bindValue(':wp_add1',$workplaceAddress1);
        $statement->bindValue(':wp_add2',$workplaceAddress2);
        $statement->bindValue(':wp_city_id',$workplaceCity);
        $statement->bindValue(':d_id',$doctor_id);
        $statement->execute();
        $statement->closeCursor();

        //redirects user to profile settings with message of success
        $successMessage = "Your workplace information was successfully added. All information is up-to-date.<br>";
        header("Location: ./doctorProfileSettings.php?successMessage=$successMessage");
    }
?>
