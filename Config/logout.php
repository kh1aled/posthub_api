<?php
require "./Permissions.php";
require "./Constant.php";
//=======================================
//===============Connection=============
//=======================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if(session_status() === PHP_SESSION_ACTIVE){
    session_unset();
    session_destroy();
    
    // إرسال رد JSON في حالة تسجيل الخروج بنجاح
    echo json_encode(["status" => "success" , "message" => "logout successfully"]);
} else {
    // في حالة وجود مشكلة في الجلسة
    echo json_encode(["status" => "failed" , "message" => "Failed to end session"]);
}