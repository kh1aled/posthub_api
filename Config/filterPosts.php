<?php
require "./Permissions.php";
require "./Constant.php";

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

session_start();
$current_user_id = $_SESSION['id'] ?? 0;

if (!$current_user_id) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["searchValue"])) {
    $searchValue = trim($data["searchValue"]);

    if ($searchValue === "") {
        echo json_encode(["data" => []]);
        exit;
    }

    $filterValue = "%" . $searchValue . "%";

    // ✅ البحث مع عدد الإعجابات + حالة is_liked
    $stmt = $connect->prepare("
        SELECT 
            posts.*, 
            categories.title AS category_name,
            COUNT(likes.id) AS likes_count,
            IF(SUM(likes.user_id = ?) > 0, 1, 0) AS is_liked
        FROM posts
        JOIN categories ON posts.categoryId = categories.id
        LEFT JOIN likes ON likes.post_id = posts.id
        WHERE posts.title LIKE ? 
           OR posts.body LIKE ? 
           OR categories.title LIKE ?
        GROUP BY posts.id
        ORDER BY posts.startdate DESC
    ");

    $stmt->bind_param("isss", $current_user_id, $filterValue, $filterValue, $filterValue);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
        $row['body']  = htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8');
        $row['category_name'] = htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8');
        $row['likes_count'] = (int) $row['likes_count'];
        $row['is_liked'] = (int) $row['is_liked'];

        $data[] = $row;
    }

    echo json_encode(["data" => $data]);

    $stmt->close();
    $connect->close();
} else {
    echo json_encode(["data" => []]);
}
?>
