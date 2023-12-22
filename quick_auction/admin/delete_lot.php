<?php
require_once '../db/db_functions.php';

session_start();
$response = ['success' => false];

if (!isset($_SESSION['user_id'])) {
    $response['error'] = "Вы не авторизованы.";
    echo json_encode($response);
    exit;
}

$conn = dbConnect();

if (!isAdmin($_SESSION['user_id'], $conn)) {
    $response['error'] = "У вас нет прав для выполнения этой операции.";
    echo json_encode($response);
    $conn->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lotId'])) {
    $lotId = $_POST['lotId'];

    if (deleteLot($lotId, $conn)) {
        $response['success'] = true;
    } else {
        $response['error'] = "Ошибка при удалении лота.";
    }
} else {
    $response['error'] = "Неверный запрос.";
}

$conn->close();
echo json_encode($response);
exit;
