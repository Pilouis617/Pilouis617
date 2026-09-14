<?php $register = $page === 'register'; ?>
<div class="auth-layout">
    <section class="auth-story">
        <p class="eyebrow">DES MOTS QUI SAVENT ATTENDRE</p>
        <h1>Bonjour,<br>toi du <em>futur.</em></h1>
        <p>Un souvenir, une promesse, une photo. Enferme un petit morceau d’aujourd’hui et retrouve-le quand le moment sera venu.</p>
        <div class="letter-sample"><span class="mini-label">UNE IDÉE POUR TA PREMIÈRE CAPSULE</span><blockquote>« J’espère que tu as enfin osé faire ce dont on parlait… »</blockquote><span class="mini-label">À ouvrir dans un an.</span></div>
        <p class="small">Le titre reste visible. Le message et la photo attendent la date choisie.</p>
    </section>
    <section class="auth-form panel">
        <p class="eyebrow"><?= $register ? 'LE DÉBUT D’UNE HISTOIRE' : 'CONTENT DE TE REVOIR' ?></p>
        <h2><?= $register ? 'Créer mon compte' : 'Reprendre le fil' ?></h2>
        <p class="muted"><?= $register ? 'Quelques informations, et tu peux commencer à écrire.' : 'Tes souvenirs t’attendent ici.' ?></p>
        <form method="post" action="index.php?page=<?= $register ? 'register' : 'login' ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="<?= $register ? 'register' : 'login' ?>">
            <?php if ($register): ?>
            <div class="field"><label for="name">Prénom</label><input id="name" name="name" autocomplete="given-name" maxlength="60" value="<?= escape(posted('name')) ?>" required></div>
            <?php endif; ?>
            <div class="field"><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" autocomplete="email" maxlength="190" value="<?= escape(posted('email')) ?>" required></div>
            <div class="field"><label for="password">Mot de passe</label><div class="password-wrap"><input id="password" name="password" type="password" autocomplete="<?= $register ? 'new-password' : 'current-password' ?>" <?= $register ? 'minlength="10"' : '' ?> required><button class="reveal" type="button" data-password="password" aria-controls="password" aria-pressed="false">Afficher</button></div><?php if ($register): ?><p class="field-help">10 caractères minimum. Maximum : 72 octets (un caractère accentué peut en occuper plusieurs).</p><?php endif; ?></div>
            <?php if ($register): ?>
            <div class="field"><label for="confirm_password">Confirmer le mot de passe</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required></div>
            <?php endif; ?>
            <button class="button full" type="submit"><?= $register ? 'Créer mon compte' : 'Me connecter' ?> <span aria-hidden="true">↗</span></button>
        </form>
        <p class="auth-switch"><?= $register ? 'Déjà un compte ?' : 'Première visite ?' ?> <a href="index.php?page=<?= $register ? 'login' : 'register' ?>"><?= $register ? 'Me connecter' : 'Créer mon compte' ?></a></p>
    </section>
</div>
