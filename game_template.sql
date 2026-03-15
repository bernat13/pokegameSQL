-- NOTA: No usamos "USE" aquí para permitir que PHP asigne la BBDD dinámicamente.

CREATE TABLE entrenadores (
    nombre_entrenador VARCHAR(50) PRIMARY KEY,
    clase_entrenador VARCHAR(50),
    region_origen VARCHAR(50),
    lema_batalla VARCHAR(150),
    color_ropa VARCHAR(30),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE especies (
    id_especie INT PRIMARY KEY,
    nombre VARCHAR(50),
    tipo_principal VARCHAR(20),
    tipo_secundario VARCHAR(20),
    stat_base_vida INT,
    stat_base_ataque INT,
    stat_base_defensa INT,
    stat_base_atq_esp INT,
    stat_base_def_esp INT,
    stat_base_velocidad INT
);

CREATE TABLE equipo_pokemon (
    id_instancia INT AUTO_INCREMENT PRIMARY KEY,
    db_usuario VARCHAR(50),
    id_especie INT,
    mote VARCHAR(50),
    nivel INT DEFAULT 1,
    victorias INT DEFAULT 0,
    vida_actual INT,
    ataque_actual INT,
    defensa_actual INT,
    atq_esp_actual INT,
    def_esp_actual INT,
    velocidad_actual INT,
    FOREIGN KEY (id_especie) REFERENCES especies (id_especie),
    FOREIGN KEY (db_usuario) REFERENCES entrenadores (nombre_entrenador)
);

CREATE TABLE eficacia_tipos (
    tipo_ataque VARCHAR(20),
    tipo_defensor VARCHAR(20),
    multiplicador DECIMAL(3, 1),
    PRIMARY KEY (tipo_ataque, tipo_defensor)
);

CREATE TABLE historial_combates (
    id_combate INT AUTO_INCREMENT PRIMARY KEY,
    entrenador_atacante VARCHAR(50),
    id_pokemon_atacante INT,
    entrenador_defensor VARCHAR(50),
    id_pokemon_defensor INT,
    resultado VARCHAR(100),
    rondas INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE historial_torneos (
    id_torneo INT AUTO_INCREMENT PRIMARY KEY,
    entrenador_1 VARCHAR(50),
    entrenador_2 VARCHAR(50),
    ganador VARCHAR(50),
    rondas INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (entrenador_1) REFERENCES entrenadores (nombre_entrenador),
    FOREIGN KEY (entrenador_2) REFERENCES entrenadores (nombre_entrenador)
);

CREATE TABLE historial_torneo_detalles (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_torneo INT,
    id_pokemon_1 INT,
    id_pokemon_2 INT,
    ganador_bout INT,
    resultado_bout VARCHAR(100),
    FOREIGN KEY (id_torneo) REFERENCES historial_torneos (id_torneo),
    FOREIGN KEY (id_pokemon_1) REFERENCES equipo_pokemon (id_instancia),
    FOREIGN KEY (id_pokemon_2) REFERENCES equipo_pokemon (id_instancia)
);
-- =========================================================================
-- 2. INSERCIÓN DE DATOS: MATRIZ Y CATÁLOGO
-- =========================================================================
INSERT IGNORE INTO
    eficacia_tipos (
        tipo_ataque,
        tipo_defensor,
        multiplicador
    )
VALUES ('Agua', 'Fuego', 2.0),
    ('Agua', 'Roca', 2.0),
    ('Agua', 'Tierra', 2.0),
    ('Agua', 'Planta', 0.5),
    ('Agua', 'Agua', 0.5),
    ('Agua', 'Dragón', 0.5),
    ('Fuego', 'Planta', 2.0),
    ('Fuego', 'Bicho', 2.0),
    ('Fuego', 'Hielo', 2.0),
    ('Fuego', 'Agua', 0.5),
    ('Fuego', 'Fuego', 0.5),
    ('Fuego', 'Roca', 0.5),
    ('Planta', 'Agua', 2.0),
    ('Planta', 'Roca', 2.0),
    ('Planta', 'Tierra', 2.0),
    ('Planta', 'Fuego', 0.5),
    ('Planta', 'Planta', 0.5),
    ('Planta', 'Bicho', 0.5),
    ('Eléctrico', 'Agua', 2.0),
    ('Eléctrico', 'Volador', 2.0),
    ('Eléctrico', 'Planta', 0.5),
    ('Eléctrico', 'Eléctrico', 0.5),
    ('Eléctrico', 'Tierra', 0.0),
    ('Psíquico', 'Lucha', 2.0),
    ('Psíquico', 'Veneno', 2.0),
    ('Psíquico', 'Psíquico', 0.5),
    ('Lucha', 'Normal', 2.0),
    ('Lucha', 'Roca', 2.0),
    ('Lucha', 'Hielo', 2.0),
    ('Lucha', 'Volador', 0.5),
    ('Lucha', 'Psíquico', 0.5),
    ('Lucha', 'Fantasma', 0.0),
    ('Fantasma', 'Fantasma', 2.0),
    ('Fantasma', 'Psíquico', 2.0),
    ('Fantasma', 'Normal', 0.0),
    ('Normal', 'Roca', 0.5),
    ('Normal', 'Fantasma', 0.0);

REPLACE INTO
    especies (
        id_especie,
        nombre,
        tipo_principal,
        tipo_secundario,
        stat_base_vida,
        stat_base_ataque,
        stat_base_defensa,
        stat_base_atq_esp,
        stat_base_def_esp,
        stat_base_velocidad
    )
VALUES (
        1,
        'Bulbasaur',
        'Planta',
        'Veneno',
        45,
        49,
        49,
        65,
        65,
        45
    ),
    (
        2,
        'Ivysaur',
        'Planta',
        'Veneno',
        60,
        62,
        63,
        80,
        80,
        60
    ),
    (
        3,
        'Venusaur',
        'Planta',
        'Veneno',
        80,
        82,
        83,
        100,
        100,
        80
    ),
    (
        4,
        'Charmander',
        'Fuego',
        NULL,
        39,
        52,
        43,
        60,
        50,
        65
    ),
    (
        5,
        'Charmeleon',
        'Fuego',
        NULL,
        58,
        64,
        58,
        80,
        65,
        80
    ),
    (
        6,
        'Charizard',
        'Fuego',
        'Volador',
        78,
        84,
        78,
        109,
        85,
        100
    ),
    (
        7,
        'Squirtle',
        'Agua',
        NULL,
        44,
        48,
        65,
        50,
        64,
        43
    ),
    (
        8,
        'Wartortle',
        'Agua',
        NULL,
        59,
        63,
        80,
        65,
        80,
        58
    ),
    (
        9,
        'Blastoise',
        'Agua',
        NULL,
        79,
        83,
        100,
        85,
        105,
        78
    ),
    (
        10,
        'Caterpie',
        'Bicho',
        NULL,
        45,
        30,
        35,
        20,
        20,
        45
    ),
    (
        12,
        'Butterfree',
        'Bicho',
        'Volador',
        60,
        45,
        50,
        90,
        80,
        70
    ),
    (
        13,
        'Weedle',
        'Bicho',
        'Veneno',
        40,
        35,
        30,
        20,
        20,
        50
    ),
    (
        15,
        'Beedrill',
        'Bicho',
        'Veneno',
        65,
        90,
        40,
        45,
        80,
        75
    ),
    (
        16,
        'Pidgey',
        'Normal',
        'Volador',
        40,
        45,
        40,
        35,
        35,
        56
    ),
    (
        18,
        'Pidgeot',
        'Normal',
        'Volador',
        83,
        80,
        75,
        70,
        70,
        101
    ),
    (
        19,
        'Rattata',
        'Normal',
        NULL,
        30,
        56,
        35,
        25,
        35,
        72
    ),
    (
        25,
        'Pikachu',
        'Eléctrico',
        NULL,
        35,
        55,
        40,
        50,
        50,
        90
    ),
    (
        26,
        'Raichu',
        'Eléctrico',
        NULL,
        60,
        90,
        55,
        90,
        80,
        110
    ),
    (
        39,
        'Jigglypuff',
        'Normal',
        'Hada',
        115,
        45,
        20,
        45,
        25,
        20
    ),
    (
        41,
        'Zubat',
        'Veneno',
        'Volador',
        40,
        45,
        35,
        30,
        40,
        55
    ),
    (
        42,
        'Golbat',
        'Veneno',
        'Volador',
        75,
        80,
        70,
        65,
        75,
        90
    ),
    (
        52,
        'Meowth',
        'Normal',
        NULL,
        40,
        45,
        35,
        40,
        40,
        90
    ),
    (
        54,
        'Psyduck',
        'Agua',
        NULL,
        50,
        52,
        48,
        65,
        50,
        55
    ),
    (
        55,
        'Golduck',
        'Agua',
        NULL,
        80,
        82,
        78,
        95,
        80,
        85
    ),
    (
        58,
        'Growlithe',
        'Fuego',
        NULL,
        55,
        70,
        45,
        70,
        50,
        60
    ),
    (
        59,
        'Arcanine',
        'Fuego',
        NULL,
        90,
        110,
        80,
        100,
        80,
        95
    ),
    (
        63,
        'Abra',
        'Psíquico',
        NULL,
        25,
        20,
        15,
        105,
        55,
        90
    ),
    (
        65,
        'Alakazam',
        'Psíquico',
        NULL,
        55,
        50,
        45,
        135,
        95,
        120
    ),
    (
        66,
        'Machop',
        'Lucha',
        NULL,
        70,
        80,
        50,
        35,
        35,
        35
    ),
    (
        68,
        'Machamp',
        'Lucha',
        NULL,
        90,
        130,
        80,
        65,
        85,
        55
    ),
    (
        74,
        'Geodude',
        'Roca',
        'Tierra',
        40,
        80,
        100,
        30,
        30,
        20
    ),
    (
        76,
        'Golem',
        'Roca',
        'Tierra',
        80,
        120,
        130,
        55,
        65,
        45
    ),
    (
        92,
        'Gastly',
        'Fantasma',
        'Veneno',
        30,
        35,
        30,
        100,
        35,
        80
    ),
    (
        94,
        'Gengar',
        'Fantasma',
        'Veneno',
        60,
        65,
        60,
        130,
        75,
        110
    ),
    (
        95,
        'Onix',
        'Roca',
        'Tierra',
        35,
        45,
        160,
        30,
        45,
        70
    ),
    (
        104,
        'Cubone',
        'Tierra',
        NULL,
        50,
        50,
        95,
        40,
        50,
        35
    ),
    (
        113,
        'Chansey',
        'Normal',
        NULL,
        250,
        5,
        5,
        35,
        105,
        50
    ),
    (
        123,
        'Scyther',
        'Bicho',
        'Volador',
        70,
        110,
        80,
        55,
        80,
        105
    ),
    (
        129,
        'Magikarp',
        'Agua',
        NULL,
        20,
        10,
        55,
        15,
        20,
        80
    ),
    (
        130,
        'Gyarados',
        'Agua',
        'Volador',
        95,
        125,
        79,
        60,
        100,
        81
    ),
    (
        131,
        'Lapras',
        'Agua',
        'Hielo',
        130,
        85,
        80,
        85,
        95,
        60
    ),
    (
        133,
        'Eevee',
        'Normal',
        NULL,
        55,
        55,
        50,
        45,
        65,
        55
    ),
    (
        134,
        'Vaporeon',
        'Agua',
        NULL,
        130,
        65,
        60,
        110,
        95,
        65
    ),
    (
        135,
        'Jolteon',
        'Eléctrico',
        NULL,
        65,
        65,
        60,
        110,
        95,
        130
    ),
    (
        136,
        'Flareon',
        'Fuego',
        NULL,
        65,
        130,
        60,
        95,
        110,
        65
    ),
    (
        143,
        'Snorlax',
        'Normal',
        NULL,
        160,
        110,
        65,
        65,
        110,
        30
    ),
    (
        144,
        'Articuno',
        'Hielo',
        'Volador',
        90,
        85,
        100,
        95,
        125,
        85
    ),
    (
        145,
        'Zapdos',
        'Eléctrico',
        'Volador',
        90,
        90,
        85,
        125,
        90,
        100
    ),
    (
        146,
        'Moltres',
        'Fuego',
        'Volador',
        90,
        100,
        90,
        125,
        85,
        90
    ),
    (
        149,
        'Dragonite',
        'Dragón',
        'Volador',
        91,
        134,
        95,
        100,
        100,
        80
    ),
    (
        150,
        'Mewtwo',
        'Psíquico',
        NULL,
        106,
        110,
        90,
        154,
        90,
        130
    );

-- =========================================================================
-- 3. TRIGGERS DE SEGURIDAD
-- =========================================================================
DELIMITER //

CREATE OR REPLACE TRIGGER trg_registro_entrenador BEFORE INSERT ON entrenadores FOR EACH ROW
BEGIN
    IF NEW.nombre_entrenador != SUBSTRING_INDEX(USER(), '@', 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '¡Tramposo! Tu nombre_entrenador debe coincidir con tu usuario de MariaDB.'; END IF;
END //

CREATE OR REPLACE TRIGGER trg_update_entrenador BEFORE UPDATE ON entrenadores FOR EACH ROW
BEGIN
    IF OLD.nombre_entrenador != SUBSTRING_INDEX(USER(), '@', 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '¡Ey! Solo puedes modificar tu propio perfil.'; END IF;
    IF NEW.nombre_entrenador != SUBSTRING_INDEX(USER(), '@', 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puedes modificar tu nombre una vez registrado.'; END IF;
END //

CREATE OR REPLACE TRIGGER trg_delete_entrenador BEFORE DELETE ON entrenadores FOR EACH ROW
BEGIN
    IF OLD.nombre_entrenador != SUBSTRING_INDEX(USER(), '@', 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '¡Alto ahí! Solo puedes borrar tu propio perfil.'; END IF;
END //

CREATE OR REPLACE TRIGGER trg_anti_trampas_combate BEFORE INSERT ON historial_combates FOR EACH ROW
BEGIN
    IF NEW.entrenador_atacante != SUBSTRING_INDEX(USER(), '@', 1) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '¡Tramposo! Solo puedes registrar ataques iniciados por ti o por el árbitro.'; END IF;
END //

CREATE OR REPLACE TRIGGER trg_check_mote_owner BEFORE UPDATE ON equipo_pokemon FOR EACH ROW
BEGIN
    -- Permitir todo si es el root (esto incluye los procedimientos con SQL SECURITY DEFINER)
    IF SUBSTRING_INDEX(USER(), '@', 1) != 'root' THEN
        -- Si intentan cambiar de dueño, error
        IF OLD.db_usuario != NEW.db_usuario THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No puedes transferir Pokémon.';
        END IF;
        
        -- Si no es su propio Pokémon, error
        IF OLD.db_usuario != SUBSTRING_INDEX(USER(), '@', 1) THEN
             SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = '¡Ey! Ese no es tu Pokémon.';
        END IF;
    END IF;
END //

-- =========================================================================
-- 4. PROCEDURES BASE: CAPTURA, ENTRENAMIENTO Y EXPERIENCIA
-- =========================================================================
CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE capturar_pokemon()
    SQL SECURITY DEFINER
BEGIN
    DECLARE v_usuario_actual VARCHAR(50); DECLARE v_entrenador_existe INT; DECLARE v_cantidad_actual INT;
    DECLARE v_especie_random INT;
    DECLARE v_hp_base INT; DECLARE v_atk_base INT; DECLARE v_def_base INT; 
    DECLARE v_spa_base INT; DECLARE v_spd_base INT; DECLARE v_spe_base INT;
    
    SET v_usuario_actual = SUBSTRING_INDEX(USER(), '@', 1);
    SELECT COUNT(*) INTO v_entrenador_existe FROM entrenadores WHERE nombre_entrenador = v_usuario_actual;
    IF v_entrenador_existe = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debes registrarte primero.'; END IF;

    SELECT COUNT(*) INTO v_cantidad_actual FROM equipo_pokemon WHERE db_usuario = v_usuario_actual;
    IF v_cantidad_actual >= 12 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Límite de 12 Pokémon alcanzado.'; END IF;

    SELECT id_especie, stat_base_vida, stat_base_ataque, stat_base_defensa, stat_base_atq_esp, stat_base_def_esp, stat_base_velocidad 
    INTO v_especie_random, v_hp_base, v_atk_base, v_def_base, v_spa_base, v_spd_base, v_spe_base FROM especies ORDER BY RAND() LIMIT 1;
    
    INSERT INTO equipo_pokemon (db_usuario, id_especie, nivel, victorias, vida_actual, ataque_actual, defensa_actual, atq_esp_actual, def_esp_actual, velocidad_actual)
    VALUES (v_usuario_actual, v_especie_random, 1, 0, v_hp_base, v_atk_base, v_def_base, v_spa_base, v_spd_base, v_spe_base);
    SELECT '¡Has capturado un Pokémon de Nivel 1! Entrénalo y llévalo a combatir.' AS Resultado;
END //

CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE entrenar_pokemon(IN p_id_instancia INT)
    SQL SECURITY DEFINER
BEGIN
    DECLARE v_usuario_actual VARCHAR(50); DECLARE v_dueno VARCHAR(50); DECLARE v_nivel INT;
    DECLARE v_hp_act INT; DECLARE v_atk_act INT; DECLARE v_def_act INT; DECLARE v_spa_act INT; DECLARE v_spd_act INT; DECLARE v_spe_act INT;
    DECLARE v_hp_base INT; DECLARE v_atk_base INT; DECLARE v_def_base INT; DECLARE v_spa_base INT; DECLARE v_spd_base INT; DECLARE v_spe_base INT;

    SET v_usuario_actual = SUBSTRING_INDEX(USER(), '@', 1);
    SELECT eq.db_usuario, eq.nivel, eq.vida_actual, eq.ataque_actual, eq.defensa_actual, eq.atq_esp_actual, eq.def_esp_actual, eq.velocidad_actual,
           e.stat_base_vida, e.stat_base_ataque, e.stat_base_defensa, e.stat_base_atq_esp, e.stat_base_def_esp, e.stat_base_velocidad
    INTO v_dueno, v_nivel, v_hp_act, v_atk_act, v_def_act, v_spa_act, v_spd_act, v_spe_act,
         v_hp_base, v_atk_base, v_def_base, v_spa_base, v_spd_base, v_spe_base
    FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.id_instancia = p_id_instancia;
    
    IF v_dueno != v_usuario_actual THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Solo puedes entrenar a tus Pokémon.'; END IF;

    IF (v_atk_act >= 300) OR (v_def_act >= 300) OR (v_hp_act >= 300) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'LÍMITE DE STATS: Alguna estadística ha llegado al máximo de 300.';
    END IF;

    UPDATE equipo_pokemon 
    SET vida_actual = v_hp_act + FLOOR(RAND() * 3), ataque_actual = v_atk_act + FLOOR(RAND() * 3),
        defensa_actual = v_def_act + FLOOR(RAND() * 3), atq_esp_actual = v_spa_act + FLOOR(RAND() * 3),
        def_esp_actual = v_spd_act + FLOOR(RAND() * 3), velocidad_actual = v_spe_act + FLOOR(RAND() * 3)
    WHERE id_instancia = p_id_instancia;
END //

CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE procesar_victoria(IN p_id_pokemon INT)
    SQL SECURITY DEFINER
BEGIN
    DECLARE v_nivel INT; DECLARE v_victorias INT; DECLARE v_meta INT;
    DECLARE v_hp_base INT; DECLARE v_atk_base INT; DECLARE v_def_base INT; DECLARE v_spa_base INT; DECLARE v_spd_base INT; DECLARE v_spe_base INT;

    SELECT eq.nivel, eq.victorias, e.stat_base_vida, e.stat_base_ataque, e.stat_base_defensa, e.stat_base_atq_esp, e.stat_base_def_esp, e.stat_base_velocidad
    INTO v_nivel, v_victorias, v_hp_base, v_atk_base, v_def_base, v_spa_base, v_spd_base, v_spe_base
    FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.id_instancia = p_id_pokemon;

    SET v_victorias = v_victorias + 1;
    SET v_meta = 10 + (v_nivel * 5); 

    IF v_victorias >= v_meta THEN
        SET v_nivel = v_nivel + 1; SET v_victorias = 0;
        UPDATE equipo_pokemon 
        SET nivel = v_nivel, victorias = v_victorias,
            vida_actual = LEAST(v_hp_base + (v_nivel * 3), 300), 
            ataque_actual = LEAST(v_atk_base + (v_nivel * 3), 300),
            defensa_actual = LEAST(v_def_base + (v_nivel * 3), 300), 
            atq_esp_actual = LEAST(v_spa_base + (v_nivel * 3), 300),
            def_esp_actual = LEAST(v_spd_base + (v_nivel * 3), 300), 
            velocidad_actual = LEAST(v_spe_base + (v_nivel * 3), 300)
        WHERE id_instancia = p_id_pokemon;
    ELSE
        UPDATE equipo_pokemon SET victorias = v_victorias WHERE id_instancia = p_id_pokemon;
    END IF;
END //

CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE procesar_derrota(IN p_id_pokemon INT)
    SQL SECURITY DEFINER
BEGIN
    DECLARE v_victorias INT;
    SELECT victorias INTO v_victorias FROM equipo_pokemon WHERE id_instancia = p_id_pokemon;
    IF v_victorias > 0 THEN UPDATE equipo_pokemon SET victorias = victorias - 1 WHERE id_instancia = p_id_pokemon; END IF;
END //

-- =========================================================================
-- 5. MOTORES DE COMBATE OFICIALES
-- =========================================================================
CREATE OR REPLACE DEFINER=`root`@`localhost` FUNCTION calcular_dano_oficial(
    p_ataque INT, 
    p_defensa INT, 
    p_nivel INT, 
    p_tipo_ataque VARCHAR(20), 
    p_tipo_defensor VARCHAR(20)
) RETURNS INT 
READS SQL DATA 
SQL SECURITY DEFINER
BEGIN
    DECLARE v_multiplicador DECIMAL(3,1) DEFAULT 1.0;
    DECLARE v_aleatorio DECIMAL(3,2);
    DECLARE v_dano_final INT;

    SELECT IFNULL(MAX(multiplicador), 1.0) 
    INTO v_multiplicador 
    FROM eficacia_tipos 
    WHERE tipo_ataque = p_tipo_ataque 
      AND tipo_defensor = p_tipo_defensor;

    SET v_aleatorio = 0.85 + (RAND() * 0.15);

    IF p_defensa <= 0 THEN 
        SET p_defensa = 1; 
    END IF;

    -- Calculo simple: (A/D) * Lvl * Multiplicador
    SET v_dano_final = FLOOR((p_ataque / p_defensa) * p_nivel * 2 * v_aleatorio * v_multiplicador);

    IF v_dano_final < 1 THEN 
        SET v_dano_final = 1; 
    END IF;

    RETURN v_dano_final;
END //

CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE batalla_individual_oficial(IN p_mi_pokemon INT, IN p_rival_pokemon INT)
    SQL SECURITY DEFINER
    DETERMINISTIC
BEGIN
    DECLARE v_yo VARCHAR(50); DECLARE v_rival VARCHAR(50);
    DECLARE v_mi_hp INT; DECLARE v_mi_atk INT; DECLARE v_mi_def INT; DECLARE v_mi_vel INT; DECLARE v_mi_lvl INT; 
    DECLARE v_mi_tipo VARCHAR(20); DECLARE v_mi_mote VARCHAR(50); DECLARE v_mi_esp VARCHAR(50);
    DECLARE v_riv_hp INT; DECLARE v_riv_atk INT; DECLARE v_riv_def INT; DECLARE v_riv_vel INT; DECLARE v_riv_lvl INT; 
    DECLARE v_riv_tipo VARCHAR(20); DECLARE v_riv_mote VARCHAR(50); DECLARE v_riv_esp VARCHAR(50);
    DECLARE v_dmg INT; DECLARE v_ganador VARCHAR(150);
    DECLARE v_rondas INT DEFAULT 0;

    SET v_yo = SUBSTRING_INDEX(USER(), '@', 1);

    SELECT eq.db_usuario, eq.vida_actual, eq.ataque_actual, eq.defensa_actual, eq.velocidad_actual, eq.nivel, e.tipo_principal, eq.mote, e.nombre 
    INTO v_yo, v_mi_hp, v_mi_atk, v_mi_def, v_mi_vel, v_mi_lvl, v_mi_tipo, v_mi_mote, v_mi_esp 
    FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie 
    WHERE id_instancia = p_mi_pokemon AND db_usuario = v_yo;

    SELECT eq.db_usuario, eq.vida_actual, eq.ataque_actual, eq.defensa_actual, eq.velocidad_actual, eq.nivel, e.tipo_principal, eq.mote, e.nombre 
    INTO v_rival, v_riv_hp, v_riv_atk, v_riv_def, v_riv_vel, v_riv_lvl, v_riv_tipo, v_riv_mote, v_riv_esp 
    FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie 
    WHERE id_instancia = p_rival_pokemon;

    combate: WHILE v_mi_hp > 0 AND v_riv_hp > 0 DO
        SET v_rondas = v_rondas + 1;
        IF v_mi_vel >= v_riv_vel THEN
            SET v_dmg = calcular_dano_oficial(v_mi_atk, v_riv_def, v_mi_lvl, v_mi_tipo, v_riv_tipo); SET v_riv_hp = v_riv_hp - v_dmg; 
            IF v_riv_hp <= 0 THEN 
                SET v_ganador = CONCAT('Victoria de ', IFNULL(CONCAT(v_mi_mote, ' (', v_mi_esp, ')'), v_mi_esp), ' de ', v_yo); 
                CALL procesar_victoria(p_mi_pokemon); CALL procesar_derrota(p_rival_pokemon); LEAVE combate; 
            END IF;
            SET v_dmg = calcular_dano_oficial(v_riv_atk, v_mi_def, v_riv_lvl, v_riv_tipo, v_mi_tipo); SET v_mi_hp = v_mi_hp - v_dmg; 
            IF v_mi_hp <= 0 THEN 
                SET v_ganador = CONCAT('Victoria de ', IFNULL(CONCAT(v_riv_mote, ' (', v_riv_esp, ')'), v_riv_esp), ' de ', v_rival); 
                CALL procesar_victoria(p_rival_pokemon); CALL procesar_derrota(p_mi_pokemon); LEAVE combate; 
            END IF;
        ELSE
            SET v_dmg = calcular_dano_oficial(v_riv_atk, v_mi_def, v_riv_lvl, v_riv_tipo, v_mi_tipo); SET v_mi_hp = v_mi_hp - v_dmg; 
            IF v_mi_hp <= 0 THEN 
                SET v_ganador = CONCAT('Victoria de ', IFNULL(CONCAT(v_riv_mote, ' (', v_riv_esp, ')'), v_riv_esp), ' de ', v_rival); 
                CALL procesar_victoria(p_rival_pokemon); CALL procesar_derrota(p_mi_pokemon); LEAVE combate; 
            END IF;
            SET v_dmg = calcular_dano_oficial(v_mi_atk, v_riv_def, v_mi_lvl, v_mi_tipo, v_riv_tipo); SET v_riv_hp = v_riv_hp - v_dmg; 
            IF v_riv_hp <= 0 THEN 
                SET v_ganador = CONCAT('Victoria de ', IFNULL(CONCAT(v_mi_mote, ' (', v_mi_esp, ')'), v_mi_esp), ' de ', v_yo); 
                CALL procesar_victoria(p_mi_pokemon); CALL procesar_derrota(p_rival_pokemon); LEAVE combate; 
            END IF;
        END IF;
    END WHILE;
    INSERT INTO historial_combates (entrenador_atacante, id_pokemon_atacante, entrenador_defensor, id_pokemon_defensor, resultado, rondas) VALUES (v_yo, p_mi_pokemon, v_rival, p_rival_pokemon, v_ganador, v_rondas);
    SELECT CONCAT('Combate 1v1 finalizado en ', v_rondas, ' rondas. ', v_ganador) AS Resultado;
END //

CREATE OR REPLACE DEFINER=`root`@`localhost` PROCEDURE batalla_equipo_oficial(IN p_entrenador_1 VARCHAR(50), IN p_entrenador_2 VARCHAR(50))
    SQL SECURITY DEFINER
BEGIN
    DECLARE v_ejecutor VARCHAR(50); DECLARE v_ganador VARCHAR(50); DECLARE v_dmg INT; DECLARE v_quedan_1 INT; DECLARE v_quedan_2 INT;
    DECLARE v_mi_id INT; DECLARE v_mi_hp INT; DECLARE v_mi_atk INT; DECLARE v_mi_def INT; DECLARE v_mi_vel INT; DECLARE v_mi_lvl INT; DECLARE v_mi_tipo VARCHAR(20);
    DECLARE v_riv_id INT; DECLARE v_riv_hp INT; DECLARE v_riv_atk INT; DECLARE v_riv_def INT; DECLARE v_riv_vel INT; DECLARE v_riv_lvl INT; DECLARE v_riv_tipo VARCHAR(20);
    DECLARE v_rondas INT DEFAULT 0;
    DECLARE v_id_torneo INT;
    DECLARE v_ganador_bout INT;

    SET v_ejecutor = SUBSTRING_INDEX(USER(), '@', 1);
    IF p_entrenador_1 = p_entrenador_2 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Un entrenador no puede pelear contra sí mismo.'; END IF;

    -- Registrar el inicio del torneo
    INSERT INTO historial_torneos (entrenador_1, entrenador_2, ganador, rondas) VALUES (p_entrenador_1, p_entrenador_2, 'PENDIENTE', 0);
    SET v_id_torneo = LAST_INSERT_ID();

    DROP TEMPORARY TABLE IF EXISTS temp_eq_1; CREATE TEMPORARY TABLE temp_eq_1 AS SELECT eq.id_instancia, eq.vida_actual, eq.ataque_actual, eq.defensa_actual, eq.velocidad_actual, eq.nivel, e.tipo_principal FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.db_usuario = p_entrenador_1 ORDER BY RAND() LIMIT 6;
    DROP TEMPORARY TABLE IF EXISTS temp_eq_2; CREATE TEMPORARY TABLE temp_eq_2 AS SELECT eq.id_instancia, eq.vida_actual, eq.ataque_actual, eq.defensa_actual, eq.velocidad_actual, eq.nivel, e.tipo_principal FROM equipo_pokemon eq JOIN especies e ON eq.id_especie = e.id_especie WHERE eq.db_usuario = p_entrenador_2 ORDER BY RAND() LIMIT 6;

    equipo_loop: LOOP
        SELECT COUNT(*) INTO v_quedan_1 FROM temp_eq_1 WHERE vida_actual > 0; SELECT COUNT(*) INTO v_quedan_2 FROM temp_eq_2 WHERE vida_actual > 0;
        IF v_quedan_1 = 0 THEN SET v_ganador = p_entrenador_2; LEAVE equipo_loop; END IF;
        IF v_quedan_2 = 0 THEN SET v_ganador = p_entrenador_1; LEAVE equipo_loop; END IF;

        SELECT id_instancia, vida_actual, ataque_actual, defensa_actual, velocidad_actual, nivel, tipo_principal INTO v_mi_id, v_mi_hp, v_mi_atk, v_mi_def, v_mi_vel, v_mi_lvl, v_mi_tipo FROM temp_eq_1 WHERE vida_actual > 0 ORDER BY nivel DESC LIMIT 1;
        SELECT id_instancia, vida_actual, ataque_actual, defensa_actual, velocidad_actual, nivel, tipo_principal INTO v_riv_id, v_riv_hp, v_riv_atk, v_riv_def, v_riv_vel, v_riv_lvl, v_riv_tipo FROM temp_eq_2 WHERE vida_actual > 0 ORDER BY nivel DESC LIMIT 1;

        combate_1v1: WHILE v_mi_hp > 0 AND v_riv_hp > 0 DO
            SET v_rondas = v_rondas + 1;
            IF v_mi_vel >= v_riv_vel THEN
                SET v_dmg = calcular_dano_oficial(v_mi_atk, v_riv_def, v_mi_lvl, v_mi_tipo, v_riv_tipo); SET v_riv_hp = v_riv_hp - v_dmg; 
                IF v_riv_hp <= 0 THEN SET v_ganador_bout = v_mi_id; LEAVE combate_1v1; END IF;
                SET v_dmg = calcular_dano_oficial(v_riv_atk, v_mi_def, v_riv_lvl, v_riv_tipo, v_mi_tipo); SET v_mi_hp = v_mi_hp - v_dmg; 
                IF v_mi_hp <= 0 THEN SET v_ganador_bout = v_riv_id; LEAVE combate_1v1; END IF;
            ELSE
                SET v_dmg = calcular_dano_oficial(v_riv_atk, v_mi_def, v_riv_lvl, v_riv_tipo, v_mi_tipo); SET v_mi_hp = v_mi_hp - v_dmg; 
                IF v_mi_hp <= 0 THEN SET v_ganador_bout = v_riv_id; LEAVE combate_1v1; END IF;
                SET v_dmg = calcular_dano_oficial(v_mi_atk, v_riv_def, v_mi_lvl, v_mi_tipo, v_riv_tipo); SET v_riv_hp = v_riv_hp - v_dmg; 
                IF v_riv_hp <= 0 THEN SET v_ganador_bout = v_mi_id; LEAVE combate_1v1; END IF;
            END IF;
        END WHILE combate_1v1;

        -- Registrar cada enfrentamiento detalle
        INSERT INTO historial_torneo_detalles (id_torneo, id_pokemon_1, id_pokemon_2, ganador_bout, resultado_bout) 
        VALUES (v_id_torneo, v_mi_id, v_riv_id, v_ganador_bout, CONCAT('Ganador: ', v_ganador_bout));

        UPDATE temp_eq_1 SET vida_actual = v_mi_hp WHERE id_instancia = v_mi_id; UPDATE temp_eq_2 SET vida_actual = v_riv_hp WHERE id_instancia = v_riv_id;
    END LOOP equipo_loop;

    DROP TEMPORARY TABLE temp_eq_1; DROP TEMPORARY TABLE temp_eq_2;
    
    -- Actualizar el ganador final
    UPDATE historial_torneos SET ganador = v_ganador, rondas = v_rondas WHERE id_torneo = v_id_torneo;
    
    SELECT CONCAT('Torneo Oficial finalizado en ', v_rondas, ' rondas. Árbitro: ', v_ejecutor, ' | Ganador: ', v_ganador) AS Resultado;
END //

DELIMITER ;

-- =========================================================================
-- 6. VISTA DE SEGURIDAD (ROW LEVEL SECURITY) Y RANKING
-- =========================================================================

-- Vista para que el usuario solo vea y edite SUS propios pokemon
CREATE OR REPLACE VIEW mi_equipo AS
SELECT *
FROM equipo_pokemon
WHERE
    db_usuario = SUBSTRING_INDEX(USER(), '@', 1)
WITH
    CHECK OPTION;

CREATE OR REPLACE VIEW ranking_liga AS
SELECT
    ganador AS entrenador_campeon,
    COUNT(*) AS victorias_oficiales
FROM historial_torneos
GROUP BY
    entrenador_campeon
ORDER BY victorias_oficiales DESC;

-- =========================================================================
-- 7. CREACIÓN DE ROLES Y USUARIOS
CREATE ROLE IF NOT EXISTS 'entrenador_role';

GRANT SELECT ON especies TO 'entrenador_role';

GRANT SELECT, UPDATE (mote) ON mi_equipo TO 'entrenador_role';

GRANT SELECT, UPDATE (mote) ON equipo_pokemon TO 'entrenador_role';

GRANT SELECT ON eficacia_tipos TO 'entrenador_role';

GRANT SELECT ON historial_combates TO 'entrenador_role';

GRANT SELECT ON historial_torneos TO 'entrenador_role';

GRANT SELECT ON historial_torneo_detalles TO 'entrenador_role';

GRANT SELECT ON ranking_liga TO 'entrenador_role';

GRANT SELECT, INSERT ON entrenadores TO 'entrenador_role';

GRANT
UPDATE (
    region_origen,
    lema_batalla,
    color_ropa
) ON entrenadores TO 'entrenador_role';

GRANT CREATE ROUTINE ON *.* TO 'entrenador_role';

GRANT EXECUTE ON PROCEDURE capturar_pokemon TO 'entrenador_role';

GRANT EXECUTE ON PROCEDURE entrenar_pokemon TO 'entrenador_role';

GRANT
EXECUTE ON PROCEDURE batalla_individual_oficial TO 'entrenador_role';

GRANT
EXECUTE ON PROCEDURE batalla_equipo_oficial TO 'entrenador_role';

GRANT EXECUTE ON FUNCTION calcular_dano_oficial TO 'entrenador_role';

FLUSH PRIVILEGES;