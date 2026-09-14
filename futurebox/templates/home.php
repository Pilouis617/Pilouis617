<?php
$now = time();
$ready = count(array_filter($capsules, fn(array $c): bool => canOpen($c, $now)));
$sealed = count($capsules) - $ready;
$visible = array_filter($capsules, fn(array $c): bool => $filter === 'all' || ($filter === 'ready' ? canOpen($c, $now) : !canOpen($c, $now)));
?>
<section class="page-heading">
    <div><p class="eyebrow">TON PETIT COIN DU TEMPS</p><h1>Bonjour, <?= escape($user['name']) ?>.</h1><p class="muted">Ce que tu gardes aujourd’hui te retrouvera demain.</p></div>
    <a class="button" href="index.php?page=new"><span aria-hidden="true">+</span> Nouvelle capsule</a>
</section>
<div class="stats" aria-label="Résumé des capsules">
    <div><span class="stat-number"><?= count($capsules) ?></span><span>capsule<?= count($capsules) > 1 ? 's' : '' ?> au total</span></div>
    <div><span class="stat-number"><?= $sealed ?></span><span>encore scellée<?= $sealed > 1 ? 's' : '' ?></span></div>
    <div><span class="stat-number accent"><?= $ready ?></span><span>prête<?= $ready > 1 ? 's' : '' ?> à ouvrir</span></div>
</div>
<div class="collection-head">
    <h2>Mes capsules</h2>
    <nav class="filters" aria-label="Filtrer les capsules">
        <?php foreach (['all' => 'Toutes', 'sealed' => 'Scellées', 'ready' => 'À ouvrir'] as $key => $label): ?>
        <a href="index.php?filter=<?= $key ?>" <?= $filter === $key ? 'aria-current="true"' : '' ?>><?= $label ?></a>
        <?php endforeach; ?>
    </nav>
</div>
<?php if (!$visible): ?>
<section class="empty panel">
    <span class="empty-symbol" aria-hidden="true">[ + ]</span>
    <h2><?= !$capsules ? 'Tout commence par quelques mots.' : 'Rien ici pour le moment.' ?></h2>
    <p><?= !$capsules ? 'Raconte ta journée, note un objectif ou garde une photo. Choisis une date et laisse le temps faire le reste.' : 'Tes autres capsules sont accessibles dans l’onglet Toutes.' ?></p>
    <a class="button <?= $capsules ? 'secondary' : '' ?>" href="<?= $capsules ? 'index.php' : 'index.php?page=new' ?>"><?= $capsules ? 'Voir toutes les capsules' : 'Écrire ma première capsule' ?></a>
</section>
<?php else: ?>
<div class="capsule-grid">
    <?php foreach ($visible as $item): $open = canOpen($item, $now); ?>
    <article class="capsule-card <?= $open ? 'is-ready' : '' ?>">
        <div class="card-top"><span class="badge <?= $open ? 'ready' : '' ?>"><?= $open ? 'À OUVRIR' : 'SCELLÉE' ?></span><span class="card-number">N° <?= str_pad((string) $item['id'], 3, '0', STR_PAD_LEFT) ?></span></div>
        <h3><a href="index.php?page=capsule&amp;id=<?= (int) $item['id'] ?>"><?= escape($item['title']) ?></a></h3>
        <p class="card-description"><?= $open ? 'Le moment est arrivé. Retrouve ce que tu avais gardé.' : 'Quelques mots t’attendent de l’autre côté du temps.' ?></p>
        <p class="card-date"><span>OUVERTURE</span><time datetime="<?= gmdate('c', (int) $item['open_at']) ?>"><?= dateLabel((int) $item['open_at']) ?></time></p>
        <div class="card-bottom"><span><?= $item['photo_mime'] ? 'Message + photo' : 'Message' ?></span><a class="card-link" href="index.php?page=capsule&amp;id=<?= (int) $item['id'] ?>"><?= $open ? 'Ouvrir' : 'Voir la capsule' ?> <span aria-hidden="true">↗</span></a></div>
    </article>
    <?php endforeach; ?>
</div>
<?php endif; ?>
