<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();

// Single 1v1 battles
$stmt = $conn->prepare("
    SELECT '1v1' as tipo, h.id_combate as id_relativo, h.fecha, h.entrenador_atacante, h.entrenador_defensor, h.resultado, h.rondas,
           e1.nombre as p1_especie, eq1.mote as p1_mote, eq1.id_especie as p1_id,
           e2.nombre as p2_especie, eq2.mote as p2_mote, eq2.id_especie as p2_id,
           NULL as detalles
    FROM historial_combates h
    LEFT JOIN equipo_pokemon eq1 ON h.id_pokemon_atacante = eq1.id_instancia
    LEFT JOIN especies e1 ON eq1.id_especie = e1.id_especie
    LEFT JOIN equipo_pokemon eq2 ON h.id_pokemon_defensor = eq2.id_instancia
    LEFT JOIN especies e2 ON eq2.id_especie = e2.id_especie
    ORDER BY h.fecha DESC
    LIMIT 100
");
$stmt->execute();
$combates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6v6 Tournaments
$stmt = $conn->prepare("
    SELECT '6v6' as tipo, t.id_torneo as id_relativo, t.fecha, t.entrenador_1 as entrenador_atacante, t.entrenador_2 as entrenador_defensor, CONCAT('GANADOR: ', t.ganador) as resultado, t.rondas,
           NULL as p1_especie, NULL as p1_mote, NULL as p1_id,
           NULL as p2_especie, NULL as p2_mote, NULL as p2_id,
           t.id_torneo
    FROM historial_torneos t
    ORDER BY t.fecha DESC
    LIMIT 100
");
$stmt->execute();
$torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch details for tournaments
foreach ($torneos as &$torneo) {
    $stmt = $conn->prepare("
        SELECT d.*, 
               p1.mote as mote_1, p2.mote as mote_2
        FROM historial_torneo_detalles d
        LEFT JOIN equipo_pokemon p1 ON d.id_pokemon_1 = p1.id_instancia
        LEFT JOIN equipo_pokemon p2 ON d.id_pokemon_2 = p2.id_instancia
        WHERE d.id_torneo = ?
    ");
    $stmt->execute([$torneo['id_relativo']]);
    $torneo['detalles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Merge and sort
$historial_completo = array_merge($combates, $torneos);
usort($historial_completo, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Combates</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="history-page">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>HISTORIAL</h2>
                </div>
                
                <div class="history-list">
                    <?php if (empty($historial_completo)): ?>
                        <p>No hay combates registrados.</p>
                    <?php else: ?>
                        <?php foreach ($historial_completo as $c): ?>
                            <div class="history-item <?= $c['tipo'] === '6v6' ? 'tournament-item' : '' ?>">
                                <div class="h-date"><?= date('d/m H:i', strtotime($c['fecha'])) ?> [<?= $c['tipo'] ?>]</div>
                                <div class="h-battle">
                                    <div class="h-side">
                                        <?php if ($c['p1_id']): ?>
                                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $c['p1_id'] ?>.png" alt="P1" class="h-pkmn-img">
                                        <?php endif; ?>
                                        <span class="h-trainer"><?= htmlspecialchars($c['entrenador_atacante']) ?></span>
                                        <?php if ($c['p1_especie']): ?>
                                            <div class="h-pkmn"><?= htmlspecialchars($c['p1_mote'] ?: $c['p1_especie']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="vs">VS</span>
                                    <div class="h-side">
                                        <?php if ($c['p2_id']): ?>
                                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $c['p2_id'] ?>.png" alt="P2" class="h-pkmn-img">
                                        <?php endif; ?>
                                        <span class="h-trainer"><?= htmlspecialchars($c['entrenador_defensor']) ?></span>
                                        <?php if ($c['p2_especie']): ?>
                                            <div class="h-pkmn"><?= htmlspecialchars($c['p2_mote'] ?: $c['p2_especie']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($c['tipo'] === '6v6' && !empty($c['detalles'])): ?>
                                    <div class="h-details">
                                        <div class="details-mini-list">
                                            <?php foreach ($c['detalles'] as $d): ?>
                                                <div class="mini-bout">
                                                    <span class="m-pkmn"><?= htmlspecialchars($d['mote_1']) ?></span>
                                                    <span class="m-vs">vs</span>
                                                    <span class="m-pkmn"><?= htmlspecialchars($d['mote_2']) ?></span>
                                                    <span class="m-win">✓ <?= $d['ganador_bout'] == $d['id_pokemon_1'] ? 'P1' : 'P2' ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="h-result">
                                    <span class="h-label"><?= $c['tipo'] === '6v6' ? '' : 'GANADOR:' ?></span>
                                    <?= htmlspecialchars($c['resultado']) ?>
                                </div>
                                <div class="h-rounds">RONDAS: <?= $c['rondas'] ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="gbc-controls">
            <div class="d-pad"></div>
            <div class="buttons">
                <div class="btn-a">A</div>
                <div class="btn-b">B</div>
            </div>
        </div>
    </div>
</body>
</html>
