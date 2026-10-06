<?php

require_once __DIR__ . "/../config/database.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$password = $data["password"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Name, email and password are required."
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Password must be at least 6 characters long."
    ]);

    exit;
}

try {

    // Check whether email already exists
    $checkQuery = "SELECT id FROM users WHERE email = ?";
    $checkStatement = $pdo->prepare($checkQuery);
    $checkStatement->execute([$email]);

    if ($checkStatement->fetch()) {
        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "Email already registered."
        ]);

        exit;
    }

    // Hash password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $insertQuery = "
        INSERT INTO users (name, email, password)
        VALUES (?, ?, ?)
    ";

    $insertStatement = $pdo->prepare($insertQuery);

    $insertStatement->execute([
        $name,
        $email,
        $hashedPassword
    ]);

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Account created successfully."
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Something went wrong while creating the account."
    ]);
}