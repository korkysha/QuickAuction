<?php
session_start();
require_once '../db/db_functions.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['loginUsername'];
    $password = $_POST['loginPassword'];

    $conn = dbConnect();

    $userId = authenticateUser($username, $password, $conn);
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        $response['success'] = true;
    } else {
        $response['error'] = 'Неверные учетные данные.';
    }

    $conn->close();
} else {
    $response['error'] = 'Неверный запрос.';
}

header('Content-Type: application/json');
echo json_encode($response);
