<?php
declare(strict_types=1);

require __DIR__ . '/includes/functions.php';
require __DIR__ . '/query_system.php';

function isAjax(): bool
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function respond(
    bool $success,
    string $message,
    int $code = 200,
    array $extra = [],
    string $ticket = ''
): void {
    if (isAjax()) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $extra));

        exit;
    }

    $status = $success ? 'success' : 'error';

    $url = base_url('Contact.php?status=' . $status);

    if ($ticket !== '') {
        $url .= '&ticket=' . rawurlencode($ticket);
    }

    redirect($url);
}

if (!is_post()) {
    respond(false, 'Invalid request method.', 405);
}

if (!verify_csrf()) {
    respond(false, 'Security validation failed. Please refresh the page and try again.', 419);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Honeypot spam trap
$honeypot = trim($_POST['website'] ?? '');

if ($honeypot !== '') {
    respond(true, 'Your query has been received.');
}

if ($name === '' || $email === '' || $message === '') {
    respond(false, 'All fields are required.', 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Please enter a valid email address.', 422);
}

if (strlen($name) > 100) {
    respond(false, 'Name is too long.', 422);
}

if (strlen($email) > 150) {
    respond(false, 'Email is too long.', 422);
}

if (strlen($message) > 5000) {
    respond(false, 'Message is too long.', 422);
}

$query = add_query($name, $email, $message);

/*
    Optional automatic email confirmation.
    For real websites, PHPMailer + SMTP is better than mail().
*/
$sendAutoEmail = false;

if ($sendAutoEmail) {
    $subject = 'We received your Bazaar query';

    $body = "Hello {$name},\n\n";
    $body .= "We received your query.\n\n";
    $body .= "Ticket ID: {$query['id']}\n\n";
    $body .= "You can use this Ticket ID and your email to check for a response.\n\n";
    $body .= "Thank you,\nBazaar Team\n";

    $headers = 'From: no-reply@yourwebsite.com' . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

    @mail($email, $subject, $body, $headers);
}

respond(
    true,
    'Your query has been received. Ticket ID: ' . $query['id'],
    200,
    [
        'ticketId' => $query['id']
    ],
    $query['id']
);
