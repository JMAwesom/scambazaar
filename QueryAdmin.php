<?php
declare(strict_types=1);

require __DIR__ . '/includes/functions.php';
require __DIR__ . '/query_system.php';
require __DIR__ . '/includes/mailer.php';

/*
    IMPORTANT:
    Generate a password hash using:

    php -r "echo password_hash('YourStrongPasswordHere', PASSWORD_DEFAULT), PHP_EOL;"

    Then paste the hash below.
*/
const ADMIN_PASSWORD_HASH = '$2y$10$fxk1vPgJgw5xuZ1HFEw88eXql1zEO.8SlIZcuAoJdE7FaexsJqlVa';

function format_query_date(string $date): string
{
    $time = strtotime($date);

    return $time ? date('M j, Y g:i A', $time) : 'Unknown';
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect(base_url('QueryAdmin.php'));
}

$loggedIn = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;

// Login screen
if (!$loggedIn) {
    $error = '';

    if (is_post()) {
        if (!verify_csrf()) {
            $error = 'Security validation failed. Please refresh and try again.';
        } elseif (ADMIN_PASSWORD_HASH === 'PASTE_YOUR_PASSWORD_HASH_HERE') {
            $error = 'Please set ADMIN_PASSWORD_HASH first.';
        } elseif (password_verify((string)($_POST['admin_password'] ?? ''), ADMIN_PASSWORD_HASH)) {
            session_regenerate_id(true);
            $_SESSION['admin_logged'] = true;

            redirect(base_url('QueryAdmin.php'));
        } else {
            $error = 'Incorrect password.';
        }
    }

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Bazaar - Query Admin Login</title>

        <link rel="stylesheet" href="<?php echo e(asset('css/base.css')); ?>">

        <style>
            .admin-login-wrap {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .admin-login-card {
                width: min(420px, 100%);
                background: #111827;
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 14px;
                padding: 30px;
                box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
            }

            .admin-login-card h1 {
                margin: 0 0 18px;
                font-size: 24px;
                color: #f2d5cf;
                text-align: center;
                font-family: "JetBrainsMonoNerdFont-BoldItalic", "JetBrains Mono Nerd Font", monospace;
            }

            .admin-login-card input[type="password"] {
                width: 100%;
                padding: 13px 14px;
                border-radius: 10px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                background: #232634;
                color: #c6d0f5;
                margin-bottom: 14px;
            }

            .admin-login-card input[type="password"]:focus {
                outline: none;
                border-color: #8caaee;
                box-shadow: 0 0 0 3px rgba(140, 170, 238, 0.15);
            }

            .admin-login-card button {
                width: 100%;
                padding: 13px;
                border: none;
                border-radius: 10px;
                background: #8caaee;
                color: #232634;
                font-weight: 700;
                cursor: pointer;
            }

            .admin-login-card button:hover {
                background: #a6c0f5;
            }

            .admin-error {
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 14px;
                font-size: 14px;
                background: rgba(234, 158, 158, 0.12);
                color: #ea9a9a;
                border: 1px solid rgba(234, 158, 158, 0.35);
            }
        </style>
    </head>
    <body>
        <div class="admin-login-wrap">
            <div class="admin-login-card">
                <h1>Query Admin</h1>

                <?php if ($error !== ''): ?>
                    <div class="admin-error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(base_url('QueryAdmin.php')); ?>">
                    <?php echo csrf_field(); ?>

                    <input
                        type="password"
                        name="admin_password"
                        placeholder="Enter admin password"
                        required
                    >

                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle reply submission
if (is_post() && isset($_POST['query_id'], $_POST['reply_message'])) {
    if (!verify_csrf()) {
        flash_set('error', 'Security validation failed.');
    } else {
        $queryId = trim((string)$_POST['query_id']);
        $replyMessage = trim((string)$_POST['reply_message']);

        if ($queryId === '' || $replyMessage === '') {
            flash_set('error', 'Reply message cannot be empty.');
        } else {
            $success = add_query_response($queryId, $replyMessage);

            if ($success) {
                $query = find_query_by_id($queryId);

                if ($query && !empty($query['email'])) {
                    $emailResult = send_query_response_email($query, $replyMessage);

                    if ($emailResult['success']) {
                        flash_set(
                            'success',
                            'Response saved and emailed to ' . $query['email'] . '.'
                        );
                    } else {
                        flash_set(
                            'error',
                            'Response saved, but email failed: ' .
                            ($emailResult['error'] ?? 'Unknown mail error.')
                        );
                    }
                } else {
                    flash_set(
                        'success',
                        'Response saved, but this query does not have a valid email address.'
                    );
                }
            } else {
                flash_set('error', 'Could not save response.');
            }
        }
    }

    redirect(base_url('QueryAdmin.php'));
}

$queries = load_queries();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bazaar - Query Admin</title>

    <link rel="stylesheet" href="<?php echo e(asset('css/base.css')); ?>">

    <style>
        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 20px;
            background: linear-gradient(180deg, #1b1f2a 0%, #232634 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .admin-topbar h1 {
            margin: 0;
            font-size: 22px;
            color: #f2d5cf;
            font-family: "JetBrainsMonoNerdFont-BoldItalic", "JetBrains Mono Nerd Font", monospace;
        }

        .admin-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-topbar-actions a {
            color: #8caaee;
            text-decoration: none;
            font-size: 14px;
        }

        .admin-topbar-actions a:hover {
            color: #f2d5cf;
            text-decoration: underline;
        }

        .admin-wrap {
            width: min(1100px, calc(100% - 40px));
            margin: 26px auto 60px;
        }

        .empty-state {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 34px;
            text-align: center;
            color: #949cbb;
        }

        .query-card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 20px;
        }

        .query-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .ticket-id {
            font-family: "JetBrainsMonoNerdFont-BoldItalic", "JetBrains Mono Nerd Font", monospace;
            color: #f2d5cf;
            font-size: 18px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .status-badge.pending {
            color: #e5c890;
            background: rgba(229, 200, 144, 0.12);
            border: 1px solid rgba(229, 200, 144, 0.35);
        }

        .status-badge.answered {
            color: #a6d189;
            background: rgba(166, 209, 137, 0.12);
            border: 1px solid rgba(166, 209, 137, 0.35);
        }

        .query-meta {
            color: #949cbb;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 14px;
        }

        .query-message-box,
        .response-box {
            background: #232634;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 14px;
            white-space: pre-wrap;
            line-height: 1.6;
        }

        .section-label {
            margin: 16px 0 8px;
            color: #f2d5cf;
            font-size: 15px;
            font-weight: 700;
        }

        .response-item {
            margin-top: 12px;
            padding-left: 14px;
            border-left: 3px solid #8caaee;
        }

        .response-meta {
            font-size: 12px;
            color: #949cbb;
            margin-bottom: 8px;
        }

        .reply-form textarea {
            width: 100%;
            min-height: 120px;
            resize: vertical;
            background: #232634;
            color: #c6d0f5;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            padding: 13px 14px;
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.6;
        }

        .reply-form textarea:focus {
            outline: none;
            border-color: #8caaee;
            box-shadow: 0 0 0 3px rgba(140, 170, 238, 0.15);
        }

        .reply-form button {
            margin-top: 12px;
            padding: 12px 22px;
            border: none;
            border-radius: 999px;
            background: #8caaee;
            color: #232634;
            font-weight: 700;
            cursor: pointer;
        }

        .reply-form button:hover {
            background: #a6c0f5;
        }
    </style>
</head>
<body>
    <header class="admin-topbar">
        <h1>Bazaar Query Admin</h1>

        <div class="admin-topbar-actions">
            <a href="<?php echo e(base_url('Contact.php')); ?>">Contact Page</a>
            <a href="<?php echo e(base_url('QueryStatus.php')); ?>">Query Status</a>
            <a href="<?php echo e(base_url('QueryAdmin.php?logout=1')); ?>">Logout</a>
        </div>
    </header>

    <main class="admin-wrap">
        <?php echo flash_render(); ?>

        <?php if (empty($queries)): ?>
            <div class="empty-state">
                No queries have been submitted yet.
            </div>
        <?php else: ?>
            <?php foreach ($queries as $query): ?>
                <?php
                    $queryId = (string)($query['id'] ?? '');
                    $queryName = (string)($query['name'] ?? 'Unknown');
                    $queryEmail = (string)($query['email'] ?? '');
                    $queryMessage = (string)($query['message'] ?? '');
                    $queryStatus = (string)($query['status'] ?? 'pending');
                    $queryDate = (string)($query['date'] ?? '');
                    $responses = $query['responses'] ?? [];
                ?>

                <article class="query-card" id="query-<?php echo e($queryId); ?>">
                    <div class="query-card-top">
                        <div class="ticket-id"><?php echo e($queryId); ?></div>

                        <?php if ($queryStatus === 'answered'): ?>
                            <span class="status-badge answered">Answered</span>
                        <?php else: ?>
                            <span class="status-badge pending">Pending</span>
                        <?php endif; ?>
                    </div>

                    <div class="query-meta">
                        <strong>Name:</strong> <?php echo e($queryName); ?><br>
                        <strong>Email:</strong> <?php echo e($queryEmail); ?><br>
                        <strong>Date:</strong> <?php echo e(format_query_date($queryDate)); ?>
                    </div>

                    <div class="section-label">Query Message</div>

                    <div class="query-message-box">
                        <?php echo e($queryMessage); ?>
                    </div>

                    <?php if (!empty($responses)): ?>
                        <div class="section-label">Responses</div>

                        <?php foreach ($responses as $response): ?>
                            <div class="response-item">
                                <div class="response-meta">
                                    <strong><?php echo e((string)($response['sender'] ?? 'Admin')); ?></strong>
                                    responded on
                                    <?php echo e(format_query_date((string)($response['date'] ?? ''))); ?>
                                </div>

                                <div class="response-box">
                                    <?php echo e((string)($response['message'] ?? '')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="section-label">Write a Response</div>

                    <form class="reply-form" method="POST" action="<?php echo e(base_url('QueryAdmin.php')); ?>">
                        <?php echo csrf_field(); ?>

                        <input type="hidden" name="query_id" value="<?php echo e($queryId); ?>">

                        <textarea
                            name="reply_message"
                            placeholder="Write your response to this query..."
                            required
                        ></textarea>

                        <button type="submit">Send Response</button>
                    </form>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</body>
</html>
