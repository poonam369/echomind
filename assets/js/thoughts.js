document.getElementById("saveThoughtBtn").addEventListener("click", () => {
    const content = document.getElementById("thoughtContent").value.trim();
    const category = document.getElementById("thoughtCategory").value;
    const msg = document.getElementById("thoughtMsg");

    if (!content || !category) {
        msg.innerText = "Please write a thought and select a category.";
        return;
    }

    fetch("backend/api/save_thought.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ content, category })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            msg.innerText = "Thought saved successfully ✔";
            document.getElementById("thoughtContent").value = "";
            document.getElementById("thoughtCategory").value = "";
        } else {
            msg.innerText = "Failed to save thought.";
        }
    })
    .catch(() => {
        msg.innerText = "Server error. Try again.";
    });
});
