<?php

require "./Permissions.php";
require "./Constant.php";
// Handle CORS preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // If it is just an OPTIONS request, exit
    exit(0);
}

//=======================================
//===============Connection=============
//=======================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// دالة لإرسال استجابة JSON وإنهاء السكربت
function sendResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}

//=======================================
//===============Check on Data=============
//=======================================
if (isset($_POST['fname']) && isset($_POST['lname']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['role']) && isset($_POST['password']) && isset($_POST['password2']) && isset($_FILES['avatar'])) {

    //======================================
    //=========Add data to variables =======
    //======================================
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $myrole = filter_var($_POST['role'], FILTER_SANITIZE_NUMBER_INT);

    //======================================
    //=========Validate password ===========
    //======================================
    $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z])[a-zA-Z\d\W]{8,}$/";

    if (!preg_match($passwordRegex, $password)) {
        sendResponse("error", "Password is not strong enough");
    }

    if ($password != $password2) {
        sendResponse("error", "Confirm password does not match");
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendResponse("error", "Invalid email format");
    }

    //======================================
    //===== Check if username or email exists
    //======================================
    $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        sendResponse("error", "Username or Email already exists");
    }
    $stmt->close();

    // تشفير كلمة المرور
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    
 //======================================
    //=========Handle Image Upload =========
    //======================================
    // تعيين المسار المحلي
    $target_dir = __DIR__ . "/uploads/posts/";

    // إنشاء المجلد إذا لم يكن موجودًا
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION));
    $uniqueFileName = uniqid() . '.' . $imageFileType; // اسم الملف فقط
    $target_file = $target_dir . $uniqueFileName; // مسار الملف النهائي

    // التحقق من نوع الملف
    $valid_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $valid_extensions)) {
        sendResponse("error", "Invalid file type");
    }

    // التحقق من حجم الملف
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES["avatar"]["size"] > $maxFileSize) {
        sendResponse("error", "File size exceeds 2 MB");
    }

    // التحقق من أن الملف صورة
    if (getimagesize($_FILES["avatar"]["tmp_name"]) === false) {
        sendResponse("error", "File is not an image");
    }

    // التعامل مع أخطاء الرفع
    if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        sendResponse("error", "Error uploading file: " . $_FILES['avatar']['error']);
    }

    // نقل الملف المرفوع
    if (!move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
        sendResponse("error", "Error uploading file");
    }

    // إنشاء رابط URL الكامل للملف لتخزينه في قاعدة البيانات أو عرضه
    $file_url = "http://localhost/blogBackend/config/uploads/posts/" . $uniqueFileName;


    //======================================
    //=========Create Query ================
    //======================================
    $stmt = $conn->prepare("INSERT INTO users (fname, lname, username, email, password, avatar , isadmin) VALUES (?, ?, ?, ?, ?, ? , ?)");
    $stmt->bind_param("ssssssi", $fname, $lname, $username, $email, $hashedPassword, $file_url , $myrole);

    if ($stmt->execute()) {
        sendResponse("success", "User added successfully ");
    } else {
        sendResponse("error", "Error: " . $stmt->error);
    }

    $stmt->close();
} else {
    sendResponse("error", "Invalid input");
}

$conn->close();

?>
