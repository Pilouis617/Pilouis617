<a class="back-link" href="index.php">← Mes capsules</a>
<section class="page-heading"><div><p class="eyebrow">ÉCRIRE AU TEMPS</p><h1>Pour toi, plus tard.</h1><p class="muted">Pas besoin de trouver les mots parfaits. Les tiens suffisent.</p></div></section>
<div class="compose-layout">
<form class="panel compose-form" method="post" enctype="multipart/form-data" action="index.php?page=new">
    <?= csrfField() ?><input type="hidden" name="action" value="create"><input type="hidden" name="MAX_FILE_SIZE" value="3145728">
    <div class="field"><label for="title">Donne un titre à ta capsule</label><input id="title" name="title" maxlength="100" placeholder="Le jour où tout a commencé…" value="<?= escape(posted('title')) ?>" required><p class="field-help">Le titre restera visible une fois la capsule scellée.</p></div>
    <div class="field"><label for="message">Ton message</label><textarea id="message" name="message" rows="10" maxlength="10000" placeholder="Cher moi du futur," required><?= escape(posted('message')) ?></textarea><div class="field-meta"><span>Un souvenir, une envie, une question…</span><span id="message-count">0 / 10 000</span></div></div>
    <div class="field"><label for="open_at">Quand veux-tu l’ouvrir ?</label><input id="open_at" name="open_at" type="datetime-local" value="<?= escape(posted('open_at')) ?>" required><p class="field-help">Heure de Paris. La date sera définitive une fois la capsule scellée.</p></div>
    <div class="field"><label for="photo">Une photo, si tu veux <span class="optional">facultatif</span></label><div class="upload"><input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="photo-help"><p id="photo-help">JPG, PNG ou WebP · 3 Mo maximum</p><p id="photo-error" role="status"></p><img id="photo-preview" alt="Aperçu de la photo choisie" hidden><button type="button" id="remove-photo" class="text-button" hidden>Retirer la photo</button></div><?php if ($error): ?><p class="field-help">Si tu avais ajouté une photo, sélectionne-la à nouveau.</p><?php endif; ?></div>
    <div class="seal-note">Après l’envoi, ton message et ta photo ne seront plus consultables avant la date choisie.</div>
    <button class="button" type="submit">Sceller ma capsule <span aria-hidden="true">↗</span></button>
</form>
<aside class="writing-notes">
    <span class="mini-label">UNE PAGE POUR TON FUTUR</span>
    <h2>Qu’aimerais-tu<br>ne pas oublier ?</h2>
    <p>Ce qui te rend heureux en ce moment. Les petites choses que tu espères. Un endroit où tu voudrais retourner.</p>
    <hr>
    <h3>Comment ça se passe ?</h3>
    <ol><li>Tu écris et tu choisis une date.</li><li>Ta capsule reste scellée.</li><li>Tu reviens ici le jour choisi pour l’ouvrir.</li></ol>
    <p class="small">Aucun e-mail automatique n’est envoyé. Pense à revenir consulter tes capsules.</p>
</aside>
</div>
