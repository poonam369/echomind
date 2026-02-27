<?php include "layout/header.php"; ?>

<main>
    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>
                    Echo your thoughts.<br>
                    <span>Shape your mind.</span>
                </h1>
                <p class="hero-subtext">
                    EchoMind is a calm, private AI-powered space designed
                    for students and professionals to reflect, build habits,
                    and gain meaningful insights.
                </p>
                <div class="hero-actions">
                    <a href="register.php" class="btn">Get Started</a>
                    <a href="login.php" class="btn btn-outline">Login</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="neural-orb">
                    <!-- Neurons -->
                    <div class="neuron" style="top:20%; left:25%;"></div>
                    <div class="neuron" style="top:50%; left:70%;"></div>
                    <div class="neuron" style="top:70%; left:40%;"></div>
                    <div class="neuron" style="top:30%; left:60%;"></div>
                    <div class="neuron" style="top:60%; left:20%;"></div>
                       <!-- Sparkles / glitter -->
<span class="sparkle s1"></span>
<span class="sparkle s2"></span>
<span class="sparkle s3"></span>
<span class="sparkle s4"></span>

                    <!-- Neural connections -->
                    <svg class="neural-lines" width="100%" height="100%">
                        <line x1="25%" y1="20%" x2="70%" y2="50%" />
                        <line x1="70%" y1="50%" x2="40%" y2="70%" />
                        <line x1="40%" y1="70%" x2="60%" y2="30%" />
                        <line x1="60%" y1="30%" x2="20%" y2="60%" />
                        <line x1="20%" y1="60%" x2="25%" y2="20%" />
                    </svg>

                    <!-- Orbiting particles -->
                    <div class="orbiting-particles">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features container">
        <h2 class="section-title">What EchoMind Helps You Do</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <div class="icon-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" 
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M12 2C9.243 2 7 4.243 7 7v1H5a2 2 0 00-2 2v2a2 2 0 002 2h2v1a5 5 0 0010 0v-1h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2V7c0-2.757-2.243-5-5-5z"/>
                    </svg>
                </div>
                <h3>Reflect Deeply</h3>
                <ul>
                    <li>Capture your thoughts privately</li>
                    <li>Analyze emotional patterns</li>
                    <li>Track insights over time</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="icon-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" 
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3>Build Habits</h3>
                <ul>
                    <li>Track daily routines easily</li>
                    <li>Set reminders and notifications</li>
                    <li>Visualize habit streaks</li>
                </ul>
            </div>

            <div class="feature-card">
                <div class="icon-glow">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" 
                        viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m0 0l-3-3m3 3l3-3"/>
                    </svg>
                </div>
                <h3>Clarify Goals</h3>
                <ul>
                    <li>Define meaningful personal goals</li>
                    <li>Track progress visually</li>
                    <li>Receive actionable insights</li>
                </ul>
            </div>
        </div>
    </section>
</main>

<?php include "layout/footer.php"; ?>

<script>
const orb = document.querySelector('.neural-orb');
const orbiting = document.querySelectorAll('.orbiting-particles span');

document.addEventListener('mousemove', (e) => {
    const x = e.clientX / window.innerWidth - 0.5;
    const y = e.clientY / window.innerHeight - 0.5;

    // Orb tilt
    orb.style.transform = `rotateY(${x*15}deg) rotateX(${-y*15}deg)`;

    // Orbiting particles react
    orbiting.forEach((p, idx) => {
        const offsetX = x*30*(idx+1)/orbiting.length;
        const offsetY = y*30*(idx+1)/orbiting.length;
        p.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
    });
});
</script>
