<?php

require_once __DIR__ . "/jwt.php";


function authenticate()
{
    $headers = getallheaders();

    $authorization = "";

    if (isset($headers["Authorization"])) {
        $authorization = $headers["Authorization"];
    } elseif (isset($headers["authorization"])) {
        $authorization = $headers["authorization"];
    }

    if ($authorization === "") {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Authorization token is required."
        ]);

        exit;
    }

    if (!preg_match(
        '/Bearer\s+(\S+)/',
        $authorization,
        $matches
    )) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Invalid authorization format."
        ]);

        exit;
    }

    $token = $matches[1];

    $payload = verifyJWT($token);

    if ($payload === false) {

        http_response_code(401);

        echo json_encode([
            "success" => false,
            "message" => "Invalid or expired token."
        ]);

        exit;
    }

    return $payload;
}