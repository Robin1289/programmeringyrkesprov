<?php

require_once "cors.php";
require_once "../config/db.php";

try {
    // fetch questions in order
    $stmt = $pdo->prepare("
        SELECT *
        FROM users 
    ");
    $stmt->execute([$quizId]);
    $questions = $stmt->fetchAll();
}