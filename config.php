<?php

$portal = "http://tatatv.cc/stalker_portal/server/load.php";
$mac = "00:1A:79:00:2B:A7";
$serial = "A44FE126E0250";      // optional
$device_id = "AEE189124634425D24481DEDFBFF7C73F6EB2B89163EE3144B5EB85144812EB8";   // optional

function request($params, $token = "")
{
    global $portal, $mac, $serial, $device_id;

    $cookie = "mac=$mac; stb_lang=en; timezone=GMT";
    if ($serial) $cookie .= "; serial=$serial";
    if ($device_id) $cookie .= "; device_id=$device_id";

    $headers = [
        "User-Agent: Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp",
        "Referer: " . str_replace("server/load.php", "c/", $portal),
        "Cookie: $cookie"
    ];

    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }

    $url = $portal . "?" . http_build_query($params);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 15
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    return json_decode($res, true);
}

function get_token()
{
    return request([
        "type" => "stb",
        "action" => "handshake",
        "JsHttpRequest" => "1-xml"
    ])['js']['token'] ?? '';
}

function get_channels($token)
{
    return request([
        "type" => "itv",
        "action" => "get_all_channels",
        "JsHttpRequest" => "1-xml"
    ], $token)['js']['data'] ?? [];
}

function get_link($id, $token)
{
    return request([
        "type" => "itv",
        "action" => "create_link",
        "cmd" => $id,
        "JsHttpRequest" => "1-xml"
    ], $token)['js']['cmd'] ?? '';
}
