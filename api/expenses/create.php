<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";


/*
|--------------------------------------------------------------------------
| Only POST requests are allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Authenticate user
|--------------------------------------------------------------------------
*/

$user = authenticate();

$userId = $user["user_id"];


/*
|--------------------------------------------------------------------------
| Read request data
|--------------------------------------------------------------------------
*/

$data = json_decode(
    file_get_contents("php://input"),
    true
);

$category = trim($data["category"] ?? "");
$amount = $data["amount"] ?? null;
$comments = trim($data["comments"] ?? "");


/*
|--------------------------------------------------------------------------
| Validate input
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Insert expense
|--------------------------------------------------------------------------
*/

try {

    $query = "
        INSERT INTO expenses
        (
            user_id,
            category,
            amount,
            comments
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
    ";

    $statement = $pdo->prepare($query);

    $statement->execute([
        $userId,
        $category,
        $amount,
        $comments !== "" ? $comments : null
    ]);


    /*
    |--------------------------------------------------------------------------
    | Get newly created expense
    |--------------------------------------------------------------------------
    */

    $expenseId = $pdo->lastInsertId();

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
        $expenseId,
        $userId
    ]);

    $expense = $selectStatement->fetch();


    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Expense added successfully.",
        "expense" => $expense
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to add expense."
    ]);
}