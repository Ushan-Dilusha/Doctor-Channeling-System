<?php
/*
    creates search page.
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

    require('./database.php');

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
    </head>
    <body>
        <!--navigation bar-->
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
            <!-- Second navigation bar -->
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
        <div class="container-fluid">
            <div class="row align-items-center body-section">
                <div class="col">              
                    <div class="container-fluid">
                        <div class="row justify-content-center">
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
                                        <input type="text" id="searchName" name="searchName" class="form-control control-height"  placeholder="Name (optional)" onchange="strTrimmer(this);">
                                    </div>
                                    <!-- Specialization-->
                                    <div class="mb-2">
                                        <select class="form-select control-height" id="searchSpec" name="searchSpec" aria-label="">
                                        <option class="select-item" value="" selected>Select a specialization (optional)</option>
                                        <?php foreach($spec_table as $spec_record):?>
                                            <option class="select-item" value="<?php echo $spec_record['specialization_id']?>"><?php echo $spec_record['specialization_name']?></option>
                                        <?php endforeach?>
                                        </select>
                                    </div>
                                    <!-- City -->
                                    <div class="mb-2">
                                        <select class="form-select control-height" id="searchCity" name="searchCity" aria-label="">
                                        <option class="select-item" value="" selected>Select a city (optional)</option>
                                        <?php foreach($city_table as $city_record):?>
                                            <option class="select-item" value="<?php echo $city_record['city_id']?>"><?php echo $city_record['city_name']?></option>
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
    </body>
</html>
