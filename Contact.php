<?php
require_once 'includes/functions.php';

$pageTitle = 'Bazaar - Contact Us';

$pageStyles = [
    'css/contact.css'
];

$pageScripts = [
    'js/contact.js'
];

$status = $_GET['status'] ?? '';
$ticket = preg_replace('/[^A-Za-z0-9\-]/', '', $_GET['ticket'] ?? '');

$statusType = '';
$statusMessage = '';

if ($status === 'success') {
    $statusType = 'success';
    $statusMessage = 'Your query has been received. We will get back to you soon.';

    if ($ticket !== '') {
        $statusMessage .= ' Your Ticket ID is: ' . $ticket . '. Keep this to check your response.';
    }
}

if ($status === 'error') {
    $statusType = 'error';
    $statusMessage = 'There was a problem with your query. Please check the fields and try again.';
}

require 'includes/header.php';
?>

<section class="page-hero">
    <h1>Contact Us</h1>
    <p>
        Have a question, suggestion, or issue? Send us a message and our team will receive your query.
    </p>
</section>

<main class="contact-section">

    <div
        id="contactAlert"
        class="contact-alert <?php echo e($statusType); ?>"
        <?php echo empty($statusMessage) ? 'hidden' : ''; ?>
    >
        <?php echo e($statusMessage); ?>
    </div>

    <form
        id="contactForm"
        class="contact-form"
        action="<?php echo e(base_url('receive_query.php')); ?>"
        method="POST"
    >
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                autocomplete="name"
                maxlength="100"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                autocomplete="email"
                maxlength="150"
                required
            >
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea
                id="message"
                name="message"
                rows="8"
                placeholder="Write your query here..."
                maxlength="5000"
                required
            ></textarea>
        </div>

        <input
            type="text"
            name="website"
            class="hp-field"
            tabindex="-1"
            autocomplete="off"
            aria-hidden="true"
        >

        <div class="contact-actions">
            <button type="submit" class="cta-btn">Submit</button>
        </div>
    </form>

    <p class="query-status-link">
        <a href="<?php echo e(base_url('QueryStatus.php')); ?>">
            Already submitted a query? Check your response here.
        </a>
    </p>

</main>

<?php require 'includes/footer.php'; ?>
