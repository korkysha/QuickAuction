<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../db/db_functions.php';

if (!isset($_GET['id'])) {
    die('Лот не найден');
}

$lotId = $_GET['id'];
$conn = dbConnect();
$lot = getLotById($lotId, $conn);

if ($lot === null) {
    $conn->close();
    die('Лот не найден');
}

$userId = $_SESSION['user_id'];
$userIsAdmin = isAdmin($userId, $conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lot['title']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h1><?php echo htmlspecialchars($lot['title']); ?></h1>
        <img src="<?php echo htmlspecialchars('../' . $lot['image_path']); ?>" style="max-height: 40vh;" class="img-fluid mb-3" alt="<?php echo htmlspecialchars($lot['title']); ?>">

        <p><?php echo htmlspecialchars($lot['description']); ?></p>

        <p id="maxBid">Текущая максимальная ставка: Загрузка...</p>
        <p>Минимальная ставка: <?php echo htmlspecialchars($lot['start_price']); ?></p>


        <form id="bidForm">
            <div class="form-group">
                <label for="bidAmount">Ваша ставка</label>
                <input type="number" class="form-control" id="bidAmount" name="bidAmount" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Сделать ставку</button>
                <?php if ($userIsAdmin) : ?>
                    <button id="endAuction" class="btn btn-warning" data-lot-id="<?php echo htmlspecialchars($lotId); ?>">Закончить торги</button>
                    <button id="deleteLot" class="btn btn-danger" data-lot-id="<?php echo htmlspecialchars($lotId); ?>">Удалить лот</button>
                <?php endif; ?>
            </div>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            function updateMaxBid() {
                $.ajax({
                    url: 'fetch_max_bid.php',
                    type: 'GET',
                    data: {
                        lotId: <?php echo json_encode($lotId); ?>
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.max_bid !== undefined) {
                            $('#maxBid').text('Текущая максимальная ставка: ' + data.max_bid);
                        }
                    }
                });
            }

            updateMaxBid();
            setInterval(updateMaxBid, 5000);

            $('#bidForm').submit(function(e) {
                e.preventDefault();
                var bidAmount = $('#bidAmount').val();

                $.post('bid_handler.php', {
                    lotId: <?php echo json_encode($lotId); ?>,
                    bidAmount: bidAmount
                }, function(response) {
                    if (response.success) {
                        alert('Ваша ставка успешно принята.');
                        updateMaxBid();
                    } else {
                        alert('Ошибка: ' + response.error);
                    }
                }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                    alert('Ошибка при отправке ставки.');
                });
            });

            $('#deleteLot').click(function() {
                var lotId = $(this).data('lot-id');
                if (confirm('Вы уверены, что хотите удалить этот лот?')) {
                    $.ajax({
                        url: '../admin/delete_lot.php',
                        type: 'POST',
                        data: {
                            lotId: lotId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                window.location.href = '../index.php';
                            } else {
                                alert('Ошибка: ' + response.error);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Ошибка при отправке запроса: ' + textStatus);
                        }
                    });
                }
            });

            $('#endAuction').click(function() {
                var lotId = $(this).data('lot-id');
                if (confirm('Вы уверены, что хотите завершить торги по этому лоту?')) {
                    $.ajax({
                        url: '../admin/end_auction.php',
                        type: 'POST',
                        data: {
                            lotId: lotId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert('Торги по лоту успешно завершены.');
                                window.location.href = '../index.php';
                            } else {
                                alert('Ошибка: ' + response.error);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert('Ошибка при отправке запроса: ' + textStatus);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
