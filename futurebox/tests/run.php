<?php
declare(strict_types=1);
require dirname(__DIR__) . '/src/functions.php';

$checks = 0;
function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException('ÉCHEC : ' . $message);
    }
    $checks++;
}

function rejects(callable $operation, string $message): void
{
    try {
        $operation();
    } catch (InvalidArgumentException $e) {
        check(true, $message);
        return;
    }
    check(false, $message);
}

$now = 1704067200; // 1er janvier 2024, UTC
check(!canOpen(['open_at' => $now + 1], $now), 'Une capsule future reste fermée.');
check(canOpen(['open_at' => $now], $now), 'Ouverture à la seconde exacte.');
check(canOpen(['open_at' => $now - 1], $now), 'Une capsule passée reste ouverte.');
check(openingTimestamp('2024-01-02T12:00', $now) === 1704193200, 'Conversion heure de Paris vers UTC.');
rejects(fn() => openingTimestamp('2024-02-30T12:00', $now), 'Rejeter une date inexistante.');
rejects(fn() => openingTimestamp('2024-03-31T02:30', $now), 'Rejeter une heure inexistante au passage à l’heure d’été.');
rejects(fn() => openingTimestamp('2024-10-27T02:30', $now), 'Rejeter une heure répétée au passage à l’heure d’hiver.');
rejects(fn() => openingTimestamp('2023-01-02T12:00', $now), 'Rejeter le passé.');
rejects(fn() => openingTimestamp('2050-01-02T12:00', $now), 'Rejeter les dates trop éloignées.');
rejects(fn() => openingTimestamp('n’importe quoi', $now), 'Rejeter les dates mal formées.');
check(escape('<script>alert("x")</script>') === '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;', 'Échapper le HTML saisi.');
echo $checks . " vérifications réussies.\n";
