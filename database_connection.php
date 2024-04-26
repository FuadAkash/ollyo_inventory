<?php
    function connect(){
        $dbHost= "localhost";
        $user= "root";
        $pass= "";
        $dbname="inventory";

        $connect= new mysqli($dbHost, $user, $pass, $dbname);
        return $connect;
    }

    // session_start(); // Remove this line

    function closeConnect($cn){
        $cn->close();
    }

    // Remove these lines

?>
