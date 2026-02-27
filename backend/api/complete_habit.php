<?php
session_start();
header("Content-Type: application/json");
if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}
include "../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? '';

if(!$id){
    echo json_encode(["status"=>"error","message"=>"Habit ID required"]);
    exit;
}

// Increment streak
$stmt = $conn->prepare("UPDATE habits SET streak=streak+1 WHERE id=?");
if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->close();

// Fetch updated habit
$stmt = $conn->prepare("SELECT name, frequency, streak FROM habits WHERE id=?");
$stmt->bind_param("s", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

$text = $row['name'].' ('.$row['frequency'].') - Completed: '.$row['streak'];
echo json_encode(["status"=>"success","text"=>$text]);
?>
