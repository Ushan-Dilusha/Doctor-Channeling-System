<?php
/*
    Signs out the user from a patient account.
*/

//Checks whther the patient_id is set in $_SESSION superglobal variable.
    //if not set, then session_start is used to resume a prevous session (if the relevant cookie exists) otherwise a new session is created
    if(!isset($_SESSION['patient_id'])){
        session_start();
    }

    //again checks whether patient_id is set. If not, it means the user is not logged in, and redirects the user to sign-in page.
    if(!isset($_SESSION['patient_id'])){
        header('Location: ./signInPatients.php');
        exit();
    }

    //if user is logged in, then following code is used to log out the user.
    else{
        //this deletes the session cookies with the respective session ID on the server.
        $_SESSION = array();    //$_SESSION superglobal variable is set to an empty array
        session_destroy();  //removes the respective session ID from the server

        //deletes the session cookie from the user's browser
        $name = session_name();
        $parameters = session_get_cookie_params();  //gets name of session cookie of user
        $expire = strtotime('-1 year'); //Creates a session time that is 1 year in the past
        //gets path, domain, secure, httponly parameters of the session cookie
        $path = $parameters['path'];
        $domain = $parameters['domain'];
        $secure = $parameters['secure'];
        $httponly = $parameters['httponly'];
        //sets parameters of the session cookie. value parameter is set to an empty string and the expire parameter is set to 1 year in the past immediately expiring it 
        setcookie($name, '', $expire, $path, $domain, $secure, $httponly);
    }

    //redirects user to patient sign-in page.
    header('Location: ./signInPatients.php');
    exit();
?>