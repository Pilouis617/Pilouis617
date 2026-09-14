<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#11151a">
    <meta name="description" content="Garde un message pour plus tard. FutureBox conserve tes capsules jusqu’au jour choisi.">
    <title>FutureBox — <?= escape(['home' => 'Mes capsules', 'new' => 'Nouvelle capsule', 'login' => 'Connexion', 'register' => 'Créer un compte', 'capsule' => 'Ma capsule'][$page] ?? 'Mes capsules') ?></title>
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<a class="skip" href="#content">Aller au contenu</a>
<header class="topbar">
    <a class="logo" href="index.php"><span class="logo-mark" aria-hidden="true">F</span>future<span>box</span><span class="logo-dot" aria-hidden="true">.</span></a>
    <?php if ($user): ?>
        <nav aria-label="Navigation principale"><a href="index.php" <?= $page === 'home' ? 'aria-current="page"' : '' ?>>Mes capsules</a><a href="index.php?page=new" <?= $page === 'new' ? 'aria-current="page"' : '' ?>>Écrire une capsule</a></nav>
        <form method="post" action="index.php">
            <?= csrfField() ?><input type="hidden" name="action" value="logout">
            <button class="text-button" type="submit">Se déconnecter</button>
        </form>
    <?php else: ?>
        <span class="top-note">Un peu de toi. Pour plus tard.</span>
    <?php endif; ?>
</header>
<main id="content" class="container">
<?php if ($error !== ''): ?><div class="notice error" role="alert"><?= escape($error) ?></div><?php endif; ?>
<?php if (!empty($_SESSION['flash'])): ?>
    <div class="notice" role="status"><?= escape($_SESSION['flash']) ?></div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
