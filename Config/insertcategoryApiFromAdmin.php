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

if (isset($data['title']) && isset($data['description'])) {
   
    //======================================
    //=========add data to variables =======
    //======================================

    $title = $connect->real_escape_string($data['title']);
    $description = $connect->real_escape_string($data['description']);
    
    //======================================
    //=========create query ================
    //======================================
    $stmt = $connect->prepare("INSERT INTO categories (title, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $description);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "category added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

?>
