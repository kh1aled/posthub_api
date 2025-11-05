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
$content = trim($data['content'] ?? '');

if(!$commentId || !$content){
    return json_encode(["error" => "Invalid input"]);
}

//================================================================
//update comment in database
//================================================================
$ins = $connect->prepare("UPDATE comments SET content = ? WHERE id = ? AND user_id = ?");
$ins->bind_param("sii", $content, $commentId, $current_user_id);
$ins->execute();

if ($ins) {
    echo json_encode(["status" => "success", "message" => "Comment updated successfully", "username" => $_SESSION['username']]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}

$connect->close();
exit;