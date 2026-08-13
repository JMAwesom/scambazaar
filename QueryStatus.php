<?php
require_once 'includes/functions.php';
require_once 'query_system.php';

$pageTitle = 'Bazaar - Query Status';

$pageStyles = [
    'css/contact.css',
    'css/query-status.css'
];

$error = '';
$query = null;

if (is_post()) {
    if (!verify_csrf()) {
        $error = 'Security validation failed. Please refresh and try again.';
    } else {
        $ticket = trim($_POST['ticket'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($ticket === '' || $email === '') {
            $error = 'Please enter both your Ticket ID and your Email.';
        } else {
            $query = find_query_by_id_and_email($ticket, $email);

            if (!$query) {
                $error = 'No query found for that Ticket ID and Email.';
            }
        }
    }
}

require 'includes/header.php';
?>

<section class="page-hero">
    <h1>Query Status</h1>
    <p>
        Enter your Ticket ID and the email you used to check the response to your query.
    </p>
</section>

<main class="contact-section">

    <?php if ($error !== ''): ?>
        <div class="contact-alert error">
            <?php echo e($error); ?>
        </div>
    <?php endif; ?>

    <form class="contact-form" method="POST" action="<?php echo e(base_url('QueryStatus.php')); ?>">
        <?php echo csrf_field(); ?>

        <div class="form-group">
            <label for="ticket">Ticket ID</label>
            <input
                type="text"
                id="ticket"
                name="ticket"
                placeholder="Example: BZ-1A2B3C4D"
                value="<?php echo e(trim($_POST['ticket'] ?? '')); ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter the email you used"
                value="<?php echo e(trim($_POST['email'] ?? '')); ?>"
                required
            >
        </div>

        <div class="contact-actions">
            <button type="submit" class="cta-btn">Check Query</button>
        </div>
    </form>

    <?php if ($query): ?>
        <div class="query-result">
            <h2>Ticket: <?php echo e($query['id']); ?></h2>

            <div class="query-result-meta">
                <strong>Status:</strong>

                <?php if ($query['status'] === 'answered'): ?>
                    <span class="query-status-badge answered">Answered</span>
                <?php else: ?>
                    <span class="query-status-badge pending">Pending</span>
                <?php endif; ?>

                <br>

                <strong>Submitted:</strong>
                <?php echo e(date('M j, Y g:i A', strtotime($query['date']))); ?>

                <br>

                <strong>From:</strong>
                <?php echo e($query['name']); ?>
            </div>

            <h3>Your Message</h3>

            <div class="query-original-message">
                <?php echo e($query['message']); ?>
            </div>

            <h3>Response</h3>

            <?php if (empty($query['responses'])): ?>
                <p class="no-response-yet">
                    No response yet. Please check again later.
                </p>
            <?php else: ?>
                <?php foreach ($query['responses'] as $response): ?>
                    <div class="query-response-item">
                        <div class="response-meta">
                            <strong><?php echo e($response['sender']); ?></strong>
                            replied on
                            <?php echo e(date('M j, Y g:i A', strtotime($response['date']))); ?>
                        </div>

                        <div class="query-response-message">
                            <?php echo e($response['message']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <p class="query-status-link">
        <a href="<?php echo e(base_url('Contact.php')); ?>">Back to Contact Us</a>
    </p>

</main>

<?php require 'includes/footer.php'; ?>
