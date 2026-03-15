<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$query = $_POST['sql_query'] ?? '';
$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($query)) {
    try {
        $stmt = $conn->query($query);
        if ($stmt) {
            if (stripos($query, 'SELECT') === 0 || stripos($query, 'SHOW') === 0 || stripos($query, 'DESC') === 0) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $result = "Comando ejecutado con éxito. Filas afectadas: " . $stmt->rowCount();
            }
        }
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Terminal SQL</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console terminal-console">
        <div class="gbc-screen">
            <div class="terminal-panel">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>SQL CONSOLE</h2>
                </div>
                
                <form method="POST" class="terminal-form">
                    <textarea name="sql_query" placeholder="Escribe tu comando SQL aquí..." required><?= htmlspecialchars($query) ?></textarea>
                    <button type="submit" class="btn-primary">EJECUTAR</button>
                </form>

                <div class="result-box scrollable">
                    <?php if ($error): ?>
                        <p class="sql-error"><?= htmlspecialchars($error) ?></p>
                    <?php elseif ($result !== null): ?>
                        <?php if (is_array($result)): ?>
                            <?php if (empty($result)): ?>
                                <p>0 filas devueltas.</p>
                            <?php else: ?>
                                <table class="mini-table">
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($result[0]) as $col): ?>
                                                <th><?= htmlspecialchars($col) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $val): ?>
                                                    <td><?= htmlspecialchars($val ?? 'NULL') ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="sql-success"><?= htmlspecialchars($result) ?></p>
                        <?php endif; ?>
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
