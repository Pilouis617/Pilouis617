<a class="back-link" href="index.php">← Mes capsules</a>
<?php if (!$capsule): ?>
<section class="empty panel"><h1>Capsule introuvable.</h1><p>Elle n’existe plus ou n’appartient pas à ton compte.</p><a class="button" href="index.php">Mes capsules</a></section>
<?php else: $open = $message !== null; ?>
<section class="page-heading"><div><p class="eyebrow"><?= $open ? 'LE MOMENT EST ARRIVÉ' : 'LE TEMPS FAIT SON CHEMIN' ?></p><h1><?= escape($capsule['title']) ?></h1><p class="muted">Scellée le <?= dateLabel((int) $capsule['created_at']) ?></p></div><span class="badge <?= $open ? 'ready' : '' ?>"><?= $open ? 'DISPONIBLE' : 'SCELLÉE' ?></span></section>
<?php if ($open): ?>
<article class="opened-letter panel">
    <div class="letter-heading"><span>UN MESSAGE DE TOI, POUR TOI.</span><span><?= dateLabel((int) $capsule['open_at']) ?></span></div>
    <div class="letter-text"><?= escape($message) ?></div>
    <?php if ($capsule['photo_mime']): ?><img class="capsule-photo" src="index.php?page=photo&amp;id=<?= (int) $capsule['id'] ?>" alt="Photo conservée dans cette capsule"><?php endif; ?>
    <p class="letter-signature">De toi, à un autre moment.</p>
</article>
<?php else: ?>
<section class="locked panel">
    <span class="mini-label">CERTAINES CHOSES MÉRITENT D’ATTENDRE.</span>
    <h2>On se retrouve bientôt.</h2>
    <p>Ta capsule s’ouvrira le <strong><?= dateLabel((int) $capsule['open_at']) ?></strong>.</p>
    <div class="countdown" data-deadline="<?= (int) $capsule['open_at'] * 1000 ?>" data-server-now="<?= time() * 1000 ?>">
        <div><strong data-days>—</strong><span>jours</span></div><div><strong data-hours>—</strong><span>heures</span></div><div><strong data-minutes>—</strong><span>minutes</span></div><div><strong data-seconds>—</strong><span>secondes</span></div>
    </div>
    <p id="countdown-status">Ton message<?= $capsule['photo_mime'] ? ' et ta photo sont conservés' : ' est conservé' ?> jusqu’à cette date.</p>
    <a class="button secondary" href="index.php?page=capsule&amp;id=<?= (int) $capsule['id'] ?>">Vérifier l’ouverture</a>
</section>
<?php endif; ?>
<details class="delete-zone"><summary>Supprimer cette capsule</summary><p>Cette action efface définitivement le message et la photo, même si la capsule est encore scellée.</p><form method="post" action="index.php?page=capsule&amp;id=<?= (int) $capsule['id'] ?>"><?= csrfField() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $capsule['id'] ?>"><label class="checkbox"><input type="checkbox" name="confirm_delete" value="yes" required> Je confirme la suppression définitive.</label><button class="button danger" type="submit">Supprimer définitivement</button></form></details>
<?php endif; ?>
