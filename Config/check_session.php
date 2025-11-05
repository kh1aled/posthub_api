<?php
require "./Permissions.php";
require "./Constant.php";

//=======================================
//===============Connection=============
//=======================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

session_start();

header('Content-Type: application/json'); 

if (isset($_SESSION["id"])) {
    echo json_encode([
        "loggedIn" => true,
        "user" => [
            "id" => $_SESSION["id"],
            "username" => $_SESSION["username"],
            "avatar" => $_SESSION["avatar"],
            "isadmin" => $_SESSION["isadmin"],
            "fname" => $_SESSION["fname"],
            "lname" => $_SESSION["lname"],
            "email" => $_SESSION["email"]
        ]
    ]);
} else {
    echo json_encode(["loggedIn" => false]);
}
