<?php
require_once 'db.php';

if (!isset($_SESSION['db_user'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PokéGame - Instrucciones</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body class="gbc-theme">
    <div class="gbc-console">
        <div class="gbc-screen">
            <div class="instructions-panel">
                <div class="top">
                    <a href="index.php" class="back-btn">&lt;</a>
                    <h2>GUÍA DB</h2>
                </div>
                
                <div class="content scrollable">
                    <section>
                        <h3>1. ¡REGLAS DE LA LIGA! 🏆</h3>
                        <p><strong>Captura:</strong> El sistema elige un bicho al azar. Todos nacen en Nivel 1. ¡No llores si te sale un Rattata!</p>
                        <p><strong>Entrenamiento:</strong> <code>CALL entrenar_pokemon(id);</code> Sube stats a lo loco (0-3 pts). El tope es 300.</p>
                        <p><strong>Niveles:</strong> Si ganas, subes. Al subir, tus stats se reajustan: <code>Base + (Nivel * 3)</code>.</p>
                    </section>
                    
                    <section>
                        <h3>2. EL ARTE DE LA TORTA 👊</h3>
                        <p><strong>Fórmula:</strong> Daño = (Atk/Def) * Nivel * Suerte * Tipos.</p>
                        <p><strong>Tipos:</strong> ¡Usa la cabeza! Si usas Agua contra Fuego, el daño es x2. ¡No seas noob!</p>
                        <p><strong>Duelos:</strong> <code>CALL batalla_individual_oficial(mi_id, rival_id);</code></p>
                    </section>

                    <section>
                        <h3>3. 🛠️ SÉ UN MASTER (PROGRAMA)</h3>
                        <p>¿Vas a estar haciendo click como un mono? ¡No! Investiga cómo crear <strong>Procedures</strong> con bucles (<code>WHILE</code>) para automatizar el entrenamiento.</p>
                        <p style="background: #000; padding: 10px; color: #0f0; border-left: 3px solid #0f0; font-family: monospace; font-size: 0.8em;">
                            PISTA: Usa DELIMITER //, DECLARE para variables, y llama a entrenar_pokemon() dentro de un bucle.
                        </p>
                        <p>Usa la <strong>Terminal SQL</strong> para lanzar tus propios scripts y automatizar tu victoria.</p>
                    </section>

                    <section style="border: 2px dashed #f00; padding: 10px; margin-top: 10px; background: rgba(255,0,0,0.1);">
                        <h3 style="color: #f00; text-align: center;">🔥 EL DESAFÍO 🔥</h3>
                        <p>¿Eres capaz de programar un bot que analice el ranking y machaque automáticamente a los más débiles de la otra clase?</p>
                        <p><strong>¡Pícalos con código y demuestra quién manda!</strong></p>
                    </section>
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
