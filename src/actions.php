<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}

$action = $_POST['action'] ?? '';
$conn = get_db_connection();

if (!$conn) {
    die("Error de conexión");
}

try {
    if ($action === 'register') {
        $stmt = $conn->prepare("INSERT INTO entrenadores (nombre_entrenador, clase_entrenador, region_origen, lema_batalla) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['db_user'],
            $_POST['clase'],
            $_POST['region'],
            $_POST['lema']
        ]);
    } elseif ($action === 'capture') {
        $stmt = $conn->prepare("CALL capturar_pokemon()");
        $stmt->execute();
    } elseif ($action === 'train') {
        $id = $_POST['id_instancia'] ?? 0;
        $stmt = $conn->prepare("CALL entrenar_pokemon(?)");
        $stmt->execute([$id]);
    } elseif ($action === 'battle1v1') {
        $mi_pkmn = $_POST['mi_pokemon'] ?? 0;
        $rival_pkmn = $_POST['rival_pokemon'] ?? 0;
        $stmt = $conn->prepare("CALL batalla_individual_oficial(?, ?)");
        $stmt->execute([$mi_pkmn, $rival_pkmn]);
        
        $stmt = $conn->prepare("SELECT * FROM historial_combates WHERE entrenador_atacante = ? ORDER BY id_combate DESC LIMIT 1");
        $stmt->execute([$_SESSION['db_user']]);
        $_SESSION['last_battle'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } elseif ($action === 'battle6v6') {
        $rival_trainer = $_POST['rival_trainer'] ?? '';
        $stmt = $conn->prepare("CALL batalla_equipo_oficial(?, ?)");
        $stmt->execute([$_SESSION['db_user'], $rival_trainer]);
        
        // Fetch tournament summary
        $stmt = $conn->prepare("SELECT * FROM historial_torneos WHERE entrenador_1 = ? OR entrenador_2 = ? ORDER BY id_torneo DESC LIMIT 1");
        $stmt->execute([$_SESSION['db_user'], $_SESSION['db_user']]);
        $tournament = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tournament) {
            // Fetch details (pairings and winners)
            $stmt = $conn->prepare("
                SELECT d.*, 
                       p1.mote as mote_1, e1.nombre as especie_1,
                       p2.mote as mote_2, e2.nombre as especie_2
                FROM historial_torneo_detalles d
                JOIN equipo_pokemon p1 ON d.id_pokemon_1 = p1.id_instancia
                JOIN especies e1 ON p1.id_especie = e1.id_especie
                JOIN equipo_pokemon p2 ON d.id_pokemon_2 = p2.id_instancia
                JOIN especies e2 ON p2.id_especie = e2.id_especie
                WHERE d.id_torneo = ?
            ");
            $stmt->execute([$tournament['id_torneo']]);
            $tournament['detalles'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $_SESSION['last_battle'] = $tournament;
        $_SESSION['is_6v6'] = true;
        
    } elseif ($action === 'rename') {
        $id = $_POST['id_instancia'] ?? 0;
        $mote = $_POST['mote'] ?? '';
        // Usar la vista mi_equipo garantiza que solo pueda editar el suyo
        $stmt = $conn->prepare("UPDATE mi_equipo SET mote = ? WHERE id_instancia = ?");
        $stmt->execute([$mote, $id]);
        $_SESSION['flash_msg'] = "Mote actualizado correctamente";
    }
} catch (PDOException $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

header('Location: index.php');
exit;
?>
