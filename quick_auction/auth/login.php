<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">Вход</h2>
                <form id="loginForm">
                    <div class="form-group">
                        <label for="loginUsername">Логин</label>
                        <input type="text" class="form-control" id="loginUsername" name="loginUsername" pattern="[A-Za-z0-9]+" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Пароль</label>
                        <input type="password" class="form-control" id="loginPassword" name="loginPassword" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </div>
                    <div class="form-group">
                        <p id="loginErrorMessage" class="text-danger"></p>
                    </div>
                </form>
                <p>Еще не зарегистрированы? <a href="register.php">Регистрация</a></p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                var username = $('#loginUsername').val();
                var password = $('#loginPassword').val();

                $.post('login_handler.php', {
                    loginUsername: username,
                    loginPassword: password
                }, function(response) {
                    if (response.error) {
                        $('#loginErrorMessage').text(response.error);
                    } else if (response.success) {
                        window.location.href = '../index.php';
                    }
                }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                    $('#errorMessage').text('Ошибка при отправке формы.');
                    console.log('Ошибка AJAX запроса: ' + textStatus + ', ' + errorThrown);
                });
            });
        });
    </script>
</body>

</html>
