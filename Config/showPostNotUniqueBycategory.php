<?php
require "./Permissions.php";
require "./Constant.php";

session_start();

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$current_user_id = $_SESSION['id'] ?? 0; // المستخدم الحالي (مرسل من الفرونت)


if (!$current_user_id) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

if (isset($data["id"])) {
    $categoryId = $data["id"];
    $is_featured = 0;

    $sql = "
        SELECT 
            p.id,
            p.title,
            p.body,
            p.image,
            p.categoryId,
            p.author_id,
            p.startdate,
            p.is_featured,
            COUNT(l.id) AS likes_count,
            IF(SUM(l.user_id = ?) > 0, 1, 0) AS is_liked
        FROM posts p
        LEFT JOIN likes l ON p.id = l.post_id
        WHERE p.is_featured = ? AND p.categoryId = ?
        GROUP BY p.id
        ORDER BY p.startdate DESC
    ";

    $stmt = $connect->prepare($sql);
    $stmt->bind_param("iii", $current_user_id, $is_featured, $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["data" => $data]);

    $stmt->close();
    $connect->close();
} else {
    echo json_encode(["data" => []]);
}
?>
