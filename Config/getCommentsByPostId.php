<?php 
session_start();
header("Content-Type: application/json");

// database connection
require "./db.php";

// Start session if not started

//================================================================
// this file to get comments for a post
//================================================================

$current_user_id = $_SESSION["id"] ?? null;

if(!$current_user_id){
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

//================================================================
// get data from request
//================================================================
$post_id = intval($_GET['id'] ?? 0);
if(!$post_id){
    echo json_encode(["error" => "Invalid post ID"]);
    exit;
}

//================================================================
// fetch comments from database
//================================================================

$get = $connect->prepare("
    SELECT c.id, c.content, c.created_at, u.username ,u.id AS user_id
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at DESC
");
$get->bind_param("i", $post_id);
$get->execute();
$result = $get->get_result();

$comments = [];
while($row = $result->fetch_assoc()){
    $comments[] = $row;
}

echo json_encode(["data" => $comments]);
$connect->close();
exit;
