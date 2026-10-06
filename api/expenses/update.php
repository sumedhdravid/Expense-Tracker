<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only PUT requests are allowed."
    ]);

    exit;
}

$user = authenticate();

$userId = $user["user_id"];

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$id = $data["id"] ?? null;
$category = trim($data["category"] ?? "");
$amount = $data["amount"] ?? null;
$comments = trim($data["comments"] ?? "");

if (!$id) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Expense ID is required."
    ]);

    exit;
}

if ($category === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Category is required."
    ]);

    exit;
}

if ($amount === null || $amount === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Amount is required."
    ]);

    exit;
}

if (!is_numeric($amount) || $amount <= 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Amount must be a valid positive number."
    ]);

    exit;
}

try {

    $query = "
        UPDATE expenses
        SET
            category = ?,
            amount = ?,
            comments = ?
        WHERE id = ?
        AND user_id = ?
    ";

    $statement = $pdo->prepare($query);

    $statement->execute([
        $category,
        $amount,
        $comments !== "" ? $comments : null,
        $id,
        $userId
    ]);

    if ($statement->rowCount() === 0) {

        $checkQuery = "
            SELECT id
            FROM expenses
            WHERE id = ?
            AND user_id = ?
        ";

        $checkStatement = $pdo->prepare($checkQuery);

        $checkStatement->execute([
            $id,
            $userId
        ]);

        if (!$checkStatement->fetch()) {

            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Expense not found."
            ]);

            exit;
        }
    }

    $selectQuery = "
        SELECT
            id,
            category,
            amount,
            comments,
            created_at,
            updated_at
        FROM expenses
        WHERE id = ?
        AND user_id = ?
    ";

    $selectStatement = $pdo->prepare($selectQuery);

    $selectStatement->execute([
        $id,
        $userId
    ]);

    $expense = $selectStatement->fetch();

    echo json_encode([
        "success" => true,
        "message" => "Expense updated successfully.",
        "expense" => $expense
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to update expense."
    ]);
}