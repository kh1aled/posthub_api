
<?PHP 
require "./Permissions.php";
require "./Constant.php";

//================================================================
//==========================connect to server=====================
//================================================================


$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($connect->connect_error){
    die("Connection failed: " . $connect->connect_error);
}

// استعلام SQL لجلب جميع الصفوف من جدول users


$data = json_decode(file_get_contents("php://input"), true);
if (isset($data["id"])) {
    $categoryId = $data["id"];
    // استخدام استعلام محضر
    $stmt = $connect->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
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