<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status"=>"error"]); exit;
}

require_once __DIR__ . "/../db.php";
$user_id=(int)$_SESSION['user_id'];

$stmt=$conn->prepare("
    SELECT type,message,confidence,status,created_at
    FROM insights
    WHERE user_id=?
    ORDER BY created_at DESC
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$res=$stmt->get_result();

$data=[];
while($r=$res->fetch_assoc()){
    $data[]=[
        "type"=>$r['type'],
        "message"=>$r['message'],
        "confidence"=>$r['confidence'],
        "status"=>$r['status'],
        "time"=>date("d M Y, h:i A",strtotime($r['created_at']))
    ];
}

echo json_encode(["status"=>"success","history"=>$data]);
$conn->close();
