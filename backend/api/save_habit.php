<?php
session_start();
header("Content-Type: application/json");
if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}
include "../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$name = trim($data['habit_name'] ?? '');
$frequency = trim($data['frequency'] ?? '');
$user_id = $_SESSION['user_id'];

if(!$name){
    echo json_encode(["status"=>"error","message"=>"Habit name required"]);
    exit;
}

// Generate unique ID for habit
$habit_id = bin2hex(random_bytes(16)); // 32 char hex = char(36) compatible

$stmt = $conn->prepare("
    INSERT INTO habits (id, user_id, name, frequency, streak) 
    VALUES (?, ?, ?, ?, 0)
");
if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}

$stmt->bind_param("ssss", $habit_id, $user_id, $name, $frequency);

if($stmt->execute()){
    echo json_encode(["status"=>"success","id"=>$habit_id]);
}else{
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>
