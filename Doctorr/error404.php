<?php
/*
    generates error404 page. Error message from the database.php file will be recieved and displayed
*/

//Recieves error message and assigns to $error_message
    $error_message = filter_input(INPUT_GET,'errorMsg');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="author" content="Janith Thilanka">
        <title>404-Error: Page Not Found</title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> <!-- Adds bootstrap CSS to webpage -->
        <script src="js/bootstrap.min.js"></script> <!-- Add Bootstrap scripts to page -->
        <link rel="stylesheet" type="text/css" href="About_Us.css"> <!-- Links custom CSS style sheets to webpage -->
    </head>
    <body>
        <!-- Aligns page content -->
        <div class="container-fluid">
            <div class="row align-items-center workspace">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row px-5">
                                <div class="col">
                                    <!-- Page Heading -->
                                    <h1 class="display-1">404</h1>
                                </div>
                            </div>
                            <div class="row px-5">
                                <div class="col">
                                    <!-- Page text -->
                                    <p class="text-justify lead">
                                        <strong>An error occured while we were accessing your content:</strong><br>
                                        <!-- Prints error message -->
                                        <?php 
                                            echo $error_message;
                                        ?>
                                    </p>
                                    <div class="lead">
                                        <small><a href="index.html" class="btn btn-primary">Back&nbsp;to&nbsp;Home</a></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Resposive width adjustment -->
                        <div class="col col-lg-5 col-12 text-center">
                            <!-- image -->
                            <img src="images/errorsteth.jpg" class="img-fluid steth-logo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
