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

if (isset($data['fname']) && isset($data['lname']) && isset($data['role']) && isset($data["id"]) ) {
   
    //======================================
    //=========add data to variables =======
    //======================================
    $fname = $connect->real_escape_string($data['fname']);
    $lname = $connect->real_escape_string($data['lname']);
    $role = $connect->real_escape_string($data['role']);
    $id = $connect->real_escape_string($data['id']);

    //======================================
    //=========create query ================
    //======================================
    $stmt = $connect->prepare("UPDATE users SET fname = ?, lname = ?, isadmin = ? WHERE id = ?;");
    $stmt->bind_param("ssii", $fname, $lname, $role, $id);  // s for string, i for integer

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();

} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$connect->close();
?>
