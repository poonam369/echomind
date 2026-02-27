<?php
session_start();
header("Content-Type: application/json");
if(!isset($_SESSION['user_id'])){
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}
include "../db.php";

$data = json_decode(file_get_contents("php://input"), true);
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');
$deadline = $data['deadline'] ?? '';
$is_smart = $data['is_smart'] ?? 0;
$user_id = $_SESSION['user_id'];

if(!$title || !$deadline){
    echo json_encode(["status"=>"error","message"=>"Title and deadline required"]);
    exit;
}

$goal_id = bin2hex(random_bytes(16));

$stmt = $conn->prepare("
    INSERT INTO goals (id, user_id, title, description, deadline, is_smart, status) 
    VALUES (?, ?, ?, ?, ?, ?, 0)
");
if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}
$stmt->bind_param("sssssi", $goal_id, $user_id, $title, $description, $deadline, $is_smart);

if($stmt->execute()){
    echo json_encode(["status"=>"success","id"=>$goal_id]);
}else{
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}
$stmt->close();
$conn->close();
?>
