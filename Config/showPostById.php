<?php

require "./Permissions.php";
require "./Constant.php";

header('Content-Type: application/json');

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $connect->connect_error]));
}

session_start();
$current_user_id = $_SESSION['id'] ?? 0;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // ✅ الاستعلام مع حساب عدد الإعجابات + حالة is_liked
    $stmt = $connect->prepare("
        SELECT 
            posts.*, 
            COUNT(likes.id) AS likes_count,
            IF(SUM(likes.user_id = ?) > 0, 1, 0) AS is_liked
        FROM posts
        LEFT JOIN likes ON likes.post_id = posts.id
        WHERE posts.id = ?
        GROUP BY posts.id
    ");

    $stmt->bind_param("ii", $current_user_id, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // تأمين القيم ضد XSS
        $row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
        $row['body'] = htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8');
        $row['likes_count'] = (int)$row['likes_count'];
        $row['is_liked'] = (int)$row['is_liked'];

        echo json_encode(["data" => $row]);
    } else {
        echo json_encode(["data" => null]);
    }

    $stmt->close();
} else {
    echo json_encode(["data" => null]);
}

$connect->close();
