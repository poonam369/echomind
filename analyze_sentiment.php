<?php
require_once "backend/db.php";
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if(!$user_id) {
    echo json_encode(["success"=>false,"error"=>"Not logged in"]);
    exit;
}

$text = $_POST['text'] ?? '';
$category = $_POST['category'] ?? '';

if(!$text) {
    echo json_encode(["success"=>false,"error"=>"Text is empty"]);
    exit;
}

// Example simple sentiment detection (replace with OpenAI if you want real AI)
$lower = strtolower($text);
if(strpos($lower,'happy')!==false || strpos($lower,'good')!==false) {
    $sentiment = "Positive";
} elseif(strpos($lower,'sad')!==false || strpos($lower,'bad')!==false) {
    $sentiment = "Negative";
} else {
    $sentiment = "Neutral";
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO thoughts (user_id, content, category, sentiment, created_at) VALUES (?,?,?,?,NOW())");
$stmt->bind_param("isss",$user_id,$text,$category,$sentiment);
$stmt->execute();

echo json_encode(["success"=>true,"sentiment"=>$sentiment]);
