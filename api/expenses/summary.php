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

    $totalQuery = "
        SELECT
            COUNT(*) AS total_expenses,
            COALESCE(SUM(amount), 0) AS total_amount
        FROM expenses
        WHERE user_id = ?
    ";

    $totalStatement = $pdo->prepare($totalQuery);

    $totalStatement->execute([
        $userId
    ]);

    $summary = $totalStatement->fetch();


    $categoryQuery = "
        SELECT
            category,
            SUM(amount) AS total
        FROM expenses
        WHERE user_id = ?
        GROUP BY category
        ORDER BY total DESC
    ";

    $categoryStatement = $pdo->prepare($categoryQuery);

    $categoryStatement->execute([
        $userId
    ]);

    $categories = $categoryStatement->fetchAll();


    echo json_encode([
        "success" => true,
        "summary" => [
            "total_expenses" =>
                (int) $summary["total_expenses"],

            "total_amount" =>
                (float) $summary["total_amount"]
        ],
        "categories" => $categories
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to load expense summary."
    ]);
}