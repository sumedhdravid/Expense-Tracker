<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only DELETE requests are allowed."
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

if (!$id) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Expense ID is required."
    ]);

    exit;
}

try {

    $query = "
        DELETE FROM expenses
        WHERE id = ?
        AND user_id = ?
    ";

    $statement = $pdo->prepare($query);

    $statement->execute([
        $id,
        $userId
    ]);

    if ($statement->rowCount() === 0) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Expense not found."
        ]);

        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Expense deleted successfully."
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to delete expense."
    ]);
}