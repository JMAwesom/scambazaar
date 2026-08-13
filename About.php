<?php
require_once 'includes/functions.php';

$pageTitle = 'Bazaar - About Us';

$pageStyles = [
    'css/AboutWS.css'
];

require 'includes/header.php';
?>

<section class="page-hero">
    <h1>About The Bazaar</h1>
    <p>
        We are a passionate collective of gamers, creators, and dreamers building the ultimate digital playground.
    </p>
</section>

<main class="about-main">
    <section class="about-grid">
        <article class="about-card">
            <span class="about-icon">🎯</span>
            <h3>Our Mission</h3>
            <p>
                To connect players with unforgettable experiences and foster a community where every voice matters.
            </p>
        </article>

        <article class="about-card">
            <span class="about-icon">🔮</span>
            <h3>Our Vision</h3>
            <p>
                To become the most trusted and vibrant hub for game discovery, discussion, and delight worldwide.
            </p>
        </article>

        <article class="about-card">
            <span class="about-icon">💎</span>
            <h3>Core Values</h3>
            <ul class="value-list">
                <li>Community First — We build with and for our players.</li>
                <li>Creative Freedom — We celebrate bold ideas and diverse voices.</li>
                <li>Radical Transparency — We communicate openly and honestly.</li>
            </ul>
        </article>
    </section>

    <section class="team-section">
        <h2>Meet the Team</h2>
        <p class="team-subtitle">The wonderful people behind the curtain.</p>

        <div class="team-grid">
            <article class="team-card">
                <div class="team-avatar"></div>
                <h4>Alex "Amenty" Rivera</h4>
                <span class="team-role">Lead Curator & Community Manager</span>
                <p class="team-bio">
                    Alex has been hunting for hidden gems in the indie scene for over a decade.
                    They speak fluent RPG and coffee.
                </p>
            </article>

            <article class="team-card">
                <div class="team-avatar"></div>
                <h4>Jordan "Zamoray" Chen</h4>
                <span class="team-role">Technical Director & Game Whisperer</span>
                <p class="team-bio">
                    Jordan can debug anything from a pixelated texture to a broken timeline.
                    They also make a mean pixel art.
                </p>
            </article>

            <article class="team-card">
                <div class="team-avatar"></div>
                <h4>Sam "Haxon" Ortiz</h4>
                <span class="team-role">Creative Director & Lore Archivist</span>
                <p class="team-bio">
                    Sam lives and breathes storytelling. If it has a narrative, they've probably written a wiki about it.
                </p>
            </article>
        </div>
    </section>

    <section class="stats-section">
        <div class="stat-item">
            <span class="stat-number">12+</span>
            <span class="stat-label">Years of Service</span>
        </div>

        <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Games Curated</span>
        </div>

        <div class="stat-item">
            <span class="stat-number">45k</span>
            <span class="stat-label">Community Members</span>
        </div>

        <div class="stat-item">
            <span class="stat-number">∞</span>
            <span class="stat-label">Passion</span>
        </div>
    </section>

    <section class="cta-section">
        <h2>Want to join the adventure?</h2>
        <p>We're always looking for collaborators, testers, and dreamers.</p>

        <a class="cta-btn" href="<?php echo e(base_url('Community.php')); ?>">Join Our Community</a>
    </section>
</main>

<?php require 'includes/footer.php'; ?>
