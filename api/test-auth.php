<?php

header("Content-Type: application/json");

require_once __DIR__ . "/config/auth.php";

$user = authenticate();

echo json_encode([
    "success" => true,
    "message" => "Authentication successful.",
    "user" => $user
]);