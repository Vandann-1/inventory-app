<?php
function sendRequestToDjango($endpoint, $data = [], $token = null, $method = 'POST') {
    $url = "http://127.0.0.1:8000/api/$endpoint";
    $ch = curl_init();

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    if ($token) {
        /* if token is available then it appends it to the headers array like
        as we have 2 array index already
        [0] -> Content-Type: application/json
        [1] -> Accept: application/json
        [2] -> Authorization: Bearer abcd1234xyz
        The "Bearer" part indicates that the token is a Bearer Token (a common form of access token used in OAuth 2.0). */
        $headers[] = "Authorization: Bearer $token";
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if (strtoupper($method) === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return ['error' => curl_error($ch)];
    }

    curl_close($ch);
    return json_decode($response, true);
}
