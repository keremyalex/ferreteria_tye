<?php
/**
 * Monitor simple de logs para pagos QR
 * Uso: php monitor_logs.php
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Archivo de log no encontrado: $logFile\n";
    exit(1);
}

echo "📊 Monitoreando logs de Laravel en tiempo real...\n";
echo "📂 Archivo: $logFile\n";
echo "⏰ Iniciado: " . date('Y-m-d H:i:s') . "\n";
echo "💡 Presiona Ctrl+C para salir\n";
echo str_repeat("=", 80) . "\n\n";

// Función para colorear mensajes según el nivel
function colorizeMessage($line) {
    if (strpos($line, '🔍') !== false || strpos($line, 'INICIO') !== false) {
        return "\033[1;34m$line\033[0m"; // Azul brillante
    }
    if (strpos($line, '✅') !== false || strpos($line, 'exitosa') !== false) {
        return "\033[1;32m$line\033[0m"; // Verde brillante
    }
    if (strpos($line, '❌') !== false || strpos($line, 'ERROR') !== false) {
        return "\033[1;31m$line\033[0m"; // Rojo brillante
    }
    if (strpos($line, '⚠️') !== false || strpos($line, 'WARNING') !== false) {
        return "\033[1;33m$line\033[0m"; // Amarillo brillante
    }
    if (strpos($line, '🚨') !== false) {
        return "\033[1;35m$line\033[0m"; // Magenta brillante
    }
    if (strpos($line, '📥') !== false || strpos($line, '📤') !== false) {
        return "\033[1;36m$line\033[0m"; // Cyan brillante
    }
    return $line; // Sin color
}

// Obtener posición actual del archivo
$size = filesize($logFile);
$fp = fopen($logFile, 'r');
fseek($fp, $size);

// Monitorear cambios
while (true) {
    $currentSize = filesize($logFile);
    
    if ($currentSize > $size) {
        $data = fread($fp, $currentSize - $size);
        $lines = explode("\n", trim($data));
        
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                // Filtrar solo logs relacionados con PagoFácil
                if (stripos($line, 'pagofacil') !== false || 
                    stripos($line, 'qr payment') !== false ||
                    stripos($line, '🔍') !== false ||
                    stripos($line, '✅') !== false ||
                    stripos($line, '❌') !== false ||
                    stripos($line, '⚠️') !== false ||
                    stripos($line, '🚨') !== false ||
                    stripos($line, '📥') !== false ||
                    stripos($line, '📤') !== false) {
                    
                    $timestamp = date('H:i:s');
                    echo "[$timestamp] " . colorizeMessage($line) . "\n";
                }
            }
        }
        
        $size = $currentSize;
    }
    
    usleep(500000); // 0.5 segundos
}

fclose($fp);