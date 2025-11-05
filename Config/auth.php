<?php
session_start();

function checkAdmin() {
    if (!isset($_SESSION["isAdmin"]) || $_SESSION["isAdmin"] != 1) {
        http_response_code(403);
        echo json_encode(["error" => "Access Denied"]);
        exit();
    }
}