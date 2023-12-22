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
        <title>Регистрация</title>
        <link rel="stylesheet" type="text/css" href="../assets/css/styles.css">

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container">
            <div class="row">
            <div class="col-md-6 offset-md-3 registration-form">
                    <h2 class="text-center">Регистрация</h2>
                    <form id="registerForm">
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" class="form-control" id="username" name="username" pattern="[A-Za-z0-9]+" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" data-state="hidden">Показать</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Подтвердите пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" data-state="hidden">Показать</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                        </div>
                        <div class="form-group">
                            <p id="errorMessage" class="text-danger"></p>
                        </div>
                    </form>
                    <p>Уже зарегистрированы? <a href="login.php">Логин</a></p>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#registerForm').submit(function(e) {
                    e.preventDefault();
                    var username = $('#username').val();
                    var password = $('#password').val();
                    var confirmPassword = $('#confirmPassword').val();
                    var email = $('#email').val();

                    
                    if (!isValidPassword(password)) {
                        $('#errorMessage').text('Пароль должен состоять минимум из 8 символов и содержать буквы и цифры.');
                        return;
                    }

                    if (password !== confirmPassword) {
                        $('#errorMessage').text('Пароли не совпадают.');
                        return;
                    }

                    $.post('register_handler.php', {
                        username: username,
                        password: password,
                        email: email
                    }, function(response) {
                        if (response.error) {
                            $('#errorMessage').text(response.error);
                        } else if (response.success) {
                            window.location.href = 'login.php';
                        }
                    }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
                        $('#errorMessage').text('Ошибка при отправке формы.');
                        console.log('Ошибка AJAX запроса: ' + textStatus + ', ' + errorThrown);
                    });
                });

                
                $('#togglePassword').click(function() {
                    togglePasswordVisibility('#password', '#togglePassword');
                });

                $('#toggleConfirmPassword').click(function() {
                    togglePasswordVisibility('#confirmPassword', '#toggleConfirmPassword');
                });

                
                function isValidPassword(password) {
                    
                    var regex = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/;
                    return regex.test(password);
                }

                
                function togglePasswordVisibility(passwordInputId, buttonId) {
                    var passwordInput = $(passwordInputId);
                    var button = $(buttonId);
                    var passwordFieldType = passwordInput.attr('type');

                    
                    if (passwordFieldType === 'password') {
                        passwordInput.attr('type', 'text');
                        button.text('Скрыть');
                    } else {
                        passwordInput.attr('type', 'password');
                        button.text('Показать');
                    }
                }
            });
        </script>
    </body>

    </html>