<?php
session_start();
include("./settings/connect_datebase.php");

if (isset($_SESSION['user'])) {
    if($_SESSION['user'] != -1) {
        $user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
        while($user_read = $user_query->fetch_row()) {
            if($user_read[3] == 0) header("Location: user.php");
            else if($user_read[3] == 1) header("Location: admin.php");
        }
    }
}
?>
<html>
<head>
    <meta charset="utf-8">
    <title>Авторизация</title>
    <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="top-menu">
        <a href=#><img src="img/logo1.png"/></a>
        <div class="name">
            <a href="index.php">
                <div class="subname">БЗОПАСНОСТЬ ВЕБ-ПРИЛОЖЕНИЙ</div>
                Пермский авиационный техникум им. А. Д. Швецова
            </a>
        </div>
    </div>
    <div class="space"></div>
    <div class="main">
        <div class="content">
            <div class="login">
                <div class="name">Авторизация</div>
                
                <div class="sub-name">Логин:</div>
                <input name="_login" type="text" id="loginInput"/>
                <div class="sub-name">Пароль:</div>
                <input name="_password" type="password" id="passwordInput"/>
                
                <div id="message" style="margin: 10px 0; color: #ff4444;"></div>
                
                <a href="regin.php">Регистрация</a>
                <br><a href="recovery.php">Забыли пароль?</a>
                <input type="button" class="button" value="Войти" onclick="LogIn()" id="loginButton"/>
                <img src="img/loading.gif" class="loading" id="loading" style="display:none;"/>
            </div>
            
            <div class="footer">
                © КГАПОУ "Авиатехникум", 2020
                <a href=#>Конфиденциальность</a>
                <a href=#>Условия</a>
            </div>
        </div>
    </div>
    
    <script>
        function LogIn() {
            const login = document.getElementById('loginInput').value.trim();
            const password = document.getElementById('passwordInput').value.trim();
            const message = document.getElementById('message');
            
            if (!login || !password) {
                message.innerText = "Заполните все поля";
                return;
            }
            
            document.getElementById('loading').style.display = "block";
            document.getElementById('loginButton').disabled = true;
            
            const data = new FormData();
            data.append("login", login);
            data.append("password", password);
            
            $.ajax({
                url: 'ajax/login_user.php',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'html',
                processData: false,
                contentType: false,
                success: function(response) {
                    document.getElementById('loading').style.display = "none";
                    document.getElementById('loginButton').disabled = false;
                    
                    if (response === "blocked") {
                        message.innerText = "Аккаунт заблокирован на 5 минут";
                    } else if (response === "") {
                        message.innerText = "Неверный логин или пароль";
                    } else {
                        localStorage.setItem("token", response);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    document.getElementById('loading').style.display = "none";
                    document.getElementById('loginButton').disabled = false;
                    
                    if (xhr.status === 429) {
                        message.innerText = "";
                    } else {
                        message.innerText = "Ошибка подключения";
                    }
                }
            });
        }
    </script>
</body>
</html>