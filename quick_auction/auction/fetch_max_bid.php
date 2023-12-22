<?php
require_once '../db/db_functions.php';

if (isset($_GET['lotId'])) {
    $lotId = $_GET['lotId'];
    $conn = dbConnect();
    $maxBid = getMaxBidForLot($lotId, $conn);
    $conn->close();

    echo json_encode(['max_bid' => $maxBid]);
}
