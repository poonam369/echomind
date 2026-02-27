<?php
// ----------------- INSIGHT ENGINE FOR ECHOMIND -----------------
if (!isset($conn) || !isset($user_id)) return []; // Fail-safe

$insights = [];

// ----------------- TIME SETUP -----------------
date_default_timezone_set("Asia/Kolkata");
$today = date("Y-m-d");
$past_7_days = date("Y-m-d", strtotime("-7 days"));
$past_14_days = date("Y-m-d", strtotime("-14 days"));

// ----------------- FETCH USER THOUGHTS -----------------
$sql = "SELECT category, sentiment, created_at 
        FROM thoughts 
        WHERE user_id = ? 
        ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$thoughts = [];
while ($row = $result->fetch_assoc()) {
    $thoughts[] = $row;
}

// ----------------- SENTIMENT TREND -----------------
$sentiment_count = ["Positive" => 0, "Negative" => 0, "Neutral" => 0];
foreach ($thoughts as $t) {
    $sentiment_count[$t['sentiment']] = ($sentiment_count[$t['sentiment']] ?? 0) + 1;
}
$insights['sentiment_trend'] = $sentiment_count;

// ----------------- HABIT CONSISTENCY -----------------
$habit_days = [];
foreach ($thoughts as $t) {
    if (strtolower($t['category']) == "habit") {
        $day = substr($t['created_at'], 0, 10);
        $habit_days[$day] = true;
    }
}
$habit_consistency = count($habit_days) > 0 ? round((count($habit_days)/7)*100) : 0;
$insights['habit_consistency'] = min($habit_consistency, 100);

// ----------------- GOAL STAGNATION -----------------
$goal_categories = ["focus","goal","career","study"];
$last_goal_date = null;
foreach ($thoughts as $t) {
    if (in_array(strtolower($t['category']), $goal_categories)) {
        $last_goal_date = $t['created_at'];
        break; // Most recent goal found
    }
}
if ($last_goal_date) {
    $days_diff = (strtotime($today) - strtotime(substr($last_goal_date,0,10)))/86400;
    $insights['goal_stagnation'] = $days_diff > 7; // stagnant if >7 days
} else {
    $insights['goal_stagnation'] = false;
}

return $insights;
