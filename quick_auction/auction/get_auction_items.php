<?php
require_once '../db/db_functions.php';

$conn = dbConnect();
$response = [];

$sql = "SELECT * FROM items WHERE is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    $response['error'] = 'В данный момент нет активных лотов.';
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
