
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

$sql ="SELECT * FROM users";

$result = $connect->query($sql);

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
$connect->close();



?>