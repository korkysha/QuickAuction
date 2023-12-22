<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

require_once 'db/db_functions.php';
$conn = dbConnect();
$userIsAdmin = isAdmin($userId, $conn);
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickAuction - Главная страница</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <script src="https://api-maps.yandex.ru/v3/?apikey=ff5f7089-20b2-4725-b52d-7b9a91c9ca5c&lang=ru_RU"></script>
</head>

<body>
    <div class="container mt-3">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">QuickAuction</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auction/won_lots.php">Выигранные лоты</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Выйти</a>
                    </li>
                </ul>
            </div>
        </nav>

        <h1 class="my-4">Активные лоты</h1>
        <div id="auction-items" class="row">
            <!-- Здесь будут отображаться лоты -->
        </div>

        <?php if ($userIsAdmin) : ?>
            <a href="admin/add_lot.php" class="btn btn-success">Добавить лот</a>
        <?php endif; ?>

    </div>

    <div id="map" style="width: 50vh; height: 50vh; margin: 10vh auto 5vh;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            function fetchAuctionItems() {
                $.ajax({
                    url: 'auction/get_auction_items.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (!data.error) {
                            var itemsHtml = data.map(function(item) {
                                var descriptionLenght = 100
                                var shortDescription = item.description.length > descriptionLenght ? item.description.substring(0, descriptionLenght) + '...' : item.description;
                                return `
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                                <div class="card-body">
                                                    <a href="auction/lot_details.php?id=${item.id}">
                                                        <h5 class="card-title">${item.title}</h5>
                                                    </a>
                                                    <p class="card-text">${shortDescription}</p>
                                                </div>
                                                <div class="card-footer">
                                                    <small class="text-muted">Стартовая цена: ${item.start_price}</small>
                                                </div>
                                        </div>
                                    </div>`;
                            }).join('');
                            $('#auction-items').html(itemsHtml);
                        } else {
                            $('#auction-items').html('<p>' + data.error + '</p>');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#auction-items').html('<p>Ошибка при загрузке лотов.</p>');
                        console.log('Ошибка AJAX запроса: ' + textStatus + ', ' + errorThrown);
                    }
                });
            }

            fetchAuctionItems();

            setInterval(fetchAuctionItems, 5000);
        });
    </script>
    <script type="text/javascript">
        initMap();

        async function initMap() {
            await ymaps3.ready;

            const {
                YMap,
                YMapDefaultSchemeLayer,
                YMapDefaultFeaturesLayer,
                YMapMarker,
            } = ymaps3;

            const {
                YMapDefaultMarker
            } = await ymaps3.import('@yandex/ymaps3-markers@0.0.1');

            const map = new YMap(document.getElementById('map'), {
                location: {
                    center: [30.32783526514379, 59.85703276787955],
                    zoom: 17,
                },

            });

            map.addChild(new YMapDefaultFeaturesLayer({
                id: 'features'
            }));
            map.addChild(new YMapDefaultMarker({
                coordinates: [30.32783526514379, 59.85703276787955],
                title: 'Склад',
                subtitle: 'Склад выигранных лотов',
                draggable: false,
            }));

            map.addChild(new YMapDefaultSchemeLayer());
        }
    </script>

</body>

</html>
