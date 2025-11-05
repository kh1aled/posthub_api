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

if (isset($data['title']) && isset($data['description']) && isset($data["id"]) ) {
   
    //======================================
    //=========add data to variables =======
    //======================================
    $title = $connect->real_escape_string($data['title']);
    $description = $connect->real_escape_string($data['description']);
    $id = $connect->real_escape_string($data['id']);

    //======================================
    //=========create query ================
    //======================================
    $stmt = $connect->prepare("UPDATE categories SET title = ?, description = ? WHERE id = ?;");
    $stmt->bind_param("ssi", $title, $description, $id);  // s for string, i for integer

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Category updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$connect->close();
?>
