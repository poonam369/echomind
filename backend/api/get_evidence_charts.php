<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status"=>"error"]); exit;
}

require_once __DIR__ . "/../db.php";
$user_id = (int) $_SESSION['user_id'];

/* Thought sentiment trend (last 7 days) */
$trend = ["labels"=>[], "Positive"=>[], "Negative"=>[], "Neutral"=>[]];

$stmt = $conn->prepare("
    SELECT DATE(created_at) d, sentiment, COUNT(*) c
    FROM thoughts
    WHERE user_id=? AND created_at >= CURDATE() - INTERVAL 6 DAY
    GROUP BY d, sentiment
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$res = $stmt->get_result();

$tmp=[];
while($r=$res->fetch_assoc()){
    $tmp[$r['d']][$r['sentiment']]=$r['c'];
}

for($i=6;$i>=0;$i--){
    $d=date("Y-m-d",strtotime("-$i days"));
    $trend["labels"][]=date("D",strtotime($d));
    foreach(["Positive","Negative","Neutral"] as $s){
        $trend[$s][]=$tmp[$d][$s] ?? 0;
    }
}

echo json_encode(["status"=>"success","thoughtTrend"=>$trend]);
$conn->close();
