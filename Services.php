<?php
require_once 'includes/functions.php';

$pageTitle = 'Bazaar - Our Services';

$pageStyles = [
    'css/ServicesWS.css'
];

require 'includes/header.php';
?>

<section class="page-hero">
    <h1>Our Services</h1>
    <p>
        We offer a suite of services tailored for gamers, creators, and publishers alike.
    </p>
</section>

<main class="services-main">
    <section class="services-grid">
        <article class="service-card">
            <span class="service-icon">🎮</span>
            <h3>Game Curation & Discovery</h3>
            <p>
                Hand-picked recommendations, deep-dive reviews, and curated collections that help you find your next favorite game.
            </p>
        </article>

        <article class="service-card">
            <span class="service-icon">🤝</span>
            <h3>Community Management</h3>
            <p>
                We build and nurture thriving communities through events, moderation, and engagement strategies tailored to your audience.
            </p>
        </article>

        <article class="service-card">
            <span class="service-icon">🔧</span>
            <h3>Technical Support & QA</h3>
            <p>
                From bug hunting to performance optimization, our team ensures your games run smoothly across all platforms.
            </p>
        </article>

        <article class="service-card">
            <span class="service-icon">📦</span>
            <h3>Digital Distribution</h3>
            <p>
                Seamless delivery, secure downloads, and robust infrastructure to get your game into the hands of players worldwide.
            </p>
        </article>

        <article class="service-card">
            <span class="service-icon">📢</span>
            <h3>Marketing & Outreach</h3>
            <p>
                Creative campaigns, influencer partnerships, and strategic promotion to amplify your game's visibility and reach.
            </p>
        </article>

        <article class="service-card">
            <span class="service-icon">🎨</span>
            <h3>Creative & Art Services</h3>
            <p>
                Concept art, UI/UX design, trailer editing, and branding assets that bring your vision to life.
            </p>
        </article>
    </section>

    <section class="service-detail-block">
        <h2>How We Work</h2>

        <div class="detail-grid">
            <article class="detail-item">
                <span class="step-num">01</span>
                <h4>Discovery</h4>
                <p>We listen to your goals and understand your unique needs.</p>
            </article>

            <article class="detail-item">
                <span class="step-num">02</span>
                <h4>Strategy</h4>
                <p>We craft a tailored roadmap that aligns with your vision and timeline.</p>
            </article>

            <article class="detail-item">
                <span class="step-num">03</span>
                <h4>Execution</h4>
                <p>Our team works diligently to deliver high-quality results on time.</p>
            </article>

            <article class="detail-item">
                <span class="step-num">04</span>
                <h4>Iteration</h4>
                <p>We continuously refine and improve based on feedback and data.</p>
            </article>
        </div>
    </section>

    <section class="cta-section">
        <h2>Ready to level up your project?</h2>
        <p>Let's talk about how we can bring your ideas to life.</p>

        <a class="cta-btn" href="<?php echo e(base_url('Contact.php')); ?>">Get in Touch</a>
    </section>
</main>

<?php require 'includes/footer.php'; ?>
