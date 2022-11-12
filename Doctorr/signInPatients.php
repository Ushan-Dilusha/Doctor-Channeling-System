<!-- 
    Generates the sign-in page for patients.
 -->
<?php
    //Recieves any message sent by the sign-in script. 
    $outputMessage = filter_input(INPUT_GET,'outputMessage');
    
    //Creates the html statement to generate the message if outputMessage is set. Otherwise it's initialized to an empty string.
    if(!empty($outputMessage)){
        $outputMessage = "<p>" . $outputMessage . "</p>";
    }
    else{
        $outputMessage="";
    } 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <meta name="author" content="Janith Thilanka">
        <title>Doctorr | Patient | Sign-In</title>

        <!-- Links bootstrap CSS and JavaScript to the HTML document -->
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
        <script src="js/bootstrap.min.js"></script>

        <!-- Links custom CSS and JavaScript to HTML document -->
        <script src="formScript.js"></script>
        <link rel="stylesheet" type="text/css" href="formStyle.css">
    </head>
    <body>
        <!-- Aligns heading using bootstrap and custom CSS classes  -->
        <div class="row my-4 align-items-center body-section">
            <div class="col">
            <!-- Page heading -->
                <header>
                    <h1 class="text-center">Sign&hybull;In&nbsp;as&nbsp;a&nbsp;Patient <img src="images/logo2.png" alt="Doctorr Logo"></h1>
                </header>
                <!-- Aligns heading using bootstrap and custom CSS classes  -->
                <div class="container-fluid">
                    <div class="row justify-content-center">
                        <div class="col col-lg-5 col-md-6 col-sm-8 col-10">
                        <!-- Creates the sign-in form for patients -->
                            <form class="container-fluid border rounded-3 pt-5 pb-4 bg-light" action="patientSignInScript.php" method="POST">
                            <!--
                                Custom JavaScript functions are used for client side validation.
                            -->
                            <!-- Prints the output message styled with custom CSS -->    
                            <div class="error-msg">
                                    <div>
                                        <?php echo $outputMessage?>
                                    </div>
                                </div>
                                <!-- Email -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input type="email" id="uEmail" name="uEmail" class="form-control" placeholder="Your E-mail" required>
                                            <label for="uEmail">E-mail</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Password -->
                                <div class="row gx-2">
                                    <div class="col">
                                        <div class="form-floating mb-2">
                                            <input type="password" id="uPassword" name="uPassword" class="form-control" placeholder="Your Password" required>
                                            <label for="uPassword">Password</label>
                                        </div>
                                    </div>  
                                </div>       
                                <!-- Submit Button -->
                                <div class="row justify-content-end mb-3">
                                    <div class="col col-sm-5 col-6 text-end d-grid gap-2 mt-4">
                                        <input type="submit" class="btn btn-primary" value="Sign-In" onclick="displayInvalid(this.form);">
                                    </div>
                                </div>
                                <div class="row justify-content-end px-0">
                                    <div class="col-12 text-end">
                                <!-- Hyperlink to patient sign-up page -->
                                        <small>Want to create an account? <a href="signUpPatients.php">Sign&hybull;Up</a></small>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="container-fluid text-center">
                <!-- Hyperlink to doctor sign-up and sign-in pages -->
                    <p><strong>Or</strong> do you want to enter as a <strong>doctor </strong>? <a href="signUpDoctors.php">Sign&hybull;Up</a>&nbsp;|&nbsp;<a href="signInDoctors.php">Sign&hybull;In</a></p>
                </div>
            </div>
        </div>
    </body>
</html>
