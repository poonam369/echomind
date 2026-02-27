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
        <h1>Your Habits</h1>
        <p>Track and manage your daily habits professionally.</p>
    </div>

    <section class="recent-section">
        <div>
            <style>
                .habit-card {
                    background: rgba(255,255,255,0.03);
                    padding: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
                    backdrop-filter: blur(8px);
                    -webkit-backdrop-filter: blur(8px);
                    margin-bottom: 16px;
                }

                #habitName {
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

                #habitFrequency {
                    width: 100%;
                    padding: 10px 14px;
                    border-radius: 12px;
                    border: 1px solid rgba(255,255,255,0.2);
                    background: #1f1f1f;
                    color: #fff;
                    font-size: 1rem;
                    margin-bottom: 10px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                    backdrop-filter: blur(8px);
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    cursor: pointer;
                }

                #habitFrequency option {
                    background-color: #1f1f1f;
                    color: #fff;
                }

                #addHabitBtn {
                    background: rgba(124,94,255,0.8);
                    color: #fff;
                    border-radius: 12px;
                    padding: 10px 20px;
                    border: none;
                    cursor: pointer;
                    transition: all 0.2s ease-in-out;
                }
                #addHabitBtn:hover {
                    background: rgba(124,94,255,1);
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(124,94,255,0.3);
                }

                .habit-list-item {
                    padding: 12px 16px;
                    border-radius: 12px;
                    margin-bottom: 10px;
                    background: rgba(255,255,255,0.05);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .habit-complete-btn {
                    background: rgba(0,0,0,0.2);
                    border: none;
                    color: #fff;
                    padding: 6px 12px;
                    border-radius: 8px;
                    cursor: pointer;
                }

                .habit-complete-btn:hover {
                    background: rgba(0,0,0,0.35);
                }
            </style>

            <div class="habit-card">
                <input type="text" id="habitName" placeholder="Enter habit name">
                <select id="habitFrequency">
                    <option value="Daily">Daily</option>
                    <option value="Weekly">Weekly</option>
                    <option value="Monthly">Monthly</option>
                </select>
                <button id="addHabitBtn">Add Habit</button>
                <p id="habitMsg"></p>
            </div>

            <div id="habitList">
                <?php
                // Fetch user habits
                $stmt = $conn->prepare("
                    SELECT id, name, frequency, streak 
                    FROM habits 
                    WHERE user_id=? 
                    ORDER BY created_at DESC
                ");
                if(!$stmt){
                    die("Prepare failed: ".$conn->error);
                }
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while($row = $result->fetch_assoc()){
                    echo '<div class="habit-list-item" data-id="'.$row['id'].'">
                            <span>'.htmlspecialchars($row['name']).' ('.htmlspecialchars($row['frequency']).') - Completed: '.$row['streak'].'</span>
                            <button class="habit-complete-btn">+1</button>
                          </div>';
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </section>
</main>

<script>
const addHabitBtn = document.getElementById("addHabitBtn");
addHabitBtn.addEventListener("click", ()=>{
    const name = document.getElementById("habitName").value.trim();
    const freq = document.getElementById("habitFrequency").value;
    const msg = document.getElementById("habitMsg");

    if(!name){
        msg.innerText = "Enter habit name";
        return;
    }

    fetch("backend/api/save_habit.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({habit_name: name, frequency: freq})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status === "success"){
            msg.innerText = "Habit added successfully!";
            document.getElementById("habitName").value = "";

            const div = document.createElement("div");
            div.classList.add("habit-list-item");
            div.dataset.id = data.id;
            div.innerHTML = `<span>${name} (${freq}) - Completed: 0</span>
                             <button class="habit-complete-btn">+1</button>`;
            document.getElementById("habitList").prepend(div);
        } else {
            msg.innerText = "Failed to add habit: " + (data.message || '');
        }
    })
    .catch(err=>{
        console.error(err);
        msg.innerText = "Error adding habit.";
    });
});

// Handle dynamic +1 complete buttons
document.getElementById("habitList").addEventListener("click", e=>{
    if(e.target.classList.contains("habit-complete-btn")){
        const parent = e.target.closest(".habit-list-item");
        const id = parent.dataset.id;
        fetch("backend/api/complete_habit.php", {
            method: "POST",
            headers: {"Content-Type":"application/json"},
            body: JSON.stringify({id})
        })
        .then(res=>res.json())
        .then(data=>{
            if(data.status==="success"){
                const span = parent.querySelector("span");
                span.innerText = data.text;
            }
        });
    }
});
</script>

<?php include "layout/footer.php"; ?>
