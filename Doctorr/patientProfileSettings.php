<?php
/*
    Generates the patient profile settings page.
*/

//checks whether user is logged in. Otherwise redirects user to sign-in page.
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

    //Gets any message passed to the page using a link
    $outputMessage = filter_input(INPUT_GET,'outputMessage');
    $successMessage = filter_input(INPUT_GET,'successMessage');

//Checks whether outputMessage1 is set, if not creates empty string variable
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

    //Retrives all records from the city table as an associative array
    $query = 'select * from city;';
    $statement = $database->prepare($query);
    $statement->execute();
    $city_table = $statement->fetchAll();
    $statement->closeCursor();

    //Retrives the record of the user from the patient table as an associative array
    $query_init = 'select * from patients where patient_id = :p_id;';
    $statement_init = $database->prepare($query_init);
    $statement_init->bindValue(':p_id', $patient_id);
    $statement_init->execute();
    $patient_record = $statement_init->fetch();
    $statement_init->closeCursor();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="HKIPP Kumarage">
        <title>Doctorr | Patient Settings</title>

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
                            <a class="nav-link" href="search.php">Search for a Doctor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="patientProfileSettings.php">Profile Settings</a>
                        </li>
                    </ul>
                </div>
                </div>
            </nav>
            <!--creates the sub-navigation bar -->
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
        <!--Aligns form-->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">

                            <!--Personal information form-->
                            <form  id="infoForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="patientInfoScript.php" method="POST">
                                <!-- 
                                    inital values for all form controls are the users current values.
                                    Javascript functions are used for client side validation.                                    
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
                                <!-- Name -->
                                <div class="row mb-2">
                                    <div class="col col-12">
                                        <div class="form-floating">
                                            <input type="text" id="uName" name="uName" class="form-control" placeholder="Your Name" value="<?php echo $patient_record['patient_name']?>" required onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="uName" class="form-label">Name</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="email" id="uEmail" name="uEmail" class="form-control" placeholder="Your E-mail" value="<?php echo $patient_record['patient_email']?>" required onblur="invalidBorder(this);">
                                            <label for="uEmail">E-mail</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Addressline 1 -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" id="uAddress1" name="uAddress1" class="form-control" placeholder="Address-line 1" value="<?php echo $patient_record['patient_address1']?>" required onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="uAddress1">Address-line 1</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Addressline 2 -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="text" id="uAddress2" name="uAddress2" class="form-control" placeholder="Address-line 2 (Optional)" value="<?php echo $patient_record['patient_address2']?>" onchange="strTrimmer(this);" onblur="invalidBorder(this);">
                                            <label for="uAddress2">Address-line 2 (Optional)</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- City -->
                                <div class="row mb-4">
                                    <div class="col">
                                        <select id="uCity" name="uCity" class="form-select control-height" onblur="invalidBorder(this);" required>
                                        <option class="select-item" value="" disabled selected>Select Nearest City</option>
                                            <!-- Generates a list of cities from the city table. Selected control is set to users current selection -->
                                        <?php foreach($city_table as $city_record):?>
                                            <?php if($city_record['city_id'] === $patient_record['patient_city_id']):?>
                                                <option class="select-item" value="<?php echo $city_record['city_id']?>" selected><?php echo $city_record['city_name']?></option>
                                            <?php else:?>
                                                <option class="select-item" value="<?php echo $city_record['city_id']?>"><?php echo $city_record['city_name']?></option>
                                            <?php endif?>
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
        <!-- Form alignment -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                            <!-- Account Security form -->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="patientPassScript.php" method="POST">
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
        <!-- Alignment of Form -->
        <div class="row align-items-center">
            <div class="col">              
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                            <!-- Delete Account Form -->
                            <form id="passForm" class="container-fluid border rounded-3 mt-5 pt-5 pb-4 bg-light" action="patientDeleteAccountScript.php" method="POST">
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
                                <div class="row mb-2 justify-content-end ">
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
