<!DOCTYPE html>
<html>
<head>
    <title>Layout with Sections</title>
    <?= App\Utils\View::getSection('head') ?>
</head>
<body>
    <header><h1>App Header</h1></header>
    <main><?= $content ?></main>
    <footer><small>Footer</small></footer>
    <?= App\Utils\View::getSection('scripts') ?>
</body>
</html>
