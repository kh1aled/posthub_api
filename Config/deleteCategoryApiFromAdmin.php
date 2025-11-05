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
    //=========create query ================
    //======================================
    $stmt = $connect->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }
    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

?>
