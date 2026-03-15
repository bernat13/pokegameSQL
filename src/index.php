<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
if (!$conn) {
    session_destroy();
    header('Location: login.php?error=connection_lost');
    exit;
}

// Get Trainer info
$stmt = $conn->prepare("SELECT * FROM entrenadores WHERE nombre_entrenador = ?");
$stmt->execute([$_SESSION['db_user']]);
$trainer = $stmt->fetch(PDO::FETCH_ASSOC);

// If trainer not registered, handle it
$is_registered = $trainer ? true : false;

// Get Team
$team = [];
if ($is_registered) {
    $stmt = $conn->prepare("
        SELECT eq.*, e.nombre as especie_nombre, e.tipo_principal 
        FROM equipo_pokemon eq 
        JOIN especies e ON eq.id_especie = e.id_especie 
        WHERE eq.db_usuario = ?
    ");
    $stmt->execute([$_SESSION['db_user']]);
    $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <?php if (isset($_SESSION['last_battle'])): 
                $battle = $_SESSION['last_battle'];
                $is_6v6 = $_SESSION['is_6v6'] ?? false;
                unset($_SESSION['last_battle'], $_SESSION['is_6v6']);

                if (!$is_6v6):
                    $p1_info = null;
                    if ($battle['id_pokemon_atacante']) {
                        $stmt = $conn->prepare("SELECT eq.*, e.nombre as esp_nombre FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.id_instancia = ?");
                        $stmt->execute([$battle['id_pokemon_atacante']]);
                        $p1_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                    $p2_info = null;
                    if ($battle['id_pokemon_defensor']) {
                        $stmt = $conn->prepare("SELECT eq.*, e.nombre as esp_nombre FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.id_instancia = ?");
                        $stmt->execute([$battle['id_pokemon_defensor']]);
                        $p2_info = $stmt->fetch(PDO::FETCH_ASSOC);
                    }
            ?>
                <div class="battle-overlay">
                    <div class="battle-info-container">
                        <?php if ($p1_info): ?>
                            <div class="battle-p-card p1-info">
                                <div class="p-name"><?= htmlspecialchars($p1_info['mote'] ?: $p1_info['esp_nombre']) ?></div>
                                <div class="p-lvl">Lv.<?= $p1_info['nivel'] ?></div>
                                <div class="p-stats">HP:<?= $p1_info['vida_actual'] ?> ATK:<?= $p1_info['ataque_actual'] ?> DEF:<?= $p1_info['defensa_actual'] ?></div>
                            </div>
                        <?php endif; ?>

                        <div class="battle-animation">
                            <div class="p1-sprite animate-p1">
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $p1_info ? $p1_info['id_especie'] : '0' ?>.png" alt="P1">
                            </div>
                            <div class="vs-text">VS</div>
                            <div class="p2-sprite animate-p2">
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $p2_info ? $p2_info['id_especie'] : '0' ?>.png" alt="P2">
                            </div>
                        </div>

                        <?php if ($p2_info): ?>
                            <div class="battle-p-card p2-info">
                                <div class="p-name"><?= htmlspecialchars($p2_info['mote'] ?: $p2_info['esp_nombre']) ?></div>
                                <div class="p-lvl">Lv.<?= $p2_info['nivel'] ?></div>
                                <div class="p-stats">HP:<?= $p2_info['vida_actual'] ?> ATK:<?= $p2_info['ataque_actual'] ?> DEF:<?= $p2_info['defensa_actual'] ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="battle-results show-after-anim">
                        <h2>RESULTADO</h2>
                        <div class="podium">
                            <div class="winner-info">
                                <div class="trophy">🏆</div>
                                <div class="winner-name"><?= htmlspecialchars($battle['resultado']) ?></div>
                            </div>
                        </div>
                        <p class="rounds-text">RONDAS: <?= $battle['rondas'] ?></p>
                        <button onclick="this.closest('.battle-overlay').remove()" class="btn-primary">CONTINUAR</button>
                    </div>
                </div>
                <?php else: ?>
                <div class="battle-overlay tournament-overlay">
                    <div class="battle-results">
                        <h2>TORNEO FINALIZADO</h2>
                        <div class="podium">
                            <div class="winner-info">
                                <div class="trophy">🏆</div>
                                <div class="winner-name">GANADOR: <?= htmlspecialchars($battle['ganador']) ?></div>
                            </div>
                        </div>
                        
                        <div class="tournament-details">
                            <h3>DETALLES DE LOS COMBATES</h3>
                            <div class="details-list">
                                <?php foreach ($battle['detalles'] as $detalle): ?>
                                    <div class="detail-row">
                                        <span class="p1"><?= htmlspecialchars($detalle['mote_1']) ?></span>
                                        <span class="vs-tiny">vs</span>
                                        <span class="p2"><?= htmlspecialchars($detalle['mote_2']) ?></span>
                                        <span class="bout-winner">✓ <?= $detalle['ganador_bout'] == $detalle['id_pokemon_1'] ? 'P1' : 'P2' ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <p class="rounds-text">RONDAS TOTALES: <?= $battle['rondas'] ?></p>
                        <button onclick="this.closest('.battle-overlay').remove()" class="btn-primary">CONTINUAR</button>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!$is_registered): ?>
                <div class="registration-form">
                    <h2>NUEVO ENTRENADOR</h2>
                    <form action="actions.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="field">
                            <label>CLASE:</label>
                            <input type="text" name="clase" placeholder="Ej: Joven" required>
                        </div>
                        <div class="field">
                            <label>REGIÓN:</label>
                            <input type="text" name="region" placeholder="Ej: Kanto" required>
                        </div>
                        <div class="field">
                            <label>LEMA:</label>
                            <input type="text" name="lema" placeholder="¡Gotta catch 'em all!" required>
                        </div>
                        <button type="submit" class="btn-primary">REGISTRAR</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="trainer-header">
                    <div class="trainer-name"><?= htmlspecialchars($trainer['nombre_entrenador']) ?></div>
                    <div class="trainer-class"><?= htmlspecialchars($trainer['clase_entrenador']) ?></div>
                </div>
                
                <div class="team-container">
                    <h3>TU EQUIPO (<?= count($team) ?>/12)</h3>
                    <div class="team-list">
                        <?php foreach ($team as $pkmn): ?>
                            <div class="pkmn-card" onclick="window.location='pokemon.php?id=<?= $pkmn['id_instancia'] ?>'">
                                <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $pkmn['id_especie'] ?>.png" alt="<?= $pkmn['especie_nombre'] ?>">
                                <div class="pkmn-info">
                                    <span class="name">
                                        <?= $pkmn['mote'] ? htmlspecialchars($pkmn['mote']) . " <small>(" . htmlspecialchars($pkmn['especie_nombre']) . ")</small>" : htmlspecialchars($pkmn['especie_nombre']) ?>
                                    </span>
                                    <span class="lvl">Lv.<?= $pkmn['nivel'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (count($team) < 12): ?>
                            <form action="actions.php" method="POST">
                                <input type="hidden" name="action" value="capture">
                                <button type="submit" class="btn-primary capture-btn">+ CAPTURAR</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="main-nav-grid">
                    <a href="trainers.php" class="nav-item">TRAINERS</a>
                    <a href="history.php" class="nav-item">HISTORIAL</a>
                    <a href="terminal.php" class="nav-item">SQL TERM</a>
                    <a href="instructions.php" class="nav-item">REGLAS</a>
                    <a href="ranking.php" class="nav-item">LIGA</a>
                    <a href="settings.php" class="nav-item">PASS</a>
                    <?php if ($_SESSION['db_user'] === 'root'): ?>
                        <a href="admin_classes.php" class="nav-item admin-nav">ADMIN</a>
                    <?php endif; ?>
                </div>

                <div class="nav-links">
                    <a href="logout.php" class="link">SALIR</a>
                </div>
            <?php endif; ?>
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
