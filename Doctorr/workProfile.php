<?php
/*
    creates Doctorr Profile.
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

//gets doctor_id from hyperlink
$doctor_id = filter_input(INPUT_GET,'d_id',FILTER_VALIDATE_INT);

//if doctor_id is not a valid integer, user is redirected to search page
if(empty($doctor_id) || $doctor_id == FALSE){
    header('Location: ./search.php');
    exit();
}

require('./database.php');

//retrieves name of patient(user)
$query_init = 'select patient_name from patients where patient_id = :p_id;';
$statement_init = $database->prepare($query_init);
$statement_init->bindValue(':p_id', $patient_id);
$statement_init->execute();
$patient_record = $statement_init->fetch();
$statement_init->closeCursor();

//retrieves workplace information of the doctor
$wp_result_query = 'select workplace_name, workplace_type_name, workplace_address1, workplace_address2, city_name from (select workplace_id, workplace_name, workplace_address1, workplace_address2,city_name, workplace_type_name, doctor_id from workplace inner join workplace_type on workplace.workplace_type_id = workplace_type.workplace_type_id inner join city on workplace.workplace_city_id = city.city_id) as table1 where doctor_id = :d_id;';
$wp_result_statement = $database->prepare($wp_result_query);
$wp_result_statement->bindValue(':d_id', $doctor_id);
$wp_result_statement->execute();
$wp_result_table = $wp_result_statement->fetchAll();
$wp_result_statement->closeCursor();

//retrieves work schedule information of the doctor
$result_query = 'select table1.day_name, table1.worktime_start, table1.worktime_end, table2.workplace_name from (select worktime_id, day.day_name, worktime_start, worktime_end, workplace_id from worktime inner join day on worktime.day_id = day.day_id) as table1 inner join (select workplace_name, workplace_id from workplace where doctor_id = :d_id) as table2 on table1.workplace_id = table2.workplace_id;';
$result_statement = $database->prepare($result_query);
$result_statement->bindValue(':d_id', $doctor_id);
$result_statement->execute();
$result_table = $result_statement->fetchAll();
$result_statement->closeCursor();

//retrieves personal information of the doctor
$info_query = 'select table1.doctor_name, table1.doctor_gender, table1.doctor_long_description, specialization.specialization_name from (select * from doctors where doctor_id = :d_id) as table1 inner join specialization on table1.doctor_specialization_id = specialization.specialization_id';
$info_statement = $database->prepare($info_query);
$info_statement->bindValue(':d_id', $doctor_id);
$info_statement->execute();
$info_record = $info_statement->fetch();
$info_statement->closeCursor();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="HKIPP Kumarage">
        <!-- Generates title of the page -->
        <title>Doctorr | Dr. <?php echo $info_record['doctor_name']?></title>

        <!-- Links bootstrap CSS and JavaScript to the HTML document -->
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>

        <!-- Links custom CSS to HTML document -->
        <link rel="stylesheet" type="text/css" href="patient-profile-settings.css">
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
            <!-- Sub-navigation bar -->
            <nav class="container-fluid sub-navigation pb-1">
                <div class="row">
                    <div class="col col-xl-11 col-sm-10 col-9 text-truncate text-start">
                    <!-- Prints patient name -->
                        <span>Patient | <?php echo $patient_record['patient_name']?></span>
                    </div>
                    <div class="col col-xl-1 col-sm-2 col-3">
                        <a href="patientSignOut.php">Sign-Out</a>
                    </div>
                </div>
            </nav>
        </div>
        <div class="container-fluid pt-5 mt-5 mb-3">
            <div class="row mt-2">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col text-center">
                                <!-- Prints doctor name -->
                                    <h1 class="display-1 card-title">Dr. <?php echo $info_record['doctor_name']?></h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-center col">
                                <!-- Prints doctor specializaiton -->
                                    <h2 class="display-5 card-subtitle mb-2 text-muted"><?php echo $info_record['specialization_name']?></h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <h4 class="card-subtitle text-muted">
                                    <!-- Prints doctor gender -->
                                        <?php 
                                            if($info_record['doctor_gender'] === 'M'){
                                                echo 'Male';
                                            }
                                            else if($info_record['doctor_gender'] === 'F'){
                                                echo 'Female';
                                            }
                                        ?>
                                    </h4>
                                </div>
                            </div>
                            <div class="row justify-content-center mt-3">
                                <div class="col-8">
                                <!-- Prints doctor long description -->
                                <p class="card-text lead"><?php echo $info_record['doctor_long_description']?></p>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                                    <!--doctor workplaces -->
                                    <div class="table-responsive">
                                        <table class="table table-hover mt-5 pt-5 pb-4 align-middle">
                                        <caption class="caption-top">Places of Work</caption>
                                            <thead class="table-light">
                                                <tr>
                                                    <th>
                                                        Workplace Name
                                                    </th>
                                                    <th>
                                                        Workplace Type
                                                    </th>
                                                    <th>
                                                        Workplace Address
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <!-- Generates the workplace table of doctor -->
                                                <?php foreach($wp_result_table as $wp_result_record):?>
                                                    <tr>
                                                        <td class="text-nowrap">
                                                            <?php echo $wp_result_record['workplace_name']?>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <?php echo $wp_result_record['workplace_type_name']?>
                                                        </td>
                                                        <td class="text-nowrap">
                                                        <!-- Generates address for each and every workplace -->
                                                            <?php
                                                            $address1 = $wp_result_record['workplace_address1'];
                                                            $address2 = $wp_result_record['workplace_address2'];
                                                                if (empty($address1)){
                                                                    $address1 = "";
                                                                }
                                                                else{
                                                                    $address1 .= "<br>";
                                                                }
                                                                if (empty($address2)){
                                                                    $address2 = "";
                                                                }
                                                                else{
                                                                    $address2 .= "<br>";
                                                                }
                                                                $city = $wp_result_record['city_name'];

                                                                echo $address1;
                                                                echo $address2;
                                                                echo $city;
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                                <!-- doctor workschedule -->
                                    <div class="table-responsive">
                                        <table class="table table-hover mt-5 pt-5 pb-4 align-middle">
                                            <caption class="caption-top">Work Schedule</caption>
                                            <thead class="table-light">
                                                <tr>
                                                    <th>
                                                        Workplace Name
                                                    </th>
                                                    <th>
                                                        Day of Work
                                                    </th>
                                                    <th>
                                                        Starting time
                                                    </th>
                                                    <th>
                                                        Ending time
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <!-- Prints the entire work schedule of doctor -->
                                                <?php foreach($result_table as $result_record):?>
                                                    <tr>
                                                        <td class="text-nowrap">
                                                            <?php echo $result_record['workplace_name']?>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <?php echo $result_record['day_name']?>
                                                        </td>
                                                        <td class="text-nowrap">
                                                            <!-- Prints start time -->
                                                            <?php
                                                                $timeStart = date_create($result_record['worktime_start']); //creates retrieved time as a date value
                                                                echo date_format($timeStart, "h:i A");  //formats date value as specified
                                                            ?>
                                                        </td>
                                                        <td class="text-nowrap">
                                                        <!-- Prints end time -->
                                                            <?php
                                                                $timeEnd = date_create($result_record['worktime_end']); //creates retrieved time as a date value
                                                                echo date_format($timeEnd, "h:i A");  //formats date value as specified
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div>
    </body>
</html>
