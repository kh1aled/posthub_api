<?php
header('Content-Type: application/json');
session_start();
require "./db.php";

// تأكد ان المستخدم مسجل دخول
$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$post_id = intval($data['post_id'] ?? 0);

if (!$post_id) {
    echo json_encode(["error" => "Invalid post ID"]);
    exit;
}

// تحقق إذا المستخدم already liked
$check = $connect->prepare("SELECT id FROM likes WHERE post_id=? AND user_id=?");
$check->bind_param("ii", $post_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Delete like
    $del = $connect->prepare("DELETE FROM likes WHERE post_id=? AND user_id=?");
    $del->bind_param("ii", $post_id, $user_id);
    $del->execute();
    echo json_encode(["liked" => false]);
} else {
    // Insert like
    $ins = $connect->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $ins->bind_param("ii", $post_id, $user_id);
    $ins->execute();
    echo json_encode(["liked" => true]);
}

$connect->close();
exit;