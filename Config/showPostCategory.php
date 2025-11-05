<?PHP
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



if (isset($data["categoryId"])) {
    // استعلام SQL لجلب جميع الصفوف من جدول users
    $id = $data['categoryId'];
    $sql = "SELECT * FROM categories WHERE id = $id";

    $result = $connect->query($sql);

    if ($result->num_rows > 0) {

        $data = [];

        while ($row = $result->fetch_assoc()) {

            $data = $row;

        }

        echo json_encode(["data" => $data]); // تحويل البيانات إلى JSON وإرجاعها
    } else {
        echo json_encode(["data" => []]); // إرجاع مصفوفة فارغة إذا لم تكن هناك بيانات
    }
} else {

    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}




// إغلاق الاتصال بقاعدة البيانات
$connect->close();



?>