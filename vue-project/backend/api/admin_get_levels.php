<?php

require_once "cors.php";
require_once '../config/db.php';

// 🔥 Fetch DISTINCT levels from STUDENTS only, SORTED by DB-ID
$levelStmt = $pdo->query("
    SELECT DISTINCT u_level_fk
    FROM user
    WHERE u_role_fk = 1
    AND u_level_fk IS NOT NULL
    ORDER BY u_level_fk ASC
");

$levelsUsed = $levelStmt->fetchAll(PDO::FETCH_COLUMN);

// No students?
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

// 🔥 Count students per DB level
$userCountsStmt = $pdo->prepare("
    SELECT u_level_fk AS level_id, COUNT(*) AS count
    FROM user
    WHERE u_role_fk = 1
    AND u_level_fk BETWEEN ? AND ?
    GROUP BY u_level_fk
    ORDER BY u_level_fk ASC
");

$userCountsStmt->execute([$minLevelId, $maxLevelId]);
$userCounts = $userCountsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// 🔥 Fetch level meta sorted by DB-ID
$levelNamesStmt = $pdo->prepare("
    SELECT l_id, l_name
    FROM level
    WHERE l_id BETWEEN ? AND ?
    ORDER BY l_id ASC
");

$levelNamesStmt->execute([$minLevelId, $maxLevelId]);
$levelNames = $levelNamesStmt->fetchAll(PDO::FETCH_ASSOC);

// 🔥 Build level list with correct display_level mapping
$levels = [];
$displayLevel = 1;

foreach ($levelNames as $lvl) {

    // Skip DB levels that have *no students at all*
    // Comment this out if you want to show empty levels.
    if (!isset($userCounts[$lvl['l_id']])) {
        // continue;  // uncomment to hide empty levels
    }

    $levels[] = [
        "db_level_id"   => (int)$lvl['l_id'],                       // 4–13
        "display_level" => $displayLevel,                           // 1–10
        "level_name"    => $lvl['l_name'],
        "users"         => (int)($userCounts[$lvl['l_id']] ?? 0)
    ];

    $displayLevel++;
}

echo json_encode([
    "success"   => true,
    "levels"    => $levels,
    "minLevel"  => $minLevelId,
    "maxLevel"  => $maxLevelId
]);
