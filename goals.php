<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include "backend/db.php";
$user_id = $_SESSION['user_id'];

include "layout/header.php";
include "layout/sidebar.php";
?>

<main class="dashboard-main">
    <div class="dashboard-hero">
        <h1>Your Goals</h1>
        <p>Track and achieve your goals professionally.</p>
    </div>

    <section class="recent-section">
        <div>
            <style>
                .goal-card {
                    background: rgba(255,255,255,0.03);
                    padding: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
                    backdrop-filter: blur(8px);
                    margin-bottom: 16px;
                }

                input, textarea, select {
                    width: 100%;
                    padding: 10px 14px;
                    border-radius: 12px;
                    border: 1px solid rgba(255,255,255,0.2);
                    background: rgba(255,255,255,0.05);
                    color: #fff;
                    font-size: 1rem;
                    margin-bottom: 10px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                    backdrop-filter: blur(8px);
                }

                #addGoalBtn {
                    background: rgba(124,94,255,0.8);
                    color: #fff;
                    border-radius: 12px;
                    padding: 10px 20px;
                    border: none;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                }

                #addGoalBtn:hover {
                    background: rgba(124,94,255,1);
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(124,94,255,0.3);
                }

                .goal-list-item {
                    padding: 12px 16px;
                    border-radius: 12px;
                    margin-bottom: 10px;
                    background: rgba(255,255,255,0.05);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-direction: column;
                }

                .goal-progress {
                    width: 100%;
                    height: 12px;
                    border-radius: 6px;
                    background: rgba(255,255,255,0.2);
                    margin: 6px 0;
                }

                .goal-progress-inner {
                    height: 100%;
                    border-radius: 6px;
                    background: rgba(124,94,255,0.8);
                    width: 0%;
                    transition: width 0.3s ease;
                }

                .goal-buttons button {
                    margin: 4px;
                    background: rgba(0,0,0,0.2);
                    color: #fff;
                    border: none;
                    padding: 6px 12px;
                    border-radius: 8px;
                    cursor: pointer;
                }

                .goal-buttons button:hover {
                    background: rgba(0,0,0,0.35);
                }
            </style>

            <!-- Add Goal Form -->
            <div class="goal-card">
                <input type="text" id="goalTitle" placeholder="Goal title">
                <textarea id="goalDescription" placeholder="Goal description (optional)"></textarea>
                <input type="date" id="goalDeadline">
                <label><input type="checkbox" id="goalSmart"> SMART Goal</label>
                <button id="addGoalBtn">Add Goal</button>
                <p id="goalMsg"></p>
            </div>

            <div id="goalList">
                <?php
                $stmt = $conn->prepare("SELECT id, title, description, deadline, is_smart, smart_feedback, status FROM goals WHERE user_id=? ORDER BY created_at DESC");
                if(!$stmt) die("Prepare failed: ".$conn->error);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()){
                    $progress = (int)$row['status'];
                    echo '<div class="goal-list-item" data-id="'.$row['id'].'">
                            <strong>'.htmlspecialchars($row['title']).'</strong>
                            <span>'.htmlspecialchars($row['description']).'</span>
                            <small>Deadline: '.$row['deadline'].'</small>
                            <div class="goal-progress"><div class="goal-progress-inner" style="width:'.$progress.'%"></div></div>
                            <div class="goal-buttons">
                                <button class="incrementGoal">+10%</button>
                                <button class="markComplete">Complete</button>
                            </div>
                          </div>';
                }
                $stmt->close();
                ?>
            </div>

        </div>
    </section>
</main>

<script>
const addGoalBtn = document.getElementById("addGoalBtn");
addGoalBtn.addEventListener("click", ()=>{
    const title = document.getElementById("goalTitle").value.trim();
    const description = document.getElementById("goalDescription").value.trim();
    const deadline = document.getElementById("goalDeadline").value;
    const is_smart = document.getElementById("goalSmart").checked ? 1 : 0;
    const msg = document.getElementById("goalMsg");

    if(!title || !deadline){
        msg.innerText = "Title and deadline are required";
        return;
    }

    fetch("backend/api/save_goal.php", {
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify({title, description, deadline, is_smart})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status==="success"){
            msg.innerText = "Goal added successfully!";
            document.getElementById("goalTitle").value = "";
            document.getElementById("goalDescription").value = "";
            document.getElementById("goalDeadline").value = "";
            document.getElementById("goalSmart").checked = false;

            const div = document.createElement("div");
            div.classList.add("goal-list-item");
            div.dataset.id = data.id;
            div.innerHTML = `<strong>${title}</strong>
                             <span>${description}</span>
                             <small>Deadline: ${deadline}</small>
                             <div class="goal-progress"><div class="goal-progress-inner" style="width:0%"></div></div>
                             <div class="goal-buttons">
                                 <button class="incrementGoal">+10%</button>
                                 <button class="markComplete">Complete</button>
                             </div>`;
            document.getElementById("goalList").prepend(div);
        } else {
            msg.innerText = "Failed to add goal: " + (data.message || '');
        }
    })
    .catch(err=>{
        console.error(err);
        msg.innerText = "Error adding goal";
    });
});

// Handle dynamic buttons
document.getElementById("goalList").addEventListener("click", e=>{
    const parent = e.target.closest(".goal-list-item");
    if(!parent) return;
    const id = parent.dataset.id;

    if(e.target.classList.contains("incrementGoal")){
        fetch("backend/api/update_goal.php", {
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body: JSON.stringify({id, action:"increment"})
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==="success"){
                parent.querySelector(".goal-progress-inner").style.width = data.progress+"%";
            }
        });
    } else if(e.target.classList.contains("markComplete")){
        fetch("backend/api/update_goal.php", {
            method:"POST",
            headers:{"Content-Type":"application/json"},
            body: JSON.stringify({id, action:"complete"})
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==="success"){
                parent.querySelector(".goal-progress-inner").style.width = "100%";
            }
        });
    }
});
</script>

<?php include "layout/footer.php"; ?>
