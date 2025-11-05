<?php 
require "./Permissions.php";
require "./Constant.php";

//================================================================
//==========================connect to server=====================
//================================================================

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}