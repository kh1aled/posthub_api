<?php
require "./Permissions.php";
require "./Constant.php";
session_start();

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$current_user_id = $_SESSION["id"] ?? null;

if (!$current_user_id) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$sql = "
    SELECT 
        posts.id,
        posts.title,
        posts.body,
        posts.image,
        posts.categoryId,
        posts.author_id,
        posts.startdate,
        posts.is_featured,
        COUNT(l.id) AS likes_count,
        IF(SUM(l.user_id = ?) > 0, 1, 0) AS is_liked
    FROM posts
    LEFT JOIN likes l ON posts.id = l.post_id
    WHERE posts.is_featured = 0
    GROUP BY posts.id
    ORDER BY posts.startdate DESC
";

$stmt = $connect->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $row['likes_count'] = (int) $row['likes_count'];
    $row['is_liked'] = (bool) $row['is_liked'];
    $row['title'] = htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8');
    $row['body']  = htmlspecialchars($row['body'], ENT_QUOTES, 'UTF-8');
    $data[] = $row;
}

echo json_encode(["data" => $data], JSON_UNESCAPED_UNICODE);

$stmt->close();
$connect->close();
