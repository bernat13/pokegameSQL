<?php
/**
 * Script para descargar los sprites de Pokémon (estilo GBC) localmente.
 * Se puede ejecutar desde la línea de comandos o visitándolo en el navegador una vez.
 */

$targetDir = __DIR__ . '/../assets/sprites/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Lista de IDs basados en el script SQL proporcionado
$pokemonIds = [
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 15, 16, 18, 19, 25, 26, 39, 41, 42, 
    52, 54, 55, 58, 59, 63, 65, 66, 68, 74, 76, 92, 94, 95, 104, 113, 123, 129, 
    130, 131, 133, 134, 135, 136, 143, 144, 145, 146, 149, 150
];

// Fuente: PokeAPI - Sprites de Pokémon Crystal (GBC auténtico)
$baseUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/versions/generation-ii/crystal/";

echo "Iniciando descarga de sprites GBC...\n";

foreach ($pokemonIds as $id) {
    $filename = $id . ".png";
    $localPath = $targetDir . $filename;
    $remoteUrl = $baseUrl . $filename;

    if (!file_exists($localPath)) {
        echo "Descargando Pokémon #$id... ";
        $content = @file_get_contents($remoteUrl);
        if ($content !== false) {
            file_put_contents($localPath, $content);
            echo "OK\n";
        } else {
            // Reintento con sprite normal si el GBC falla
            $fallbackUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/$id.png";
            $content = @file_get_contents($fallbackUrl);
            if ($content !== false) {
                file_put_contents($localPath, $content);
                echo "OK (Fallback)\n";
            } else {
                echo "ERROR\n";
            }
        }
    } else {
        echo "Pokémon #$id ya existe.\n";
    }
}

echo "Proceso finalizado.\n";
?>
