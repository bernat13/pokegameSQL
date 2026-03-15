<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
    SELECT eq.*, e.* 
    FROM equipo_pokemon eq 
    JOIN especies e ON eq.id_especie = e.id_especie 
    WHERE eq.id_instancia = ? AND eq.db_usuario = ?
");
$stmt->execute([$id, $_SESSION['db_user']]);
$pkmn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pkmn) {
    header('Location: index.php');
    exit;
}

// Get rivals (other trainers + own team excluding this specific pkmn)
$stmt = $conn->prepare("
    (SELECT eq.id_instancia, eq.db_usuario, e.nombre 
     FROM equipo_pokemon eq 
     JOIN especies e ON eq.id_especie = e.id_especie 
     WHERE eq.db_usuario != ?)
    UNION ALL
    (SELECT eq.id_instancia, 'TU EQUIPO' as db_usuario, e.nombre 
     FROM equipo_pokemon eq 
     JOIN especies e ON eq.id_especie = e.id_especie 
     WHERE eq.db_usuario = ? AND eq.id_instancia != ?)
    ORDER BY RAND() LIMIT 10
");
$stmt->execute([$_SESSION['db_user'], $_SESSION['db_user'], $id]);
$opponents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - <?= htmlspecialchars($pkmn['nombre']) ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="pkmn-detail">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>
                        <?= htmlspecialchars($pkmn['mote'] ?: $pkmn['nombre']) ?>
                        <?php if ($pkmn['mote']): ?>
                            <br><small style="font-size: 0.5em; opacity: 0.7;">(<?= htmlspecialchars($pkmn['nombre']) ?>)</small>
                        <?php endif; ?>
                    </h2>
                </div>
                
                <div class="sprite-row">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/<?= $pkmn['id_especie'] ?>.png" alt="<?= $pkmn['nombre'] ?>" class="large-sprite">
                    <div class="stats-mini">
                        <p>LVL: <?= $pkmn['nivel'] ?></p>
                        <p>TIPO: <?= $pkmn['tipo_principal'] ?></p>
                        <p>WINS: <?= $pkmn['victorias'] ?></p>
                    </div>
                </div>

                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>STAT</th>
                            <th>BASE</th>
                            <th>ACTUAL</th>
                            <th>MEJORA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $stats_map = [
                            'VIDA' => ['base' => 'stat_base_vida', 'actual' => 'vida_actual'],
                            'ATQ' => ['base' => 'stat_base_ataque', 'actual' => 'ataque_actual'],
                            'DEF' => ['base' => 'stat_base_defensa', 'actual' => 'defensa_actual'],
                            'AT.E' => ['base' => 'stat_base_atq_esp', 'actual' => 'atq_esp_actual'],
                            'DF.E' => ['base' => 'stat_base_def_esp', 'actual' => 'def_esp_actual'],
                            'VEL' => ['base' => 'stat_base_velocidad', 'actual' => 'velocidad_actual'],
                        ];
                        foreach ($stats_map as $label => $fields): 
                            $diff = $pkmn[$fields['actual']] - $pkmn[$fields['base']];
                        ?>
                            <tr>
                                <td class="stat-name"><?= $label ?></td>
                                <td class="stat-val"><?= $pkmn[$fields['base']] ?></td>
                                <td class="stat-val stat-actual"><?= $pkmn[$fields['actual']] ?></td>
                                <td class="stat-val stat-diff"><?= ($diff > 0 ? "+$diff" : "$diff") ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pkmn-actions">
                    <form action="actions.php" method="POST">
                        <input type="hidden" name="action" value="train">
                        <input type="hidden" name="id_instancia" value="<?= $pkmn['id_instancia'] ?>">
                        <button type="submit" class="btn-primary">ENTRENAR</button>
                    </form>

                    <form action="actions.php" method="POST" class="nickname-form">
                        <input type="hidden" name="action" value="rename">
                        <input type="hidden" name="id_instancia" value="<?= $pkmn['id_instancia'] ?>">
                        <input type="text" name="mote" placeholder="NUEVO NOMBRE" maxlength="20" required>
                        <button type="submit" class="btn-primary small-btn">NOMBRAR</button>
                    </form>

                    <div class="battle-section">
                        <h3>BATALLA 1v1</h3>
                        <form action="actions.php" method="POST">
                            <input type="hidden" name="action" value="battle1v1">
                            <input type="hidden" name="mi_pokemon" value="<?= $pkmn['id_instancia'] ?>">
                            <select name="rival_pokemon" required>
                                <option value="">ELIGE RIVAL</option>
                                <?php foreach ($opponents as $opp): ?>
                                    <option value="<?= $opp['id_instancia'] ?>">
                                        <?= htmlspecialchars($opp['db_usuario']) ?> - <?= htmlspecialchars($opp['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-primary battle-btn">LUCHAR</button>
                        </form>
                    </div>
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
