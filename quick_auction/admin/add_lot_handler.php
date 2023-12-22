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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_price = $_POST['start_price'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $imagePath = '';

    if (empty($title) || empty($description) || !is_numeric($start_price) || floatval($start_price) <= 0 || !isset($_FILES['image'])) {
        $response['error'] = 'Пожалуйста, заполните все обязательные поля и убедитесь, что начальная цена является положительным числом.';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = uploadImage($_FILES['image'], $conn);
            if (!$imagePath) {
                $response['error'] = 'Произошла ошибка при загрузке файла изображения.';
            }
        }

        if (empty($response['error']) && addLot($title, $description, floatval($start_price), $is_active, $imagePath, $conn)) {
            $response['success'] = true;
        } else {
            $response['error'] = $response['error'] ?: 'Ошибка при добавлении лота.';
        }
    }

    $conn->close();
} else {
    $response['error'] = 'Неверный запрос.';
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
