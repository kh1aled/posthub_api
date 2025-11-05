<?php
session_start();
header('Content-Type: application/json');

// Include your database connection
require_once 'db.php';

// Function to send JSON response and exit
function sendResponse($status, $message)
{
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION["id"])) {
    sendResponse("error", "Not logged in");
}

// Get logged-in user ID
$userId = $_SESSION['id'];

// Collect POST data
$fname = $_POST['fname'] ?? '';
$lname = $_POST['lname'] ?? '';
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirmation = $_POST['password_confirmation'] ?? '';

// Validate required fields
if (empty($fname) || empty($lname) || empty($username) || empty($email)) {
    sendResponse("error", "Please fill all required fields");
}

// Validate password confirmation
if ($password && $password !== $password_confirmation) {
    sendResponse("error", "Passwords do not match");
}

//======================================
//========= Handle Avatar Upload =======
//======================================

// Set target directory for avatars
$target_dir = __DIR__ . "/uploads/avatar/";

// Create the directory if it does not exist
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Check if an avatar file was uploaded
$file_url = null;
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $imageFileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $uniqueFileName = uniqid() . '.' . $imageFileType; // Unique file name
    $target_file = $target_dir . $uniqueFileName; // Full path

    // Validate file type
    $valid_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $valid_extensions)) {
        sendResponse("error", "Invalid file type");
    }

    // Validate file size (max 2MB)
    $maxFileSize = 2 * 1024 * 1024;
    if ($_FILES["avatar"]["size"] > $maxFileSize) {
        sendResponse("error", "File size exceeds 2 MB");
    }

    // Validate that file is an image
    if (getimagesize($_FILES["avatar"]["tmp_name"]) === false) {
        sendResponse("error", "File is not an image");
    }

    // Move uploaded file to target directory
    if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        sendResponse("error", "Error uploading file");
    }

    // Set the URL to store in database
    $file_url = "http://localhost/blogBackend/config/uploads/avatar/" . $uniqueFileName;
}

//======================================
//========= Update User in DB ==========
//======================================

// Build SQL dynamically
$setFields = "firstname = ?, lastname = ?, username = ?, email = ?";
$params = [$fname, $lname, $username, $email];

// Include password if provided
if ($password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $setFields .= ", password = ?";
    $params[] = $hashedPassword;
}

// Include avatar if uploaded
if ($file_url) {
    $setFields .= ", avatar = ?";
    $params[] = $file_url;
}

// Add user ID for WHERE clause
$params[] = $userId;

// Prepare and execute the SQL statement
$stmt = $connect->prepare("UPDATE users SET $setFields WHERE id = ?");
$stmt->execute($params);

// Return success response


//when Profile updated successfully
//update session variables 
$_SESSION['fname'] = $fname;
$_SESSION['lname'] = $lname;
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
if ($file_url) {
    $_SESSION['avatar'] = $file_url;
}

sendResponse("success", "Profile updated successfully");


$stmt->close();
$connect->close();
