const askBtn = document.getElementById('askBtn');
const askInput = document.getElementById('askInput');
const askSection = document.getElementById('askSection');

askBtn.addEventListener('click', async () => {
    const question = askInput.value.trim();
    if(!question) {
        alert('Please type a question.');
        return;
    }

    try {
        const formData = new URLSearchParams();
        formData.append('question', question);

        const res = await fetch('backend/api/ask_echomind.php', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();

        // Display answer
        const div = document.createElement('div');
        div.classList.add('thoughtCard');
        div.textContent = data.answer;
        askSection.prepend(div);

        askInput.value = '';
    } catch(err) {
        console.error(err);
        alert('Error contacting EchoMind.');
    }
});
