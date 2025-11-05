<?php 

// database connection
header("Content-Type: application/json");
require "./db.php";
//================================================================
//this file to add comment on post
//================================================================
session_start();

$current_user_id = $_SESSION['id'] ?? null;

if(!$current_user_id){
    return json_encode(["error" => "User not logged in"]);
}

//================================================================
//get data from request
//================================================================
$data = json_decode(file_get_contents("php://input"), true);
$commentId = intval($data['id'] ?? 0);

if(!$commentId ){
    return json_encode(["error" => "Invalid input"]);
}

//================================================================
//delete comment from database
//================================================================
$ins = $connect->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
$ins->bind_param("ii", $commentId, $current_user_id);
$ins->execute();

if ($ins) {
    echo json_encode(["status" => "success", "message" => "Comment deleted successfully", "username" => $_SESSION['username']]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}

$connect->close();
exit;