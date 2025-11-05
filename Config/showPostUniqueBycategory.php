<?php
require "./Permissions.php";
require "./Constant.php";

//================================================================
//==========================connect to server=====================
//================================================================

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

// المتغير الي فيه القيمة الي هفلتر عليها البوستات
if (isset($data["id"])) {
    $categoryId = $data["id"]; // إضافة علامات النسبة لتطبيق البحث باستخدام LIKE
    $is_featured = 1;
    // استخدام استعلام محضر
    $stmt = $connect->prepare("SELECT * FROM posts WHERE is_featured = ? AND (categoryId = ?)");
    $stmt->bind_param("ii",$is_featured ,  $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(["data" => $data]); // تحويل البيانات إلى JSON وإرجاعها
    } else {
        echo json_encode(["data" => []]); // إرجاع مصفوفة فارغة إذا لم تكن هناك بيانات
    }

    // إغلاق الاتصال بقاعدة البيانات
    $stmt->close();
    $connect->close();
} else {
    echo json_encode(["data" => []]);
}





?>