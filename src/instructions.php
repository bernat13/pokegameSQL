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
                        <h3>📊 MAPA DE PODER (ESQUEMA)</h3>
                        <div style="text-align: center; margin: 10px 0;">
                            <img src="img/db_diagram.png" alt="Database Diagram" style="width: 100%; max-width: 600px; border: 4px solid var(--gbc-screen-light); border-radius: 8px; box-shadow: 4px 4px 0 black;">
                        </div>
                    </section>

                    <section>
                        <h3>1. DEFINICIÓN DEL MUNDO 🌎</h3>
                        <div class="erd-container">
                            <div class="erd-table">
                                <h4>entrenadores</h4>
                                <p>- Perfil del jugador e identidad SQL.</p>
                            </div>
                            <div class="erd-table">
                                <h4>especies</h4>
                                <p>- Catálogo de Pokémon y sus stats base.</p>
                            </div>
                            <div class="erd-table">
                                <h4>equipo_pokemon</h4>
                                <p>- Instancias de tus Pokémon (id_instancia, nivel, stats actuales).</p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3>2. CÓDIGO DE LA LIGA 🏆</h3>
                        <p>Usa estos procedimientos oficiales en tu Terminal:</p>
                        <ul>
                            <li><strong>Captura:</strong> <code>CALL capturar_pokemon();</code> (Límite: 12 Pokémon).</li>
                            <li><strong>Entreno:</strong> <code>CALL entrenar_pokemon(id);</code> (Tope: 300 en stats).</li>
                            <li><strong>Evolución:</strong> Tras ganar suficientes batallas, subes de nivel. <strong>Tus stats no suben solas</strong>, pero el nivel aumenta tu daño en combate.</li>
                        </ul>
                    </section>
                    
                    <section>
                        <h3>3. CIENCIA DEL TORTAZO 👊</h3>
                        <p><strong>Fórmula Real:</strong><br>
                        <code>Daño = FLOOR((Atk / Def) * Nivel * 2 * Suerte * Tipos)</code></p>
                        <p><strong>Velocidad:</strong> El Pokémon con más <code>velocidad_actual</code> golpea primero. ¡Si eres lento, asegúrate de tener mucha Defensa!</p>
                    </section>

                    <section>
                        <h3>4. 🛠️ TU TURNO: SÉ UN MASTER</h3>
                        <p>Los mejores entrenadores automatizan. Crea tus propios <strong>Procedures</strong>:</p>
                        <div style="background: rgba(0,0,0,0.8); padding: 15px; color: #0f0; border-left: 5px solid #0f0; font-family: monospace; font-size: 0.9em; margin: 10px 0;">
                            <strong>MISIÓN:</strong> Investiga cómo usar <code>WHILE</code>, <code>DECLARE</code> y <code>SET</code> para entrenar a tus Pokémon sin mover un dedo.
                        </div>
                    </section>

                    <section style="border: 2px dashed #f00; padding: 15px; margin-top: 10px; background: rgba(255,0,0,0.1);">
                        <h3 style="color: #f00; text-align: center;">🔥 EL DESAFÍO FINAL 🔥</h3>
                        <p><strong>Analiza el Historial de Combates:</strong> Busca debilidades en los Pokémon de la otra clase y programa el bot de ataque perfecto. ¡Que gane el mejor programador!</p>
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
