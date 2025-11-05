<?php
require "./Permissions.php";
require "./Constant.php";

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}


// دالة لإرسال استجابة JSON وإنهاء السكربت
function sendResponse($status, $message)
{
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $message]);
    exit();
}
//================================================================
//==========================GET DATA==============================
//================================================================

if (isset($_POST['title']) && isset($_POST['description']) && isset($_FILES['myimg']) && isset($_POST['categoryId']) && isset($_POST['Author_id']) && isset($_POST['is_featured'])) {

    //======================================
    //=========add data to variables =======
    //======================================

    $title = $connect->real_escape_string($_POST['title']);
    $body = $connect->real_escape_string($_POST['description']);
    $categoryId = $connect->real_escape_string($_POST['categoryId']);
    $Author_id = $connect->real_escape_string($_POST['Author_id']);
    $is_featured = $connect->real_escape_string($_POST['is_featured']);
    $d = date_create();
    $startdate = date_format($d, "Y-m-d h:i:s");

    if ($is_featured == "false") {
        $is_featured_new = 0;
    } else {
        $is_featured_new = 1;
    }



    //======================================
    //=========Handle Image Upload =========
    //======================================
    // تعيين المسار المحلي
    $target_dir = __DIR__ . "/uploads/posts/";

    // إنشاء المجلد إذا لم يكن موجودًا
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES["myimg"]["name"], PATHINFO_EXTENSION));
    $uniqueFileName = uniqid() . '.' . $imageFileType; // اسم الملف فقط
    $target_file = $target_dir . $uniqueFileName; // مسار الملف النهائي

    // التحقق من نوع الملف
    $valid_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $valid_extensions)) {
        sendResponse("error", "Invalid file type");
    }

    // التحقق من حجم الملف
    $maxFileSize = 2 * 1024 * 1024; // 2MB
    if ($_FILES["myimg"]["size"] > $maxFileSize) {
        sendResponse("error", "File size exceeds 2 MB");
    }

    // التحقق من أن الملف صورة
    if (getimagesize($_FILES["myimg"]["tmp_name"]) === false) {
        sendResponse("error", "File is not an image");
    }

    // التعامل مع أخطاء الرفع
    if ($_FILES['myimg']['error'] !== UPLOAD_ERR_OK) {
        sendResponse("error", "Error uploading file: " . $_FILES['myimg']['error']);
    }

    // نقل الملف المرفوع
    if (!move_uploaded_file($_FILES["myimg"]["tmp_name"], $target_file)) {
        sendResponse("error", "Error uploading file");
    }

    // إنشاء رابط URL الكامل للملف لتخزينه في قاعدة البيانات أو عرضه
    $file_url = "http://localhost/blogBackend/config/uploads/posts/" . $uniqueFileName;

    //======================================
    //=========create query ================
    //======================================

    $stmt = $connect->prepare("INSERT INTO posts (title, body, image, categoryId, is_featured, author_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiii", $title, $body, $file_url, $categoryId, $is_featured_new, $Author_id);


    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "post added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
