<?php
require "config.php";

$id = $_GET['id'] ?? '';
if (!$id) die("No ID");

// Get token
$token = get_token();
if (!$token) die("Token failed");

// Get stream
$cmd = get_link($id, $token);
if (!$cmd) die("Stream failed");

// Clean ffmpeg
$url = trim(str_replace("ffmpeg ", "", $cmd));

// Fetch m3u8
$opts = [
  "http" => [
    "header" => "User-Agent: Mozilla/5.0\r\n"
  ]
];

$context = stream_context_create($opts);
$m3u8 = file_get_contents($url, false, $context);

if (!$m3u8) die("Failed to load stream");

// Fix TS paths
$base = dirname($url);

$m3u8 = preg_replace_callback('/(.*\.ts)/', function($m) use ($base) {
    return $base . "/" . trim($m[1]);
}, $m3u8);

// Output stream
header("Content-Type: application/x-mpegURL");
echo $m3u8;
