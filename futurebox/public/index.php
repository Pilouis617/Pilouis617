<?php
declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();
require dirname(__DIR__) . '/src/functions.php';

header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header("Content-Security-Policy: default-src 'self'; img-src 'self' blob:; base-uri 'none'; form-action 'self'; frame-ancestors 'none'");

$page = $_GET['page'] ?? 'home';
$page = is_string($page) && in_array($page, ['home', 'login', 'register', 'new', 'capsule', 'photo'], true) ? $page : 'home';
$error = '';
$user = null;
$capsule = null;
$message = null;
$capsules = [];
$filter = $_GET['filter'] ?? 'all';
$filter = is_string($filter) && in_array($filter, ['all', 'sealed', 'ready'], true) ? $filter : 'all';
$fatal = false;

try {
    $user = currentUser();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkCsrf();
        $action = posted('action');

        if ($action === 'register') {
            $name = trim(posted('name'));
            $email = strtolower(trim(posted('email')));
            $password = posted('password');
            if ($name === '' || mb_strlen($name) > 60) {
                throw new InvalidArgumentException('Le prénom doit contenir entre 1 et 60 caractères.');
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
                throw new InvalidArgumentException('Saisis une adresse e-mail valide.');
            }
            if (strlen($password) < 10 || strlen($password) > 72) {
                throw new InvalidArgumentException('Le mot de passe doit contenir au moins 10 caractères et au maximum 72 octets.');
            }
            if ($password !== posted('confirm_password')) {
                throw new InvalidArgumentException('Les deux mots de passe ne correspondent pas.');
            }
            try {
                $query = db()->prepare('INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, ?)');
                $query->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), time()]);
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    throw new InvalidArgumentException('Cette adresse est déjà utilisée. Connecte-toi à ton compte.');
                }
                throw $e;
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) db()->lastInsertId();
            unset($_SESSION['csrf']);
            flash('Ton compte est prêt. Écris ta première capsule.');
            redirect('new');
        }

        if ($action === 'login') {
            $email = strtolower(trim(posted('email')));
            $query = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
            $query->execute([$email]);
            $account = $query->fetch();
            // Même calcul pour une adresse inconnue, afin de limiter les différences de durée.
            $hash = $account['password_hash'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
            $valid = password_verify(posted('password'), $hash);
            if (!$account || !$valid) {
                throw new InvalidArgumentException('Adresse e-mail ou mot de passe incorrect.');
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $account['id'];
            unset($_SESSION['csrf']);
            redirect();
        }

        if (!$user) {
            redirect('login');
        }
        if ($action === 'logout') {
            $_SESSION = [];
            session_regenerate_id(true);
            redirect('login');
        }

        if ($action === 'create') {
            $title = trim(posted('title'));
            $body = trim(posted('message'));
            if ($title === '' || mb_strlen($title) > 100) {
                throw new InvalidArgumentException('Le titre doit contenir entre 1 et 100 caractères.');
            }
            if ($body === '' || mb_strlen($body) > 10000) {
                throw new InvalidArgumentException('Écris un message de 1 à 10 000 caractères.');
            }
            $opening = openingTimestamp(posted('open_at'));
            $photoUpload = $_FILES['photo'] ?? [];
            if (!is_array($photoUpload)) {
                throw new InvalidArgumentException('Envoi de photo invalide.');
            }
            [$photo, $mime] = readPhoto($photoUpload);
            $query = db()->prepare(
                'INSERT INTO capsules (user_id, title, message, open_at, created_at, photo, photo_mime) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $query->execute([$user['id'], $title, $body, $opening, time(), $photo, $mime]);
            flash('Capsule scellée. Rendez-vous le ' . dateLabel($opening) . '.');
            redirect();
        }

        if ($action === 'delete') {
            if (posted('confirm_delete') !== 'yes') {
                throw new InvalidArgumentException('Coche la confirmation avant de supprimer la capsule.');
            }
            $id = filter_var(posted('id'), FILTER_VALIDATE_INT);
            if (!$id || !findCapsule($id, (int) $user['id'])) {
                throw new InvalidArgumentException('Cette capsule est introuvable.');
            }
            $query = db()->prepare('DELETE FROM capsules WHERE id = ? AND user_id = ?');
            $query->execute([$id, $user['id']]);
            flash('La capsule a été supprimée.');
            redirect();
        }
        throw new InvalidArgumentException('Cette action n’est pas disponible.');
    }
} catch (InvalidArgumentException $e) {
    $error = $e->getMessage();
    http_response_code(422);
} catch (Throwable $e) {
    error_log('FutureBox: ' . $e->getMessage());
    $error = 'FutureBox ne peut pas accéder aux données. Vérifie la configuration et le démarrage de MySQL.';
    $fatal = true;
    http_response_code(503);
}

try {
    if (!$fatal) {
        if (!$user && !in_array($page, ['login', 'register', 'photo'], true)) {
            redirect('login');
        }
        if ($user && in_array($page, ['login', 'register'], true) && $error === '') {
            redirect();
        }
        if ($page === 'photo') {
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            $capsule = $user && $id ? findCapsule($id, (int) $user['id']) : null;
            if (!$capsule || !canOpen($capsule) || !$capsule['photo_mime']) {
                http_response_code(404);
                exit('Photo indisponible.');
            }
            $query = db()->prepare(
                'SELECT photo, photo_mime FROM capsules WHERE id = ? AND user_id = ? AND open_at <= ?'
            );
            $query->execute([$id, $user['id'], time()]);
            $photo = $query->fetch();
            if (!$photo || $photo['photo'] === null) {
                http_response_code(404);
                exit('Photo indisponible.');
            }
            header('Content-Type: ' . $photo['photo_mime']);
            header('Content-Disposition: inline');
            echo $photo['photo'];
            exit;
        }
        if ($page === 'capsule') {
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            $capsule = $id ? findCapsule($id, (int) $user['id']) : null;
            if (!$capsule) {
                http_response_code(404);
            } elseif (canOpen($capsule)) {
                $message = capsuleMessage((int) $capsule['id'], (int) $user['id']);
            }
        }
        if ($page === 'home') {
            $query = db()->prepare(
                'SELECT id, title, open_at, created_at, photo_mime FROM capsules WHERE user_id = ? ORDER BY open_at ASC, id DESC'
            );
            $query->execute([$user['id']]);
            $capsules = $query->fetchAll();
        }
    }
} catch (Throwable $e) {
    error_log('FutureBox: ' . $e->getMessage());
    $fatal = true;
    $error = 'Impossible de charger les capsules pour le moment.';
    http_response_code(503);
}

require dirname(__DIR__) . '/templates/header.php';
if ($fatal) {
    echo '<section class="empty"><h1>Un instant…</h1><p>Le service est momentanément indisponible.</p><a class="button" href="index.php">Réessayer</a></section>';
} else {
    $template = ['home' => 'home', 'login' => 'auth', 'register' => 'auth', 'new' => 'new', 'capsule' => 'capsule'][$page] ?? 'auth';
    require dirname(__DIR__) . '/templates/' . $template . '.php';
}
require dirname(__DIR__) . '/templates/footer.php';
