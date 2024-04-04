<?php

session_start();

    if (isset($_SESSION['ffff'])){
        $_SESSION['ffff']=array();

        session_destroy();

        header("Location: ../");

    }else{
        header("Location: ../login.php");
    }



?>