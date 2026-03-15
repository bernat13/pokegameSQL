<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();

if (!$conn) {
    die("Error: No se pudo conectar a la base de datos con tus credenciales.");
}

$message = '';
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'] ?? '';
    
    if (!empty($new_pass)) {
        try {
            // Use session credentials to change password
            // SET PASSWORD is the most reliable way for regular users to change their own password
            $stmt = $conn->prepare("SET PASSWORD = PASSWORD(?)");
            $stmt->execute([$new_pass]);
            
            $_SESSION['db_pass'] = $new_pass; // Update session
            $message = "¡Contraseña cambiada!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $error = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="settings-panel">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>PASSWORD</h2>
                </div>
                
                <form method="POST" class="settings-form">
                    <div class="field">
                        <label>NUEVA CLAVE:</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <?php if ($message): ?>
                        <p class="<?= $error ? 'error' : 'sql-success' ?> p-small"><?= htmlspecialchars($message) ?></p>
                    <?php endif; ?>
                    <button type="submit" class="btn-primary">CAMBIAR</button>
                </form>
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
