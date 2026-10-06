<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only GET requests are allowed."
    ]);

    exit;
}

$user = authenticate();

$userId = $user["user_id"];

try {

    $query = "
        SELECT
            id,
            category,
            amount,
            comments,
            created_at,
            updated_at
        FROM expenses
        WHERE user_id = ?
        ORDER BY created_at DESC
    ";

    $statement = $pdo->prepare($query);

    $statement->execute([$userId]);

    $expenses = $statement->fetchAll();

    echo json_encode([
        "success" => true,
        "expenses" => $expenses
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to fetch expenses."
    ]);
}