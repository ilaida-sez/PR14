<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

if (!isset($_SESSION['last_request'])) {
    $_SESSION['last_request'] = 0;
}

$now = time();
if ($now - $_SESSION['last_request'] < 1) {
    exit;
}
$_SESSION['last_request'] = $now;

$user = $mysqli->query("SELECT * FROM users WHERE login = '$login'")->fetch_assoc();

if ($user) {
    $id = $user['id'];
    $failed = $user['failed_attempts'];
    $blocked = $user['blocked_until'];
    
    // Проверяем, заблокирован ли
    if ($blocked && strtotime($blocked) > time()) {
        echo "blocked";
        exit;
    }
    
    // Проверяем пароль
    if ($user['password'] == $password) {
        // Успех — сбрасываем
        $mysqli->query("UPDATE users SET failed_attempts = 0, blocked_until = NULL WHERE id = $id");
        $_SESSION['user'] = $id;
        echo md5(md5($id));
    } else {
        // Неудача
        $new_attempts = $failed + 1;
        
        if ($new_attempts >= 5) {
            // Блокируем на 5 минут
            $block_time = date('Y-m-d H:i:s', time() + 300);
            $mysqli->query("UPDATE users SET failed_attempts = $new_attempts, blocked_until = '$block_time' WHERE id = $id");
            echo "blocked";
        } else {
            $mysqli->query("UPDATE users SET failed_attempts = $new_attempts WHERE id = $id");
            echo "";
        }
    }
} else {
    echo "";
}
?>