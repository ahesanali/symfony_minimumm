<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Simple Dashboard</h1>
    <p>Welcome to Simple symfony application. Here we are demonstrating a login functionality with simple symfony application development framework. That delights development workflow.</p>

    <h3>Logged in user detail</h3>
    <p>User ID: <?= $loggedInUser->id ?></p>
    <p>User Name: <?= $loggedInUser->firstName.' '.$loggedInUser->lastName ?></p>
    <!--  route('logout') -->
    <p><a href="<?= route('app_logout') ?>">Click here to Logout</a></p>
    <h5><?= renderFlashMessages($session)?></h5>
</body>
</html>