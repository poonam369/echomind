<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "backend/db.php";
$user_id = $_SESSION['user_id'];

/* Fetch user info */
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

include "layout/header.php";
include "layout/sidebar.php";
?>

<main class="dashboard-main">

    <!-- HERO -->
    <div class="dashboard-hero">
        <h1>Welcome back, <?= htmlspecialchars($user['name']) ?>!</h1>
        <p>Here's your EchoMind overview today.</p>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="dashboard-actions">
        <a href="thoughts.php" class="btn">Add Thought</a>
        <a href="habits.php" class="btn">Update Habit</a>
        <a href="goals.php" class="btn">Add Goal</a>
    </div>

    <!-- OVERVIEW CARDS -->
    <section class="card-grid">
        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) total FROM thoughts WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $thoughts = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        ?>
        <div class="feature-card">
            <div class="icon-glow">📝</div>
            <h3>Total Thoughts</h3>
            <p><?= $thoughts ?></p>
        </div>

        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) total FROM habits WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $habits = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        ?>
        <div class="feature-card">
            <div class="icon-glow">✅</div>
            <h3>Total Habits</h3>
            <p><?= $habits ?></p>
        </div>

        <?php
        $stmt = $conn->prepare("SELECT COUNT(*) total FROM goals WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $goals = $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        ?>
        <div class="feature-card">
            <div class="icon-glow">🎯</div>
            <h3>Total Goals</h3>
            <p><?= $goals ?></p>
        </div>
    </section>

    <!-- ADD THOUGHT -->
    <section class="recent-section">
        <h2>Add a New Thought</h2>

        <textarea id="thoughtContent" placeholder="Write your thought clearly..."></textarea>

        <select id="thoughtCategory">
            <option value="">Select Category</option>
            <option>Reflection</option>
            <option>Stress</option>
            <option>Career</option>
            <option>Gratitude</option>
            <option>Focus</option>
            <option>Learning</option>
            <option>Personal</option>
            <option>Ideas</option>
            <option>Memories</option>
            <option>Goals</option>
        </select>

        <button class="btn" id="saveThoughtBtn">Save Thought</button>
        <p id="thoughtMsg"></p>
    </section>

    <!-- TOMORROW ALERTS -->
    <?php
    $tomorrow = date("Y-m-d", strtotime("+1 day"));
    $stmt = $conn->prepare("
        SELECT message 
        FROM alerts 
        WHERE user_id=? AND alert_date=? AND status=0
    ");
    $stmt->bind_param("is", $user_id, $tomorrow);
    $stmt->execute();
    $alerts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    ?>

    <?php if (!empty($alerts)): ?>
    <section class="recent-section">
        <h2>Tomorrow’s Focus</h2>
        <ul class="alert-list">
            <?php foreach ($alerts as $a): ?>
                <li><?= htmlspecialchars($a['message']) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <?php endif; ?>

</main>

<!-- SCOPED PROFESSIONAL INPUT STYLES -->
<style>
.dashboard-main textarea#thoughtContent {
    width: 100%;
    min-height: 110px;
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.06);
    color: #fff;
    font-size: 0.95rem;
    line-height: 1.6;
    resize: vertical;
    backdrop-filter: blur(10px);
}

.dashboard-main select#thoughtCategory {
    width: 100%;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(255,255,255,0.06);
    color: #fff;
    margin-top: 10px;
}

.dashboard-main select option {
    background: #111;
    color: #fff;
}

.alert-list {
    list-style: none;
    padding-left: 0;
}

.alert-list li {
    padding: 10px 14px;
    margin-bottom: 8px;
    border-radius: 10px;
    background: rgba(255,255,255,0.05);
}
</style>

<script>
document.getElementById("saveThoughtBtn").addEventListener("click", () => {
    const content = thoughtContent.value.trim();
    const category = thoughtCategory.value;
    if (!content || !category) return alert("Fill both fields");

    fetch("backend/api/save_thought.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({content, category})
    })
    .then(r => r.json())
    .then(d => {
        thoughtMsg.innerText = d.status === "success"
            ? "Thought saved successfully ✔"
            : "Failed to save thought";
        thoughtContent.value = "";
        thoughtCategory.value = "";
    });
});

<section class="recent-section" id="insightsSection">
    <?php include "insights.php"; ?>
</section>



</script>

<?php include "layout/footer.php"; ?>
