<?php
include "../db.php";
$tomorrow=date("Y-m-d",strtotime("+1 day"));
$res=$conn->query("SELECT id,name,email FROM users");
while($user=$res->fetch_assoc()){
    $user_id=$user['id']; $microTasks=[];
    $stmt=$conn->prepare("SELECT title,status FROM goals WHERE user_id=?"); $stmt->bind_param("s",$user_id); $stmt->execute(); $goals=$stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    foreach($goals as $g){ if($g['status']<100) $microTasks[]="Work towards goal '{$g['title']}'"; }
    $stmt=$conn->prepare("SELECT name,streak FROM habits WHERE user_id=?"); $stmt->bind_param("s",$user_id); $stmt->execute(); $habits=$stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    foreach($habits as $h){ if($h['streak']<7) $microTasks[]="Complete habit '{$h['name']}' to maintain streak"; }
    $stmt=$conn->prepare("INSERT INTO alerts (user_id,alert_date,message) VALUES (?,?,?)");
    foreach($microTasks as $task){ $stmt->bind_param("sss",$user_id,$tomorrow,$task); $stmt->execute(); }
    $stmt->close();
}
echo "Micro-task alerts generated for $tomorrow.";
?>
