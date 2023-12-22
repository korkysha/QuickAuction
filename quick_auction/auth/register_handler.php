<?php
require_once '../db/db_functions.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    $conn = dbConnect();

    if (empty($username) || empty($password)) {
        $response['error'] = 'Пожалуйста, заполните все поля.';
    } else if (userExists($username, $conn)) {
        $response['error'] = 'Этот логин уже занят.';
    } else if (registerUser($username, $password, $conn)) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Ошибка при регистрации.';
    }

    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
