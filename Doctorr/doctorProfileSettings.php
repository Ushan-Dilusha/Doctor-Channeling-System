<?php
/*
    Generates the doctor profile settings page.
*/

//checks whether user is logged in. Otherwise redirects user to sign-in page.
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

//Gets any message passed to the page using a link
    $outputMessage = filter_input(INPUT_GET,'outputMessage');
    $successMessage = filter_input(INPUT_GET,'successMessage');

//Checks whether outputMessage is set, if not creates empty string variable
    if(!empty($outputMessage)){
        $outputMessage = "<p>" . $outputMessage . "</p>";
    }
    else{
        $outputMessage="";
    }

//Checks whether successMessage is set, if not creates empty string variable
    if(!empty($successMessage)){
        $successMessage = "<p>" . $successMessage . "</p>";
    }
    else{
        $successMessage="";
    }

    //Calls the database.php file. If an error occurs, script will terminates raising fatal error
    require('./database.php');

    //Retrives the record of the user from the doctor table as an associative array
    $query_init = 'select * from doctors where doctor_id = :d_id;';
    $statement_init = $database->prepare($query_init);
    $statement_init->bindValue(':d_id', $doctor_id);
    $statement_init->execute();
    $doctor_record = $statement_init->fetch();
    $statement_init->closeCursor();

    //Retrives all records from the specialization table as an associative array
    $query = "select * from specialization;";
    $statement_spec = $database->prepare($query);
    $statement_spec->execute();
    $spec_table = $statement_spec->fetchAll();
    $statement_spec->closeCursor();

    //Retrives all records from the city table as an associative array
    $query = "select * from city;";
    $statement_city = $database->prepare($query);
    $statement_city->execute();
    $city_table = $statement_city->fetchAll();
    $statement_city->closeCursor();

    //Retrives all records from the workplace type table as an associative array
    $query = "select * from workplace_type;";
    $statement_wp_type = $database->prepare($query);
    $statement_wp_type->execute();
    $wp_type_table = $statement_wp_type->fetchAll();
    $statement_wp_type->closeCursor();

    //Retrives all the workplace_ids and workplace_names of the user from the workplace table as an associative array
    $query = "select workplace_id, workplace_name from workplace where doctor_id = :d_id;";
    $statement_wp = $database->prepare($query);
    $statement_wp->bindValue(':d_id', $doctor_id);
    $statement_wp->execute();
    $wp_list_table = $statement_wp->fetchAll();
    $statement_wp->closeCursor();

    //Retrives all records from the day table as an associative array
    $query = "select * from day;";
    $statement_day = $database->prepare($query);
    $statement_day->execute();
    $day_table = $statement_day->fetchAll();
    $statement_day->closeCursor();

    //Retrives all workplace information of the user from the workplace table as an associative array
    $wp_result_query = 'select workplace_id, workplace_name, workplace_type_name, workplace_address1, workplace_address2, city_name from (select workplace_id, workplace_name, workplace_address1, workplace_address2,city_name, workplace_type_name, doctor_id from workplace inner join workplace_type on workplace.workplace_type_id = workplace_type.workplace_type_id inner join city on workplace.workplace_city_id = city.city_id) as table1 where doctor_id = :d_id;';
    $wp_result_statement = $database->prepare($wp_result_query);
    $wp_result_statement->bindValue(':d_id', $doctor_id);
    $wp_result_statement->execute();
    $wp_result_table = $wp_result_statement->fetchAll();
    $wp_result_statement->closeCursor();

    //Retrives all work schedule information of the user from the workplace table as an associative array
    $result_query = 'select table1.worktime_id, table1.day_name, table1.worktime_start, table1.worktime_end, table2.workplace_name from (select worktime_id, day.day_name, worktime_start, worktime_end, workplace_id from worktime inner join day on worktime.day_id = day.day_id) as table1 inner join (select workplace_name, workplace_id from workplace where doctor_id = :d_id) as table2 on table1.workplace_id = table2.workplace_id;';
    $result_statement = $database->prepare($result_query);
    $result_statement->bindValue(':d_id', $doctor_id);
    $result_statement->execute();
    $result_table = $result_statement->fetchAll();
    $result_statement->closeCursor();
?>
<!--
    The entire HTML document is divided into  forms, each with there own external scripts to process data.
    (In addition to the header containing two navigation bars)
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="HKIPP Kumarage">
        <title>Doctorr | Doctor Settings</title>

        <!-- Links bootstrap CSS and JavaScript to the HTML document -->
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>

        <!-- Links custom CSS and JavaScript to HTML document -->
        <link rel="stylesheet" type="text/css" href="patient-profile-settings.css">
        <script src="formScript.js"></script>
    </head>
    <body>
        <!--Creates the navigation bar-->
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
                            <a class="nav-link active" href="doctorProfileSettings.php">Profile Settings</a>
                        </li>
                    </ul>
                </div>
                </div>
            </nav>
        <!--creates the sub-navigation bar -->
            <nav class="container-fluid sub-navigation pb-1">
                <div class="row">
                    <div class="col col-xl-11 col-sm-10 col-9 text-truncate text-start">
                        <!-- Genrates the name of the user as per the doctor id in the session -->
                        <span>Doctor | <?php echo $doctor_record['doctor_name']?></span>
                    </div>
                    <div class="col col-xl-1 col-sm-2 col-3">
                        <a href="doctorSignOut.php">Sign-Out</a>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Form alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">

                            <!--Personal information form-->
                            <form  id="infoForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="doctorInfoScript.php" method="POST">
                                <!-- 
                                    inital values for all form controls are the users current values.
                                    Javascript functiosn are used for client side validation.                                    
                                -->
                                <div class="error-msg">
                                    <div>
                                        <?php echo $outputMessage?>
                                    </div>
                                </div>
                                <div class="success-msg">
                                    <div>
                                        <?php echo $successMessage?>
                                    </div>
                                </div>
                                <header>
                                    <div>
                                    <h1>Profile Settings&nbsp;<img src="images/logo2.png" alt="Doctorr Logo"></h1>
                                    <strong class="fieldHeader">Personal Information</strong>
                                    </div>
                                </header>
                                <hr>
                                <!-- Doctor Name -->
                                <div class="row mb-2">
                                    <div class="col col-12">
                                        <div class="input-group">
                                            <span class="input-group-text">Dr.</span>
                                            <input type="text" id="uName" name="uName" class="form-control control-height"  placeholder="Name" value="<?php echo $doctor_record['doctor_name']?>" onchange="strTrimmer(this);" onblur="invalidBorder(this);" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="email" id="uEmail" name="uEmail" class="form-control" placeholder="Your E-mail" value="<?php echo $doctor_record['doctor_email']?>" required onblur="invalidBorder(this);">
                                            <label for="uEmail">E-mail</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Speciaization -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <select id="doctorSpec" name="doctorSpec" class="form-select control-height mb-2" onblur="invalidBorder(this);" required>
                                            <!-- Generates a list of specializations table. Selected control is set to users current selection -->
                                            <?php foreach($spec_table as $spec_record):?>
                                                <?php if($spec_record['specialization_id'] === $doctor_record['doctor_specialization_id']):?>
                                                    <option class="select-item" value="<?php echo $spec_record['specialization_id']?>" selected><?php echo $spec_record['specialization_name']?></option>
                                                <?php else:?>
                                                    <option class="select-item" value="<?php echo $spec_record['specialization_id']?>"><?php echo $spec_record['specialization_name']?></option>
                                                <?php endif?>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <!--Short Description About The Doctor-->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="doctorSmallDesc" class="form-label">Briefly describe what you do:</label>
                                            <textarea class="form-control" id="doctorSmallDesc" name="doctorSmallDesc" rows="5" maxlength="300" placeholder="This information will be displayed in your search card. So keep it simple, professional, and attractive (300 characters max)." onchange="strTrimmer(this);"><?php echo $doctor_record['doctor_short_description']?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!--Long Description About The Doctor-->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="doctorLongDesc" class="form-label">Describe your career and achievements:</label>
                                            <textarea class="form-control" id="doctorLongDesc" name="doctorLongDesc" rows="20" maxlength="1000" placeholder="This information will be displayed in your Doctorr profile. Let patients know why you are the best doctor for them. Include any other information that you like (1000 characters max)." onchange="strTrimmer(this);"><?php echo $doctor_record['doctor_long_description']?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!--Form Upload Button-->
                                <div class="row mb-2 justify-content-end">
                                    <div class="col col-xl-5 col-md-6 col-sm-12 col-12 text-end d-grid gap-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update&nbsp;Information" onclick="validateForm(this.form);">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- form alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                        <!-- workplace information form -->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="doctorWorkScript.php" method="POST">
                                <header>
                                    <div>
                                        <strong class="fieldHeader">Workplace Information</strong>
                                    </div>
                                </header>
                                <hr>
                                <!-- Workplace Name -->
                                <div class="row mb-2">
                                    <div class="col col-12">
                                        <div class="form-floating">
                                            <input type="text" id="workplaceName" name="workplaceName" class="form-control" placeholder="Workplace Name" required onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="workplaceName" class="form-label">Workplace Name</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Workplace Addressline 1 -->
                                <div class="row mb-2 gx-2">
                                    <div class="col-xl-6 col-12">
                                        <div class="form-floating">
                                            <input type="text" id="workplaceAddress1" name="workplaceAddress1" class="form-control" placeholder="Address-line 1 (Optional)" onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="workplaceAddress1">Addressline 1 (Optional)</label>
                                        </div>
                                    </div>
                                <!-- Workplace Addressline 2 -->
                                    <div class="col-xl-6 col-12">
                                        <div class="form-floating">
                                            <input type="text" id="workplaceAddress2" name="workplaceAddress2" class="form-control" placeholder="Address-line 2 (Optional)" onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="workplaceAddress2">Addressline 2 (Optional)</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Workplace City -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <select id="workplaceCity" name="workplaceCity" class="form-select control-height" onblur="invalidBorder(this);" required>
                                            <option class="select-item" value="" disabled selected>Select Nearest City</option>
                                            <!-- Generates a list of cities table retireved from database -->
                                            <?php foreach($city_table as $city_record):?>
                                                <option class="select-item" value="<?php echo $city_record['city_id']?>"><?php echo $city_record['city_name']?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Workplace Type -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <select id="workplaceType" name="workplaceType" class="form-select control-height" onblur="invalidBorder(this);" required>
                                            <option class="select-item" value="" disabled selected>Select the type of Workplace</option>
                                            <!-- Generates a list of workplace types table retireved from database -->
                                            <?php foreach($wp_type_table as $wp_type_record):?>
                                                <option class="select-item" value="<?php echo $wp_type_record['workplace_type_id']?>"><?php echo $wp_type_record['workplace_type_name']?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <!--Form Upload Button-->
                                <div class="row mb-2 justify-content-end ">
                                    <div class="col col-xl-5 col-md-6 col-sm-12 col-12 text-end d-grid gap-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update&nbsp;Information" onclick="validateForm(this.form);">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                        <!-- Workplace table -->
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
                                            <th>
                                                Delete Record
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <!-- foreach loop which goes through all records of $result_table and displays them as records in a table in HTML -->
                                        <?php foreach($wp_result_table as $wp_result_record):?>
                                            <tr>
                                                <td class="text-nowrap">
                                                    <?php echo $wp_result_record['workplace_name']?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo $wp_result_record['workplace_type_name']?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <!-- Generates an address from available address1, address2, city values -->
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
                                                <td class="text-nowrap">
                                                    <!--Creates delete button for current record-->
                                                    <a href="deleteWorkplace.php?workplaceId=<?php echo $wp_result_record['workplace_id']?>" class="btn btn-primary">Delete</a>
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
        <!-- Form alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                            <!-- Work Schedule form -->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="doctorScheduleScript.php" method="POST">
                                <header>
                                    <div>
                                        <strong class="fieldHeader">Work Schedule Information</strong>
                                    </div>
                                </header>
                                <hr>
                                <!-- Workplace Name -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <select id="timePlaceName" name="timePlaceName" class="form-select control-height" onblur="invalidBorder(this);" required>
                                            <option class="select-item" value="" disabled selected>Select the Workplace Name</option>
                                            <!-- Generates a list of workplaces of the user table retireved from database -->
                                            <?php foreach($wp_list_table as $wp_list_record):?>
                                                <option class="select-item" value="<?php echo $wp_list_record['workplace_id']?>"><?php echo $wp_list_record['workplace_name']?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Day -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <select id="workDay" name="workDay" class="form-select control-height" onblur="invalidBorder(this);" required>
                                            <option class="select-item" value="" disabled selected>Select the Day of Work</option>
                                            <!-- Generates a list of days using the day table retireved from database -->
                                            <?php foreach($day_table as $day_record):?>
                                                <option class="select-item" value="<?php echo $day_record['day_id']?>"><?php echo $day_record['day_name']?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Workplace Start time -->
                                <div class="row">
                                    <label for="timeStart" class="col-4 col-form-label mb-2 text-end">Starts at:</label>
                                    <div class="col-8">
                                        <input type="time" id="timeStart" name="timeStart" class="form-control mb-2" onblur="invalidBorder(this);" required>
                                    </div>
                                <!-- Workplace End time -->
                                    <label for="timeEnd" class="col-4 col-form-label mb-2 text-end">Ends at:</label>
                                    <div class="col-8">
                                        <input type="time" id="timeEnd" name="timeEnd" class="form-control mb-2" onblur="invalidBorder(this);" required>
                                    </div>
                                </div>
                                <!--Form Upload Button-->
                                <div class="row mb-2 justify-content-end ">
                                    <div class="col col-xl-5 col-md-6 col-sm-12 col-12 text-end d-grid gap-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update&nbsp;Information" onclick="validateForm(this.form);">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table allignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                        <!-- Work Schedule table -->
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
                                            <th>
                                                Delete Record
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <!-- foreach loop which goes through all records of $result_table and displays them as records in a table in HTML -->
                                        <?php foreach($result_table as $result_record):?>
                                            <tr>
                                                <td class="text-nowrap">
                                                    <?php echo $result_record['workplace_name']?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo $result_record['day_name']?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php
                                                        $timeStart = date_create($result_record['worktime_start']); //Retrieved time is converted to date form
                                                        echo date_format($timeStart, "h:i A"); //Prints time according to the given format
                                                    ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php
                                                        $timeEnd = date_create($result_record['worktime_end']); //Retrieved time is converted to date form
                                                        echo date_format($timeEnd, "h:i A");    //Prints time according to the given format
                                                    ?>
                                                </td>
                                                <td class="text-nowrap">
                                                <!--Creates delete button for current record-->
                                                    <a href="deleteWorktime.php?worktimeId=<?php echo $result_record['worktime_id']?>" class="btn btn-primary">Delete</a>
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
        <!-- form alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                            <!-- Account Security Form -->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="doctorPassScript.php" method="POST">
                                <header>
                                    <div>
                                        <strong class="fieldHeader">Account Security</strong>
                                    </div>
                                </header>
                                <hr>
                                <!-- Password -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="password" id="uPassOld" name="uPassOld" class="form-control" placeholder="Current Password" required onblur="invalidBorder(this);">
                                            <label for="uPassOld">Current Password</label>
                                        </div>
                                    </div>
                                </div>
                                <!--New Password -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="password" id="uPassword" name="uPassword" class="form-control" minlength="8" placeholder="New Password" required onblur="invalidBorder(this);">
                                            <label for="uPass">New Password</label>
                                        </div>
                                    </div>
                                </div>
                                <!--New Password Repeat -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="password" id="uPassRepeat" name="uPassRepeat" class="form-control" placeholder="Re-enter Your Password" required onblur="invalidBorder(this);" oninput="clearInvalid(this);">
                                            <label for="uPassRepeat">Re-enter New Password</label>
                                        </div>
                                    </div>
                                </div>
                                <!--Form Upload Button-->
                                <div class="row mb-2 justify-content-end ">
                                    <div class="col col-xl-5 col-md-6 col-sm-12 col-12 text-end d-grid gap-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Update&nbsp;Information" onclick="validateForm(this.form);">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Form Alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                        <!--Delete Account Form-->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="doctorDeleteAccountScript.php" method="POST">
                                <!--Account security-->
                                <header>
                                    <div>
                                        <strong class="fieldHeader">Delete Your Account</strong>
                                    </div>
                                </header>
                                <hr>
                                <!-- Password -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="password" id="uPasswordDel" name="uPasswordDel" class="form-control" placeholder="Current Password" required onblur="invalidBorder(this);">
                                            <label for="uPasswordDel">Password</label>
                                        </div>
                                    </div>
                                </div>
                                <!--Form Upload Button-->
                                <div class="row mb-2 justify-content-end">
                                    <div class="col col-xl-5 col-md-6 col-sm-12 col-12 text-end d-grid gap-2 mb-2">
                                        <input type="submit" class="btn btn-primary" value="Delete&nbsp;Account" onclick="validateForm(this.form);">
                                    </div>
                                </div> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
