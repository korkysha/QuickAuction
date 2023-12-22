<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление лота</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h2>Добавить новый лот</h2>
        <form id="addLotForm" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Название лота</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Описание лота</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="start_price">Начальная цена</label>
                <input type="number" class="form-control" id="start_price" name="start_price" required>
            </div>
            <div class="form-group">
                <label for="image">Изображение лота</label>
                <input type="file" class="form-control-file" id="image" name="image" required>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">Лот активен</label>
            </div>
            <button type="submit" class="btn btn-primary">Добавить лот</button>
            <div id="formError" class="text-danger mt-2"></div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addLotForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'add_lot_handler.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '../index.php';
                        } else {
                            $('#formError').text(response.error || 'Произошла ошибка при добавлении лота.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#formError').text('Ошибка при отправке формы: ' + textStatus);
                    }
                });
            });
        });
    </script>
</body>

</html>
