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
$post_id = intval($data['post_id'] ?? 0);
$content = trim($data['content'] ?? '');

if(!$post_id || !$content){
    return json_encode(["error" => "Invalid input"]);
}

//================================================================
//insert comment into database
//================================================================
$ins = $connect->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
$ins->bind_param("iis", $post_id, $current_user_id, $content);
$ins->execute();

if ($ins) {
    echo json_encode(["status" => "success", "message" => "Comment added successfully", "username" => $_SESSION['username']]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}

$connect->close();
exit;