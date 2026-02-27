<?php
// ---------------- DEBUG SETTINGS ----------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

// ---------------- AUTH CHECK ----------------
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized or session expired"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ---------------- DATABASE CONNECTION ----------------
require_once "../db.php";
if (!$conn) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . mysqli_connect_error()
    ]);
    exit;
}

// ---------------- LOAD INSIGHT ENGINE ----------------
$insight_path = __DIR__ . "/../insights/insight_engine.php";
$insights = [];

try {
    if (file_exists($insight_path)) {
        $insights = require $insight_path; // Must return an array
    }
} catch (Throwable $e) {
    error_log("Insight engine error: " . $e->getMessage());
}

// ---------------- SET DEFAULTS IF EMPTY ----------------
$insights = array_merge([
    "goal_stagnation" => false,
    "habit_consistency" => 0,
    "sentiment_trend" => [
        "Positive" => 0,
        "Negative" => 0,
        "Neutral" => 0
    ]
], $insights);

// ---------------- RETURN JSON ----------------
echo json_encode([
    "status" => "success",
    "insights" => $insights
]);

$conn->close();
