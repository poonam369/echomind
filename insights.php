<?php
session_start();
if (!isset($_SESSION['user_id'])) exit;

require_once "backend/db.php";
$user_id_int = (int) $_SESSION['user_id'];
$user_id_str = (string) $_SESSION['user_id'];
$today = date('Y-m-d');

/* ================= SENTIMENT (LAST 14 DAYS) ================= */
$sentimentDays = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $sentimentDays[$d] = ['Positive'=>0,'Neutral'=>0,'Negative'=>0];
}

$sql = "SELECT DATE(created_at) d, sentiment, COUNT(*) c
        FROM thoughts
        WHERE user_id=? AND created_at >= DATE_SUB(?, INTERVAL 14 DAY)
        GROUP BY d, sentiment";
$stmt = $conn->prepare($sql);
if (!$stmt) die("Thoughts query failed");
$stmt->bind_param("is", $user_id_int, $today);
$stmt->execute();
$r = $stmt->get_result();
while($row = $r->fetch_assoc()){
    if (isset($sentimentDays[$row['d']])) {
        $sentimentDays[$row['d']][$row['sentiment']] = (int)$row['c'];
    }
}
$stmt->close();

/* Totals */
$pos=$neu=$neg=0;
foreach($sentimentDays as $v){ $pos+=$v['Positive']; $neu+=$v['Neutral']; $neg+=$v['Negative']; }

/* ================= HABITS ================= */
$stmt = $conn->prepare("SELECT COUNT(*) total FROM habits WHERE user_id=?");
if (!$stmt) die("Habits query failed");
$stmt->bind_param("i", $user_id_int);
$stmt->execute();
$h = $stmt->get_result()->fetch_assoc();
$stmt->close();
$totalHabits = (int)$h['total'];
$habitPercent = $totalHabits>0 ? min(100,$totalHabits*20) : 0;

/* Habit indicator */
if ($habitPercent >= 70) $habitIndicator = "🟢";
elseif ($habitPercent >= 40) $habitIndicator = "🟡";
else $habitIndicator = "🔴";

/* ================= GOAL STAGNATION ================= */
$stmt = $conn->prepare("SELECT COUNT(*) c FROM goals WHERE user_id=? AND status='pending'");
if (!$stmt) die("Goal stagnation query failed");
$stmt->bind_param("s", $user_id_str);
$stmt->execute();
$g = $stmt->get_result()->fetch_assoc();
$stmt->close();
$goalStagnation = $g['c']>0;
$goalIndicator = $goalStagnation ? "🔺" : "🔻";

/* ================= GOAL TREND (LAST 14 DAYS) ================= */
$goalTrend = [];
for ($i = 13; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $goalTrend[$d] = ['pending'=>0,'completed'=>0];
}
$stmt = $conn->prepare("SELECT DATE(created_at) d, status, COUNT(*) c
                        FROM goals
                        WHERE user_id=? AND created_at >= DATE_SUB(?, INTERVAL 14 DAY)
                        GROUP BY d, status");
if (!$stmt) die("Goal trend query failed");
$stmt->bind_param("ss", $user_id_str, $today);
$stmt->execute();
$r = $stmt->get_result();
while($row=$r->fetch_assoc()){
    if(isset($goalTrend[$row['d']])) $goalTrend[$row['d']][$row['status']] = (int)$row['c'];
}
$stmt->close();

/* ================= SENTIMENT TREND (LAST 7 DAYS) ================= */
$sentimentTrend7 = array_slice($sentimentDays, -7, 7, true);
$posTrend=$neuTrend=$negTrend=0;
$prev=null;
foreach($sentimentTrend7 as $day=>$vals){
    if($prev){
        $posTrend+=$vals['Positive']-$prev['Positive'];
        $neuTrend+=$vals['Neutral']-$prev['Neutral'];
        $negTrend+=$vals['Negative']-$prev['Negative'];
    }
    $prev=$vals;
}
$posArrow = $posTrend>=0?"🔺":"🔻";
$neuArrow = $neuTrend>=0?"🔺":"🔻";
$negArrow = $negTrend>=0?"🔺":"🔻";
?>

<section class="recent-section">
<h2>Insights Overview</h2>
<p>Evidence-based analysis derived from your thoughts, habits, and goals.</p>

<div class="insight-card <?= $goalStagnation?'warning':'' ?>">
<h3>Goal Progress Signals <?= $goalIndicator ?></h3>
<p><?= $goalStagnation
? "Your goals remain active without measurable completion signals."
: "Your goals show healthy execution and closure." ?></p>
</div>

<div class="insight-card">
<h3>Habit Consistency <?= $habitIndicator ?></h3>
<div class="progress-bar">
<div class="progress-fill" style="width:<?= $habitPercent ?>%"><?= $habitPercent ?>%</div>
</div>
</div>

<div class="insight-card">
<h3>Thought Sentiment (14 Days)</h3>
<div class="sentiment-bar">
<div class="sentiment positive"><?= $pos ?> <?= $posArrow ?></div>
<div class="sentiment neutral"><?= $neu ?> <?= $neuArrow ?></div>
<div class="sentiment negative"><?= $neg ?> <?= $negArrow ?></div>
</div>
</div>

<div class="insight-card" style="height:200px;">
<h3>Sentiment Trend</h3>
<canvas id="sentimentChart"></canvas>
</div>

<div class="insight-card" style="height:200px;">
<h3>Goal Completion Trend</h3>
<canvas id="goalChart"></canvas>
</div>

<div class="insight-card">
<p>Confidence: <?= min(100,$habitPercent+15) ?>%</p>
<p>Status: <?= $goalStagnation?'attention':'new' ?></p>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const sentimentData = <?= json_encode($sentimentDays) ?>;
new Chart(document.getElementById('sentimentChart'),{
type:'line',
data:{
labels:Object.keys(sentimentData),
datasets:[
{label:'Positive',data:Object.values(sentimentData).map(v=>v.Positive),borderColor:'#4caf50',backgroundColor:'rgba(76,175,80,0.2)',tension:0.3},
{label:'Neutral',data:Object.values(sentimentData).map(v=>v.Neutral),borderColor:'#ffc107',backgroundColor:'rgba(255,193,7,0.2)',tension:0.3},
{label:'Negative',data:Object.values(sentimentData).map(v=>v.Negative),borderColor:'#f44336',backgroundColor:'rgba(244,67,54,0.2)',tension:0.3}
]},
options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}
});

const goalData = <?= json_encode($goalTrend) ?>;
new Chart(document.getElementById('goalChart'),{
type:'line',
data:{
labels:Object.keys(goalData),
datasets:[
{label:'Pending',data:Object.values(goalData).map(v=>v.pending),borderColor:'#f44336',backgroundColor:'rgba(244,67,54,0.2)',tension:0.3},
{label:'Completed',data:Object.values(goalData).map(v=>v.completed),borderColor:'#4caf50',backgroundColor:'rgba(76,175,80,0.2)',tension:0.3}
]},
options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}
});
</script>

<style>
.insight-card{background:rgba(255,255,255,.05);padding:16px;border-radius:12px;margin-bottom:12px;overflow:hidden;}
.progress-bar{height:20px;background:rgba(255,255,255,.1);border-radius:12px;overflow:hidden;}
.progress-fill{height:100%;background:#4caf50;color:#fff;text-align:center;line-height:20px;font-weight:500;}
.sentiment-bar{display:flex;height:20px;border-radius:12px;overflow:hidden;font-weight:500;}
.sentiment{flex:1;text-align:center;color:#fff;line-height:20px;}
.positive{background:#4caf50;}
.neutral{background:#ffc107;color:#111;}
.negative{background:#f44336;}
.warning{border:1px solid #f44336;}
.recent-section{max-width:960px;margin:auto;}
canvas{width:100%!important;height:100%!important;}
</style>
