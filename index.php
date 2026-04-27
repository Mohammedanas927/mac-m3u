<?php

$id = $_GET['id'] ?? die("Missing ID");
$user_ip = $_SERVER['REMOTE_ADDR'];

$portal = "tatatv.cc";
$mac = "00:1A:79:00:2B:A7";
$serial = "A44FE126E0250";
$deviceid = "AEE189124634425D24481DEDFBFF7C73F6EB2B89163EE3144B5EB85144812EB8";

// =====================
// 🔐 HANDSHAKE
// =====================
$n1 = "http://$portal/stalker_portal/server/load.php?type=stb&action=handshake&JsHttpRequest=1-xml";

$headers = [
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
    "X-Forwarded-For: $user_ip",
    "User-Agent: Mozilla/5.0 (QtEmbedded; Linux) MAG200",
];

$res1 = curl($n1, $headers);
$data1 = json_decode($res1, true);

if (!$data1 || !isset($data1['js']['token'])) {
    die("Handshake failed: " . $res1);
}

$token = $data1['js']['token'];

// =====================
// 👤 PROFILE
// =====================
$n2 = "http://$portal/stalker_portal/server/load.php?type=stb&action=get_profile&JsHttpRequest=1-xml";

$headers[] = "Authorization: Bearer $token";

$res2 = curl($n2, $headers);

// =====================
// 📺 CREATE LINK
// =====================
$n3 = "http://$portal/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffrt%20http://localhost/ch/$id&JsHttpRequest=1-xml";

$res3 = curl($n3, $headers);
$data3 = json_decode($res3, true);

if (!$data3 || !isset($data3['js']['cmd'])) {
    die("Stream failed: " . $res3);
}

$stream = $data3['js']['cmd'];

// =====================
// 🔁 REDIRECT
// =====================
header("Location: $stream");
exit;


// =====================
// 🔧 CURL FUNCTION
// =====================
function curl($url, $headers) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    return curl_exec($ch);
}
