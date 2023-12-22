<?php
session_start();
require_once '../db/db_functions.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'], $_POST['lotId'], $_POST['bidAmount'])) {
    $userId = $_SESSION['user_id'];
    $lotId = $_POST['lotId'];
    $bidAmount = $_POST['bidAmount'];

    $conn = dbConnect();

    $lot = getLotById($lotId, $conn);

    if ($lot === null) {
        $response['error'] = 'Лот не существует или не активен.';
    } else if (!is_numeric($bidAmount) || $bidAmount <= 0) {
        $response['error'] = 'Неверная сумма ставки.';
    } else {
        $maxBid = getMaxBidForLot($lotId, $conn);
        if ($bidAmount <= $maxBid || $bidAmount < $lot['start_price']) {
            $response['error'] = 'Ставка должна быть больше текущей максимальной и начальной ставки.';
        } else if (addBid($lotId, $userId, $bidAmount, $conn)) {
            $response['success'] = true;
        } else {
            $response['error'] = 'Не удалось сделать ставку.';
        }
    }

    $conn->close();
} else {
    $response['error'] = 'Неверный запрос или пользователь не авторизован.';
}

header('Content-Type: application/json');
echo json_encode($response);
