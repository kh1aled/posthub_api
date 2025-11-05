<?php
require "./db.php";

session_start();
//================================================================
//==========================connect to server=====================
//================================================================



$data = json_decode(file_get_contents("php://input"), true);

$current_user_id = $_SESSION["id"] ?? null;

if (!$current_user_id) {
    echo json_encode(["error" => "User not authenticated"]);
    return;
} 

if (isset($data["id"])) {
    $categoryId = $data["id"];
    $userId = $current_user_id;

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
        WHERE posts.categoryId = ?
        GROUP BY posts.id
        ORDER BY posts.startdate DESC
    ";

    $stmt = $connect->prepare($sql);

    if (!$stmt) {
        die(json_encode(["error" => "Failed to prepare statement: " . $connect->error]));
    }

    // ربط القيم (user_id, categoryId)
    $stmt->bind_param("ii", $userId, $categoryId);
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
    echo json_encode(["data" => ["no data"]]);
}
