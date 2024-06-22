<?php

    $database= new mysqli("localhost","root","","Appointment dbms");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>