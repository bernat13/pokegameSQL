<?php
require_once 'db.php';

if ($_SESSION['db_user'] !== 'root') {
    header('Location: login.php');
    exit;
}

$conn = get_db_connection();
$stmt = $conn->query("SELECT * FROM clases ORDER BY fecha_creacion DESC");
$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper to get detailed stats of a class
function get_class_stats($dbName) {
    global $conn;
    try {
        // Query the tenant DB for stats
        $stmt = $conn->query("
            SELECT 
                u.db_usuario,
                COALESCE(e.nombre_entrenador, '---') as entrenador,
                COALESCE((SELECT COUNT(*) FROM `$dbName`.equipo_pokemon WHERE db_usuario = u.db_usuario COLLATE utf8mb4_unicode_ci), 0) as num_pokemon,
                COALESCE((SELECT SUM(victorias) FROM `$dbName`.equipo_pokemon WHERE db_usuario = u.db_usuario COLLATE utf8mb4_unicode_ci), 0) as total_victorias_1v1,
                (SELECT COUNT(*) FROM `$dbName`.historial_torneos WHERE ganador = u.db_usuario COLLATE utf8mb4_unicode_ci) as victorias_6v6
            FROM pokegame_admin.usuarios_clases u
            LEFT JOIN `$dbName`.entrenadores e ON u.db_usuario = e.nombre_entrenador COLLATE utf8mb4_unicode_ci
            WHERE u.db_name = '$dbName'
            GROUP BY u.db_usuario
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$selected_class = $_GET['view'] ?? null;
$stats = [];
if ($selected_class) {
    $stats = get_class_stats($selected_class);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PokéGame Admin - Clases</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gbc-green: #d4e157;
            --gbc-blue: #00bcd4;
            --gbc-red: #ff5252;
            --gbc-bg: #8c7b8c;
            --gbc-panel: #ffffff;
            --gbc-text: #212121;
        }
        body { background-color: var(--gbc-bg); color: var(--gbc-text); padding: 20px; font-family: 'Inter', sans-serif; }
        .admin-container { max-width: 1200px; margin: 0 auto; display: flex; gap: 20px; flex-wrap: wrap; }
        .panel { 
            background: var(--gbc-panel); 
            border: 4px solid #000; 
            box-shadow: 8px 8px 0px rgba(0,0,0,0.2);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .main-panel { flex: 1; min-width: 500px; }
        .side-panel { width: 350px; }
        h1, h2, h3 { color: #000; margin-top: 0; text-transform: uppercase; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        
        .class-card { 
            background: #f5f5f5; 
            border: 2px solid #000; 
            padding: 15px; 
            margin-bottom: 15px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .class-card:hover { background: #eee; }
        .btn { 
            padding: 8px 16px; 
            text-decoration: none; 
            border: 2px solid #000; 
            font-weight: bold; 
            text-transform: uppercase; 
            cursor: pointer;
            display: inline-block;
            margin: 2px;
            font-size: 12px;
        }
        .btn-blue { background: var(--gbc-blue); color: #fff; }
        .btn-green { background: var(--gbc-green); color: #000; }
        .btn-red { background: var(--gbc-red); color: #fff; }
        .btn-small { font-size: 10px; padding: 4px 8px; }

        .msg { background: #e8f5e9; color: #2e7d32; padding: 10px; margin-bottom: 20px; border: 2px solid #2e7d32; }
        .err { background: #ffebee; color: #c62828; padding: 10px; margin-bottom: 20px; border: 2px solid #c62828; }

        form label { display: block; margin: 10px 0 5px; font-weight: bold; }
        form input[type="text"], form input[type="password"], form input[type="file"] { 
            width: 100%; padding: 8px; border: 2px solid #000; border-radius: 4px; box-sizing: border-box; 
        }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        th { background: #f0f0f0; }

        .tag { font-size: 10px; padding: 2px 5px; border: 1px solid #000; border-radius: 3px; font-weight: bold; }
        .tag-db { background: #eee; }
    </style>
</head>
<body>

<div class="panel" style="display: flex; justify-content: space-between; align-items: center;">
    <h1>POKÉGAME ADMIN PANEL v2.0</h1>
    <div>
        <span style="font-weight: bold; margin-right: 15px;">ROOT ACCESS</span>
        <a href="logout.php" class="btn btn-red">Salir</a>
    </div>
</div>

<?php if(isset($_SESSION['msg'])): ?>
    <div class="msg"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="err"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="admin-container">
    <!-- List of Classes -->
    <div class="panel main-panel">
        <h2>Clases Activas</h2>
        <?php if(empty($clases)): ?>
            <p>No hay clases creadas aún.</p>
        <?php else: ?>
            <?php foreach($clases as $c): ?>
                <div class="class-card">
                    <div>
                        <strong style="font-size: 1.1em;"><?= htmlspecialchars($c['nombre_clase']) ?></strong><br>
                        <span class="tag tag-db">ID: <?= htmlspecialchars($c['db_name']) ?></span>
                        <small style="display:block; margin-top:5px;"><?= $c['fecha_creacion'] ?></small>
                    </div>
                    <div>
                        <a href="?view=<?= $c['db_name'] ?>" class="btn btn-blue">Detalles</a>
                        <form action="actions_admin.php" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="switch_to_class">
                            <input type="hidden" name="db_name" value="<?= $c['db_name'] ?>">
                            <button type="submit" class="btn btn-green">Entrar</button>
                        </form>
                        <form action="actions_admin.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que quieres borrar TODA la clase y sus usuarios?')">
                            <input type="hidden" name="action" value="delete_class">
                            <input type="hidden" name="db_name" value="<?= $c['db_name'] ?>">
                            <button type="submit" class="btn btn-red">Borrar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Create Class -->
    <div class="panel side-panel">
        <h2>Nueva Clase</h2>
        <form action="actions_admin.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create_class">
            <label>Nombre de la Clase</label>
            <input type="text" name="class_name" placeholder="Ej: 2º ASIR B" required>
            <label>ID BBDD (minús, sin esp)</label>
            <input type="text" name="db_id" placeholder="Ej: asir2024" required pattern="[a-z0-9_]+">
            <label>Usuarios (CSV: user,pass)</label>
            <input type="file" name="users_csv" accept=".csv">
            <p style="font-size: 11px; margin-top: 5px;">* Dejar vacío para alta manual.</p>
            <button type="submit" class="btn btn-blue" style="width: 100%; margin-top: 10px;">Crear Clase</button>
        </form>
    </div>

    <!-- Stats and User Management -->
    <?php if($selected_class): ?>
    <div class="panel" style="width: 100%;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>GESTIÓN DE ALUMNOS: <?= htmlspecialchars($selected_class) ?></h2>
            <a href="admin_classes.php" class="btn btn-small btn-blue">Cerrar</a>
        </div>
        
        <div style="display: flex; gap: 30px; margin-top:20px;">
            <div style="flex: 2;">
                <h3>Lista de Usuarios</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Entrenador</th>
                            <th>Pokémon</th>
                            <th>Vics 1v1</th>
                            <th>Vics 6v6</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stats as $s): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($s['db_usuario']) ?></strong></td>
                                <td><?= htmlspecialchars($s['entrenador']) ?></td>
                                <td><?= $s['num_pokemon'] ?></td>
                                <td><?= $s['total_victorias_1v1'] ?></td>
                                <td><?= $s['victorias_6v6'] ?></td>
                                <td>
                                    <form action="actions_admin.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Borrar usuario de MariaDB y de la clase?')">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="username" value="<?= $s['db_usuario'] ?>">
                                        <button type="submit" class="btn btn-red btn-small">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($stats)): ?>
                            <tr><td colspan="6">No hay usuarios en esta clase.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="flex: 1; background: #f9f9f9; padding: 20px; border: 2px solid #000; height: fit-content;">
                <h3>Alta Manual</h3>
                <form action="actions_admin.php" method="POST">
                    <input type="hidden" name="action" value="add_user">
                    <input type="hidden" name="db_name" value="<?= $selected_class ?>">
                    <label>Nombre Usuario</label>
                    <input type="text" name="username" required>
                    <label>Contraseña</label>
                    <input type="password" name="password" required>
                    <button type="submit" class="btn btn-green" style="width: 100%; margin-top: 15px;">Añadir Usuario</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
