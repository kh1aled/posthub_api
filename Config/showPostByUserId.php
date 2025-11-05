<?php
require "./Permissions.php";
require "./Constant.php";

header('Content-Type: application/json');

$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $connect->connect_error]));
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $connect->prepare("SELECT * FROM posts WHERE author_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode(["data" => $posts]);

    $stmt->close();
} else {
    echo json_encode(["data" => []]);
}

$connect->close();
