<?php
function dbConnect()
{
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "quick_auction";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function userExists($username, $conn)
{
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $num_rows = $stmt->num_rows;
    $stmt->close();
    return $num_rows > 0;
}

function registerUser($username, $password, $conn)
{
    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function authenticateUser($username, $password, $conn)
{
    $sql = "SELECT id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            return $user['id'];
        }
    }
    return false;
}

function getLotById($lotId, $conn)
{
    $sql = "SELECT * FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lotId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

function isLotValid($lotId, $conn)
{
    $sql = "SELECT COUNT(*) as count FROM items WHERE id = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lotId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['count'] > 0;
    }

    return false;
}


function addBid($lotId, $userId, $bidAmount, $conn)
{
    $sql = "INSERT INTO bids (item_id, user_id, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $lotId, $userId, $bidAmount);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

function getMaxBidForLot($lotId, $conn)
{
    $sql = "SELECT MAX(amount) as max_bid FROM bids WHERE item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lotId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['max_bid'] ? $row['max_bid'] : 0;
    }

    return 0;
}

function isAdmin($userId, $conn)
{
    $sql = "SELECT superuser FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return $row['superuser'];
    }

    return false;
}

function uploadImage($image)
{
    $uploadDir = '../uploads/';
    $fileName = time() . basename($image['name']);
    $targetPath = $uploadDir . $fileName;
    if (move_uploaded_file($image['tmp_name'], $targetPath)) {
        return 'uploads/' . $fileName;
    }
    return false;
}

function addLot($title, $description, $start_price, $is_active, $imagePath, $conn)
{
    $sql = "INSERT INTO items (title, description, start_price, is_active, image_path) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssdis", $title, $description, $start_price, $is_active, $imagePath);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    return false;
}

function deleteLot($lotId, $conn)
{
    $sql = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lotId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function getMaxBidUserId($lotId, $conn)
{
    $sql = "SELECT user_id FROM bids WHERE item_id = ? ORDER BY amount DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lotId);
    $stmt->execute();
    $result = $stmt->get_result();
    $winner = $result->fetch_assoc();
    $stmt->close();
    return $winner['user_id'] ?? null;
}

function endAuction($lotId, $winnerId, $conn)
{
    $sql = "UPDATE items SET is_active = 0, user_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $winnerId, $lotId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function getWonLotsByUser($userId, $conn)
{
    $sql = "SELECT * FROM items WHERE user_id = ? AND is_active = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $lots = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $lots;
}
