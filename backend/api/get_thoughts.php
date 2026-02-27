<?php
session_start();
header("Content-Type: application/json");
if(!isset($_SESSION['user_id'])){ echo json_encode([]); exit; }
include "../db.php"; $user_id=$_SESSION['user_id'];
$stmt=$conn->prepare("SELECT content, category, sentiment, summary, DATE_FORMAT(created_at,'%d/%m/%Y, %h:%i %p') as time FROM thoughts WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i",$user_id); $stmt->execute();
$result=$stmt->get_result();
echo json_encode($result->fetch_all(MYSQLI_ASSOC));
$stmt->close();
$conn->close();
?>
