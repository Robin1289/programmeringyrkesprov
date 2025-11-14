<?php
$frontendOrigin = 'http://localhost:5174';

header("Access-Control-Allow-Origin: $frontendOrigin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// Allow OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/db.php';

// Get all distinct levels from STUDENTS ONLY
$levelStmt = $pdo->query("
    SELECT DISTINCT u_level_fk
    FROM user
    WHERE u_level_fk IS NOT NULL
    AND u_role_fk = 1          -- 🔥 Only count students
    ORDER BY u_level_fk
");

$levelsUsed = $levelStmt->fetchAll(PDO::FETCH_COLUMN);

// No student levels found?
if (count($levelsUsed) === 0) {
    echo json_encode([
        "success" => true,
        "levels" => [],
        "message" => "No student levels found"
    ]);
    exit;
}

$minLevelId = (int)min($levelsUsed);
$maxLevelId = (int)max($levelsUsed);

// Count STUDENTS per level
$userCountsStmt = $pdo->prepare("
    SELECT u_level_fk AS level_id, COUNT(*) AS count
    FROM user
    WHERE u_level_fk BETWEEN ? AND ?
    AND u_role_fk = 1              -- 🔥 Only count students
    GROUP BY u_level_fk
    ORDER BY u_level_fk
");

$userCountsStmt->execute([$minLevelId, $maxLevelId]);
$userCounts = $userCountsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch matching level names
$levelNamesStmt = $pdo->prepare("
    SELECT l_id, l_name
    FROM level
    WHERE l_id BETWEEN ? AND ?
    ORDER BY l_id
");

$levelNamesStmt->execute([$minLevelId, $maxLevelId]);
$levelNames = $levelNamesStmt->fetchAll(PDO::FETCH_ASSOC);

// Build final JSON
$levels = [];
$visibleLevel = 1;

foreach ($levelNames as $lvl) {
    $levels[] = [
        "db_level_id"   => (int)$lvl['l_id'],
        "display_level" => $visibleLevel,
        "level_name"    => $lvl['l_name'],
        "users"         => (int)($userCounts[$lvl['l_id']] ?? 0)
    ];
    $visibleLevel++;
}

echo json_encode([
    "success"   => true,
    "levels"    => $levels,
    "minLevel"  => $minLevelId,
    "maxLevel"  => $maxLevelId
]);
