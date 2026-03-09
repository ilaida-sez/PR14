<?php
session_start();
include("../settings/connect_datebase.php");

if (!isset($_SESSION['user']) || $_SESSION['user'] == -1) {
    http_response_code(403);
    echo "not_authorized";
    exit;
}

$IdUser = $_SESSION['user'];
$Message = trim($_POST["Message"] ?? '');
$IdPost = $_POST["IdPost"] ?? 0;

if (!isset($_SESSION['last_comment_time'])) {
    $_SESSION['last_comment_time'] = 0;
}

$now = time();
if ($now - $_SESSION['last_comment_time'] < 3) {
    http_response_code(429);
    echo "rate_limit";
    exit;
}
$_SESSION['last_comment_time'] = $now;

if (empty($Message)) {
    echo "empty";
    exit;
}

if (strlen($Message) > 500) {
    echo "too_long";
    exit;
}

$Message = htmlspecialchars($Message, ENT_QUOTES, 'UTF-8');

$mysqli->query("INSERT INTO comments (IdUser, IdPost, Messages) 
                VALUES ($IdUser, $IdPost, '$Message')");

echo "ok";
?>