<?php

header('Content-Type: application/json');

// DB connection
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Universal function to return JSON safely
function sendJson($data) {
    echo json_encode($data);
    exit;
}

// Optional: override default error handler to return JSON
set_exception_handler(function($e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
});

set_error_handler(function($errno, $errstr) {
    echo json_encode(['success' => false, 'error' => $errstr]);
    exit;
});
