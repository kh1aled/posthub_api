<?php
require "./Permissions.php";
require "./Constant.php";

// connect
$connect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["author_id"])) {
    $id = $data['author_id'];

    // prepared statement
    $stmt = $connect->prepare("SELECT firstname, lastname, avatar FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["data" => $row]);
    } else {
        echo json_encode(["data" => null]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$connect->close();
