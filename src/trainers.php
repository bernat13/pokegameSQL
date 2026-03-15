<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$stmt = $conn->query("SELECT * FROM entrenadores ORDER BY fecha_registro DESC");
$trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Entrenadores</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="trainers-panel">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>ENTRENADORES</h2>
                </div>
                
                <div class="trainer-list scrollable">
                    <?php if (empty($trainers)): ?>
                        <p>No hay entrenadores registrados.</p>
                    <?php else: ?>
                        <?php foreach ($trainers as $t): ?>
                            <div class="trainer-item">
                                <div class="t-info">
                                    <span class="t-name"><?= htmlspecialchars($t['nombre_entrenador']) ?></span>
                                    <span class="t-origin"><?= htmlspecialchars($t['clase_entrenador']) ?> de <?= htmlspecialchars($t['region_origen']) ?></span>
                                    <span class="t-motto">"<?= htmlspecialchars($t['lema_batalla']) ?>"</span>
                                </div>
                                <?php if ($t['nombre_entrenador'] !== $_SESSION['db_user']): ?>
                                    <form action="actions.php" method="POST" class="challenge-form">
                                        <input type="hidden" name="action" value="battle6v6">
                                        <input type="hidden" name="rival_trainer" value="<?= htmlspecialchars($t['nombre_entrenador']) ?>">
                                        <button type="submit" class="btn-primary challenge-btn">RETAR 6v6</button>
                                    </form>
                                <?php endif; ?>
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
                <div class="btn-b :">B</div>
            </div>
        </div>
    </div>
</body>
</html>
