<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include "backend/db.php";
include "layout/header.php";
include "layout/sidebar.php";
?>

<main class="dashboard-main">

    <div class="dashboard-hero">
        <h1>Your Thoughts</h1>
        <p>Capture what’s on your mind. EchoMind will organize it.</p>
    </div>

    <!-- Add Thought Form -->
<!-- Add Thought Form -->
<section class="recent-section">
    <h2>Add a New Thought</h2>
    <div>
        <style>
            /* Container for subtle card effect */
            .thought-card {
                background: rgba(255,255,255,0.03);
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.25);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                margin-top: 10px;
            }

            /* Textarea styling */
            #thoughtContent {
                width: 100%;
                min-height: 120px;
                padding: 12px 16px;
                border-radius: 12px;
                border: 1px solid rgba(255,255,255,0.2);
                background: rgba(255,255,255,0.05);
                color: #fff;
                font-size: 1rem;
                resize: vertical;
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
                backdrop-filter: blur(8px);
                transition: all 0.2s ease-in-out;
            }

            #thoughtContent:focus {
                border-color: rgba(124,94,255,0.6);
                box-shadow: 0 0 6px rgba(124,94,255,0.5);
                outline: none;
            }

            /* Select styling */
            

            #thoughtCategory {
    width: 100%;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    background-color: #1f1f1f; /* dark background instead of semi-transparent */
    color: #fff; /* white text */
    font-size: 1rem;
    margin-top: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    backdrop-filter: blur(8px);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}

/* Style dropdown options */
#thoughtCategory option {
    background-color: #1f1f1f; /* same dark background */
    color: #fff; /* white text */
}

/* Focus effect */
#thoughtCategory:focus {
    border-color: rgba(124,94,255,0.6);
    box-shadow: 0 0 6px rgba(124,94,255,0.5);
    outline: none;
}


            /* Save button subtle */
            #saveThoughtBtn {
                background: rgba(124,94,255,0.8);
                color: #fff;
                border-radius: 12px;
                padding: 10px 20px;
                border: none;
                cursor: pointer;
                margin-top: 12px;
                transition: all 0.2s ease-in-out;
            }

            #saveThoughtBtn:hover {
                background: rgba(124,94,255,1);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(124,94,255,0.3);
            }

            /* Optional: placeholder color */
            #thoughtContent::placeholder {
                color: rgba(255,255,255,0.6);
            }
        </style>

        <div class="thought-card">
            <textarea id="thoughtContent" placeholder="Write something here..."></textarea>
            <select id="thoughtCategory">
                <option value="">Select Category</option>
                <option value="Reflection">Reflection</option>
                <option value="Stress">Stress</option>
                <option value="Career">Career</option>
                <option value="Gratitude">Gratitude</option>
                <option value="Focus">Focus</option>
                <option value="Learning">Learning</option>
                <option value="Personal">Personal</option>
                <option value="Ideas">Ideas</option>
                <option value="Memories">Memories</option>
                <option value="Goals">Goals</option>
            </select>
            <button class="btn" id="saveThoughtBtn">Save Thought</button>
            <p id="thoughtMsg"></p>
        </div>
    </div>
</section>



</main>

<script src="assets/js/thoughts.js"></script>

<?php include "layout/footer.php"; ?>
