<?php
require "./Permissions.php";
require "./Constant.php";

//================================================================
//==========================connect to server=====================
//================================================================

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($connect->connect_error){
    die("Connection failed: " . $connect->connect_error);
}

//================================================================
//==========================GET DATA==============================
//================================================================
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
    //======================================
    //=========add data to variables =======
    //======================================
    $id = $connect->real_escape_string($data['id']);

    //======================================
    //=========fetch image path=============
    //======================================
    $stmt = $connect->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // حذف الصورة من السيرفر إذا كانت موجودة
    if ($image_path && file_exists($image_path)) {
        unlink($image_path);
    }

    //======================================
    //=========create delete query==========
    //======================================
    $stmt = $connect->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Post and its image deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$connect->close();
?>
