<?php
/*
    creates search results page.
*/
//checks whether user is logged in.
//if not signed in, user is redirected to sign in page
if(!isset($_SESSION['patient_id'])){
        session_start();
    }
    if(!isset($_SESSION['patient_id'])){
        header('Location: ./signInPatients.php');
        exit();
    }
    else{
        $patient_id = $_SESSION['patient_id'];
    }
    //gets input data passed from the seach form. (in search page or search results page)
    $search_name = $search_name_out = filter_input(INPUT_GET,'searchName');
    $search_spec_id = filter_input(INPUT_GET,'searchSpec',FILTER_VALIDATE_INT);
    $search_city_id = filter_input(INPUT_GET,'searchCity',FILTER_VALIDATE_INT);

    require('./database.php');

    /* 
        there are four possible combinations for $search name and $search_spec_id.
        for each of these combinations a different query must be prepared, bind values and executed.
        This is done in the following if-else if structure.

        simillarly there are two possible states for the $search_city_id
        therefore two seperate queries must be executed for each state
    */
    if(empty($search_name) && (empty($search_spec_id) || $search_spec_id === FALSE)){
        //retrieves information from all doctors of all specializations
        $query_doctor_table = "select table1.doctor_id, table1.doctor_name, table1.doctor_short_description, table2.specialization_name from (select * from doctors) as table1 inner join specialization as table2 on table1.doctor_specialization_id = table2.specialization_id";
        $statement_doctor_table = $database->prepare($query_doctor_table);
         $statement_doctor_table->execute();
         $doctor_table = $statement_doctor_table->fetchAll();
         $statement_doctor_table->closeCursor();
    }
    else if(empty($search_name) && !(empty($search_spec_id) || $search_spec_id === FALSE)){
        //retrieves information from all doctors of given specializations
        $query_doctor_table = "select table1.doctor_id, table1.doctor_name, table1.doctor_short_description, table2.specialization_name from (select * from doctors) as table1 inner join (select * from specialization where specialization_id = :search_spec_id) as table2 on table1.doctor_specialization_id = table2.specialization_id";
        $statement_doctor_table = $database->prepare($query_doctor_table);
        $statement_doctor_table->bindValue(':search_spec_id',$search_spec_id);
        $statement_doctor_table->execute();
        $doctor_table = $statement_doctor_table->fetchAll();
        $statement_doctor_table->closeCursor();
    }
    else if(!empty($search_name) && (empty($search_spec_id) || $search_spec_id === FALSE)){
        //retrieves information from given doctors of all specializations
        $search_name = "%$search_name%";
        $query_doctor_table = "select table1.doctor_id, table1.doctor_name, table1.doctor_short_description, table2.specialization_name from (select * from doctors where doctor_name like :search_d_name) as table1 inner join specialization as table2 on table1.doctor_specialization_id = table2.specialization_id";
        $statement_doctor_table = $database->prepare($query_doctor_table);
        $statement_doctor_table->bindValue(':search_d_name',$search_name);
        $statement_doctor_table->execute();
        $doctor_table = $statement_doctor_table->fetchAll();
        $statement_doctor_table->closeCursor();
    }
    else if(!empty($search_name) && (!empty($search_spec_id) || $search_spec_id === FALSE)){
        //retrieves information from given doctors of given specializations
        $search_name = "%$search_name%";
        $query_doctor_table = "select table1.doctor_id, table1.doctor_name, table1.doctor_short_description, table2.specialization_name from (select * from doctors where doctor_name like :search_d_name) as table1 inner join (select * from specialization where specialization_id = :search_spec_id) as table2 on table1.doctor_specialization_id = table2.specialization_id";
        $statement_doctor_table = $database->prepare($query_doctor_table);
        $statement_doctor_table->bindValue(':search_d_name',$search_name);
        $statement_doctor_table->bindValue(':search_spec_id',$search_spec_id);
        $statement_doctor_table->execute();
        $doctor_table = $statement_doctor_table->fetchAll();
        $statement_doctor_table->closeCursor();
    }

    if(empty($search_city_id) || $search_city_id == FALSE){
        //retieves information from all cities
        $query_work_table = "select distinct table3.doctor_id, table4.city_name from (select * from workplace) as table3 inner join (select * from city) as table4 on table3.workplace_city_id = table4.city_id";
        $statement_work_table = $database->prepare($query_work_table);
        $statement_work_table->execute();
        $work_table = $statement_work_table->fetchAll();
        $statement_work_table->closeCursor();

        $work_card_table = $work_table;
    }
    else if(!(empty($search_city_id) || $search_city_id == FALSE)){
        //retieves information from given cities
        $query_work_table = "select distinct table3.doctor_id, table4.city_name from (select * from workplace) as table3 inner join (select * from city where city_id = :search_c_id) as table4 on table3.workplace_city_id = table4.city_id";
        $statement_work_table = $database->prepare($query_work_table);
        $statement_work_table->bindValue(':search_c_id',$search_city_id);
        $statement_work_table->execute();
        $work_table = $statement_work_table->fetchAll();
        $statement_work_table->closeCursor();

        $query_work_card_table = "select distinct table3.doctor_id, table4.city_name from (select * from workplace) as table3 inner join (select * from city) as table4 on table3.workplace_city_id = table4.city_id";
        $statement_work_card_table = $database->prepare($query_work_card_table);
        $statement_work_card_table->execute();
        $work_card_table = $statement_work_card_table->fetchAll();
        $statement_work_card_table->closeCursor();
    }
//eleminates all records in $doctor_table array, that do not match the $work_table array
    foreach($doctor_table as $key=>$doctor_record){
        $checkRelationship = FALSE;
        foreach($work_table as $work_record){
            if($doctor_record['doctor_id'] === $work_record['doctor_id']){
                $checkRelationship = TRUE;
            }
        }
        if($checkRelationship === FALSE){
            unset($doctor_table[$key]);
        }
    }
    //retrieves name of the user (patient)
    $query_init = 'select patient_name from patients where patient_id = :p_id;';
    $statement_init = $database->prepare($query_init);
    $statement_init->bindValue(':p_id', $patient_id);
    $statement_init->execute();
    $patient_record = $statement_init->fetch();
    $statement_init->closeCursor();

    //retireves all record from specialization
    $query_spec = "select * from specialization;";
    $statement_spec = $database->prepare($query_spec);
    $statement_spec->execute();
    $spec_table = $statement_spec->fetchAll();
    $statement_spec->closeCursor();

    //retireves all records from city
    $query_city = "select * from city;";
    $statement_city = $database->prepare($query_city);
    $statement_city->execute();
    $city_table = $statement_city->fetchAll();
    $statement_city->closeCursor();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="HKIPP Kumarage">
        <title>Doctorr | Find a Doctor</title>

        <!-- Links bootstrap CSS and JavaScript to the HTML document -->
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>

        <!-- Links custom CSS and JavaScript to HTML document -->
        <link rel="stylesheet" type="text/css" href="patient-profile-settings.css">
        <script src="formScript.js"></script>
        <script>
            //function to scroll to a paticular element
            function scrolltoElement(){
            var elmnt = document.getElementById('searchBox');
            elmnt.scrollIntoView();
            }
        </script>
    </head>
    <body onload="scrolltoElement();">
        <!-- navigation bar -->
        <div class="fixed-top">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
                <div class="container-fluid">
                <a class="navbar-brand" href="index.html"><img src="images/logo.png" alt="Doctorr Logo" class="logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="search.php">Search for a Doctor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="patientProfileSettings.php">Profile Settings</a>
                        </li>
                    </ul>
                </div>
                </div>
            </nav>
            <!-- Sub-navigation bar -->
            <nav class="container-fluid sub-navigation pb-1">
                <div class="row">
                    <div class="col col-xl-11 col-sm-10 col-9 text-truncate text-start">
                        <span>Patient | <?php echo $patient_record['patient_name']?></span>
                    </div>
                    <div class="col col-xl-1 col-sm-2 col-3">
                        <a href="patientSignOut.php">Sign-Out</a>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Search form -->
        <div class="container-fluid my-5">
            <div class="row align-items-center">
                <div class="col">              
                    <div class="container-fluid">
                        <div class="row justify-content-center mt-5">
                            <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                            <form class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="searchResults.php" method="GET">
                                    <header>
                                        <div>
                                            <h1>Find a Doctor&nbsp;<img src="images/logo2.png" alt="Doctorr Logo"></h1>
                                        </div>
                                    </header>
                                    <hr>
                                    <!-- Name -->
                                    <div class="input-group mb-2">
                                        <span class="input-group-text control-height">Dr.</span>
                                        <input type="text" id="searchName" name="searchName" class="form-control control-height"  placeholder="Name (optional)" value="<?php echo $search_name_out?>" onchange="strTrimmer(this);">
                                    </div>
                                    <!-- Specialization-->
                                    <div class="mb-2">
                                        <select class="form-select control-height" id="searchSpec" name="searchSpec" aria-label="">
                                        <option class="select-item" value="">Select a specialization (optional)</option>
                                        <?php foreach($spec_table as $spec_record):?>
                                            <?php if($search_spec_id == $spec_record['specialization_id']):?>
                                                <option class="select-item" value="<?php echo $spec_record['specialization_id']?>" selected><?php echo $spec_record['specialization_name']?></option>
                                            <?php else:?>
                                                <option class="select-item" value="<?php echo $spec_record['specialization_id']?>"><?php echo $spec_record['specialization_name']?></option>
                                            <?php endif?>    
                                        <?php endforeach?>
                                        </select>
                                    </div>
                                    <!-- City -->
                                    <div class="mb-2">
                                        <select class="form-select control-height" id="searchCity" name="searchCity" aria-label="">
                                        <option class="select-item" value="">Select a city (optional)</option>
                                        <?php foreach($city_table as $city_record):?>
                                            <?php if($search_city_id == $city_record['city_id']):?>
                                                <option class="select-item" value="<?php echo $city_record['city_id']?>" selected><?php echo $city_record['city_name']?></option>
                                            <?php else:?>
                                                <option class="select-item" value="<?php echo $city_record['city_id']?>"><?php echo $city_record['city_name']?></option>
                                            <?php endif?> 
                                        <?php endforeach?>
                                        </select>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="row justify-content-end mb-3">
                                        <div class="col col-xl-5 col-sm-6 col-8 text-end d-grid gap-2 mt-4">
                                            <input type="submit" class="btn btn-primary" value="Search">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Search Box -->
        <div class="container pb-5">
            <div class="row justify-content-center">
                <div id="searchBox" class="col col-10 bg-light body-section-2 border rounded px-4 pt-4">

                <?php foreach ($doctor_table as $doctor_record):?>
                    <!-- Generates Search cards for each doctor -->
                    <div class="row justify-content-center pb-4">
                        <div class="card col col-md-10">
                            <div class="card-body">
                            <h5 class="card-title">Dr. <?php echo $doctor_record['doctor_name']?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $doctor_record['specialization_name']?></h6>
                            <p class="lead card-text">
                                <span>Works at:</span><br>
                                <em>
                                    <!-- Generates a list of cities where specific doctor works in -->
                                    <?php
                                        $output_str = "";
                                        foreach($work_card_table as $work_card_record){
                                            if($work_card_record['doctor_id'] == $doctor_record['doctor_id']){
                                                $output_str .= $work_card_record['city_name'] . ", ";
                                            }
                                        }
                                        $output_str = substr($output_str, 0, -2);
                                        echo $output_str;
                                    ?>
                                </em>
                                </p>
                            <p class="card-text">
                            <?php echo $doctor_record['doctor_short_description']?>
                            </p>
                            <!-- Generates hyperlink to specific doctor's doctorr profile -->
                            <div class="text-end"><a href="workProfile.php?d_id=<?php echo $doctor_record['doctor_id']?>" class="btn btn-primary">View Doctorr</a></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach?>
                </div>
            </div>
        </div>
    </body>
</html>
