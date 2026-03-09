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
    <title>Регистрация</title>
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
                <div class="name">Регистрация</div>
                
                <div class="sub-name">Логин:</div>
                <input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
                <div class="sub-name">Пароль:</div>
                <input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
                <div class="sub-name">Повторите пароль:</div>
                <input name="_passwordCopy" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
                
                <a href="login.php">Вернуться</a>
                <input type="button" class="button" value="Зайти" onclick="RegIn()" style="margin-top: 0px;"/>
                <img src="img/loading.gif" class="loading" style="margin-top: 0px;"/>
            </div>
            
            <div class="footer">
                © КГАПОУ "Авиатехникум", 2020
                <a href=#>Конфиденциальность</a>
                <a href=#>Условия</a>
            </div>
        </div>
    </div>
    
    <script>
        var loading = document.getElementsByClassName("loading")[0];
        var button = document.getElementsByClassName("button")[0];
        
        function RegIn() {
            var _login = document.getElementsByName("_login")[0].value.trim();
            var _password = document.getElementsByName("_password")[0].value.trim();
            var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value.trim();
            
            if(_login == "") {
                alert("Введите логин.");
                return;
            }
            if(_password == "") {
                alert("Введите пароль.");
                return;
            }
            if(_password != _passwordCopy) {
                alert("Пароли не совпадают.");
                return;
            }
            if(_password.length < 4) {
                alert("Пароль должен быть минимум 4 символа.");
                return;
            }
            
            loading.style.display = "block";
            button.className = "button_diactive";
            
            var data = new FormData();
            data.append("login", _login);
            data.append("password", _password);
            
            $.ajax({
                url: 'ajax/regin_user.php',
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'html',
                processData: false,
                contentType: false,
                success: function (_data) {
                    loading.style.display = "none";
                    button.className = "button";
                    
                    if(_data == "exists") {
                        alert("Пользователь с таким логином уже существует.");
                    }
                    else if(_data == "empty") {
                        alert("Заполните все поля.");
                    }
                    else if(_data == "short_password") {
                        alert("Пароль слишком короткий (минимум 4 символа).");
                    }
                    else if(_data == "rate_limit") {
                        alert("Слишком частые попытки регистрации. Подождите 2 секунды.");
                    }
                    else if(_data > 0) {
                        alert("Регистрация успешна!");
                        location.reload();
                    }
                    else {
                        alert("Ошибка регистрации.");
                    }
                },
                error: function(xhr) {
                    loading.style.display = "none";
                    button.className = "button";
                    
                    if(xhr.status === 429) {
                        alert("Слишком много запросов. Подождите.");
                    } else {
                        alert("Системная ошибка!");
                    }
                }
            });
        }
        
        function PressToEnter(e) {
            if (e.keyCode == 13) {
                RegIn();
            }
        }
    </script>
</body>
</html>