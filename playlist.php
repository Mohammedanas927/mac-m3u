<?php
require "config.php";

header("Content-Type: application/x-mpegURL");
header("Access-Control-Allow-Origin: *");

echo "#EXTM3U\n";

// 🔥 Auto detect domain (Render + localhost)
$host = $_SERVER['HTTP_HOST'];
$protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']))
    ? $_SERVER['HTTP_X_FORWARDED_PROTO']
    : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http");

$base = $protocol . "://" . $host;

// Get token
$token = get_token();
if (!$token) die("# Token Error");

// Get channels
$channels = get_channels($token);

foreach ($channels as $ch) {

    $id = $ch['id'] ?? '';
    $name = $ch['name'] ?? 'Channel';

    if (!$id) continue;

    echo "#EXTINF:-1,$name\n";
    echo $base . "/play.php?id=$id\n";
}
