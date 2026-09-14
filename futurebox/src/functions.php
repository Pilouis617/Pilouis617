<?php
declare(strict_types=1);

function config(): array
{
    static $config;
    return $config ??= require dirname(__DIR__) . '/config.php';
}

function db(): PDO
{
    static $connection;
    if (!$connection) {
        $c = config();
        $connection = new PDO(
            "mysql:host={$c['host']};port={$c['port']};dbname={$c['database']};charset=utf8mb4",
            $c['user'],
            $c['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $connection;
}

function escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function posted(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
}

function csrfToken(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' . escape(csrfToken()) . '">';
}

function checkCsrf(): void
{
    if (!hash_equals(csrfToken(), posted('csrf'))) {
        throw new InvalidArgumentException('Le formulaire a expiré. Recharge la page puis réessaie.');
    }
}

function redirect(string $page = 'home'): void
{
    header('Location: index.php?page=' . $page, true, 303);
    exit;
}

function flash(string $message): void
{
    $_SESSION['flash'] = $message;
}

function dateLabel(int $timestamp): string
{
    return (new DateTimeImmutable('@' . $timestamp))
        ->setTimezone(new DateTimeZone(config()['timezone']))
        ->format('d/m/Y à H:i');
}

// L'horloge du serveur décide de l'ouverture, jamais celle du navigateur.
function canOpen(array $capsule, ?int $now = null): bool
{
    return (int) $capsule['open_at'] <= ($now ?? time());
}

function openingTimestamp(string $value, ?int $now = null): int
{
    $date = DateTimeImmutable::createFromFormat(
        '!Y-m-d\\TH:i', $value, new DateTimeZone(config()['timezone'])
    );
    $errors = DateTimeImmutable::getLastErrors();

    if (!$date || ($errors !== false && ($errors['warning_count'] || $errors['error_count']))
        || $date->format('Y-m-d\\TH:i') !== $value) {
        throw new InvalidArgumentException('Choisis une date et une heure valides.');
    }

    $timestamp = $date->getTimestamp();
    // Une heure répétée au passage à l'heure d'hiver est ambiguë.
    $zone = new DateTimeZone(config()['timezone']);
    foreach ([-3600, 3600] as $offset) {
        $other = (new DateTimeImmutable('@' . ($timestamp + $offset)))->setTimezone($zone);
        if ($other->format('Y-m-d\\TH:i') === $value) {
            throw new InvalidArgumentException('Cette heure existe deux fois au changement d’heure. Choisis un autre horaire.');
        }
    }
    $now ??= time();
    if ($timestamp <= $now) {
        throw new InvalidArgumentException('La capsule doit s’ouvrir dans le futur.');
    }
    if ($timestamp > $now + 20 * 366 * 86400) {
        throw new InvalidArgumentException('Choisis une date dans les vingt prochaines années.');
    }
    return $timestamp;
}

function readPhoto(array $upload): array
{
    $error = $upload['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('La photo n’a pas été reçue. Vérifie sa taille puis sélectionne-la à nouveau.');
    }
    $path = $upload['tmp_name'] ?? '';
    if (!is_string($path) || !is_uploaded_file($path)) {
        throw new InvalidArgumentException('Envoi de photo invalide.');
    }
    $size = filesize($path);
    if ($size === false || $size < 1 || $size > 3 * 1024 * 1024) {
        throw new InvalidArgumentException('La photo doit peser au maximum 3 Mo.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
    $dimensions = @getimagesize($path);
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)
        || !$dimensions || $dimensions[0] > 8000 || $dimensions[1] > 8000) {
        throw new InvalidArgumentException('Choisis une photo JPG, PNG ou WebP de 8 000 pixels maximum par côté.');
    }
    $bytes = file_get_contents($path);
    if ($bytes === false) {
        throw new RuntimeException('Impossible de lire la photo.');
    }
    return [$bytes, $mime];
}

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $query = db()->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $query->execute([$_SESSION['user_id']]);
    return $query->fetch() ?: null;
}

// Les listes et les capsules fermées ne récupèrent ni message ni image.
function findCapsule(int $id, int $userId): ?array
{
    $query = db()->prepare(
        'SELECT id, title, open_at, created_at, photo_mime FROM capsules WHERE id = ? AND user_id = ?'
    );
    $query->execute([$id, $userId]);
    return $query->fetch() ?: null;
}

function capsuleMessage(int $id, int $userId): ?string
{
    $query = db()->prepare(
        'SELECT message FROM capsules WHERE id = ? AND user_id = ? AND open_at <= ?'
    );
    $query->execute([$id, $userId, time()]);
    $value = $query->fetchColumn();
    return $value === false ? null : $value;
}
