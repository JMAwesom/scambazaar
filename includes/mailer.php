<?php
declare(strict_types=1);

require_once __DIR__ . '/mail_config.php';

/**
 * Send an email using PHPMailer + Gmail SMTP.
 */
function send_bazaar_email(
    string $to,
    string $subject,
    string $textBody,
    string $htmlBody = ''
): array {
    try {
        if (!defined('MAIL_ENABLED') || MAIL_ENABLED !== true) {
            return [
                'success' => false,
                'error' => 'Email sending is disabled.'
            ];
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'error' => 'Invalid recipient email address.'
            ];
        }

        $phpMailerDirectory = __DIR__ . '/PHPMailer/src';

        $requiredFiles = [
            $phpMailerDirectory . '/Exception.php',
            $phpMailerDirectory . '/PHPMailer.php',
            $phpMailerDirectory . '/SMTP.php'
        ];

        foreach ($requiredFiles as $file) {
            if (!is_file($file)) {
                return [
                    'success' => false,
                    'error' => 'PHPMailer file not found: ' . basename($file)
                ];
            }

            require_once $file;
        }

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();

        $mail->Host = MAIL_SMTP_HOST;
        $mail->Port = MAIL_SMTP_PORT;
        $mail->SMTPAuth = true;

        $mail->Username = MAIL_SMTP_USERNAME;
        $mail->Password = MAIL_SMTP_PASSWORD;

        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

        $mail->CharSet = PHPMailer\PHPMailer\PHPMailer::CHARSET_UTF8;

        if (defined('MAIL_DEBUG') && MAIL_DEBUG === true) {
            $mail->SMTPDebug = 2;
        } else {
            $mail->SMTPDebug = 0;
        }

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addReplyTo(MAIL_REPLY_TO_EMAIL, MAIL_REPLY_TO_NAME);

        $mail->addAddress($to);

        $mail->Subject = $subject;

        if ($htmlBody !== '') {
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody;
        } else {
            $mail->isHTML(false);
            $mail->Body = $textBody;
        }

        $mail->send();

        return [
            'success' => true,
            'message' => 'Email sent successfully.'
        ];

    } catch (Throwable $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Send a query response email to the person who made the query.
 */
function send_query_response_email(array $query, string $replyMessage): array
{
    $to = trim((string)($query['email'] ?? ''));
    $name = trim((string)($query['name'] ?? 'there'));
    $ticket = trim((string)($query['id'] ?? ''));

    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return [
            'success' => false,
            'error' => 'The query does not have a valid email address.'
        ];
    }

    $subject = 'Response to your Bazaar query ' . $ticket;

    $textBody = "Hello {$name},\n\n";
    $textBody .= "Your query received a response from Bazaar Support.\n\n";
    $textBody .= "Ticket ID: {$ticket}\n\n";
    $textBody .= "Response:\n";
    $textBody .= $replyMessage . "\n\n";
    $textBody .= "Thank you,\n";
    $textBody .= "Bazaar Team\n";

    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeTicket = htmlspecialchars($ticket, ENT_QUOTES, 'UTF-8');
    $safeReply = nl2br(htmlspecialchars($replyMessage, ENT_QUOTES, 'UTF-8'));

    $htmlBody = '
    <div style="font-family: Arial, sans-serif; background: #292c3c; padding: 20px;">
        <div style="max-width: 640px; margin: 0 auto; background: #111827; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; color: #c6d0f5;">
            <h1 style="margin: 0 0 16px; font-size: 20px; color: #f2d5cf;">
                Bazaar Query Response
            </h1>

            <p style="margin: 0 0 14px; line-height: 1.6;">
                Hello <strong>' . $safeName . '</strong>,
            </p>

            <p style="margin: 0 0 14px; line-height: 1.6;">
                Your query received a response:
            </p>

            <div style="background: #232634; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 14px; line-height: 1.6; white-space: pre-wrap;">
                ' . $safeReply . '
            </div>

            <p style="margin: 16px 0 0; font-size: 13px; color: #949cbb;">
                Ticket ID: <strong>' . $safeTicket . '</strong>
            </p>

            <p style="margin: 16px 0 0; font-size: 13px; color: #949cbb;">
                Thank you,<br>
                Bazaar Team
            </p>
        </div>
    </div>
    ';

    return send_bazaar_email($to, $subject, $textBody, $htmlBody);
}
