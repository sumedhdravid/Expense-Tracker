<?php

$jwtSecret = "iauro_expense_tracker_secret_2026_change_this";


function base64UrlEncode($data)
{
    return rtrim(
        strtr(
            base64_encode($data),
            '+/',
            '-_'
        ),
        '='
    );
}


function base64UrlDecode($data)
{
    return base64_decode(
        strtr(
            $data,
            '-_',
            '+/'
        )
    );
}


function generateJWT($userId, $email)
{
    global $jwtSecret;

    $header = [
        "alg" => "HS256",
        "typ" => "JWT"
    ];

    $payload = [
        "user_id" => $userId,
        "email" => $email,
        "iat" => time(),
        "exp" => time() + (60 * 60 * 24)
    ];

    $encodedHeader = base64UrlEncode(
        json_encode($header)
    );

    $encodedPayload = base64UrlEncode(
        json_encode($payload)
    );

    $signature = hash_hmac(
        "sha256",
        $encodedHeader . "." . $encodedPayload,
        $jwtSecret,
        true
    );

    $encodedSignature = base64UrlEncode(
        $signature
    );

    return $encodedHeader . "." .
           $encodedPayload . "." .
           $encodedSignature;
}


function verifyJWT($token)
{
    global $jwtSecret;

    $parts = explode(".", $token);

    if (count($parts) !== 3) {
        return false;
    }

    $encodedHeader = $parts[0];
    $encodedPayload = $parts[1];
    $providedSignature = $parts[2];

    $signature = hash_hmac(
        "sha256",
        $encodedHeader . "." . $encodedPayload,
        $jwtSecret,
        true
    );

    $expectedSignature = base64UrlEncode(
        $signature
    );

    if (!hash_equals(
        $expectedSignature,
        $providedSignature
    )) {
        return false;
    }

    $payload = json_decode(
        base64UrlDecode($encodedPayload),
        true
    );

    if (!$payload) {
        return false;
    }

    if (!isset($payload["exp"])) {
        return false;
    }

    if ($payload["exp"] < time()) {
        return false;
    }

    return $payload;
}