<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = get_db_connection($username, $password);

    if ($conn) {
        $_SESSION['db_user'] = $username;
        $_SESSION['db_pass'] = $password;

        if ($username === 'root') {
            $_SESSION['db_name'] = 'pokegame_admin'; // Root starts in admin DB
            header('Location: admin_classes.php');
        } else {
            // Lookup assigned DB for regular users (use a fresh root connection for lookup if needed, 
            // but here we use the user's own connection which has select on admin db)
            $stmt = $conn->prepare("SELECT db_name FROM pokegame_admin.usuarios_clases WHERE db_usuario = ?");
            $stmt->execute([$username]);
            $assigned = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $_SESSION['db_name'] = $assigned ? $assigned['db_name'] : 'pokegame';
            header('Location: index.php');
        }
        exit;
    } else {
        // Find out WHY it failed
        $error = "Login fallido. ";
        try {
            $host = getenv('DB_HOST') ?: 'localhost';
            $db   = getenv('DB_NAME') ?: 'pokegame';
            new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $username, $password);
        } catch (PDOException $e) {
            $error .= " (Host: $host, User: $username, Pass: $password) - " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - GBC Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <h1>POKÉGAME</h1>
            <div class="login-box">
                <?php if (isset($error)): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>
                <form method="POST">
                    <div class="field">
                        <label>USUARIO:</label>
                        <input type="text" name="username" required autocomplete="off">
                    </div>
                    <div class="field">
                        <label>CONTRASEÑA:</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn-primary">START</button>
                </form>
            </div>
            <div class="footer-msg">ASIR 2026</div>
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
