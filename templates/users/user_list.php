<?php ob_start(); ?>
<h2>Welcome to the User List page</h2>
<p>This is a sample PHP app with clean routing.</p>
<a href="<?= route('app_conference')?>">users</a>
<a href="<?= route('app_login')?>">Login</a>
<?php $content = ob_get_clean(); ?>

<?php include_once  templatePath() ."/main_layout.php"; ?>

