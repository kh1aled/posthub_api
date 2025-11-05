<?php
require "./Permissions.php";
require "./Constant.php";

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

function sendResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

if (isset($_POST['title']) && isset($_POST['body']) && isset($_POST['categoryId']) && isset($_POST['Author_id']) && isset($_POST['is_featured']) && isset($_POST['post_id'])) {
    
    $title = $connect->real_escape_string($_POST['title']);
    $body = $connect->real_escape_string($_POST['body']);
    $categoryId = $connect->real_escape_string($_POST['categoryId']);
    $Author_id = $connect->real_escape_string($_POST['Author_id']);
    $post_id = $connect->real_escape_string($_POST['post_id']);
    $is_featured = $connect->real_escape_string($_POST['is_featured']);
    $startdate = date("Y-m-d H:i:s");
    $file_url = null;

    // ================= Optional Image Upload =================
    if (!empty($_FILES['myimg']['name'])) {
        $target_dir = __DIR__ . "/uploads/posts/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES["myimg"]["name"], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $uniqueFileName;

        $valid_extensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $valid_extensions)) {
            sendResponse("error", "Invalid file type");
        }

        if ($_FILES["myimg"]["size"] > 2 * 1024 * 1024) {
            sendResponse("error", "File size exceeds 2 MB");
        }

        if (getimagesize($_FILES["myimg"]["tmp_name"]) === false) {
            sendResponse("error", "File is not an image");
        }

        if (!move_uploaded_file($_FILES["myimg"]["tmp_name"], $target_file)) {
            sendResponse("error", "Error uploading file");
        }

        $file_url = "http://localhost/blogBackend/config/uploads/posts/" . $uniqueFileName;
    }

    // ================= Update Query =================
    if ($file_url) {
        $stmt = $connect->prepare("UPDATE posts SET title = ?, body = ?, image = ?, categoryId = ?, startdate = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $title, $body, $file_url, $categoryId, $startdate, $post_id);
    } else {
        $stmt = $connect->prepare("UPDATE posts SET title = ?, body = ?, categoryId = ?, startdate = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $title, $body, $categoryId, $startdate, $post_id);
    }

    if ($stmt->execute()) {
        sendResponse("success", "Post updated successfully");
    } else {
        sendResponse("error", "Database error: " . $stmt->error);
    }

    $stmt->close();
} else {
    sendResponse("error", "Invalid input");
}
?>
