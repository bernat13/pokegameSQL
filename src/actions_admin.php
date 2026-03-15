<?php
require_once 'db.php';

if ($_SESSION['db_user'] !== 'root') {
    header('Location: login.php');
    exit;
}

$action = $_POST['action'] ?? '';
$conn = null;

// Debug log for session
$debug = "[" . date('Y-m-d H:i:s') . "] Action: $action | User: " . ($_SESSION['db_user'] ?? 'N/A') . " | Pass set: " . (isset($_SESSION['db_pass']) ? 'YES' : 'NO') . " | DB: " . ($_SESSION['db_name'] ?? 'N/A') . "\n";
file_put_contents('/tmp/admin_actions.log', $debug, FILE_APPEND);

$conn = get_db_connection();

try {
    if ($action === 'create_class') {
        $className = $_POST['class_name'];
        $dbName = $_POST['db_id'];
        
        // 1. Create the database
        $conn->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $sqlPath = __DIR__ . '/../game_template.sql';
        if (file_exists($sqlPath)) {
            $gameSql = file_get_contents($sqlPath);
            $tmpSqlPath = "/tmp/template_" . $dbName . ".sql";
            file_put_contents($tmpSqlPath, $gameSql);

            $host = getenv('DB_HOST') ?: 'localhost';
            $pass = $_SESSION['db_pass'];
            // CLI is still best for loading the massive template
            $cmd = "MYSQL_PWD=" . escapeshellarg($pass) . " mariadb -h $host -u root " . escapeshellarg($dbName) . " < " . escapeshellarg($tmpSqlPath);
            shell_exec($cmd);
            unlink($tmpSqlPath);
        }

        // 3. Register the class in admin DB
        $stmt = $conn->prepare("INSERT INTO clases (nombre_clase, db_name) VALUES (?, ?)");
        $stmt->execute([$className, $dbName]);

        // 4. Parse CSV and provision users
        $processed = 0;
        if (isset($_FILES['users_csv']) && $_FILES['users_csv']['error'] == 0) {
            $handle = fopen($_FILES['users_csv']['tmp_name'], "r");
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($data) >= 2) {
                    $u = trim($data[0]);
                    $p = trim($data[1]);
                    if (empty($u)) continue;

                    try {
                        // User creation via PDO is more direct here
                        $conn->exec("CREATE USER IF NOT EXISTS '$u'@'%' IDENTIFIED BY '$p'");
                        $conn->exec("GRANT 'entrenador_role' TO '$u'@'%'");
                        $conn->exec("SET DEFAULT ROLE 'entrenador_role' FOR '$u'@'%'");
                        
                        $stmtMapping = $conn->prepare("INSERT INTO usuarios_clases (db_usuario, db_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE db_name = VALUES(db_name)");
                        $stmtMapping->execute([$u, $dbName]);
                        $processed++;
                    } catch (Exception $e) {}
                }
            }
            fclose($handle);
        }
        $_SESSION['msg'] = "Clase '$className' creada. $processed usuarios provisionados.";

    } elseif ($action === 'delete_class') {
        $dbName = $_POST['db_name'];
        
        // 1. Find all users associated with this class to drop them from MariaDB
        $stmt = $conn->prepare("SELECT db_usuario FROM usuarios_clases WHERE db_name = ?");
        $stmt->execute([$dbName]);
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($users as $user) {
            try {
                $conn->exec("DROP USER IF EXISTS '$user'@'%'");
            } catch (Exception $e) {}
        }
        
        // 2. Drop the database
        $conn->exec("DROP DATABASE IF EXISTS `$dbName` ");
        
        // 3. Delete from admin metadata
        $stmt = $conn->prepare("DELETE FROM clases WHERE db_name = ?");
        $stmt->execute([$dbName]);
        
        $_SESSION['msg'] = "Clase '$dbName' borrada por completo (BBDD y usuarios).";

    } elseif ($action === 'add_user') {
        $u = trim($_POST['username']);
        $p = trim($_POST['password']);
        $dbName = $_POST['db_name'];
        
        $conn->exec("CREATE USER IF NOT EXISTS '$u'@'%' IDENTIFIED BY '$p'");
        $conn->exec("GRANT 'entrenador_role' TO '$u'@'%'");
        $conn->exec("SET DEFAULT ROLE 'entrenador_role' FOR '$u'@'%'");
        
        $stmt = $conn->prepare("INSERT INTO usuarios_clases (db_usuario, db_name) VALUES (?, ?) ON DUPLICATE KEY UPDATE db_name = VALUES(db_name)");
        $stmt->execute([$u, $dbName]);
        
        $_SESSION['msg'] = "Usuario '$u' añadido a la clase '$dbName'.";

    } elseif ($action === 'delete_user') {
        $u = $_POST['username'];
        
        try { $conn->exec("DROP USER IF EXISTS '$u'@'%'"); } catch(Exception $e) {}
        $stmt = $conn->prepare("DELETE FROM usuarios_clases WHERE db_usuario = ?");
        $stmt->execute([$u]);
        
        $_SESSION['msg'] = "Usuario '$u' eliminado correctamente.";

    } elseif ($action === 'switch_to_class') {
        $dbName = $_POST['db_name'];
        $_SESSION['db_name'] = $dbName;
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header('Location: admin_classes.php');
exit;
