<?php
require "./Constant.php";
require "./Permissions.php";
session_start();

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $conn->real_escape_string($data['email']);
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT id, username, avatar, password, isadmin , firstname , lastname , email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $username, $avatar, $hashedPassword, $isadmin, $firstname, $lastname , $email);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['avatar'] = $avatar;
            $_SESSION['isadmin'] = $isadmin;
            $_SESSION['fname'] = $firstname;
            $_SESSION['lname'] = $lastname;
            $_SESSION['email'] = $email;

            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user" => [
                    "id" => $userId,
                    "username" => $username,
                    "avatar" => $avatar,
                    "isadmin" => $isadmin,
                    'fname' => $firstname,
                    'lname' => $lastname,
                    'email' => $email
                ]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}

$conn->close();
