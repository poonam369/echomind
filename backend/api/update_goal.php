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
$action = $data['action'] ?? '';

if(!$id || !$action){
    echo json_encode(["status"=>"error","message"=>"Invalid request"]);
    exit;
}

if($action === "increment"){
    $stmt = $conn->prepare("UPDATE goals SET status = LEAST(status+10,100) WHERE id=?");
} elseif($action === "complete"){
    $stmt = $conn->prepare("UPDATE goals SET status = 100 WHERE id=?");
} else{
    echo json_encode(["status"=>"error","message"=>"Unknown action"]);
    exit;
}

if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}

$stmt->bind_param("s",$id);
$stmt->execute();
$stmt->close();

// Return updated progress
$stmt = $conn->prepare("SELECT status FROM goals WHERE id=?");
$stmt->bind_param("s",$id);
$stmt->execute();
$status = $stmt->get_result()->fetch_assoc()['status'];
$stmt->close();
$conn->close();

echo json_encode(["status"=>"success","progress"=>$status]);
?>
