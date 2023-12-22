<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

require_once '../db/db_functions.php';
$conn = dbConnect();
$wonLots = getWonLotsByUser($userId, $conn);
$conn->close();

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выигранные лоты</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h1>Ваши выигранные лоты</h1>
        <?php if (empty($wonLots)) : ?>
            <p class="text-center" style="font-size: 1.5rem;">У вас нет выигранных лотов</p>
        <?php else : ?>
            <div class="row">
                <?php foreach ($wonLots as $lot) : ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars('../' . $lot['image_path']); ?>" alt="<?php echo htmlspecialchars($lot['title']); ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($lot['title']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($lot['description']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
