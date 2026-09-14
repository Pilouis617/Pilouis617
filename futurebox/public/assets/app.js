'use strict';

// Ces interactions améliorent l'interface. Les contrôles restent aussi en PHP.
document.querySelectorAll('[data-password]').forEach(button => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.password);
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        button.textContent = reveal ? 'Masquer' : 'Afficher';
        button.setAttribute('aria-pressed', String(reveal));
    });
});

const message = document.getElementById('message');
if (message) {
    const updateCount = () => {
        document.getElementById('message-count').textContent =
            Array.from(message.value).length.toLocaleString('fr-FR') + ' / 10 000';
    };
    message.addEventListener('input', updateCount);
    updateCount();
}

const photo = document.getElementById('photo');
if (photo) {
    const preview = document.getElementById('photo-preview');
    const remove = document.getElementById('remove-photo');
    const feedback = document.getElementById('photo-error');
    let previewUrl;

    function clearPreview() {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        previewUrl = undefined;
        preview.hidden = true;
        preview.removeAttribute('src');
        remove.hidden = true;
    }
    photo.addEventListener('change', () => {
        clearPreview();
        feedback.textContent = '';
        const file = photo.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 3 * 1024 * 1024) {
            feedback.textContent = 'Choisis une photo JPG, PNG ou WebP de 3 Mo maximum.';
            photo.value = '';
            return;
        }
        previewUrl = URL.createObjectURL(file);
        preview.src = previewUrl;
        preview.hidden = false;
        remove.hidden = false;
    });
    preview.addEventListener('error', () => {
        clearPreview();
        photo.value = '';
        feedback.textContent = 'Cette image ne peut pas être affichée. Choisis une autre photo.';
    });
    remove.addEventListener('click', () => {
        clearPreview();
        photo.value = '';
        feedback.textContent = '';
    });
}

const countdown = document.querySelector('[data-deadline]');
if (countdown) {
    const deadline = Number(countdown.dataset.deadline);
    const serverNow = Number(countdown.dataset.serverNow);
    const started = performance.now();
    let timer;

    function updateCountdown() {
        const remaining = Math.max(0, Math.ceil((deadline - serverNow - (performance.now() - started)) / 1000));
        const values = {
            days: Math.floor(remaining / 86400),
            hours: Math.floor(remaining / 3600) % 24,
            minutes: Math.floor(remaining / 60) % 60,
            seconds: remaining % 60,
        };
        Object.entries(values).forEach(([key, value]) => {
            countdown.querySelector('[data-' + key + ']').textContent = String(value).padStart(2, '0');
        });
        if (remaining === 0) {
            clearInterval(timer);
            document.getElementById('countdown-status').textContent =
                'Le moment est arrivé. Clique sur « Vérifier l’ouverture » pour ouvrir ta capsule.';
        }
    }
    timer = setInterval(updateCountdown, 1000);
    updateCountdown();
}
