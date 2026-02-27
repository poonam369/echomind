

<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);


ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode(["status"=>"error","message"=>"Unauthorized"]);
    exit;
}

require_once "../db.php";
if (!$conn) {
    echo json_encode(["status"=>"error","message"=>"DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$content = trim($data['content'] ?? '');
$category = trim($data['category'] ?? '');

if ($content === '' || $category === '') {
    echo json_encode(["status"=>"error","message"=>"Content and category required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$sentiment = "Neutral";
$summary = substr($content,0,120);

$stmt = $conn->prepare("INSERT INTO thoughts (user_id, content, category, sentiment, summary) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $user_id, $content, $category, $sentiment, $summary);

if (!$stmt->execute()) {
    echo json_encode(["status"=>"error","message"=>"DB insert failed: ".$stmt->error]);
    exit;
}

echo json_encode([
    "status"=>"success",
    "sentiment"=>$sentiment,
    "time"=>date("Y-m-d H:i:s")
]);

$stmt->close();
$conn->close();
