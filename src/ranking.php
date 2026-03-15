<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$stmt = $conn->query("SELECT * FROM ranking_liga");
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Ranking</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="ranking-panel">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>TOP TRAINERS</h2>
                </div>
                
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th>TRAINER</th>
                            <th>WINS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['entrenador_campeon']) ?></td>
                                <td><?= $row['victorias_oficiales'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($ranking)): ?>
                            <tr><td colspan="2">SIN DATOS</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
