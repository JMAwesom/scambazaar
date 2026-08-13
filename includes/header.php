<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Bazaar';
$pageStyles = $pageStyles ?? [];
$pageScripts = $pageScripts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo e($pageTitle); ?></title>

    <link rel="stylesheet" href="<?php echo e(asset('css/base.css')); ?>">

    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="<?php echo e(asset($style)); ?>">
    <?php endforeach; ?>
</head>
<body>
    <div class="container">

        <header class="headcon">
            <a id="logo-holder" href="<?php echo e(base_url('index.php')); ?>">
                <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Bazaar Logo">
            </a>

            <nav class="main-nav" aria-label="Main navigation">
                <ul>
                    <li>
                        <a href="<?php echo e(base_url('index.php')); ?>" class="<?php echo e(is_active('index.php')); ?>">
                            Home
                        </a>
                    </li>

                    <li class="has-submenu">
                        <a href="<?php echo e(base_url('index.php')); ?>">Games</a>

                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(base_url('index.php')); ?>">This Season</a>
                            </li>
                        </ul>
                    </li>

                    <li class="has-submenu">
                        <a href="<?php echo e(base_url('Community.php')); ?>" class="<?php echo e(is_active('Community.php')); ?>">
                            Community
                        </a>

                        <ul class="submenu">
                            <li>
                                <a href="<?php echo e(base_url('Community.php')); ?>">Discussions</a>
                            </li>
                            <li><a href="#">Groups</a></li>
                            <li><a href="#">Workshop</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="<?php echo e(base_url('About.php')); ?>" class="<?php echo e(is_active('About.php')); ?>">
                            About Us
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(base_url('Services.php')); ?>" class="<?php echo e(is_active('Services.php')); ?>">
                            Services
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(base_url('Contact.php')); ?>" class="<?php echo e(is_active('Contact.php')); ?>">
                            Contact Us
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="searchbar">
                <input type="search" placeholder="Search" aria-label="Search">
            </div>
        </header>

        <?php echo flash_render(); ?>
