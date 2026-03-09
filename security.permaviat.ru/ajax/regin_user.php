<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (!isset($_SESSION['last_reg_request'])) {
    $_SESSION['last_reg_request'] = 0;
}

$now = time();
if ($now - $_SESSION['last_reg_request'] < 2) {
    http_response_code(429);
    echo "rate_limit";
    exit;
}
$_SESSION['last_reg_request'] = $now;

// Проверка на пустые поля
if (empty($login) || empty($password)) {
    echo "empty";
    exit;
}

if (strlen($password) < 4) {
    echo "short_password";
    exit;
}

$check = $mysqli->query("SELECT * FROM users WHERE login = '$login'");
if ($check->fetch_assoc()) {
    echo "exists";
    exit;
}

$mysqli->query("INSERT INTO users (login, password, roll, failed_attempts, blocked_until) 
                VALUES ('$login', '$password', 0, 0, NULL)");

$new_user = $mysqli->query("SELECT * FROM users WHERE login = '$login'")->fetch_assoc();
$id = $new_user['id'];

$_SESSION['user'] = $id;
echo $id;
?>