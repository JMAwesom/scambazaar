        <footer id="footer">
            <ul>
                <li><a href="<?php echo e(base_url('About.php')); ?>">About Us</a></li>
                <li><a href="<?php echo e(base_url('Services.php')); ?>">Services</a></li>
                <li><a href="<?php echo e(base_url('Contact.php')); ?>">Contact Us</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </footer>

    </div>

    <script src="<?php echo e(asset('js/burger-menu.js')); ?>"></script>

    <?php foreach ($pageScripts as $script): ?>
        <script src="<?php echo e(asset($script)); ?>"></script>
    <?php endforeach; ?>
</body>
</html>
